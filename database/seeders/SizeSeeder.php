<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            ['name' => 'XS', 'code' => 'XS', 'size_type' => 'LETTER', 'sort_order' => 1],
            ['name' => 'S', 'code' => 'S', 'size_type' => 'LETTER', 'sort_order' => 2],
            ['name' => 'M', 'code' => 'M', 'size_type' => 'LETTER', 'sort_order' => 3],
            ['name' => 'L', 'code' => 'L', 'size_type' => 'LETTER', 'sort_order' => 4],
            ['name' => 'XL', 'code' => 'XL', 'size_type' => 'LETTER', 'sort_order' => 5],
            ['name' => 'XXL', 'code' => 'XXL', 'size_type' => 'LETTER', 'sort_order' => 6],
            ['name' => '28', 'code' => '28', 'size_type' => 'NUMBER', 'sort_order' => 7],
            ['name' => '29', 'code' => '29', 'size_type' => 'NUMBER', 'sort_order' => 8],
            ['name' => '30', 'code' => '30', 'size_type' => 'NUMBER', 'sort_order' => 9],
            ['name' => '31', 'code' => '31', 'size_type' => 'NUMBER', 'sort_order' => 10],
            ['name' => '32', 'code' => '32', 'size_type' => 'NUMBER', 'sort_order' => 11],
            ['name' => '33', 'code' => '33', 'size_type' => 'NUMBER', 'sort_order' => 12],
            ['name' => '34', 'code' => '34', 'size_type' => 'NUMBER', 'sort_order' => 13],
            ['name' => 'Freesize', 'code' => 'FS', 'size_type' => 'FREE_SIZE', 'sort_order' => 14],
        ];

        foreach ($sizes as $size) {
            DB::table('sizes')->updateOrInsert(
                ['name' => $size['name']],
                [
                    'code' => $size['code'],
                    'size_type' => $size['size_type'],
                    'sort_order' => $size['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
