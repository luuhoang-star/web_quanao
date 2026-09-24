<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cấu trúc danh mục 5 nhóm chính chuẩn theo các sản phẩm trong hệ thống
        $tree = [
            'Thời trang Nam' => [
                'Áo Nam',
                'Áo thun Nam',
                'Áo sơ mi Nam',
                'Áo polo Nam',
                'Áo khoác Nam',
                'Quần Nam',
                'Quần tây Nam',
                'Quần jean Nam',
            ],
            'Thời trang Nữ' => [
                'Áo Nữ',
                'Áo thun Nữ',
                'Áo sơ mi Nữ',
                'Quần Nữ',
                'Quần jean Nữ',
                'Váy Nữ',
                'Áo khoác Nữ',
            ],
            'Phụ kiện' => [
                'Mũ & Nón',
                'Ví da',
                'Túi xách & Balo',
                'Thắt lưng',
                'Kính mát',
                'Đồng hồ',
            ],
            'Đồ ngủ' => [
                'Bộ đồ ngủ Pijama',
                'Áo ngủ',
                'Quần ngủ',
                'Váy ngủ lụa',
            ],
            'Đồ lót' => [
                'Áo lót',
                'Quần lót',
                'Boxer Nam',
                'Tất & Vớ',
            ],
        ];

        Schema::disableForeignKeyConstraints();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();

        $parentOrder = 1;
        foreach ($tree as $parentName => $children) {
            // Insert danh mục Cha (Root)
            $parentId = DB::table('categories')->insertGetId([
                'parent_id' => null,
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'image' => null,
                'sort_order' => $parentOrder++,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert danh mục Con (Sub-categories)
            $childOrder = 1;
            foreach ($children as $childName) {
                DB::table('categories')->insert([
                    'parent_id' => $parentId,
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'image' => null,
                    'sort_order' => $childOrder++,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
