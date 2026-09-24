<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('coupons')->truncate();
        Schema::enableForeignKeyConstraints();

        $coupons = [
            [
                'code' => 'ATINO50K',
                'type' => 'fixed_amount',
                'value' => 50000,
                'min_order_value' => 300000,
                'max_discount_amount' => 50000,
                'usage_limit' => 100,
                'used_count' => 8,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(60),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'free_shipping',
                'value' => 30000,
                'min_order_value' => 299000,
                'max_discount_amount' => 30000,
                'usage_limit' => 500,
                'used_count' => 34,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(90),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SUMMER2026',
                'type' => 'percent',
                'value' => 15,
                'min_order_value' => 400000,
                'max_discount_amount' => 100000,
                'usage_limit' => 200,
                'used_count' => 19,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(45),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'WELCOME10',
                'type' => 'percent',
                'value' => 10,
                'min_order_value' => 200000,
                'max_discount_amount' => 50000,
                'usage_limit' => 1000,
                'used_count' => 52,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(180),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'VIP200K',
                'type' => 'fixed_amount',
                'value' => 200000,
                'min_order_value' => 1500000,
                'max_discount_amount' => 200000,
                'usage_limit' => 50,
                'used_count' => 6,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(30),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('coupons')->insert($coupons);
    }
}
