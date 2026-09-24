<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    /**
     * Thêm địa chỉ giao hàng mới cho người dùng.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:20'],
            'province_city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'ward' => ['required', 'string', 'max:255'],
            'street_address' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận.',
            'recipient_phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
            'province_city.required' => 'Vui lòng chọn Tỉnh / Thành phố.',
            'district.required' => 'Vui lòng nhập/chọn Quận / Huyện.',
            'ward.required' => 'Vui lòng nhập/chọn Phường / Xã.',
            'street_address.required' => 'Vui lòng nhập số nhà, tên đường chi tiết.',
        ]);

        $userId = Auth::id();
        $isDefault = $request->boolean('is_default');

        // Nếu đây là địa chỉ đầu tiên hoặc được đánh dấu mặc định, bỏ mặc định các địa chỉ khác
        $addressCount = UserAddress::where('user_id', $userId)->count();
        if ($addressCount === 0 || $isDefault) {
            $isDefault = true;
            UserAddress::where('user_id', $userId)->update(['is_default' => false]);
        }

        UserAddress::create([
            'user_id' => $userId,
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'province_city' => $request->province_city,
            'district' => $request->district,
            'ward' => $request->ward,
            'street_address' => $request->street_address,
            'is_default' => $isDefault,
        ]);

        return back()->with('active_tab', 'addresses')->with('success', 'Thêm địa chỉ nhận hàng thành công!');
    }

    /**
     * Cập nhật địa chỉ giao hàng.
     */
    public function update(Request $request, UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa địa chỉ này.');
        }

        $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:20'],
            'province_city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'ward' => ['required', 'string', 'max:255'],
            'street_address' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'recipient_name.required' => 'Vui lòng nhập tên người nhận.',
            'recipient_phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
            'province_city.required' => 'Vui lòng chọn Tỉnh / Thành phố.',
            'district.required' => 'Vui lòng nhập/chọn Quận / Huyện.',
            'ward.required' => 'Vui lòng nhập/chọn Phường / Xã.',
            'street_address.required' => 'Vui lòng nhập số nhà, tên đường chi tiết.',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            UserAddress::where('user_id', Auth::id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update([
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'province_city' => $request->province_city,
            'district' => $request->district,
            'ward' => $request->ward,
            'street_address' => $request->street_address,
            'is_default' => $isDefault || $address->is_default,
        ]);

        return back()->with('active_tab', 'addresses')->with('success', 'Cập nhật địa chỉ nhận hàng thành công!');
    }

    /**
     * Đặt địa chỉ làm mặc định.
     */
    public function setDefault(UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('active_tab', 'addresses')->with('success', 'Đã đặt địa chỉ làm mặc định!');
    }

    /**
     * Xóa địa chỉ giao hàng.
     */
    public function destroy(UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // Nếu xóa địa chỉ mặc định, tự động gắn địa chỉ đầu tiên còn lại làm mặc định
        if ($wasDefault) {
            $nextAddress = UserAddress::where('user_id', Auth::id())->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return back()->with('active_tab', 'addresses')->with('success', 'Đã xóa địa chỉ nhận hàng!');
    }
}
