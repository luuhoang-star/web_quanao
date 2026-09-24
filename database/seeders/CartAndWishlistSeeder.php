<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CartAndWishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('cart_items')->truncate();
        DB::table('carts')->truncate();
        DB::table('wishlists')->truncate();
        Schema::enableForeignKeyConstraints();

        $user2 = User::where('email', 'user@example.com')->first();
        $user4 = User::where('email', 'user@gmail.com')->first();

        if (! $user2) {
            return;
        }

        $products = Product::with('variants')->where('is_active', true)->get();
        if ($products->count() < 6) {
            return;
        }

        // 1. Tạo Giỏ hàng cho User 2 (Nguyễn Văn An)
        $cart2 = Cart::create([
            'user_id' => $user2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Thêm 2 món vào giỏ hàng của User 2
        $p1 = $products[0];
        $v1 = $p1->variants->first();
        if ($v1) {
            CartItem::create([
                'cart_id' => $cart2->id,
                'product_id' => $p1->id,
                'product_variant_id' => $v1->id,
                'quantity' => 2,
                'price' => $p1->sale_price ?: $p1->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $p2 = $products[2];
        $v2 = $p2->variants->first();
        if ($v2) {
            CartItem::create([
                'cart_id' => $cart2->id,
                'product_id' => $p2->id,
                'product_variant_id' => $v2->id,
                'quantity' => 1,
                'price' => $p2->sale_price ?: $p2->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Tạo Giỏ hàng cho User 4 (Trần Thị Mai)
        if ($user4) {
            $cart4 = Cart::create([
                'user_id' => $user4->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $p3 = $products[8];
            $v3 = $p3->variants->first();
            if ($v3) {
                CartItem::create([
                    'cart_id' => $cart4->id,
                    'product_id' => $p3->id,
                    'product_variant_id' => $v3->id,
                    'quantity' => 1,
                    'price' => $p3->sale_price ?: $p3->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Tạo Danh sách Yêu thích (Wishlist)
        // User 2 yêu thích sản phẩm 1, 4, 7, 10
        $favIndexesUser2 = [1, 4, 7, 10];
        foreach ($favIndexesUser2 as $fIdx) {
            if (isset($products[$fIdx])) {
                DB::table('wishlists')->insert([
                    'user_id' => $user2->id,
                    'product_id' => $products[$fIdx]->id,
                    'created_at' => now()->subDays(rand(1, 5)),
                ]);
            }
        }

        // User 4 yêu thích sản phẩm 8, 9, 11, 14
        if ($user4) {
            $favIndexesUser4 = [8, 9, 11, 14];
            foreach ($favIndexesUser4 as $fIdx) {
                if (isset($products[$fIdx])) {
                    DB::table('wishlists')->insert([
                        'user_id' => $user4->id,
                        'product_id' => $products[$fIdx]->id,
                        'created_at' => now()->subDays(rand(1, 5)),
                    ]);
                }
            }
        }
    }
}
