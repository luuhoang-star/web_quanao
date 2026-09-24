<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use App\Services\TelegramNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentWebhookController extends Controller
{
    /**
     * Nhận và xử lý Webhook biến động số dư / thanh toán tự động (SePay, Casso, Ngân hàng).
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('Payment Webhook received:', $request->all());

        $settings = Cache::get('site_settings', []);
        $configuredKey = $settings['webhook_api_key'] ?? '';
        $autoConfirm = $settings['auto_confirm_payment'] ?? true;

        if (! $autoConfirm) {
            return response()->json([
                'success' => false,
                'message' => 'Tính năng tự động xác nhận thanh toán qua Webhook hiện đang tạm tắt trong cài đặt hệ thống.',
            ], 403);
        }

        // 1. Xác thực API Key nếu có cấu hình
        if (! empty($configuredKey)) {
            $providedKey = $request->header('Authorization')
                ?: $request->header('X-API-KEY')
                ?: $request->input('api_key')
                ?: $request->input('apiKey');

            if (str_starts_with((string) $providedKey, 'Bearer ')) {
                $providedKey = substr((string) $providedKey, 7);
            }

            if ($providedKey !== $configuredKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khóa xác thực API Secret Key không chính xác hoặc không được cung cấp.',
                ], 401);
            }
        }

        // 2. Thu thập dữ liệu giao dịch từ payload
        // Hỗ trợ cả SePay, Casso hoặc custom webhook
        $transactions = [];

        if ($request->has('data') && is_array($request->data)) {
            // Định dạng Casso
            $transactions = $request->data;
        } elseif ($request->has('content') || $request->has('transferAmount') || $request->has('gateway')) {
            // Định dạng SePay
            $transactions = [$request->all()];
        } else {
            // Generic webhook
            $transactions = [$request->all()];
        }

        $processedOrders = [];

        foreach ($transactions as $trans) {
            $content = (string) ($trans['content'] ?? $trans['description'] ?? $trans['note'] ?? $trans['order_code'] ?? '');
            $amount = (float) ($trans['transferAmount'] ?? $trans['amount'] ?? 0);
            $referenceCode = (string) ($trans['referenceCode'] ?? $trans['tid'] ?? $trans['id'] ?? $trans['transaction_id'] ?? 'BANK_'.time());

            // Tìm mã đơn hàng dạng ORD-YYYYMMDD-XXXXX
            if (preg_match('/ORD-\d{8}-[A-Za-z0-9]{4,6}/i', $content, $matches)) {
                $orderCode = strtoupper($matches[0]);
                $order = Order::with(['payment', 'user'])->where('order_code', $orderCode)->first();

                if ($order) {
                    $payment = $order->payment ?? $order->payments()->first();

                    // Nếu đã thanh toán rồi thì ghi nhận bỏ qua
                    if ($payment && in_array($payment->payment_status, ['paid', 'completed'])) {
                        $processedOrders[] = [
                            'order_code' => $orderCode,
                            'status' => 'already_paid',
                            'message' => 'Đơn hàng này đã được xác nhận thanh toán trước đó.',
                        ];

                        continue;
                    }

                    // Cập nhật trạng thái thanh toán & đơn hàng
                    DB::transaction(function () use ($order, $payment, $amount, $referenceCode) {
                        if ($payment) {
                            $payment->update([
                                'payment_status' => 'paid',
                                'amount' => $amount > 0 ? $amount : $payment->amount,
                            ]);
                        }

                        if ($order->status === 'pending') {
                            $order->update(['status' => 'confirmed']);
                        }

                        $note = "Thanh toán tự động thành công qua Webhook Ngân hàng (Mã GD: {$referenceCode}".($amount > 0 ? ' - Số tiền: '.number_format($amount).'đ' : '').')';
                        $order->logStatusHistory('confirmed', $note, 'system', null, 'Cổng Ngân Hàng Tự Động');
                    });

                    // Gửi email thông báo xác nhận thanh toán cho khách hàng
                    try {
                        if ($order->user && $order->user->email) {
                            Mail::to($order->user->email)->send(new OrderStatusUpdatedMail($order, 'Đã xác nhận thanh toán'));
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send webhook paid mail: '.$e->getMessage());
                    }

                    // Bắn thông báo Telegram cho chủ shop
                    try {
                        $tgMsg = "💰 <b>XÁC NHẬN THANH TOÁN TỰ ĐỘNG THÀNH CÔNG!</b>\n"
                            ."━━━━━━━━━━━━━━━━━━━━\n"
                            ."🔖 <b>Mã đơn hàng:</b> #<code>{$order->order_code}</code>\n"
                            ."👤 <b>Khách hàng:</b> {$order->recipient_name}\n"
                            .'💵 <b>Số tiền nhận:</b> '.number_format($amount > 0 ? $amount : $order->total_amount)."đ\n"
                            ."🏦 <b>Mã tham chiếu:</b> <code>{$referenceCode}</code>\n"
                            .'⏰ <b>Thời gian:</b> '.now()->format('d/m/Y H:i:s')."\n"
                            ."━━━━━━━━━━━━━━━━━━━━\n"
                            .'Đơn hàng đã được tự động duyệt sang trạng thái: <b>Đã Xác Nhận</b>.';
                        TelegramNotificationService::sendMessage($tgMsg);
                    } catch (\Exception $e) {
                        Log::error('Failed to send webhook tg notify: '.$e->getMessage());
                    }

                    $processedOrders[] = [
                        'order_code' => $orderCode,
                        'status' => 'success',
                        'message' => 'Đã xác nhận thanh toán thành công cho đơn hàng #'.$orderCode,
                    ];
                }
            }
        }

        if (empty($processedOrders)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy mã đơn hàng phù hợp trong nội dung chuyển khoản.',
                'received_data' => $transactions,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Xử lý webhook thanh toán thành công!',
            'results' => $processedOrders,
        ], 200);
    }
}
