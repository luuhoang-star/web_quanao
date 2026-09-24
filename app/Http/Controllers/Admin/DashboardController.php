<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Hiển thị bảng điều khiển Admin Dashboard & Báo cáo Thống kê.
     */
    public function index(Request $request): View
    {
        $period = $request->get('period', 'all');
        $orderQuery = Order::query();

        if ($period === 'today') {
            $orderQuery->whereDate('created_at', now()->today());
        } elseif ($period === 'this_week') {
            $orderQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'this_month') {
            $orderQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($period === 'this_year') {
            $orderQuery->whereYear('created_at', now()->year);
        }

        $stats = [
            'period' => $period,
            'total_users' => User::whereHas('role', fn ($q) => $q->where('name', 'user'))->count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => (clone $orderQuery)->count(),
            'pending_orders' => (clone $orderQuery)->where('status', 'pending')->count(),
            'completed_orders' => (clone $orderQuery)->where('status', 'completed')->count(),
            'cancelled_orders' => (clone $orderQuery)->where('status', 'cancelled')->count(),
            'returned_orders' => (clone $orderQuery)->where('status', 'returned')->count(),
            'total_revenue' => (clone $orderQuery)->where('status', 'completed')->sum('total_amount'),
        ];

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $latestProducts = Product::with(['category'])->latest()->take(5)->get();

        // Top 5 Sản phẩm bán chạy nhất theo mốc thời gian lọc (loại trừ đơn hủy / trả hàng)
        $topProductsQuery = OrderItem::select(
            'order_items.product_id',
            'order_items.product_name',
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.total_price) as total_revenue')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned']);

        if ($period === 'today') {
            $topProductsQuery->whereDate('orders.created_at', now()->today());
        } elseif ($period === 'this_week') {
            $topProductsQuery->whereBetween('orders.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'this_month') {
            $topProductsQuery->whereMonth('orders.created_at', now()->month)
                ->whereYear('orders.created_at', now()->year);
        } elseif ($period === 'this_year') {
            $topProductsQuery->whereYear('orders.created_at', now()->year);
        }

        $topProducts = $topProductsQuery->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_sold')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        // 1. Thống kê biểu đồ doanh thu & đơn hàng 7 ngày gần nhất
        $chartDates = [];
        $chartRevenues = [];
        $chartOrderCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $dayObj = now()->subDays($i);
            $dateStr = $dayObj->format('Y-m-d');
            $displayDate = $dayObj->format('d/m');
            $chartDates[] = $displayDate;

            // Doanh thu các đơn hàng giao thành công hoặc đã xác nhận trong ngày
            $dayRevenue = Order::whereDate('created_at', $dateStr)
                ->where('status', 'completed')
                ->sum('total_amount');
            $chartRevenues[] = (float) $dayRevenue;

            $dayCount = Order::whereDate('created_at', $dateStr)->count();
            $chartOrderCounts[] = $dayCount;
        }

        // 2. Thống kê tỷ lệ trạng thái đơn hàng (Doughnut Chart)
        $statusDistribution = [
            'Chờ xử lý' => (clone $orderQuery)->where('status', 'pending')->count(),
            'Đã xác nhận' => (clone $orderQuery)->where('status', 'confirmed')->count(),
            'Đang đóng gói' => (clone $orderQuery)->where('status', 'processing')->count(),
            'Đang giao' => (clone $orderQuery)->where('status', 'shipping')->count(),
            'Hoàn thành' => (clone $orderQuery)->where('status', 'completed')->count(),
            'Đã hủy' => (clone $orderQuery)->where('status', 'cancelled')->count(),
            'Trả hàng' => (clone $orderQuery)->where('status', 'returned')->count(),
        ];

        // 3. Thống kê phương thức thanh toán
        $paymentCounts = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->select('payments.payment_method', DB::raw('count(*) as total'))
            ->groupBy('payments.payment_method')
            ->pluck('total', 'payments.payment_method')
            ->toArray();

        $paymentDistribution = [
            'Tiền mặt (COD)' => $paymentCounts['COD'] ?? 0,
            'VietQR Chuyển khoản' => $paymentCounts['bank_transfer'] ?? 0,
            'Ví MoMo' => $paymentCounts['momo'] ?? 0,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'latestProducts',
            'topProducts',
            'chartDates',
            'chartRevenues',
            'chartOrderCounts',
            'statusDistribution',
            'paymentDistribution'
        ));
    }
}
