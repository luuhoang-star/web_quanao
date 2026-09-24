<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Danh sách tất cả đơn hàng phía Admin.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'payment'])->latest();

        // Lọc theo trạng thái đơn hàng
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->whereHas('payment', function ($pq) use ($request) {
                $pq->where('payment_method', $request->payment_method);
            });
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($pq) use ($request) {
                $pq->where('payment_status', $request->payment_status);
            });
        }

        // Tìm kiếm theo mã đơn hàng, tên hoặc SĐT người nhận
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'like', "%{$keyword}%")
                    ->orWhere('recipient_name', 'like', "%{$keyword}%")
                    ->orWhere('recipient_phone', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $statusCounts = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'all' => $statusCounts->sum(),
            'pending' => $statusCounts->get('pending', 0),
            'confirmed' => $statusCounts->get('confirmed', 0),
            'processing' => $statusCounts->get('processing', 0),
            'shipping' => $statusCounts->get('shipping', 0),
            'completed' => $statusCounts->get('completed', 0),
            'cancelled' => $statusCounts->get('cancelled', 0),
            'returned' => $statusCounts->get('returned', 0),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    /**
     * Xem chi tiết đơn hàng phía Admin.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.productVariant.color', 'items.productVariant.size', 'statusHistories', 'returns.processor']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * In Hóa đơn bán hàng & Phiếu giao hàng phía Admin.
     */
    public function invoice(Order $order): View
    {
        $order->load([
            'user',
            'items.product.primaryImage',
            'items.productVariant.color',
            'items.productVariant.size',
            'coupon',
            'payment',
            'statusHistories',
        ]);

        return view('orders.invoice', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng.
     */
    public function updateStatus(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipping,completed,cancelled,returned'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'status.in' => 'Trạng thái đơn hàng không hợp lệ.',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Trạng thái đơn hàng không thay đổi.');
        }

        // Ràng buộc nghiệp vụ: Đơn thanh toán chuyển khoản/online (bank_transfer, momo) phải hoàn tất thanh toán trước khi cho đi giao hàng (shipping, completed)
        if (in_array($newStatus, ['shipping', 'completed'])) {
            $payment = $order->payment ?? $order->payments()->first();
            if ($payment && in_array($payment->payment_method, ['bank_transfer', 'momo']) && $payment->payment_status !== 'paid') {
                $methodLabel = match ($payment->payment_method) {
                    'bank_transfer' => 'Chuyển khoản Ngân hàng (VietQR)',
                    'momo' => 'Ví điện tử MoMo',
                    default => 'Chuyển khoản',
                };
                $errorMsg = "Đơn hàng thanh toán bằng {$methodLabel} chưa được thanh toán! Phải xác nhận thanh toán trước mới có thể chuyển đơn hàng đi giao.";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 422);
                }

                return back()->with('error', $errorMsg);
            }
        }

        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang chuẩn bị hàng',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã giao hàng thành công',
            'cancelled' => 'Hủy đơn hàng',
            'returned' => 'Trả hàng / Hoàn tiền',
        ];

        $adminUser = auth('admin')->user();
        $adminName = $adminUser ? $adminUser->name : 'Quản trị viên';
        $label = $statusLabels[$newStatus] ?? $newStatus;
        $adminNote = trim((string) $request->note);

        try {
            DB::transaction(function () use ($order, $oldStatus, $newStatus, $adminUser, $adminName, $label, $adminNote) {
                $lockedOrder = Order::lockForUpdate()->find($order->id);

                $lockedOrder->update([
                    'status' => $newStatus,
                ]);

                // 1. Đồng bộ trạng thái Thanh toán (Payment Status)
                if ($lockedOrder->payment) {
                    if ($newStatus === 'completed') {
                        $lockedOrder->payment->update(['payment_status' => 'paid']);
                    } elseif ($newStatus === 'cancelled') {
                        $lockedOrder->payment->update(['payment_status' => 'cancelled']);
                    } elseif ($newStatus === 'returned') {
                        $lockedOrder->payment->update(['payment_status' => 'refunded']);
                    }
                }

                // 2. Chuyển từ Đang giao/Hoạt động -> Hủy đơn hoặc Trả hàng
                if (in_array($newStatus, ['cancelled', 'returned']) && ! in_array($oldStatus, ['cancelled', 'returned'])) {
                    $type = $newStatus === 'returned' ? 'order_return' : 'order_cancel';
                    $typeText = $newStatus === 'returned' ? 'trả hàng' : 'hủy đơn';

                    // Hoàn lại lượt Coupon
                    if ($lockedOrder->coupon_id && $lockedOrder->coupon) {
                        $coupon = Coupon::lockForUpdate()->find($lockedOrder->coupon_id);
                        if ($coupon && $coupon->used_count > 0) {
                            $coupon->decrement('used_count');
                        }
                    }

                    foreach ($lockedOrder->items as $item) {
                        // Hoàn lại Tồn kho
                        if ($item->product_variant_id) {
                            $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                            if ($variant) {
                                $beforeQty = $variant->stock_quantity;
                                $variant->increment('stock_quantity', $item->quantity);

                                StockMovement::create([
                                    'product_variant_id' => $variant->id,
                                    'user_id' => $adminUser ? $adminUser->id : null,
                                    'type' => $type,
                                    'quantity_change' => $item->quantity,
                                    'before_quantity' => $beforeQty,
                                    'after_quantity' => $variant->stock_quantity,
                                    'reference_code' => $lockedOrder->order_code,
                                    'note' => "Admin {$adminName} cập nhật {$typeText} cho Đơn hàng #{$lockedOrder->order_code}",
                                ]);
                            }
                        }

                        // Giảm sold_quantity của Flash Sale
                        if ($item->product_id) {
                            $flashSale = FlashSale::where('product_id', $item->product_id)
                                ->where('sold_quantity', '>', 0)
                                ->first();
                            if ($flashSale) {
                                $deductQty = min($flashSale->sold_quantity, $item->quantity);
                                $flashSale->decrement('sold_quantity', $deductQty);
                            }
                        }
                    }
                }

                // 3. Khôi phục từ Hủy/Trả hàng -> Trở lại trạng thái hoạt động (Re-deduct stock)
                if (in_array($oldStatus, ['cancelled', 'returned']) && ! in_array($newStatus, ['cancelled', 'returned'])) {
                    // Tăng lại lượt Coupon nếu có
                    if ($lockedOrder->coupon_id && $lockedOrder->coupon) {
                        $coupon = Coupon::lockForUpdate()->find($lockedOrder->coupon_id);
                        if ($coupon) {
                            $coupon->increment('used_count');
                        }
                    }

                    foreach ($lockedOrder->items as $item) {
                        if ($item->product_variant_id) {
                            $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                            if ($variant) {
                                if ($variant->stock_quantity < $item->quantity) {
                                    throw new \Exception("Không thể khôi phục đơn hàng vì sản phẩm '{$item->product_name}' (SKU: {$variant->sku}) đã hết hàng trong kho (Còn {$variant->stock_quantity} món)!");
                                }

                                $beforeQty = $variant->stock_quantity;
                                $variant->decrement('stock_quantity', $item->quantity);

                                StockMovement::create([
                                    'product_variant_id' => $variant->id,
                                    'user_id' => $adminUser ? $adminUser->id : null,
                                    'type' => 'order_sale',
                                    'quantity_change' => -$item->quantity,
                                    'before_quantity' => $beforeQty,
                                    'after_quantity' => $variant->stock_quantity,
                                    'reference_code' => $lockedOrder->order_code,
                                    'note' => "Admin {$adminName} khôi phục Đơn hàng #{$lockedOrder->order_code}",
                                ]);
                            }
                        }

                        // Tăng lại sold_quantity của Flash Sale
                        if ($item->product_id) {
                            $activeFlashSale = FlashSale::active()->where('product_id', $item->product_id)->first();
                            if ($activeFlashSale) {
                                $activeFlashSale->increment('sold_quantity', $item->quantity);
                            }
                        }
                    }
                }

                $historyNote = "Cập nhật trạng thái đơn hàng thành: {$label}";
                if (! empty($adminNote)) {
                    $historyNote .= " (Ghi chú: {$adminNote})";
                }

                $lockedOrder->logStatusHistory(
                    $newStatus,
                    $historyNote,
                    'admin',
                    $adminUser ? $adminUser->id : null,
                    $adminName
                );
            });

            // Gửi Email thông báo tới Khách hàng qua Queue bất đồng bộ (tránh block request 2-3s)
            try {
                $order->load('user');
                if ($order->user && $order->user->email) {
                    Mail::to($order->user->email)->queue(new OrderStatusUpdatedMail($order, $label));

                    // Kích hoạt worker ngầm để xử lý gửi mail ngay lập tức mà không giữ kết nối trình duyệt
                    $phpBinary = escapeshellarg(str_replace('php-cgi.exe', 'php.exe', PHP_BINARY));
                    $artisanBinary = escapeshellarg(base_path('artisan'));
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        pclose(popen("start /B \"\" {$phpBinary} {$artisanBinary} queue:work --stop-when-empty > NUL 2>&1", 'r'));
                    } else {
                        exec("{$phpBinary} {$artisanBinary} queue:work --stop-when-empty > /dev/null 2>&1 &");
                    }
                }
            } catch (\Exception $mailEx) {
                Log::error("Failed to send OrderStatusUpdatedMail: {$mailEx->getMessage()}");
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật trạng thái đơn hàng #'.$order->order_code.' thành công!',
                    'new_status' => $newStatus,
                    'updated_at' => now()->format('d/m/Y H:i'),
                ]);
            }

            return back()->with('success', 'Đã cập nhật trạng thái đơn hàng #'.$order->order_code.' thành công!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cập nhật trạng thái thanh toán của đơn hàng (Xác nhận đã nhận chuyển khoản).
     */
    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed,refunded,cancelled'],
        ]);

        $payment = $order->payment ?? $order->payments()->first();
        if (! $payment) {
            return back()->with('error', 'Không tìm thấy thông tin thanh toán của đơn hàng này.');
        }

        $payment->update([
            'payment_status' => $request->payment_status,
        ]);

        $adminUser = auth('admin')->user();
        $adminName = $adminUser ? $adminUser->name : 'Quản trị viên';
        $statusLabels = [
            'pending' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
            'cancelled' => 'Đã hủy',
        ];
        $label = $statusLabels[$request->payment_status] ?? $request->payment_status;

        $order->logStatusHistory(
            $order->status,
            "{$adminName} đã cập nhật trạng thái thanh toán thành: {$label}",
            'admin',
            $adminUser ? $adminUser->id : null,
            $adminName
        );

        return back()->with('success', "Đã cập nhật trạng thái thanh toán đơn #{$order->order_code} thành: {$label}!");
    }

    /**
     * Xuất danh sách đơn hàng ra file Excel / CSV có định dạng UTF-8 chuẩn tiếng Việt.
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'payment', 'items.product', 'items.productVariant.color', 'items.productVariant.size'])->latest();

        // Áp dụng các bộ lọc tương tự như danh sách
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->whereHas('payment', function ($pq) use ($request) {
                $pq->where('payment_method', $request->payment_method);
            });
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->whereHas('payment', function ($pq) use ($request) {
                $pq->where('payment_status', $request->payment_status);
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'like', "%{$keyword}%")
                    ->orWhere('recipient_name', 'like', "%{$keyword}%")
                    ->orWhere('recipient_phone', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->get();

        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang đóng gói',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'returned' => 'Trả hàng/Hoàn tiền',
        ];

        $paymentMethodLabels = [
            'COD' => 'Tiền mặt khi nhận hàng (COD)',
            'bank_transfer' => 'Chuyển khoản Ngân hàng (VietQR)',
            'momo' => 'Ví điện tử MoMo',
        ];

        $paymentStatusLabels = [
            'pending' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền',
            'cancelled' => 'Đã hủy',
        ];

        $filename = 'DonHang_ATINO_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders, $statusLabels, $paymentMethodLabels, $paymentStatusLabels) {
            $file = fopen('php://output', 'w');

            // Thêm UTF-8 BOM để Excel hiển thị tiếng Việt không bị lỗi font
            fwrite($file, "\xEF\xBB\xBF");

            // Header các cột
            fputcsv($file, [
                'Mã Đơn Hàng',
                'Thời Gian Đặt',
                'Tên Khách Hàng',
                'Số Điện Thoại',
                'Địa Chỉ Giao Hàng',
                'Chi Tiết Sản Phẩm',
                'Tạm Tính (VNĐ)',
                'Giảm Giá (VNĐ)',
                'Phí Vận Chuyển (VNĐ)',
                'Tổng Tiền (VNĐ)',
                'Phương Thức Thanh Toán',
                'Trạng Thái Thanh Toán',
                'Trạng Thái Đơn Hàng',
                'Ghi Chú Đơn Hàng',
            ]);

            foreach ($orders as $order) {
                // Ghép danh sách sản phẩm thành chuỗi mô tả
                $itemsText = $order->items->map(function ($item) {
                    return "{$item->product_name} ({$item->variant_info}) x{$item->quantity}";
                })->implode('; ');

                $payment = $order->payment ?? $order->payments()->first();

                fputcsv($file, [
                    $order->order_code,
                    $order->created_at->format('d/m/Y H:i:s'),
                    $order->recipient_name,
                    $order->recipient_phone,
                    $order->shipping_address,
                    $itemsText,
                    $order->subtotal,
                    $order->discount_amount,
                    $order->shipping_fee,
                    $order->total_amount,
                    $paymentMethodLabels[$payment?->payment_method ?? 'COD'] ?? ($payment?->payment_method ?? 'COD'),
                    $paymentStatusLabels[$payment?->payment_status ?? 'pending'] ?? ($payment?->payment_status ?? 'pending'),
                    $statusLabels[$order->status] ?? $order->status,
                    $order->note ?: '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
