<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use App\Services\MoMoService;
use App\Services\TelegramNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MoMoPaymentController extends Controller
{
    /**
     * Tạo phiên thanh toán MoMo cho đơn hàng (Dùng khi thanh toán lại từ trang chi tiết đơn).
     */
    public function create(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác trên đơn hàng này.');
        }

        $payment = $order->payment ?? $order->payments()->first();
        if ($payment && in_array($payment->payment_status, ['paid', 'completed'])) {
            return redirect()->route('orders.show', $order->order_code)
                ->with('info', 'Đơn hàng này đã được thanh toán trước đó.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('orders.show', $order->order_code)
                ->with('error', 'Đơn hàng này đã bị hủy, không thể tiếp tục thanh toán.');
        }

        $result = MoMoService::createPayment($order);

        if ($result['success'] && ! empty($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        return redirect()->route('orders.show', $order->order_code)
            ->with('error', 'Không thể kết nối đến Cổng MoMo: '.($result['message'] ?? 'Vui lòng thử lại sau.'));
    }

    /**
     * Nhận kết quả điều hướng về từ MoMo sau khi khách hàng thao tác thanh toán (Return URL).
     */
    public function callback(Request $request): RedirectResponse
    {
        Log::info('MoMo Callback received:', $request->all());

        $isValidSignature = MoMoService::verifySignature($request->all());

        if (! $isValidSignature) {
            Log::warning('MoMo Callback signature verification failed', $request->all());

            return redirect()->route('home')->with('error', 'Chữ ký xác thực từ MoMo không hợp lệ!');
        }

        $order = $this->resolveOrderFromRequest($request);

        if (! $order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng tương ứng với giao dịch MoMo.');
        }

        $resultCode = (int) $request->input('resultCode', -1);
        $transId = (string) $request->input('transId', '');
        $amount = (float) $request->input('amount', 0);
        $message = (string) $request->input('message', '');

        $payment = $order->payment ?? $order->payments()->first();

        if ($resultCode === 0) {
            // Giao dịch MoMo thành công
            if (! $payment || ! in_array($payment->payment_status, ['paid', 'completed'])) {
                DB::transaction(function () use ($order, $payment, $transId, $amount) {
                    if ($payment) {
                        $payment->update([
                            'payment_method' => 'momo',
                            'payment_status' => 'paid',
                            'transaction_code' => $transId,
                            'paid_at' => now(),
                            'amount' => $amount > 0 ? $amount : $payment->amount,
                        ]);
                    }

                    if ($order->status === 'pending') {
                        $order->update(['status' => 'confirmed']);
                    }

                    $note = "Thanh toán trực tuyến thành công qua Cổng MoMo (Mã GD: {$transId} - Số tiền: ".number_format($amount > 0 ? $amount : $order->total_amount).'đ)';
                    $order->logStatusHistory('confirmed', $note, 'system', null, 'Cổng Thanh Toán MoMo');
                });

                // Gửi email cập nhật trạng thái đơn
                try {
                    if ($order->user && $order->user->email) {
                        Mail::to($order->user->email)->send(new OrderStatusUpdatedMail($order, 'Đã thanh toán MoMo'));
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed to send MoMo paid email: '.$e->getMessage());
                }

                // Gửi thông báo Telegram cho Admin
                try {
                    $tgMsg = "🌸 <b>THANH TOÁN MOMO THÀNH CÔNG!</b>\n"
                        ."━━━━━━━━━━━━━━━━━━━━\n"
                        ."🔖 <b>Mã đơn hàng:</b> #<code>{$order->order_code}</code>\n"
                        ."👤 <b>Khách hàng:</b> {$order->recipient_name}\n"
                        .'💵 <b>Số tiền:</b> '.number_format($amount > 0 ? $amount : $order->total_amount)."đ\n"
                        ."📱 <b>Mã giao dịch MoMo:</b> <code>{$transId}</code>\n"
                        .'⏰ <b>Thời gian:</b> '.now()->format('d/m/Y H:i:s')."\n"
                        ."━━━━━━━━━━━━━━━━━━━━\n"
                        .'Đơn hàng đã được tự động duyệt sang trạng thái: <b>Đã Xác Nhận</b>.';
                    TelegramNotificationService::sendMessage($tgMsg);
                } catch (\Throwable $e) {
                    Log::error('Failed to send MoMo TG notify: '.$e->getMessage());
                }
            }

            return redirect()->route('orders.show', $order->order_code)
                ->with('success', 'Thanh toán qua Cổng MoMo thành công! Đơn hàng của bạn đã được xác nhận.');
        }

        // Giao dịch không thành công hoặc khách bấm Hủy
        Log::info("MoMo payment cancelled or failed for order {$order->order_code}: Code {$resultCode} - {$message}");

        return redirect()->route('orders.show', $order->order_code)
            ->with('error', "Giao dịch MoMo không thành công ({$message}). Bạn có thể bấm nút Thanh toán lại bên dưới.");
    }

    /**
     * Nhận thông báo tức thì (IPN Webhook) ngầm từ máy chủ MoMo.
     */
    public function ipn(Request $request): JsonResponse|Response
    {
        Log::info('MoMo IPN Webhook received:', $request->all());

        $isValidSignature = MoMoService::verifySignature($request->all());

        if (! $isValidSignature) {
            Log::warning('MoMo IPN signature verification failed', $request->all());

            return response()->json([
                'resultCode' => 11007,
                'message' => 'Chữ ký không hợp lệ',
            ], 400);
        }

        $order = $this->resolveOrderFromRequest($request);

        if (! $order) {
            return response()->json([
                'resultCode' => 11001,
                'message' => 'Không tìm thấy đơn hàng',
            ], 404);
        }

        $resultCode = (int) $request->input('resultCode', -1);
        $transId = (string) $request->input('transId', '');
        $amount = (float) $request->input('amount', 0);

        $payment = $order->payment ?? $order->payments()->first();

        if ($resultCode === 0) {
            if (! $payment || ! in_array($payment->payment_status, ['paid', 'completed'])) {
                DB::transaction(function () use ($order, $payment, $transId, $amount) {
                    if ($payment) {
                        $payment->update([
                            'payment_method' => 'momo',
                            'payment_status' => 'paid',
                            'transaction_code' => $transId,
                            'paid_at' => now(),
                            'amount' => $amount > 0 ? $amount : $payment->amount,
                        ]);
                    }

                    if ($order->status === 'pending') {
                        $order->update(['status' => 'confirmed']);
                    }

                    $note = "Thanh toán tự động ghi nhận qua Webhook MoMo IPN (Mã GD: {$transId} - Số tiền: ".number_format($amount > 0 ? $amount : $order->total_amount).'đ)';
                    $order->logStatusHistory('confirmed', $note, 'system', null, 'Cổng Thanh Toán MoMo IPN');
                });

                try {
                    if ($order->user && $order->user->email) {
                        Mail::to($order->user->email)->send(new OrderStatusUpdatedMail($order, 'Đã xác nhận thanh toán MoMo'));
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed to send MoMo IPN mail: '.$e->getMessage());
                }

                try {
                    $tgMsg = "🌸 <b>MOMO IPN: XÁC NHẬN THANH TOÁN TỰ ĐỘNG!</b>\n"
                        ."━━━━━━━━━━━━━━━━━━━━\n"
                        ."🔖 <b>Mã đơn hàng:</b> #<code>{$order->order_code}</code>\n"
                        ."👤 <b>Khách hàng:</b> {$order->recipient_name}\n"
                        .'💵 <b>Số tiền:</b> '.number_format($amount > 0 ? $amount : $order->total_amount)."đ\n"
                        ."📱 <b>Mã GD MoMo:</b> <code>{$transId}</code>\n"
                        .'⏰ <b>Thời gian:</b> '.now()->format('d/m/Y H:i:s');
                    TelegramNotificationService::sendMessage($tgMsg);
                } catch (\Throwable $e) {
                    Log::error('Failed to send MoMo IPN TG notify: '.$e->getMessage());
                }
            }
        }

        // Phản hồi HTTP 204 No Content theo chuẩn MoMo Webhook
        return response()->noContent();
    }

    /**
     * Tìm Order tương ứng từ payload MoMo.
     */
    protected function resolveOrderFromRequest(Request $request): ?Order
    {
        // 1. Thử giải mã từ extraData
        $extraData = $request->input('extraData');
        if (! empty($extraData)) {
            $decoded = json_decode(base64_decode($extraData), true);
            if (! empty($decoded['order_code'])) {
                $order = Order::with(['payment', 'user'])->where('order_code', $decoded['order_code'])->first();
                if ($order) {
                    return $order;
                }
            }
            if (! empty($decoded['order_id'])) {
                $order = Order::with(['payment', 'user'])->find($decoded['order_id']);
                if ($order) {
                    return $order;
                }
            }
        }

        // 2. Tìm mã đơn dạng ORD-YYYYMMDD-XXXXX trong orderId hoặc orderInfo
        $searchString = (string) $request->input('orderId', '').' '.(string) $request->input('orderInfo', '');
        if (preg_match('/ORD-\d{8}-[A-Za-z0-9]{4,6}/i', $searchString, $matches)) {
            $orderCode = strtoupper($matches[0]);

            return Order::with(['payment', 'user'])->where('order_code', $orderCode)->first();
        }

        return null;
    }
}
