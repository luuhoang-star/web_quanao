<?php

namespace Database\Seeders;

use App\Models\Outfit;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OutfitSeeder extends Seeder
{
    /**
     * Seed dữ liệu 4 Outfit ban đầu.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('outfit_product')->truncate();
        DB::table('outfits')->truncate();
        Schema::enableForeignKeyConstraints();

        $products = Product::where('is_active', true)->take(12)->get();

        $outfitsData = [
            [
                'slug' => 'office',
                'name' => 'Thanh Lịch Công Sở (Office Outfit)',
                'badge' => 'OFFICE SMART',
                'tagline' => 'Chỉn chu, lịch lãm & Tự tin trong mọi cuộc họp',
                'gradient' => 'linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%)',
                'hero_image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Set đồ chuẩn mực dành cho quý ông công sở hiện đại. Kết hợp hài hòa giữa áo sơ mi slimfit chống nhăn tôn dáng, quần tây lịch lãm cùng áo polo trẻ trung. Outfit mang lại vẻ ngoài lịch thiệp, chuyên nghiệp và đầy sức hút.',
                'items_included' => ['Áo Sơ Mi Slimfit', 'Quần Tây Hàn Quốc', 'Áo Polo Classic'],
                'sort_order' => 1,
                'is_active' => true,
                'product_ids' => [6, 26, 10],
            ],
            [
                'slug' => 'summer',
                'name' => 'Dạo Phố Cuối Tuần (Summer Outfit)',
                'badge' => 'SUMMER WEEKEND',
                'tagline' => 'Thoải mái, phóng khoáng & Tươi trẻ dưới nắng hè',
                'gradient' => 'linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #075985 100%)',
                'hero_image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Phong cách street style phóng khoáng cho những buổi dạo phố hay chuyến du lịch cuối tuần. Kết hợp giữa áo thun thoáng mát, áo khoác nhẹ chống nắng và quần jean cạp cao thanh lịch.',
                'items_included' => ['Áo Khoác Mùa Hè', 'Váy Xòe Floral', 'Áo Thun AIRism', 'Quần Jean Cạp Cao'],
                'sort_order' => 2,
                'is_active' => true,
                'product_ids' => $products->slice(3, 3)->pluck('id')->toArray(),
            ],
            [
                'slug' => 'sport',
                'name' => 'Năng Động Thể Thao (Sport Outfit)',
                'badge' => 'ACTIVE SPORT',
                'tagline' => 'Bứt phá giới hạn với chất liệu co giãn 4 chiều',
                'gradient' => 'linear-gradient(135deg, #991b1b 0%, #be123c 50%, #e11d48 100%)',
                'hero_image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Set đồ thể thao chuyên dụng tích hợp công nghệ thấm hút mồ hôi Dri-FIT vượt trội. Co giãn 4 chiều tối đa giúp bạn thoải mái vận động trong phòng tập gym hay các môn thể thao ngoài trời.',
                'items_included' => ['Áo Thun Nike Dri-FIT', 'Mũ Lưỡi Trai Nike', 'Balo Thể Thao Adidas', 'Tất Cổ Ngắn Nike'],
                'sort_order' => 3,
                'is_active' => true,
                'product_ids' => $products->slice(6, 3)->pluck('id')->toArray(),
            ],
            [
                'slug' => 'minimal',
                'name' => 'Phong Cách Tối Giản (Minimal Outfit)',
                'badge' => 'MINIMAL OUTFIT',
                'tagline' => 'Tối giản chi tiết - Tối đa sự tinh tế',
                'gradient' => 'linear-gradient(135deg, #18181b 0%, #27272a 50%, #3f3f46 100%)',
                'hero_image' => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Sự kết hợp tinh tế giữa những gam màu trung tính chuẩn Minimalism. Thiết kế đơn giản nhưng tỉ mỉ trong từng đường kim mũi chỉ, tôn lên nét quyến rũ tự nhiên.',
                'items_included' => ['Áo Polo Atino Classic', 'Bộ Đồ Ngủ Lụa Satin', 'Váy Ngủ Lụa Dây', 'Set Quần Lót Boxer'],
                'sort_order' => 4,
                'is_active' => true,
                'product_ids' => $products->slice(9, 3)->pluck('id')->toArray(),
            ],
        ];

        foreach ($outfitsData as $data) {
            $productIds = $data['product_ids'];
            unset($data['product_ids']);

            $outfit = Outfit::create($data);

            if (! empty($productIds)) {
                $syncData = [];
                foreach ($productIds as $index => $pid) {
                    $syncData[$pid] = ['sort_order' => $index + 1];
                }
                $outfit->products()->sync($syncData);
            }
        }
    }
}
