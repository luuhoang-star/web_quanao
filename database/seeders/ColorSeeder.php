<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Đen', 'hex_code' => '#111827', 'code' => 'BLACK'],
            ['name' => 'Trắng', 'hex_code' => '#FFFFFF', 'code' => 'WHITE'],
            ['name' => 'Xám Tiêu', 'hex_code' => '#6B7280', 'code' => 'GRAY'],
            ['name' => 'Xanh Navy', 'hex_code' => '#1E3A8A', 'code' => 'NAVY'],
            ['name' => 'Đỏ Rượu', 'hex_code' => '#991B1B', 'code' => 'BURGUNDY'],
            ['name' => 'Beige Sữa', 'hex_code' => '#F5F5DC', 'code' => 'BEIGE'],
            ['name' => 'Xanh Rêu', 'hex_code' => '#3F6212', 'code' => 'OLIVE'],
            ['name' => 'Nâu Cà Phê', 'hex_code' => '#78350F', 'code' => 'BROWN'],
            ['name' => 'Xanh Pastel', 'hex_code' => '#93C5FD', 'code' => 'LIGHTBLUE'],
            ['name' => 'Hồng Pastel', 'hex_code' => '#F472B6', 'code' => 'PINK'],
        ];

        foreach ($colors as $color) {
            DB::table('colors')->updateOrInsert(
                ['name' => $color['name']],
                [
                    'hex_code' => $color['hex_code'],
                    'code' => $color['code'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
