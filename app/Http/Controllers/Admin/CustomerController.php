<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Danh sách khách hàng phía Admin.
     */
    public function index(Request $request): View
    {
        $query = User::with(['role'])
            ->withCount('orders')
            ->withSum(['orders' => function ($q) {
                $q->where('status', 'completed');
            }], 'total_amount')
            ->whereHas('role', function ($q) {
                $q->where('name', 'user');
            })
            ->latest();

        // Lọc theo từ khóa (Tên, Email, Số điện thoại)
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->paginate(10)->withQueryString();

        $totalCustomers = User::whereHas('role', function ($q) {
            $q->where('name', 'user');
        })->count();

        $activeCount = User::whereHas('role', function ($q) {
            $q->where('name', 'user');
        })->where('status', 'active')->count();

        $bannedCount = User::whereHas('role', function ($q) {
            $q->where('name', 'user');
        })->where('status', 'banned')->count();

        return view('admin.customers.index', compact('customers', 'totalCustomers', 'activeCount', 'bannedCount'));
    }

    /**
     * Xem chi tiết thông tin & đơn hàng của khách hàng.
     */
    public function show(User $customer): View
    {
        $customer->load(['role', 'orders.items']);
        $customer->loadCount('orders');
        $totalSpent = $customer->orders->where('status', 'completed')->sum('total_amount');

        return view('admin.customers.show', compact('customer', 'totalSpent'));
    }

    /**
     * Khóa / Mở khóa tài khoản khách hàng (Toggle Status).
     */
    public function toggleStatus(User $customer): RedirectResponse
    {
        if ($customer->role && $customer->role->name === 'admin') {
            return back()->with('error', 'Không thể thao tác trên tài khoản Quản trị viên!');
        }

        $newStatus = $customer->status === 'banned' ? 'active' : 'banned';
        $customer->update(['status' => $newStatus]);

        $message = $newStatus === 'banned'
            ? 'Đã khóa tài khoản khách hàng "'.$customer->name.'" thành công!'
            : 'Đã mở khóa tài khoản khách hàng "'.$customer->name.'" thành công!';

        return back()->with('success', $message);
    }
}
