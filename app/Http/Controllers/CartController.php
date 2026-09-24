<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Lấy tổng số lượng sản phẩm trong giỏ hàng.
     */
    public static function getCartCount(): int
    {
        return (new CartService)->getCartCount();
    }

    /**
     * Hợp nhất Giỏ hàng Guest Session vào DB khi đăng nhập.
     */
    public static function mergeSessionCartToUser(int $userId): void
    {
        (new CartService)->mergeSessionCartToUser($userId);
    }

    /**
     * Lấy hoặc khởi tạo Giỏ hàng của người dùng.
     */
    private function getCart(): ?Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        return null;
    }

    /**
     * Hiển thị trang giỏ hàng.
     */
    public function index(): View
    {
        if (Auth::check()) {
            $cart = $this->getCart();
            $cart->load(['items.product.images', 'items.variant.color', 'items.variant.size']);

            $validItems = collect();
            foreach ($cart->items as $item) {
                if (! $item->product) {
                    $item->delete();

                    continue;
                }

                $realtimePrice = CartService::getRealtimePrice($item->product, $item->variant);
                if ($item->price != $realtimePrice) {
                    $item->update(['price' => $realtimePrice]);
                }
                $validItems->push($item);
            }

            $cart->setRelation('items', $validItems);
            $totalAmount = $validItems->sum(fn ($item) => $item->price * $item->quantity);

            return view('cart.index', compact('cart', 'totalAmount'));
        }

        $sessionCart = session('cart', []);
        $productIds = array_column($sessionCart, 'product_id');
        $products = Product::with(['images'])->whereIn('id', $productIds)->get()->keyBy('id');

        $variantIds = array_filter(array_column($sessionCart, 'product_variant_id'));
        $variants = ProductVariant::with(['color', 'size'])->whereIn('id', $variantIds)->get()->keyBy('id');

        $virtualItems = collect();
        $totalAmount = 0;

        foreach ($sessionCart as $key => $item) {
            $product = $products->get($item['product_id']);
            if (! $product || ! $product->is_active) {
                continue;
            }

            $variant = isset($item['product_variant_id']) ? $variants->get($item['product_variant_id']) : null;
            if ($variant && ! $variant->is_active) {
                continue;
            }

            $realtimePrice = CartService::getRealtimePrice($product, $variant);
            $subtotal = $realtimePrice * $item['quantity'];
            $totalAmount += $subtotal;

            $virtualItems->push((object) [
                'id' => $key,
                'product_id' => $product->id,
                'product_variant_id' => $item['product_variant_id'] ?? null,
                'product' => $product,
                'variant' => $variant,
                'quantity' => $item['quantity'],
                'price' => $realtimePrice,
            ]);
        }

        $cart = (object) ['items' => $virtualItems];

        return view('cart.index', compact('cart', 'totalAmount'));
    }

    /**
     * Lấy dữ liệu giỏ hàng dạng JSON cho Cart Drawer.
     */
    public function jsonContent(): JsonResponse
    {
        $items = collect();
        $totalAmount = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cart->load(['items.product.images', 'items.variant.color', 'items.variant.size']);
                foreach ($cart->items as $item) {
                    $product = $item->product;
                    if (! $product || ! $product->is_active) {
                        continue;
                    }

                    $realtimePrice = CartService::getRealtimePrice($product, $item->variant);
                    $subtotal = $realtimePrice * $item->quantity;
                    $totalAmount += $subtotal;

                    $img = $product->images->first()?->image_path
                        ? asset('storage/'.$product->images->first()->image_path)
                        : null;

                    $sizeName = $item->variant?->size?->name ?? null;
                    $colorName = $item->variant?->color?->name ?? null;
                    $variantText = $colorName ? ($colorName.($sizeName ? ' / '.$sizeName : '')) : ($sizeName ?: 'Mặc định');

                    $items->push([
                        'id' => $item->id,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'image' => $img,
                        'variant' => $variantText,
                        'quantity' => $item->quantity,
                        'price' => number_format($realtimePrice).'đ',
                        'subtotal' => number_format($subtotal).'đ',
                        'raw_subtotal' => $subtotal,
                    ]);
                }
            }
        } else {
            $sessionCart = session('cart', []);
            $productIds = array_column($sessionCart, 'product_id');
            $products = Product::with(['images'])->whereIn('id', $productIds)->get()->keyBy('id');

            $variantIds = array_filter(array_column($sessionCart, 'product_variant_id'));
            $variants = ProductVariant::with(['color', 'size'])->whereIn('id', $variantIds)->get()->keyBy('id');

            foreach ($sessionCart as $key => $item) {
                $product = $products->get($item['product_id']);
                if (! $product || ! $product->is_active) {
                    continue;
                }

                $variant = isset($item['product_variant_id']) ? $variants->get($item['product_variant_id']) : null;
                if ($variant && ! $variant->is_active) {
                    continue;
                }

                $realtimePrice = CartService::getRealtimePrice($product, $variant);
                $subtotal = $realtimePrice * $item['quantity'];
                $totalAmount += $subtotal;

                $img = $product->images->first()?->image_path
                    ? asset('storage/'.$product->images->first()->image_path)
                    : null;

                $sizeName = $variant?->size?->name ?? null;
                $colorName = $variant?->color?->name ?? null;
                $variantText = $colorName ? ($colorName.($sizeName ? ' / '.$sizeName : '')) : ($sizeName ?: 'Mặc định');

                $items->push([
                    'id' => $key,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $img,
                    'variant' => $variantText,
                    'quantity' => $item['quantity'],
                    'price' => number_format($realtimePrice).'đ',
                    'subtotal' => number_format($subtotal).'đ',
                    'raw_subtotal' => $subtotal,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'count' => (int) $items->sum('quantity'),
            'total' => number_format($totalAmount).'đ',
            'raw_total' => $totalAmount,
            'items' => $items,
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'quantity.min' => 'Số lượng mua tối thiểu là 1.',
        ]);

        try {
            $result = $this->cartService->addToCart(
                (int) $request->product_id,
                $request->product_variant_id ? (int) $request->product_variant_id : null,
                (int) $request->quantity
            );
        } catch (\InvalidArgumentException $e) {
            $msg = $e->getMessage();

            return $request->ajax() || $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $product = $result['product'];
        $cartCount = $result['count'];

        if ($request->has('buy_now') || $request->buy_now) {
            return redirect()->route('orders.checkout');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm "'.$product->name.'" vào giỏ hàng!',
                'count' => $cartCount,
            ]);
        }

        return back()->with('success', 'Đã thêm sản phẩm "'.$product->name.'" vào giỏ hàng!');
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng.
     */
    public function update(Request $request, string|int $id): RedirectResponse|JsonResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quantity = (int) $request->quantity;

        if (Auth::check()) {
            $cartItem = CartItem::with(['variant', 'product'])->findOrFail($id);
            $cart = $this->getCart();
            if ($cartItem->cart_id !== $cart->id) {
                abort(403);
            }

            if (! $cartItem->product || ! $cartItem->product->is_active) {
                $cartItem->delete();
                $msg = 'Sản phẩm này hiện đã tạm ngưng kinh doanh và đã được xóa khỏi giỏ hàng.';

                return $request->ajax() || $request->wantsJson()
                    ? response()->json(['success' => false, 'message' => $msg], 422)
                    : back()->with('error', $msg);
            }

            if ($cartItem->variant) {
                if (! $cartItem->variant->is_active) {
                    $cartItem->delete();
                    $msg = 'Biến thể sản phẩm này hiện đã ngưng bán và đã được xóa khỏi giỏ hàng.';

                    return $request->ajax() || $request->wantsJson()
                        ? response()->json(['success' => false, 'message' => $msg], 422)
                        : back()->with('error', $msg);
                }

                if ($quantity > $cartItem->variant->stock_quantity) {
                    $msg = "Số lượng tồn kho không đủ (Hiện còn {$cartItem->variant->stock_quantity} món)!";

                    return $request->ajax() || $request->wantsJson()
                        ? response()->json(['success' => false, 'message' => $msg], 422)
                        : back()->with('error', $msg);
                }
            }

            $realtimePrice = CartService::getRealtimePrice($cartItem->product, $cartItem->variant);
            $cartItem->update([
                'quantity' => $quantity,
                'price' => $realtimePrice,
            ]);
        } else {
            $cart = session('cart', []);
            if (isset($cart[$id])) {
                $product = Product::find($cart[$id]['product_id']);
                $variant = isset($cart[$id]['product_variant_id']) ? ProductVariant::find($cart[$id]['product_variant_id']) : null;

                if (! $product || ! $product->is_active || ($variant && (! $variant->is_active || $variant->product_id !== $product->id))) {
                    unset($cart[$id]);
                    session(['cart' => $cart]);
                    $msg = 'Sản phẩm này hiện không khả dụng và đã bị xóa khỏi giỏ hàng.';

                    return $request->ajax() || $request->wantsJson()
                        ? response()->json(['success' => false, 'message' => $msg], 422)
                        : back()->with('error', $msg);
                }

                if ($variant && $quantity > $variant->stock_quantity) {
                    $msg = "Số lượng tồn kho không đủ (Hiện còn {$variant->stock_quantity} món)!";

                    return $request->ajax() || $request->wantsJson()
                        ? response()->json(['success' => false, 'message' => $msg], 422)
                        : back()->with('error', $msg);
                }

                $cart[$id]['quantity'] = $quantity;
                $cart[$id]['price'] = CartService::getRealtimePrice($product, $variant);
                session(['cart' => $cart]);
            }
        }

        $cartCount = $this->cartService->getCartCount();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng sản phẩm!',
                'count' => $cartCount,
            ]);
        }

        return back()->with('success', 'Đã cập nhật số lượng sản phẩm!');
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng.
     */
    public function destroy(string|int $id): RedirectResponse|JsonResponse
    {
        if (Auth::check()) {
            $cartItem = CartItem::findOrFail($id);
            $cart = $this->getCart();
            if ($cartItem->cart_id !== $cart->id) {
                abort(403);
            }
            $cartItem->delete();
        } else {
            $cart = session('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session(['cart' => $cart]);
            }
        }

        $cartCount = $this->cartService->getCartCount();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                'count' => $cartCount,
            ]);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /**
     * Làm trống toàn bộ giỏ hàng.
     */
    public function clear(): RedirectResponse|JsonResponse
    {
        if (Auth::check()) {
            $cart = $this->getCart();
            if ($cart) {
                $cart->items()->delete();
            }
        } else {
            session()->forget('cart');
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã làm trống giỏ hàng!',
                'count' => 0,
            ]);
        }

        return back()->with('success', 'Đã làm trống giỏ hàng!');
    }
}
