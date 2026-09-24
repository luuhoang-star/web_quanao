<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cập nhật sale_price cho sản phẩm ID 1 và các sản phẩm có biến thể giảm giá
        DB::table('products')
            ->where('id', 1)
            ->update(['sale_price' => 350000]);

        // 2. Đồng bộ giá sale_price thấp nhất từ biến thể cho toàn bộ sản phẩm
        $products = DB::table('products')->get();
        foreach ($products as $p) {
            $minVariantSale = DB::table('product_variants')
                ->where('product_id', $p->id)
                ->where('is_active', true)
                ->whereNotNull('sale_price')
                ->where('sale_price', '>', 0)
                ->whereRaw('sale_price < price')
                ->min('sale_price');

            if ($minVariantSale && (! $p->sale_price || $minVariantSale < $p->sale_price)) {
                DB::table('products')
                    ->where('id', $p->id)
                    ->update(['sale_price' => $minVariantSale]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
