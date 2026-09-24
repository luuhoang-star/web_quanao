<?php

namespace Database\Seeders;

use App\Models\Lookbook;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LookbookSeeder extends Seeder
{
    /**
     * Seed 4 Lookbooks ban đầu cho hệ thống.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('lookbook_product')->truncate();
        DB::table('lookbooks')->truncate();
        Schema::enableForeignKeyConstraints();

        $products = Product::where('is_active', true)->take(12)->get();

        $lookbooksData = [
            [
                'title' => 'Lookbook: Năng Động Thể Thao',
                'subtitle' => 'Phong cách phối đồ thể thao hiện đại, bứt phá giới hạn',
                'image_path' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=600&q=80',
                'instagram_url' => 'https://instagram.com',
                'button_text' => 'Xem & Mua 3 Sản Phẩm',
                'sort_order' => 1,
                'is_active' => true,
                'product_ids' => $products->slice(0, 3)->pluck('id')->toArray(),
            ],
            [
                'title' => 'Lookbook: Dạo Phố Mùa Hè',
                'subtitle' => 'Thoải mái, phóng khoáng & tươi trẻ dưới nắng hè',
                'image_path' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=600&q=80',
                'instagram_url' => 'https://instagram.com',
                'button_text' => 'Xem & Mua 2 Sản Phẩm',
                'sort_order' => 2,
                'is_active' => true,
                'product_ids' => $products->slice(3, 2)->pluck('id')->toArray(),
            ],
            [
                'title' => 'Lookbook: Thanh Lịch Công Sở',
                'subtitle' => 'Chỉn chu, lịch lãm & tự tin trong mọi cuộc họp',
                'image_path' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=600&q=80',
                'instagram_url' => 'https://instagram.com',
                'button_text' => 'Xem & Mua 3 Sản Phẩm',
                'sort_order' => 3,
                'is_active' => true,
                'product_ids' => $products->slice(5, 3)->pluck('id')->toArray(),
            ],
            [
                'title' => 'Lookbook: Phong Cách Tối Giản Minimal',
                'subtitle' => 'Tối giản chi tiết - Tối đa sự tinh tế',
                'image_path' => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=600&q=80',
                'instagram_url' => 'https://instagram.com',
                'button_text' => 'Xem & Mua 2 Sản Phẩm',
                'sort_order' => 4,
                'is_active' => true,
                'product_ids' => $products->slice(8, 2)->pluck('id')->toArray(),
            ],
        ];

        foreach ($lookbooksData as $data) {
            $productIds = $data['product_ids'];
            unset($data['product_ids']);

            $lookbook = Lookbook::create($data);

            if (! empty($productIds)) {
                $syncData = [];
                foreach ($productIds as $index => $pid) {
                    $syncData[$pid] = ['sort_order' => $index + 1];
                }
                $lookbook->products()->sync($syncData);
            }
        }
    }
}
