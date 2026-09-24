<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('collections')->truncate();
        Schema::enableForeignKeyConstraints();

        $collections = [
            [
                'name' => 'Summer Collection 2026',
                'slug' => 'summer-2026',
                'badge_text' => 'BST MÙA HÈ',
                'subtitle' => 'Thoáng mát & Thanh lịch',
                'description' => 'Bộ sưu tập mùa hè tươi mát với các chất liệu thoáng khí, kiểu dáng năng động.',
                'image' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=600&q=80',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minimal Collection',
                'slug' => 'minimal',
                'badge_text' => 'MINIMALIST',
                'subtitle' => 'Tối giản & Hiện đại',
                'description' => 'Phong cách tối giản, tinh tế dành cho những người yêu thích sự đơn giản và thanh lịch.',
                'image' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=600&q=80',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Office Collection',
                'slug' => 'office',
                'badge_text' => 'CÔNG SỞ',
                'subtitle' => 'Lịch lãm & Sang trọng',
                'description' => 'Trang phục công sở lịch lãm, hiện đại mang lại sự tự tin cho ngày làm việc.',
                'image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=600&q=80',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sport Collection',
                'slug' => 'sport',
                'badge_text' => 'THỂ THAO',
                'subtitle' => 'Co giãn 4 chiều',
                'description' => 'Trang phục thể thao co giãn 4 chiều, hỗ trợ tập luyện tối đa.',
                'image' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=600&q=80',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('collections')->insert($collections);
    }
}
