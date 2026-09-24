<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlacedMail;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\CartService;
use App\Services\TelegramNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Hiển thị trang Thanh Toán (Checkout).
     */
    public function checkout(): View|RedirectResponse
    {
        $user = Auth::user();

        // Kiểm tra xem số điện thoại đã được xác thực bằng OTP chưa
        if ($user && ! $user->isPhoneVerified()) {
            return redirect()->route('otp.show')->with('error', 'Vui lòng xác thực số điện thoại của bạn bằng mã OTP trước khi tiến hành thanh toán!');
        }

        $cart = Cart::where('user_id', Auth::id())->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Giỏ hàng của bạn đang trống! Vui lòng chọn sản phẩm trước khi thanh toán.');
        }

        $cart->load(['items.product', 'items.variant.color', 'items.variant.size']);

        $subtotal = 0;
        foreach ($cart->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->is_active) {
                return redirect()->route('cart.index')->with('error', 'Giỏ hàng chứa sản phẩm đã tạm ngưng kinh doanh. Vui lòng kiểm tra lại!');
            }

            if ($item->variant && (! $item->variant->is_active || $item->variant->product_id !== $product->id)) {
                return redirect()->route('cart.index')->with('error', 'Giỏ hàng chứa biến thể không khả dụng. Vui lòng kiểm tra lại!');
            }

            $realtimePrice = CartService::getRealtimePrice($product, $item->variant);
            $subtotal += $realtimePrice * $item->quantity;
        }

        $appliedCoupon = session('applied_coupon');
        $discountAmount = 0;
        $couponModel = null;

        if ($appliedCoupon) {
            $couponModel = Coupon::find($appliedCoupon['id'] ?? 0);
            if ($couponModel && $couponModel->is_active && $couponModel->start_date <= now() && $couponModel->end_date >= now()) {
                $userUsedCount = 0;
                if ($couponModel->user_usage_limit && Auth::check()) {
                    $userUsedCount = Order::where('user_id', Auth::id())
                        ->where('coupon_id', $couponModel->id)
                        ->where('status', '!=', 'cancelled')
                        ->count();
                }

                if ($subtotal >= $couponModel->min_order_value && (! $couponModel->user_usage_limit || $userUsedCount < $couponModel->user_usage_limit)) {
                    if ($couponModel->type === 'percent') {
                        $discountAmount = ($subtotal * $couponModel->value) / 100;
                        if ($couponModel->max_discount_amount) {
                            $discountAmount = min($discountAmount, (float) $couponModel->max_discount_amount);
                        }
                    } elseif ($couponModel->type === 'fixed_amount') {
                        $discountAmount = min($subtotal, (float) $couponModel->value);
                    } elseif ($couponModel->type === 'free_shipping') {
                        $discountAmount = 30000;
                    }
                } else {
                    session()->forget('applied_coupon');
                    $appliedCoupon = null;
                }
            } else {
                session()->forget('applied_coupon');
                $appliedCoupon = null;
            }
        }

        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        if ($appliedCoupon && $couponModel?->type === 'free_shipping') {
            $shippingFee = 0;
        }

        $totalAmount = max(0, $subtotal + $shippingFee - $discountAmount);

        if ($user) {
            $user->load('addresses');
        }

        return view('orders.checkout', compact('cart', 'subtotal', 'shippingFee', 'discountAmount', 'totalAmount', 'user', 'appliedCoupon'));
    }

    /**
     * Áp dụng mã giảm giá.
     */
    public function applyCoupon(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'coupon_code' => ['required', 'string'],
        ], [
            'coupon_code.required' => 'Vui lòng nhập mã giảm giá.',
        ]);

        $code = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon || ! $coupon->is_active) {
            $msg = 'Mã giảm giá không tồn tại hoặc đã bị khóa!';

            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $now = now();
        if ($coupon->start_date > $now || $coupon->end_date < $now) {
            $msg = 'Mã giảm giá đã hết hạn hoặc chưa đến thời gian áp dụng!';

            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            $msg = 'Mã giảm giá này đã hết lượt sử dụng!';

            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        if ($coupon->user_usage_limit && Auth::check()) {
            $userUsedCount = Order::where('user_id', Auth::id())
                ->where('coupon_id', $coupon->id)
                ->where('status', '!=', 'cancelled')
                ->count();

            if ($userUsedCount >= $coupon->user_usage_limit) {
                $msg = 'Bạn đã sử dụng hết số lần cho phép ('.$coupon->user_usage_limit.' lần) của mã giảm giá này!';

                return $request->wantsJson() || $request->ajax()
                    ? response()->json(['success' => false, 'message' => $msg], 422)
                    : back()->with('error', $msg);
            }
        }

        $cart = Cart::where('user_id', Auth::id())->first();
        if (! $cart || $cart->items->isEmpty()) {
            $msg = 'Giỏ hàng của bạn đang trống!';

            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $subtotal = 0;
        foreach ($cart->items as $item) {
            if ($item->product && $item->product->is_active) {
                $realtimePrice = CartService::getRealtimePrice($item->product, $item->variant);
                $subtotal += $realtimePrice * $item->quantity;
            }
        }

        if ($subtotal < $coupon->min_order_value) {
            $msg = 'Giá trị giỏ hàng tối thiểu để dùng mã này là '.number_format($coupon->min_order_value).'đ!';

            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $discountAmount = 0;
        if ($coupon->type === 'percent') {
            $discountAmount = ($subtotal * $coupon->value) / 100;
            if ($coupon->max_discount_amount) {
                $discountAmount = min($discountAmount, (float) $coupon->max_discount_amount);
            }
        } elseif ($coupon->type === 'fixed_amount') {
            $discountAmount = min($subtotal, (float) $coupon->value);
        } elseif ($coupon->type === 'free_shipping') {
            $discountAmount = 30000;
        }

        session([
            'applied_coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount' => $discountAmount,
            ],
        ]);

        $msg = 'Áp dụng mã giảm giá '.$coupon->code.' thành công!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'discount' => $discountAmount,
                'formatted_discount' => number_format($discountAmount).'đ',
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Hủy bỏ mã giảm giá.
     */
    public function removeCoupon(Request $request): RedirectResponse|JsonResponse
    {
        session()->forget('applied_coupon');

        $msg = 'Đã hủy áp dụng mã giảm giá!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Xử lý Đặt Hàng.
     */
    public function processCheckout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user && ! $user->isPhoneVerified()) {
            return redirect()->route('otp.show')->with('error', 'Vui lòng xác thực số điện thoại của bạn bằng mã OTP trước khi đặt hàng!');
        }

        $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'payment_method' => ['required', 'in:COD,bank_transfer,momo'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'recipient_name.required' => 'Vui lòng nhập họ tên người nhận hàng.',
            'recipient_phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng cụ thể.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Giỏ hàng đang trống!');
        }

        try {
            $order = DB::transaction(function () use ($request, $cart) {
                // 1. Kiểm tra sản phẩm active và tính subtotal thực tế
                $subtotal = 0;
                $validatedItems = [];

                foreach ($cart->items as $cartItem) {
                    $product = Product::find($cartItem->product_id);
                    if (! $product || ! $product->is_active) {
                        throw new \Exception("Sản phẩm '{$cartItem->product?->name}' hiện đã ngưng kinh doanh!");
                    }

                    $variant = $cartItem->product_variant_id
                        ? ProductVariant::lockForUpdate()->find($cartItem->product_variant_id)
                        : null;

                    if ($cartItem->product_variant_id && (! $variant || ! $variant->is_active || $variant->product_id !== $product->id)) {
                        throw new \Exception("Biến thể của sản phẩm '{$product->name}' không còn khả dụng!");
                    }

                    $itemRealtimePrice = CartService::getRealtimePrice($product, $variant);
                    $itemSubtotal = $itemRealtimePrice * $cartItem->quantity;
                    $subtotal += $itemSubtotal;

                    $validatedItems[] = [
                        'cart_item' => $cartItem,
                        'product' => $product,
                        'variant' => $variant,
                        'price' => $itemRealtimePrice,
                        'total_price' => $itemSubtotal,
                    ];
                }

                // 2. Xử lý Coupon giảm giá
                $couponId = null;
                $discountAmount = 0;
                $appliedCoupon = session('applied_coupon');

                if ($appliedCoupon) {
                    $coupon = Coupon::lockForUpdate()->find($appliedCoupon['id'] ?? 0);
                    if ($coupon && $coupon->is_active && $coupon->start_date <= now() && $coupon->end_date >= now() && (! $coupon->usage_limit || $coupon->used_count < $coupon->usage_limit)) {
                        $userUsedCount = 0;
                        if ($coupon->user_usage_limit && Auth::check()) {
                            $userUsedCount = Order::where('user_id', Auth::id())
                                ->where('coupon_id', $coupon->id)
                                ->where('status', '!=', 'cancelled')
                                ->count();
                        }

                        if ($subtotal >= $coupon->min_order_value && (! $coupon->user_usage_limit || $userUsedCount < $coupon->user_usage_limit)) {
                            $couponId = $coupon->id;
                            if ($coupon->type === 'percent') {
                                $discountAmount = ($subtotal * $coupon->value) / 100;
                                if ($coupon->max_discount_amount) {
                                    $discountAmount = min($discountAmount, (float) $coupon->max_discount_amount);
                                }
                            } elseif ($coupon->type === 'fixed_amount') {
                                $discountAmount = min($subtotal, (float) $coupon->value);
                            } elseif ($coupon->type === 'free_shipping') {
                                $discountAmount = 30000;
                            }
                            $coupon->increment('used_count');
                        }
                    }
                    session()->forget('applied_coupon');
                }

                $isFreeShippingCoupon = ($couponId && isset($coupon) && $coupon->type === 'free_shipping');
                $shippingFee = ($subtotal >= 500000 || $isFreeShippingCoupon) ? 0 : 30000;
                $totalAmount = max(0, $subtotal + $shippingFee - $discountAmount);

                $orderCode = 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(5));

                // 3. Tạo đơn hàng chính
                $order = Order::create([
                    'order_code' => $orderCode,
                    'user_id' => Auth::id(),
                    'coupon_id' => $couponId,
                    'recipient_name' => $request->recipient_name,
                    'recipient_phone' => $request->recipient_phone,
                    'shipping_address' => $request->shipping_address,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'shipping_fee' => $shippingFee,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'note' => $request->note,
                ]);

                // 4. Tạo thông tin thanh toán (Payment)
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $totalAmount,
                    'payment_status' => 'pending',
                ]);

                // 5. Tạo chi tiết từng món hàng trong đơn & Trừ tồn kho & Ghi log StockMovement
                foreach ($validatedItems as $vItem) {
                    $cartItem = $vItem['cart_item'];
                    $product = $vItem['product'];
                    $variant = $vItem['variant'];
                    $itemPrice = $vItem['price'];
                    $itemTotalPrice = $vItem['total_price'];

                    if ($variant) {
                        if ($variant->stock_quantity < $cartItem->quantity) {
                            throw new \Exception("Sản phẩm '{$product->name}' (SKU: {$variant->sku}) không đủ số lượng tồn kho (Còn {$variant->stock_quantity} món)!");
                        }

                        $beforeQty = $variant->stock_quantity;
                        $variant->decrement('stock_quantity', $cartItem->quantity);

                        StockMovement::create([
                            'product_variant_id' => $variant->id,
                            'user_id' => Auth::id(),
                            'type' => 'order_sale',
                            'quantity_change' => -$cartItem->quantity,
                            'before_quantity' => $beforeQty,
                            'after_quantity' => $variant->stock_quantity,
                            'reference_code' => $orderCode,
                            'note' => 'Bán hàng cho Đơn hàng #'.$orderCode,
                        ]);
                    }

                    // 6. Nếu có đợt Flash Sale đang active cho sản phẩm này, tăng sold_quantity
                    $activeFlashSale = FlashSale::active()->where('product_id', $product->id)->first();
                    if ($activeFlashSale) {
                        $activeFlashSale->increment('sold_quantity', $cartItem->quantity);
                    }

                    $variantInfo = 'Mặc định';
                    if ($variant) {
                        $colorName = $variant->color ? $variant->color->name : '';
                        $sizeName = $variant->size ? 'Size: '.$variant->size->name : '';
                        $variantInfo = trim("{$colorName} {$sizeName}");
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_variant_id' => $cartItem->product_variant_id,
                        'product_name' => $product->name,
                        'variant_info' => $variantInfo ?: 'Mặc định',
                        'price' => $itemPrice,
                        'quantity' => $cartItem->quantity,
                        'total_price' => $itemTotalPrice,
                    ]);
                }

                // 5. Làm trống giỏ hàng
                $cart->items()->delete();

                // 6. Ghi nhận lịch sử trạng thái
                $order->logStatusHistory('pending', 'Đơn hàng đã được khởi tạo thành công trên hệ thống', 'system');

                return $order;
            });

            // Gửi Email xác nhận đặt hàng thành công tới Khách hàng
            try {
                if ($user && $user->email) {
                    Mail::to($user->email)->send(new OrderPlacedMail($order));
                }
            } catch (\Exception $mailEx) {
                Log::error("Failed to send OrderPlacedMail: {$mailEx->getMessage()}");
            }

            // Gửi thông báo tức thì về Telegram Bot cho Quản trị viên
            try {
                TelegramNotificationService::notifyNewOrder($order);
            } catch (\Exception $tgEx) {
                Log::error("Failed to send Telegram notify: {$tgEx->getMessage()}");
            }

            return redirect()->route('orders.show', $order->order_code)->with('success', 'Đặt hàng thành công! Mã đơn hàng của bạn là #'.$order->order_code);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Lịch sử đơn hàng của người dùng (Hỗ trợ bộ lọc Tab trạng thái & Eager Load).
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $query = Order::with(['items.product.primaryImage', 'items.productVariant.color', 'items.productVariant.size', 'returns', 'statusHistories'])
            ->where('user_id', $userId)
            ->latest();

        // Lọc theo trạng thái đơn hàng nếu có
        $currentStatus = $request->get('status', 'all');
        if ($currentStatus !== 'all' && in_array($currentStatus, ['pending', 'confirmed', 'processing', 'shipping', 'completed', 'cancelled', 'returned'])) {
            $query->where('status', $currentStatus);
        }

        $orders = $query->paginate(8)->withQueryString();

        // Đếm số lượng đơn theo từng trạng thái phục vụ hiển thị trên các Tab
        $statusCountsRaw = Order::where('user_id', $userId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $counts = [
            'all' => $statusCountsRaw->sum(),
            'pending' => $statusCountsRaw->get('pending', 0),
            'confirmed' => $statusCountsRaw->get('confirmed', 0),
            'processing' => $statusCountsRaw->get('processing', 0),
            'shipping' => $statusCountsRaw->get('shipping', 0),
            'completed' => $statusCountsRaw->get('completed', 0),
            'cancelled' => $statusCountsRaw->get('cancelled', 0),
            'returned' => $statusCountsRaw->get('returned', 0),
        ];

        return view('orders.index', compact('orders', 'currentStatus', 'counts'));
    }

    /**
     * Chi tiết đơn hàng.
     */
    public function show(string $order_code): View
    {
        $order = Order::with([
            'items.product.primaryImage',
            'items.productVariant.color',
            'items.productVariant.size',
            'statusHistories',
            'coupon',
            'payment',
            'returns.processor',
        ])
            ->where('order_code', $order_code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    /**
     * Hiển thị Hóa đơn & Phiếu giao hàng phía Khách Hàng.
     */
    public function invoice(string $order_code): View
    {
        $order = Order::with([
            'items.product.primaryImage',
            'items.productVariant.color',
            'items.productVariant.size',
            'coupon',
            'payment',
            'user',
        ])
            ->where('order_code', $order_code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('orders.invoice', compact('order'));
    }

    /**
     * Mua lại đơn hàng cũ (Re-order): Thêm các món trong đơn vào Giỏ hàng hiện tại.
     */
    public function reorder(Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'items.productVariant']);
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $addedCount = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->is_active) {
                continue;
            }

            $variant = $item->productVariant;
            if ($item->product_variant_id && (! $variant || ! $variant->is_active || $variant->stock_quantity <= 0)) {
                continue;
            }

            $cartItem = $cart->items()
                ->where('product_id', $product->id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            $qtyToAdd = $item->quantity;
            if ($variant) {
                $qtyToAdd = min($qtyToAdd, $variant->stock_quantity);
            }

            if ($qtyToAdd <= 0) {
                continue;
            }

            $realtimePrice = CartService::getRealtimePrice($product, $variant);

            if ($cartItem) {
                $cartItem->increment('quantity', $qtyToAdd);
                $cartItem->update(['price' => $realtimePrice]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $qtyToAdd,
                    'price' => $realtimePrice,
                ]);
            }
            $addedCount++;
        }

        if ($addedCount > 0) {
            return redirect()->route('cart.index')->with('success', 'Đã thêm thành công các sản phẩm từ đơn hàng #'.$order->order_code.' vào giỏ hàng của bạn!');
        }

        return back()->with('error', 'Các sản phẩm trong đơn hàng này hiện đã hết hàng hoặc ngưng kinh doanh!');
    }

    /**
     * Hủy đơn hàng (Khi đơn hàng ở trạng thái Chờ xử lý - pending).
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'cancel_note' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = trim((string) $request->input('reason', 'Đổi ý, không muốn mua nữa'));
        $cancelNote = trim((string) $request->input('cancel_note', ''));
        $fullNote = 'Khách hàng hủy đơn: '.$reason.($cancelNote !== '' ? ' (Ghi chú: '.$cancelNote.')' : '');

        try {
            DB::transaction(function () use ($order, $fullNote) {
                // Khóa bản ghi đơn hàng chống Race Condition khi gửi nhiều request đồng thời
                $lockedOrder = Order::lockForUpdate()->find($order->id);

                if (! $lockedOrder || $lockedOrder->status !== 'pending') {
                    throw new \Exception('Chỉ có thể hủy đơn hàng khi ở trạng thái Chờ xử lý!');
                }

                $lockedOrder->update(['status' => 'cancelled']);

                // Đồng bộ trạng thái thanh toán
                if ($lockedOrder->payment) {
                    $lockedOrder->payment->update(['payment_status' => 'cancelled']);
                }

                // Hoàn lại lượt sử dụng Coupon
                if ($lockedOrder->coupon_id && $lockedOrder->coupon) {
                    $coupon = Coupon::lockForUpdate()->find($lockedOrder->coupon_id);
                    if ($coupon && $coupon->used_count > 0) {
                        $coupon->decrement('used_count');
                    }
                }

                // Hoàn lại tồn kho & Trừ lại sold_quantity của Flash Sale
                foreach ($lockedOrder->items as $item) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                        if ($variant) {
                            $beforeQty = $variant->stock_quantity;
                            $variant->increment('stock_quantity', $item->quantity);

                            StockMovement::create([
                                'product_variant_id' => $variant->id,
                                'user_id' => Auth::id(),
                                'type' => 'order_cancel',
                                'quantity_change' => $item->quantity,
                                'before_quantity' => $beforeQty,
                                'after_quantity' => $variant->stock_quantity,
                                'reference_code' => $lockedOrder->order_code,
                                'note' => 'Khách hàng hủy Đơn hàng #'.$lockedOrder->order_code.': '.$fullNote,
                            ]);
                        }
                    }

                    // Giảm sold_quantity của đợt Flash Sale tương ứng nếu có
                    if ($item->product_id) {
                        $flashSale = FlashSale::where('product_id', $item->product_id)
                            ->where('sold_quantity', '>', 0)
                            ->first();

                        if ($flashSale) {
                            $deductQty = min($flashSale->sold_quantity, $item->quantity);
                            $flashSale->decrement('sold_quantity', $deductQty);
                        }
                    }
                }

                // Ghi nhận lịch sử hủy đơn kèm lý do rõ ràng
                $user = Auth::user();
                $lockedOrder->logStatusHistory('cancelled', $fullNote, 'user', Auth::id(), $user ? $user->name : 'Khách hàng');
            });

            return back()->with('success', 'Đã hủy đơn hàng #'.$order->order_code.' thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Gửi yêu cầu Đổi / Trả hàng (Khi đơn hàng đã hoàn thành trong vòng 7 ngày).
     */
    public function requestReturn(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['statusHistories', 'returns']);

        if (! $order->canBeReturned()) {
            return back()->with('error', 'Đơn hàng không đủ điều kiện gửi yêu cầu đổi/trả (chỉ áp dụng cho đơn hoàn thành trong 7 ngày và chưa có yêu cầu đang xử lý)!');
        }

        $request->validate([
            'type' => ['required', 'in:exchange,return'],
            'reason' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'bank_name' => ['required_if:type,return', 'nullable', 'string', 'max:100'],
            'bank_account_number' => ['required_if:type,return', 'nullable', 'string', 'max:50'],
            'bank_account_holder' => ['required_if:type,return', 'nullable', 'string', 'max:100'],
        ], [
            'type.required' => 'Vui lòng chọn hình thức: Đổi hàng hoặc Trả hàng hoàn tiền.',
            'type.in' => 'Hình thức yêu cầu không hợp lệ.',
            'reason.required' => 'Vui lòng chọn lý do đổi/trả hàng.',
            'images.max' => 'Bạn chỉ được tải lên tối đa 3 hình ảnh.',
            'images.*.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, webp.',
            'images.*.max' => 'Mỗi hình ảnh không được vượt quá 3MB.',
            'bank_name.required_if' => 'Vui lòng nhập tên ngân hàng để nhận lại tiền hoàn.',
            'bank_account_number.required_if' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account_holder.required_if' => 'Vui lòng nhập họ tên chủ tài khoản ngân hàng.',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('returns', 'public');
                $imagePaths[] = $path;
            }
        }

        $typeLabel = $request->type === 'exchange' ? 'Đổi size / màu' : 'Trả hàng & Hoàn tiền';

        $return = OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'reason' => $request->reason,
            'note' => $request->note,
            'images' => ! empty($imagePaths) ? $imagePaths : null,
            'bank_name' => $request->type === 'return' ? $request->bank_name : null,
            'bank_account_number' => $request->type === 'return' ? $request->bank_account_number : null,
            'bank_account_holder' => $request->type === 'return' ? $request->bank_account_holder : null,
            'status' => 'pending',
        ]);

        // Ghi nhật ký vào status histories
        $user = Auth::user();
        $order->logStatusHistory(
            'returned',
            "Khách gửi yêu cầu [{$typeLabel}]: {$request->reason}".($request->note ? ' (Ghi chú: '.$request->note.')' : ''),
            'user',
            Auth::id(),
            $user ? $user->name : 'Khách hàng'
        );

        // Gửi thông báo tới Telegram Bot của Quản trị viên
        try {
            TelegramNotificationService::notifyReturnRequest($return);
        } catch (\Exception $tgEx) {
            Log::error("Failed to send Telegram return notify: {$tgEx->getMessage()}");
        }

        return back()->with('success', 'Yêu cầu '.$typeLabel.' cho đơn hàng #'.$order->order_code.' đã được gửi thành công! Shop sẽ kiểm tra và phản hồi sớm nhất.');
    }
}
