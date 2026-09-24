<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderReturnController extends Controller
{
    /**
     * Danh sách tất cả các yêu cầu Đổi / Trả hàng.
     */
    public function index(Request $request): View
    {
        $query = OrderReturn::with(['order', 'user', 'processor'])->latest();

        // Lọc theo trạng thái xử lý
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo loại yêu cầu (exchange, return)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Tìm kiếm theo mã đơn hoặc tên/SĐT khách hàng
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('order', function ($oq) use ($keyword) {
                    $oq->where('order_code', 'like', "%{$keyword}%")
                        ->orWhere('recipient_name', 'like', "%{$keyword}%")
                        ->orWhere('recipient_phone', 'like', "%{$keyword}%");
                })->orWhereHas('user', function ($uq) use ($keyword) {
                    $uq->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            });
        }

        $returns = $query->paginate(15)->withQueryString();

        $statusCounts = OrderReturn::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'all' => $statusCounts->sum(),
            'pending' => $statusCounts->get('pending', 0),
            'approved' => $statusCounts->get('approved', 0),
            'rejected' => $statusCounts->get('rejected', 0),
            'completed' => $statusCounts->get('completed', 0),
        ];

        return view('admin.returns.index', compact('returns', 'counts'));
    }

    /**
     * Xem chi tiết yêu cầu Đổi / Trả hàng.
     */
    public function show(OrderReturn $return): View
    {
        $return->load([
            'order.items.productVariant.color',
            'order.items.productVariant.size',
            'order.items.product.primaryImage',
            'order.payment',
            'user',
            'processor',
        ]);

        return view('admin.returns.show', compact('return'));
    }

    /**
     * Chấp thuận yêu cầu Đổi / Trả hàng.
     */
    public function approve(Request $request, OrderReturn $return): RedirectResponse
    {
        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($return->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý trước đó!');
        }

        $admin = Auth::guard('admin')->user();
        $adminId = $admin ? $admin->id : null;
        $adminName = $admin ? $admin->name : 'Quản trị viên';
        $adminNote = trim((string) $request->admin_note);

        try {
            DB::transaction(function () use ($return, $adminId, $adminName, $adminNote) {
                $return->update([
                    'status' => 'approved',
                    'admin_note' => $adminNote ?: 'Shop đã chấp thuận yêu cầu. Vui lòng gửi lại sản phẩm theo hướng dẫn của chúng tôi.',
                    'processed_by' => $adminId,
                    'processed_at' => now(),
                ]);

                $order = $return->order;
                $typeLabel = $return->type === 'exchange' ? 'Đổi size/màu' : 'Trả hàng & Hoàn tiền';

                // Nếu là hình thức Trả hàng hoàn tiền (return): Chuyển trạng thái đơn sang returned và hoàn tồn kho
                if ($return->type === 'return' && $order->status !== 'returned') {
                    $order->update(['status' => 'returned']);

                    if ($order->payment) {
                        $order->payment->update(['payment_status' => 'refunded']);
                    }

                    foreach ($order->items as $item) {
                        if ($item->product_variant_id) {
                            $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                            if ($variant) {
                                $beforeQty = $variant->stock_quantity;
                                $variant->increment('stock_quantity', $item->quantity);

                                StockMovement::create([
                                    'product_variant_id' => $variant->id,
                                    'user_id' => $adminId,
                                    'type' => 'order_return',
                                    'quantity_change' => $item->quantity,
                                    'before_quantity' => $beforeQty,
                                    'after_quantity' => $variant->stock_quantity,
                                    'reference_code' => $order->order_code,
                                    'note' => "Chấp thuận trả hàng & hoàn tiền đơn #{$order->order_code}",
                                ]);
                            }
                        }
                    }
                }

                // Ghi nhật ký vào status histories
                $order->logStatusHistory(
                    $return->type === 'return' ? 'returned' : 'confirmed',
                    "Shop đã chấp thuận yêu cầu [{$typeLabel}]".($adminNote ? ": {$adminNote}" : ''),
                    'admin',
                    $adminId,
                    $adminName
                );
            });

            return back()->with('success', 'Đã duyệt yêu cầu Đổi/Trả hàng thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    /**
     * Từ chối yêu cầu Đổi / Trả hàng.
     */
    public function reject(Request $request, OrderReturn $return): RedirectResponse
    {
        $request->validate([
            'admin_note' => ['required', 'string', 'max:500'],
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối yêu cầu để phản hồi cho khách hàng.',
        ]);

        if ($return->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý trước đó!');
        }

        $admin = Auth::guard('admin')->user();
        $adminId = $admin ? $admin->id : null;
        $adminName = $admin ? $admin->name : 'Quản trị viên';
        $adminNote = trim((string) $request->admin_note);

        try {
            DB::transaction(function () use ($return, $adminId, $adminName, $adminNote) {
                $return->update([
                    'status' => 'rejected',
                    'admin_note' => $adminNote,
                    'processed_by' => $adminId,
                    'processed_at' => now(),
                ]);

                $order = $return->order;
                $typeLabel = $return->type === 'exchange' ? 'Đổi size/màu' : 'Trả hàng & Hoàn tiền';

                $order->logStatusHistory(
                    'completed',
                    "Shop từ chối yêu cầu [{$typeLabel}]. Lý do: {$adminNote}",
                    'admin',
                    $adminId,
                    $adminName
                );
            });

            return back()->with('success', 'Đã từ chối yêu cầu Đổi/Trả hàng và gửi phản hồi đến khách hàng!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }
}
