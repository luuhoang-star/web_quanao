<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * Danh sách mã giảm giá.
     */
    public function index(Request $request): View
    {
        $query = Coupon::latest();

        // Tìm kiếm theo mã giảm giá
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where('code', 'like', "%{$keyword}%");
        }

        // Lọc theo loại giảm giá
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $coupons = $query->paginate(10)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Form tạo mã giảm giá mới.
     */
    public function create(): View
    {
        return view('admin.coupons.create');
    }

    /**
     * Lưu mã giảm giá vào CSDL.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'type' => ['required', 'in:percent,fixed_amount,free_shipping'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'user_usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá (Coupon Code).',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'type.required' => 'Vui lòng chọn loại giảm giá.',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($request->code)),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_value' => $request->min_order_value ?: 0,
            'max_discount_amount' => $request->max_discount_amount ?: null,
            'usage_limit' => $request->usage_limit ?: null,
            'user_usage_limit' => $request->user_usage_limit ?: null,
            'used_count' => 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Thêm mã giảm giá mới thành công!');
    }

    /**
     * Form chỉnh sửa mã giảm giá.
     */
    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Cập nhật thông tin mã giảm giá.
     */
    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code,'.$coupon->id],
            'type' => ['required', 'in:percent,fixed_amount,free_shipping'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'user_usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
        ]);

        $data = [
            'code' => strtoupper(trim($request->code)),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_value' => $request->min_order_value ?: 0,
            'max_discount_amount' => $request->max_discount_amount ?: null,
            'usage_limit' => $request->usage_limit ?: null,
            'user_usage_limit' => $request->user_usage_limit ?: null,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        if ($request->has('is_active')) {
            $data['is_active'] = (bool) $request->is_active;
        }

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    /**
     * Bật/Tắt nhanh trạng thái hoạt động của mã giảm giá.
     */
    public function toggleStatus(Request $request, Coupon $coupon): RedirectResponse|JsonResponse
    {
        $coupon->update([
            'is_active' => ! $coupon->is_active,
        ]);

        $statusLabel = $coupon->is_active ? 'kích hoạt (đang chạy)' : 'tạm ẩn';

        if ($request->ajax() || $request->wantsJson()) {
            $isExpired = $coupon->end_date && $coupon->end_date->isPast();

            return response()->json([
                'success' => true,
                'is_active' => $coupon->is_active,
                'is_expired' => $isExpired,
                'message' => "Đã chuyển trạng thái mã '{$coupon->code}' sang {$statusLabel}!",
            ]);
        }

        return back()->with('success', "Đã đổi trạng thái mã giảm giá '{$coupon->code}' sang {$statusLabel}!");
    }

    /**
     * Xóa mã giảm giá.
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Xóa mã giảm giá thành công!');
    }
}
