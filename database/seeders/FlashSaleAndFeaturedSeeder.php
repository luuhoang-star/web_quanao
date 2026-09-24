<?php

namespace Database\Seeders;

use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FlashSaleAndFeaturedSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Đồng bộ cấu trúc bảng nếu cột mới chưa xuất hiện
        if (! Schema::hasColumn('products', 'is_featured')) {
            Schema::table('products', function ($table) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            });
        }

        if (! Schema::hasColumn('flash_sales', 'quantity')) {
            Schema::table('flash_sales', function ($table) {
                $table->integer('quantity')->default(10)->after('end_time');
                $table->integer('sold_quantity')->default(0)->after('quantity');
            });
        }

        // Xóa sạch flash sales cũ để tái tạo mới
        Schema::disableForeignKeyConstraints();
        DB::table('flash_sales')->truncate();
        Schema::enableForeignKeyConstraints();

        // Lấy danh sách sản phẩm
        $products = Product::where('is_active', true)->get();
        if ($products->count() < 8) {
            return;
        }

        // Chọn 8 sản phẩm đầu tiên làm Flash Sale đang hoạt động
        $flashProducts = $products->take(8);
        $discountRates = [0.65, 0.70, 0.60, 0.75, 0.55, 0.68, 0.72, 0.50]; // Giảm từ 25% đến 50%

        foreach ($flashProducts as $idx => $product) {
            $rate = $discountRates[$idx] ?? 0.7;
            $salePrice = round(($product->price * $rate) / 1000) * 1000;
            $product->update([
                'is_featured' => ($idx < 6), // 6 sản phẩm đầu vừa nổi bật vừa flash sale
                'sale_price' => $salePrice,
            ]);

            FlashSale::create([
                'product_id' => $product->id,
                'sale_price' => $salePrice,
                'start_time' => now()->subHours(2),
                'end_time' => now()->addHours(14),
                'quantity' => 30 + ($idx * 5),
                'sold_quantity' => 8 + ($idx * 3),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Đánh dấu thêm một số sản phẩm khác là Nổi bật (không thuộc Flash Sale)
        $remainingFeatured = $products->slice(8, 4);
        foreach ($remainingFeatured as $product) {
            $product->update(['is_featured' => true]);
        }
    }
}
