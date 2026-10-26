<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Schema::hasTable('banners')) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('banners')->truncate();
        Schema::enableForeignKeyConstraints();

        $banners = [
            [
                'title' => 'BỘ SƯU TẬP MÙA HÈ 2026 - TƯƠI MÁT & NĂNG ĐỘNG',
                'image_url' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=1400&q=80',
                'link_url' => '/shop?collection=summer-2026',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'PHONG CÁCH TỐI GIẢN - TINH TẾ MỖI NGÀY',
                'image_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1400&q=80',
                'link_url' => '/shop?collection=minimal',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'THỜI TRANG CÔNG SỞ LỊCH LÃM - CHỈN CHU & HIỆN ĐẠI',
                'image_url' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1400&q=80',
                'link_url' => '/shop?collection=office',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'THỂ THAO NĂNG ĐỘNG - BỨT PHÁ MỌI GIỚI HẠN',
                'image_url' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=1400&q=80',
                'link_url' => '/shop?collection=sport',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('banners')->insert($banners);
    }
}
