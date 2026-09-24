<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartService
{
    protected static ?int $cartCountMemo = null;

    /**
     * Xóa cache memoize số lượng giỏ hàng khi có thay đổi.
     */
    public static function clearCartCountCache(): void
    {
        self::$cartCountMemo = null;
    }

    /**
     * Lấy số lượng vật phẩm trong giỏ hàng.
     */
    public function getCartCount(): int
    {
        if (self::$cartCountMemo !== null) {
            return self::$cartCountMemo;
        }

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->with('items')->first();

            self::$cartCountMemo = $cart ? (int) $cart->items->sum('quantity') : 0;
        } else {
            $sessionCart = session('cart', []);

            self::$cartCountMemo = (int) array_sum(array_column($sessionCart, 'quantity'));
        }

        return self::$cartCountMemo;
    }

    /**
     * Tính toán giá sản phẩm thực tế theo thời gian thực (Ưu tiên FlashSale -> Biến thể -> Sản phẩm).
     */
    public static function getRealtimePrice(Product $product, ?ProductVariant $variant = null): float
    {
        $activeFlashSale = FlashSale::active()->where('product_id', $product->id)->first();
        if ($activeFlashSale) {
            return (float) $activeFlashSale->sale_price;
        }

        if ($variant) {
            if ($variant->sale_price && $variant->sale_price > 0 && $variant->sale_price < $variant->price) {
                return (float) $variant->sale_price;
            }

            return (float) $variant->price;
        }

        if ($product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->price) {
            return (float) $product->sale_price;
        }

        return (float) $product->price;
    }

    /**
     * Merge giỏ hàng Session vào DB khi người dùng vừa Đăng Nhập.
     */
    public function mergeSessionCartToUser(int $userId): void
    {
        $sessionCart = session('cart', []);
        if (empty($sessionCart)) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($sessionCart as $item) {
            $product = Product::find($item['product_id']);
            if (! $product || ! $product->is_active) {
                continue;
            }

            $variant = isset($item['product_variant_id']) ? ProductVariant::find($item['product_variant_id']) : null;
            if ($variant && (! $variant->is_active || $variant->product_id !== $product->id)) {
                continue;
            }

            $realtimePrice = self::getRealtimePrice($product, $variant);

            $cartItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $item['product_id'])
                ->where('product_variant_id', $item['product_variant_id'] ?? null)
                ->first();

            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $cartItem->quantity + $item['quantity'],
                    'price' => $realtimePrice,
                ]);
            } else {
                CartItem::create([
                    'cart_id' => $userCart->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $realtimePrice,
                ]);
            }
        }

        session()->forget('cart');
        self::clearCartCountCache();
    }

    /**
     * Thêm sản phẩm vào giỏ hàng với đầy đủ kiểm tra bảo mật & tồn kho.
     */
    public function addToCart(int $productId, ?int $variantId, int $quantity): array
    {
        $product = Product::findOrFail($productId);

        // 1. Kiểm tra trạng thái hoạt động của sản phẩm
        if (! $product->is_active) {
            throw new \InvalidArgumentException("Sản phẩm '{$product->name}' hiện đã tạm ngưng kinh doanh.");
        }

        $variant = $variantId ? ProductVariant::find($variantId) : null;

        // 2. Kiểm tra tính hợp lệ của biến thể
        if ($variantId && ! $variant) {
            throw new \InvalidArgumentException('Biến thể sản phẩm không tồn tại.');
        }

        if ($variant) {
            if (! $variant->is_active) {
                throw new \InvalidArgumentException('Biến thể sản phẩm đã chọn hiện không khả dụng.');
            }

            if ($variant->product_id !== $product->id) {
                throw new \InvalidArgumentException('Biến thể đã chọn không thuộc về sản phẩm này!');
            }
        }

        // 3. Tính toán giá thời gian thực
        $price = self::getRealtimePrice($product, $variant);

        // 4. Kiểm tra giới hạn số lượng tồn kho
        $existingQty = 0;
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($cartItem) {
                $existingQty = $cartItem->quantity;
            }
        } else {
            $cart = session('cart', []);
            $key = $product->id.'_'.($variantId ?? 0);
            if (isset($cart[$key])) {
                $existingQty = $cart[$key]['quantity'];
            }
        }

        $totalTargetQty = $existingQty + $quantity;

        if ($variant && $totalTargetQty > $variant->stock_quantity) {
            $available = max(0, $variant->stock_quantity - $existingQty);
            if ($available <= 0) {
                throw new \InvalidArgumentException("Số lượng tồn kho không đủ (Hiện còn {$variant->stock_quantity} món, bạn đã có {$existingQty} món trong giỏ).");
            }
            throw new \InvalidArgumentException("Số lượng tồn kho không đủ! Bạn chỉ có thể thêm tối đa {$available} món nữa vào giỏ.");
        }

        // 5. Thêm/cập nhật giỏ hàng
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $variantId)
                ->first();

            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $cartItem->quantity + $quantity,
                    'price' => $price,
                ]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }
        } else {
            $cart = session('cart', []);
            $key = $product->id.'_'.($variantId ?? 0);

            if (isset($cart[$key])) {
                $cart[$key]['quantity'] += $quantity;
                $cart[$key]['price'] = $price;
            } else {
                $cart[$key] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variantId,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }

            session(['cart' => $cart]);
        }

        self::clearCartCountCache();

        return [
            'product' => $product,
            'count' => $this->getCartCount(),
        ];
    }
}
