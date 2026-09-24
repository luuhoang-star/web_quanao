<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Size;
use App\Models\User;

beforeEach(function () {
    $userRole = Role::firstOrCreate(['id' => 2], ['name' => 'user', 'description' => 'Khách hàng']);

    $this->user = User::firstOrCreate(
        ['email' => 'user@example.com'],
        [
            'role_id' => $userRole->id,
            'name' => 'Nguyễn Văn A',
            'phone' => '0987654321',
            'password' => bcrypt('123456'),
            'status' => 'active',
            'phone_verified_at' => now(),
        ]
    );

    $this->category = Category::firstOrCreate(
        ['slug' => 'test-category'],
        ['name' => 'Danh mục Test', 'is_active' => true]
    );

    $this->product = Product::firstOrCreate(
        ['slug' => 'test-product'],
        [
            'category_id' => $this->category->id,
            'name' => 'Sản phẩm Test',
            'sku' => 'SKU-TEST-01',
            'price' => 250000,
            'is_active' => true,
        ]
    );
});

test('truy cập thành công Trang chủ Cửa hàng', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('ATINO');
    $response->assertSee('TOP 5 SẢN PHẨM BÁN CHẠY NHẤT');
});

test('truy cập thành công Trang Cửa hàng Shop', function () {
    $response = $this->get('/shop');
    $response->assertStatus(200);
});

test('truy cập thành công Trang Flash Sale', function () {
    $response = $this->get('/flash-sale');
    $response->assertStatus(200);
});

test('truy cập thành công Trang Chi tiết sản phẩm', function () {
    $response = $this->get('/product/'.$this->product->slug);
    $response->assertStatus(200);
    $response->assertSee($this->product->name);
});

test('truy cập thành công Trang Giỏ hàng', function () {
    $response = $this->get('/cart');
    $response->assertStatus(200);
});

test('truy cập thành công Trang Đăng nhập Khách hàng', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('truy cập thành công Trang Đăng ký Khách hàng', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

test('thành viên truy cập thành công Trang Hồ sơ cá nhân', function () {
    $response = $this->actingAs($this->user, 'web')->get('/profile');
    $response->assertStatus(200);
});

test('thành viên truy cập thành công Trang Lịch sử Đơn hàng', function () {
    $response = $this->actingAs($this->user, 'web')->get('/orders');
    $response->assertStatus(200);
});

test('thành viên truy cập thành công Trang Sản phẩm Yêu thích (Wishlist)', function () {
    $response = $this->actingAs($this->user, 'web')->get('/wishlist');
    $response->assertStatus(200);
});

test('thành viên truy cập thành công Trang Thanh toán (Checkout)', function () {
    $cart = Cart::firstOrCreate(['user_id' => $this->user->id]);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $this->product->id,
        'quantity' => 1,
        'price' => $this->product->price,
    ]);

    $response = $this->actingAs($this->user, 'web')->get('/checkout');
    $response->assertStatus(200);
});

test('API Live Search trả về kết quả gợi ý sản phẩm chính xác', function () {
    $response = $this->getJson('/shop/search-suggestions?q=Test');
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});

test('API Quick View trả về thông tin chi tiết sản phẩm', function () {
    $response = $this->getJson('/shop/quick-view/'.$this->product->id);
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
            ],
        ]);
});

test('trang chi tiết sản phẩm hiển thị công cụ tư vấn chọn size thông minh ATINO Smart Fit Finder', function () {
    $size = Size::firstOrCreate(['name' => 'L'], ['code' => 'L', 'size_type' => 'TOPS']);
    $color = Color::firstOrCreate(['name' => 'Đen'], ['hex_code' => '#000000']);
    ProductVariant::create([
        'product_id' => $this->product->id,
        'size_id' => $size->id,
        'color_id' => $color->id,
        'sku' => 'SKU-TEST-L-BLACK',
        'price' => 250000,
        'stock_quantity' => 10,
        'is_active' => true,
    ]);

    $response = $this->get('/product/'.$this->product->slug);
    $response->assertStatus(200);
    $response->assertSee('ATINO SMART FIT FINDER');
    $response->assertSee('Tư vấn chọn size thông minh');
});

test('khách hàng đăng nhập có thể xem và in hóa đơn đơn hàng của chính mình', function () {
    $order = Order::create([
        'order_code' => 'ORD-TEST-INV-01',
        'user_id' => $this->user->id,
        'recipient_name' => 'Nguyễn Văn A',
        'recipient_phone' => '0987654321',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 250000,
        'discount_amount' => 0,
        'shipping_fee' => 30000,
        'total_amount' => 280000,
        'status' => 'confirmed',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'variant_info' => 'Màu: Đen, Size: L',
        'quantity' => 1,
        'price' => 250000,
        'total_price' => 250000,
    ]);

    $response = $this->actingAs($this->user, 'web')->get(route('orders.invoice', $order->order_code));
    $response->assertStatus(200);
    $response->assertSee($order->order_code);
    $response->assertSee('HÓA ĐƠN BÁN HÀNG');
    $response->assertSee('ATINO');
});

test('khách hàng không thể xem trái phép hóa đơn của người khác', function () {
    $otherUser = User::create([
        'role_id' => $this->user->role_id,
        'name' => 'Khách hàng khác',
        'email' => 'other_user@example.com',
        'phone' => '0912345678',
        'password' => bcrypt('123456'),
        'status' => 'active',
    ]);

    $otherOrder = Order::create([
        'order_code' => 'ORD-OTHER-01',
        'user_id' => $otherUser->id,
        'recipient_name' => 'Người khác',
        'recipient_phone' => '0912345678',
        'shipping_address' => 'TP.HCM',
        'subtotal' => 500000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
        'total_amount' => 500000,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->user, 'web')->get(route('orders.invoice', $otherOrder->order_code));
    $response->assertStatus(404);
});

test('khách hàng có thể mua lại đơn hàng cũ thành công mà không bị lỗi thiếu price trong giỏ hàng', function () {
    $order = Order::create([
        'order_code' => 'ORD-REORDER-01',
        'user_id' => $this->user->id,
        'recipient_name' => 'Nguyễn Văn A',
        'recipient_phone' => '0987654321',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 250000,
        'discount_amount' => 0,
        'shipping_fee' => 30000,
        'total_amount' => 280000,
        'status' => 'completed',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'variant_info' => 'Mặc định',
        'quantity' => 2,
        'price' => 250000,
        'total_price' => 500000,
    ]);

    $response = $this->actingAs($this->user, 'web')->post(route('orders.reorder', $order->id));
    $response->assertRedirect(route('cart.index'));
    $response->assertSessionHas('success');

    $cart = Cart::where('user_id', $this->user->id)->first();
    expect($cart)->not->toBeNull();
    $cartItem = $cart->items()->where('product_id', $this->product->id)->first();
    expect($cartItem)->not->toBeNull();
    expect((float) $cartItem->price)->toBe(250000.0);
    expect($cartItem->quantity)->toBe(2);
});
