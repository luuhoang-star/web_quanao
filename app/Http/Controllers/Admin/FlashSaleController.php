<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlashSaleController extends Controller
{
    /**
     * Danh sách các đợt Flash Sale (hỗ trợ Tìm kiếm, Lọc trạng thái, Thống kê, Phân trang).
     */
    public function index(Request $request): View
    {
        $now = now();
        $query = FlashSale::with(['product.images', 'product.category']);

        // Tìm kiếm theo tên sản phẩm hoặc mã SKU
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'ongoing') {
                $query->where('is_active', true)
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->whereRaw('(quantity - sold_quantity) > 0');
            } elseif ($status === 'upcoming') {
                $query->where('is_active', true)
                    ->where('start_time', '>', $now);
            } elseif ($status === 'expired') {
                $query->where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->where('end_time', '<', $now)
                            ->orWhereRaw('(quantity - sold_quantity) <= 0');
                    });
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $flashSales = $query->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Tính toán số liệu thống kê tổng quan tối ưu SQL Count
        $stats = [
            'total' => FlashSale::count(),
            'ongoing' => FlashSale::where('is_active', true)
                ->where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->whereRaw('(quantity - sold_quantity) > 0')
                ->count(),
            'upcoming' => FlashSale::where('is_active', true)
                ->where('start_time', '>', $now)
                ->count(),
            'expired' => FlashSale::where('is_active', true)
                ->where(function ($q) use ($now) {
                    $q->where('end_time', '<', $now)
                        ->orWhereRaw('(quantity - sold_quantity) <= 0');
                })
                ->count(),
            'inactive' => FlashSale::where('is_active', false)->count(),
        ];

        return view('admin.flash_sales.index', compact('flashSales', 'stats'));
    }

    /**
     * Form tạo đợt Flash Sale mới.
     */
    public function create(): View
    {
        $products = Product::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.flash_sales.create', compact('products'));
    }

    /**
     * Lưu đợt Flash Sale vào Database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sale_price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm áp dụng Flash Sale.',
            'sale_price.required' => 'Vui lòng nhập giá giảm Flash Sale.',
            'sale_price.numeric' => 'Giá giảm phải là số hợp lệ.',
            'sale_price.min' => 'Giá giảm phải lớn hơn 0đ.',
            'start_time.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'end_time.required' => 'Vui lòng chọn thời gian kết thúc.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $product = Product::findOrFail($request->product_id);
        if ($request->sale_price >= $product->price) {
            return back()->withInput()->withErrors([
                'sale_price' => 'Giá giảm Flash Sale phải nhỏ hơn giá gốc của sản phẩm ('.number_format($product->price).'đ).',
            ]);
        }

        $isActive = $request->has('is_active') ? (bool) $request->is_active : true;

        // Nếu kích hoạt Flash Sale mới, vô hiệu hóa các chương trình Flash Sale active cũ của sản phẩm này
        if ($isActive) {
            FlashSale::where('product_id', $product->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        FlashSale::create([
            'product_id' => $request->product_id,
            'sale_price' => $request->sale_price,
            'quantity' => $request->filled('quantity') ? (int) $request->quantity : 10,
            'sold_quantity' => 0,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.flash-sales.index')->with('success', 'Tạo đợt Flash Sale thành công!');
    }

    /**
     * Form chỉnh sửa đợt Flash Sale.
     */
    public function edit(FlashSale $flashSale): View
    {
        $products = Product::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.flash_sales.edit', compact('flashSale', 'products'));
    }

    /**
     * Cập nhật đợt Flash Sale.
     */
    public function update(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sale_price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'sale_price.required' => 'Vui lòng nhập giá giảm Flash Sale.',
            'start_time.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'end_time.required' => 'Vui lòng chọn thời gian kết thúc.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $product = Product::findOrFail($request->product_id);
        if ($request->sale_price >= $product->price) {
            return back()->withInput()->withErrors([
                'sale_price' => 'Giá giảm Flash Sale phải nhỏ hơn giá gốc của sản phẩm ('.number_format($product->price).'đ).',
            ]);
        }

        $isActive = $request->has('is_active') ? (bool) $request->is_active : (bool) $flashSale->is_active;

        if ($isActive) {
            FlashSale::where('product_id', $product->id)
                ->where('id', '!=', $flashSale->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $flashSale->update([
            'product_id' => $request->product_id,
            'sale_price' => $request->sale_price,
            'quantity' => $request->filled('quantity') ? (int) $request->quantity : $flashSale->quantity,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.flash-sales.index')->with('success', 'Cập nhật đợt Flash Sale thành công!');
    }

    /**
     * Cập nhật nhanh trạng thái Kích hoạt / Tắt kích hoạt Flash Sale.
     */
    public function toggleStatus(Request $request, FlashSale $flashSale): RedirectResponse|JsonResponse
    {
        $newStatus = $request->has('is_active')
            ? (bool) $request->input('is_active')
            : ! $flashSale->is_active;

        if ($newStatus) {
            FlashSale::where('product_id', $flashSale->product_id)
                ->where('id', '!=', $flashSale->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $flashSale->update(['is_active' => $newStatus]);

        if ($request->ajax() || $request->wantsJson()) {
            $now = now();
            $isOngoing = $flashSale->is_active && $flashSale->start_time <= $now && $flashSale->end_time >= $now && $flashSale->remaining_quantity > 0;
            $isUpcoming = $flashSale->is_active && $flashSale->start_time > $now;

            return response()->json([
                'success' => true,
                'is_active' => $newStatus,
                'is_ongoing' => $isOngoing,
                'is_upcoming' => $isUpcoming,
                'message' => $newStatus ? 'Đã kích hoạt chương trình Flash Sale!' : 'Đã tắt kích hoạt chương trình Flash Sale!',
            ]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái Flash Sale!');
    }

    /**
     * Xóa đợt Flash Sale.
     */
    public function destroy(FlashSale $flashSale): RedirectResponse
    {
        $flashSale->delete();

        return redirect()->route('admin.flash-sales.index')->with('success', 'Đã xóa đợt Flash Sale thành công!');
    }

    /**
     * Xử lý thao tác hàng loạt (Kích hoạt, Tắt, Xóa).
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:flash_sales,id'],
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất 1 đợt Flash Sale để thực hiện.',
            'action.required' => 'Vui lòng chọn thao tác cần xử lý.',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids');
        $count = count($ids);

        if ($action === 'activate') {
            FlashSale::whereIn('id', $ids)->update(['is_active' => true]);

            return back()->with('success', "Đã kích hoạt {$count} đợt Flash Sale!");
        }

        if ($action === 'deactivate') {
            FlashSale::whereIn('id', $ids)->update(['is_active' => false]);

            return back()->with('success', "Đã tắt {$count} đợt Flash Sale!");
        }

        if ($action === 'delete') {
            FlashSale::whereIn('id', $ids)->delete();

            return back()->with('success', "Đã xóa thành công {$count} đợt Flash Sale!");
        }

        return back();
    }
}
