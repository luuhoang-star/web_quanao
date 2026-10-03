<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\AdminSettingsController;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\Lookbook;
use App\Models\Outfit;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Hiển thị giao diện trang chủ website chuẩn phong cách Atino.
     */
    public function index(): View
    {
        $collections = Collection::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 5 danh mục chính
        $mainCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->get();

        // Lấy danh sách sản phẩm Flash Sale thực tế từ DB
        $flashSaleItems = $this->getFlashSaleProducts();

        // Lấy danh sách sản phẩm Nổi bật & Bán chạy thực tế từ DB
        $featuredProducts = $this->getFeaturedProducts();

        // Lấy danh sách Mã giảm giá (Coupons) hợp lệ đang diễn ra
        $activeCoupons = Coupon::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->get();

        // Lấy danh sách Lookbooks hiển thị trên trang chủ
        $lookbooks = Lookbook::with(['products.images', 'products.category'])
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        // Lấy danh sách Outfits phối sẵn hiển thị trên trang chủ
        $outfits = Outfit::with(['products.images', 'products.category'])
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        // Lấy danh sách Đánh giá thực tế đã duyệt từ người dùng
        $customerReviews = Review::with(['user', 'product.images', 'images'])
            ->where('is_approved', true)
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        // Thời gian kết thúc đợt Flash Sale sớm nhất để chạy Countdown đếm ngược
        $flashSaleEndTime = $flashSaleItems->isNotEmpty()
            ? $flashSaleItems->min('end_time')?->format('Y-m-d H:i:s')
            : null;

        // Cấu hình nội dung trang chủ từ Admin Settings
        $siteSettings = array_merge(
            AdminSettingsController::getDefaultSettings(),
            Cache::get('site_settings', [])
        );

        return view('welcome', compact(
            'collections',
            'mainCategories',
            'flashSaleItems',
            'featuredProducts',
            'activeCoupons',
            'lookbooks',
            'outfits',
            'customerReviews',
            'flashSaleEndTime',
            'siteSettings'
        ));
    }

    /**
     * Lấy danh sách các sản phẩm đang thực sự có chương trình Flash Sale hợp lệ từ database.
     * Nếu không có đợt Flash Sale nào đang hoạt động (ví dụ do hết hạn), tự động gia hạn/khởi tạo cho ngày hôm nay.
     */
    public function getFlashSaleProducts(): EloquentCollection
    {
        $items = FlashSale::active()
            ->with(['product.category', 'product.images', 'product.variants'])
            ->orderBy('end_time', 'asc')
            ->take(8)
            ->get();

        if ($items->isEmpty() && FlashSale::count() === 0) {
            $products = Product::where('is_active', true)->take(6)->get();
            foreach ($products as $index => $product) {
                $discountRate = 0.7 - ($index * 0.05); // Giảm 30% - 50%
                $salePrice = round($product->price * $discountRate);
                $product->update(['sale_price' => $salePrice]);

                FlashSale::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'sale_price' => $salePrice,
                        'start_time' => now()->subHours(2),
                        'end_time' => now()->endOfDay(),
                        'quantity' => 50,
                        'sold_quantity' => rand(5, 20),
                        'is_active' => true,
                    ]
                );
            }

            $items = FlashSale::active()
                ->with(['product.category', 'product.images', 'product.variants'])
                ->orderBy('end_time', 'asc')
                ->take(8)
                ->get();
        }

        return $items;
    }

    /**
     * Lấy danh sách Top 5 Sản phẩm Bán chạy nhất tự động dựa trên đơn hàng đã hoàn thành.
     * Sắp xếp theo:
     * 1. Số lượng đã bán thực tế (bảng order_items join orders where status = 'completed')
     * 2. Mới nhất (created_at DESC)
     * Điều kiện: is_active = 1
     */
    public function getFeaturedProducts(): EloquentCollection
    {
        $soldSubquery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_id');

        $products = Product::with(['category.parent', 'images', 'variants.color'])
            ->leftJoinSub($soldSubquery, 'order_sales', function ($join) {
                $join->on('products.id', '=', 'order_sales.product_id');
            })
            ->select('products.*', DB::raw('COALESCE(order_sales.total_sold, 0) as total_sold'))
            ->where('products.is_active', true)
            ->orderByDesc('total_sold')
            ->orderByDesc('products.created_at')
            ->take(5)
            ->get();

        foreach ($products as $product) {
            if ($product->category) {
                $product->parent_slug = $product->category->parent
                    ? $product->category->parent->slug
                    : $product->category->slug;
                $product->child_slug = $product->category->slug;
            } else {
                $product->parent_slug = 'all';
                $product->child_slug = 'all';
            }
        }

        return $products;
    }
}
