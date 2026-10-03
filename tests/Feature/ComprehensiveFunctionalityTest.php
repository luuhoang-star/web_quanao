<?php

use App\Models\Cart;
use App\Models\Category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Role;
use App\Models\Size;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->userRole = Role::firstOrCreate(['name' => 'user'], ['description' => 'Khách hàng']);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Quản trị viên']);

    $this->user = User::create([
        'role_id' => $this->userRole->id,
        'name' => 'Nguyễn Văn Test',
        'email' => 'nguyenvantest@example.com',
        'phone' => '0988776655',
        'password' => bcrypt('password123'),
        'status' => 'active',
        'phone_verified_at' => now(),
    ]);

    $this->admin = User::create([
        'role_id' => $this->adminRole->id,
        'name' => 'Quản Trị Viên Test',
        'email' => 'admintest@example.com',
        'phone' => '0901234888',
        'password' => bcrypt('admin123456'),
        'status' => 'active',
    ]);

    $this->category = Category::create([
        'name' => 'Áo Nam Test',
        'slug' => 'ao-nam-test',
        'is_active' => true,
    ]);

    $this->product = Product::create([
        'category_id' => $this->category->id,
        'name' => 'Áo Polo Thể Thao Nam Test',
        'slug' => 'ao-polo-the-thao-nam-test',
        'sku' => 'POLO-TEST-001',
        'price' => 200000,
        'is_active' => true,
    ]);

    $this->color = Color::create(['name' => 'Xanh Navy', 'code' => '#000080', 'is_active' => true]);
    $this->size = Size::create(['name' => 'XL', 'code' => 'XL', 'size_type' => 'TOPS', 'is_active' => true]);

    $this->variant = ProductVariant::create([
        'product_id' => $this->product->id,
        'color_id' => $this->color->id,
        'size_id' => $this->size->id,
        'sku' => 'POLO-TEST-001-XL-NAVY',
        'price' => 220000,
        'stock_quantity' => 50,
        'low_stock_threshold' => 5,
        'is_active' => true,
    ]);

    // Helper tạo nhanh giỏ hàng & sản phẩm kèm giá tuỳ biến
    $this->createCart = function (int $quantity = 1, ?float $price = null) {
        $cart = Cart::firstOrCreate(['user_id' => $this->user->id]);
        $item = $cart->items()->create([
            'product_id' => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'quantity' => $quantity,
            'price' => $price ?? $this->variant->price,
        ]);

        return [$cart, $item];
    };
});

// ==========================================
// 1. AUTHENTICATION & PROFILE
// ==========================================
test('người dùng có thể đăng ký tài khoản mới thành công', function () {
    $this->post('/register', [
        'name' => 'Trần Thị B',
        'email' => 'tranthib@example.com',
        'phone' => '0977112233',
        'password' => 'matkhau123',
        'password_confirmation' => 'matkhau123',
    ])->assertRedirect(route('otp.show'));

    $this->assertDatabaseHas('users', [
        'email' => 'tranthib@example.com',
        'phone' => '0977112233',
    ]);
});

test('người dùng có thể đăng nhập tài khoản bằng email và mật khẩu', function () {
    $this->post('/login', [
        'email' => $this->user->email,
        'password' => 'password123',
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($this->user, 'web');
});

test('người dùng bị từ chối đăng nhập nếu sai mật khẩu', function () {
    $this->post('/login', [
        'email' => $this->user->email,
        'password' => 'saimatkhau',
    ])->assertSessionHas('error');

    $this->assertGuest('web');
});

test('người dùng có thể cập nhật thông tin cá nhân', function () {
    $this->actingAs($this->user, 'web')->put('/profile', [
        'name' => 'Nguyễn Văn Đã Cập Nhật',
        'email' => $this->user->email,
        'phone' => '0988776655',
        'gender' => 'male',
        'birthday' => '1995-05-15',
    ])->assertSessionHas('success');

    expect($this->user->fresh()->name)->toBe('Nguyễn Văn Đã Cập Nhật');
});

test('người dùng có thể đổi mật khẩu tài khoản thành công', function () {
    $this->actingAs($this->user, 'web')->put('/profile/password', [
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertSessionHas('success');

    expect(Hash::check('newpassword123', $this->user->fresh()->password))->toBeTrue();
});

test('người dùng có thể đăng xuất khỏi hệ thống', function () {
    $this->actingAs($this->user, 'web')->post('/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest('web');
});

// ==========================================
// 2. GIỎ HÀNG (CART)
// ==========================================
test('người dùng đăng nhập có thể thêm sản phẩm có biến thể vào giỏ hàng', function () {
    $this->actingAs($this->user, 'web')->post(route('cart.store'), [
        'product_id' => $this->product->id,
        'product_variant_id' => $this->variant->id,
        'quantity' => 2,
    ])->assertSessionHas('success');

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $this->product->id,
        'product_variant_id' => $this->variant->id,
        'quantity' => 2,
    ]);
});

test('người dùng có thể cập nhật số lượng sản phẩm trong giỏ hàng', function () {
    [, $item] = ($this->createCart)(1);

    $this->actingAs($this->user, 'web')->put(route('cart.update', $item->id), [
        'quantity' => 4,
    ])->assertRedirect();

    expect($item->fresh()->quantity)->toBe(4);
});

test('người dùng có thể xóa một món và xóa toàn bộ giỏ hàng', function () {
    [$cart, $item] = ($this->createCart)(1);

    $this->actingAs($this->user, 'web')->delete(route('cart.destroy', $item->id))
        ->assertSessionHas('success');
    expect($cart->fresh()->items()->count())->toBe(0);

    ($this->createCart)(2);
    $this->actingAs($this->user, 'web')->delete(route('cart.clear'))
        ->assertSessionHas('success');
    expect($cart->fresh()->items()->count())->toBe(0);
});

// ==========================================
// 3. MÃ GIẢM GIÁ (COUPON)
// ==========================================
test('người dùng có thể áp dụng mã giảm giá phần trăm hợp lệ', function () {
    Coupon::create([
        'code' => 'DISCOUNT10',
        'type' => 'percent',
        'value' => 10,
        'min_order_value' => 100000,
        'max_discount_amount' => 50000,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(5),
        'is_active' => true,
    ]);

    ($this->createCart)(1, 220000);

    $this->actingAs($this->user, 'web')->post(route('coupon.apply'), [
        'coupon_code' => 'DISCOUNT10',
    ])->assertSessionHas('success');

    expect(session('applied_coupon.code'))->toBe('DISCOUNT10');
    expect((float) session('applied_coupon.discount'))->toBe(22000.0);
});

test('người dùng không thể áp dụng mã giảm giá khi đơn hàng chưa đạt giá trị tối thiểu', function () {
    Coupon::create([
        'code' => 'BIGSALE',
        'type' => 'fixed_amount',
        'value' => 50000,
        'min_order_value' => 500000,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(5),
        'is_active' => true,
    ]);

    ($this->createCart)(1, 220000);

    $this->actingAs($this->user, 'web')->post(route('coupon.apply'), [
        'coupon_code' => 'BIGSALE',
    ])->assertSessionHas('error');

    expect(session('applied_coupon'))->toBeNull();
});

test('người dùng có thể hủy áp dụng mã giảm giá', function () {
    session(['applied_coupon' => ['id' => 1, 'code' => 'TEST', 'discount' => 10000]]);

    $this->actingAs($this->user, 'web')->post(route('coupon.remove'))
        ->assertSessionHas('success');

    expect(session('applied_coupon'))->toBeNull();
});

// ==========================================
// 4. QUY TRÌNH ĐẶT HÀNG & TRỪ TỒN KHO (CHECKOUT)
// ==========================================
test('người dùng đặt hàng COD thành công, trừ tồn kho và ghi nhận lịch sử kho', function () {
    [$cart] = ($this->createCart)(3, 220000);
    $initialStock = $this->variant->stock_quantity;

    $this->actingAs($this->user, 'web')->post(route('orders.processCheckout'), [
        'recipient_name' => 'Nguyễn Văn Nhận Hàng',
        'recipient_phone' => '0988112233',
        'shipping_address' => 'Số 123 Đường Cầu Giấy, Hà Nội',
        'payment_method' => 'COD',
        'note' => 'Giao hàng giờ hành chính',
    ])->assertSessionHas('success')->assertRedirect();

    expect($cart->fresh()->items()->count())->toBe(0);
    expect($this->variant->fresh()->stock_quantity)->toBe($initialStock - 3);

    $order = Order::where('user_id', $this->user->id)->latest()->first();
    expect($order)->not->toBeNull();
    expect($order->status)->toBe('pending');
    expect((float) $order->subtotal)->toBe(660000.0);
    expect((float) $order->shipping_fee)->toBe(0.0);
    expect((float) $order->total_amount)->toBe(660000.0);
    expect($order->payment?->payment_method)->toBe('COD');
    expect($order->payment?->payment_status)->toBe('pending');
    expect($order->items()->count())->toBe(1);

    $this->assertDatabaseHas('stock_movements', [
        'product_variant_id' => $this->variant->id,
        'type' => 'order_sale',
        'quantity_change' => -3,
        'after_quantity' => $initialStock - 3,
    ]);
});

test('người dùng không thể đặt hàng nếu số lượng mua vượt quá tồn kho', function () {
    $this->variant->update(['stock_quantity' => 2]);
    ($this->createCart)(5, 220000);

    $this->actingAs($this->user, 'web')->post(route('orders.processCheckout'), [
        'recipient_name' => 'Nguyễn Văn Nhận',
        'recipient_phone' => '0988112233',
        'shipping_address' => 'Số 123 Đường Cầu Giấy, Hà Nội',
        'payment_method' => 'COD',
    ])->assertSessionHas('error');

    expect(Order::count())->toBe(0);
    expect($this->variant->fresh()->stock_quantity)->toBe(2);
});

// ==========================================
// 5. TRA CỨU ĐƠN HÀNG VÃNG LAI
// ==========================================
test('khách vãng lai có thể tra cứu đơn hàng bằng mã đơn và số điện thoại', function () {
    Order::create([
        'order_code' => 'ORD-20260926-TEST1',
        'user_id' => $this->user->id,
        'recipient_name' => 'Khách Tra Cứu',
        'recipient_phone' => '0912349999',
        'shipping_address' => 'Đà Nẵng',
        'subtotal' => 220000,
        'total_amount' => 250000,
        'status' => 'pending',
    ]);

    $this->get('/tra-cuu-don-hang?search=ORD-20260926-TEST1')
        ->assertOk()
        ->assertSee('ORD-20260926-TEST1')
        ->assertSee('Khách Tra Cứu');
});

// ==========================================
// 6. WEBHOOK THANH TOÁN TỰ ĐỘNG (SEPAY / CASSO)
// ==========================================
test('webhook thanh toán tự động xác nhận đơn hàng và cập nhật trạng thái đã thanh toán', function () {
    $order = Order::create([
        'order_code' => 'ORD-20260926-AUTO1',
        'user_id' => $this->user->id,
        'recipient_name' => 'Khách CK',
        'recipient_phone' => '0912349999',
        'shipping_address' => 'Đà Nẵng',
        'subtotal' => 300000,
        'total_amount' => 300000,
        'status' => 'pending',
    ]);

    Payment::create([
        'order_id' => $order->id,
        'payment_method' => 'bank_transfer',
        'amount' => 300000,
        'payment_status' => 'pending',
    ]);

    $this->postJson(route('api.webhook.payment'), [
        'content' => 'Thanh toan don hang ORD-20260926-AUTO1',
        'transferAmount' => 300000,
        'referenceCode' => 'MB_BANK_12345678',
    ])->assertOk()->assertJson(['success' => true]);

    expect($order->fresh()->status)->toBe('confirmed');
    expect($order->fresh()->payment->payment_status)->toBe('paid');
});

// ==========================================
// 7. SỔ ĐỊA CHỈ KHÁCH HÀNG (USER ADDRESSES)
// ==========================================
test('người dùng có thể thêm, sửa, đặt mặc định và xóa địa chỉ nhận hàng', function () {
    $this->actingAs($this->user, 'web')->post(route('user.addresses.store'), [
        'recipient_name' => 'Nguyễn Văn A',
        'recipient_phone' => '0988776655',
        'province_city' => 'Hà Nội',
        'district' => 'Cầu Giấy',
        'ward' => 'Dịch Vọng',
        'street_address' => 'Số 10 Phạm Văn Bạch',
        'is_default' => 1,
    ])->assertSessionHas('success');

    $address = UserAddress::where('user_id', $this->user->id)->first();
    expect($address->street_address)->toBe('Số 10 Phạm Văn Bạch');
    expect($address->is_default)->toBeTrue();

    $this->actingAs($this->user, 'web')->put(route('user.addresses.update', $address->id), [
        'recipient_name' => 'Nguyễn Văn A Đã Đổi',
        'recipient_phone' => '0988776655',
        'province_city' => 'Hà Nội',
        'district' => 'Cầu Giấy',
        'ward' => 'Dịch Vọng',
        'street_address' => 'Số 20 Phạm Văn Bạch',
        'is_default' => 1,
    ])->assertSessionHas('success');

    expect($address->fresh()->street_address)->toBe('Số 20 Phạm Văn Bạch');

    $this->actingAs($this->user, 'web')->delete(route('user.addresses.destroy', $address->id))
        ->assertSessionHas('success');
    expect(UserAddress::where('id', $address->id)->count())->toBe(0);
});

// ==========================================
// 8. SẢN PHẨM YÊU THÍCH (WISHLIST)
// ==========================================
test('người dùng có thể thêm và xóa sản phẩm khỏi danh sách yêu thích', function () {
    $this->actingAs($this->user, 'web')->post(route('wishlist.toggle', $this->product->id))
        ->assertSessionHas('success');
    expect(Wishlist::where('user_id', $this->user->id)->where('product_id', $this->product->id)->exists())->toBeTrue();

    $this->actingAs($this->user, 'web')->post(route('wishlist.toggle', $this->product->id))
        ->assertSessionHas('success');
    expect(Wishlist::where('user_id', $this->user->id)->where('product_id', $this->product->id)->exists())->toBeFalse();
});

// ==========================================
// 9. ĐÁNH GIÁ SẢN PHẨM (REVIEWS)
// ==========================================
test('chỉ khách hàng đã nhận hàng thành công mới được viết đánh giá sản phẩm', function () {
    $this->actingAs($this->user, 'web')->post(route('reviews.store', $this->product->id), [
        'rating' => 5,
        'comment' => 'Sản phẩm quá đẹp, mặc rất vừa vặn và thoải mái!',
    ])->assertSessionHas('error');
    expect(Review::count())->toBe(0);

    $order = Order::create([
        'order_code' => 'ORD-COMPLETED-REV-1',
        'user_id' => $this->user->id,
        'recipient_name' => 'Nguyễn Văn Test',
        'recipient_phone' => '0988776655',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 200000,
        'total_amount' => 230000,
        'status' => 'completed',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'variant_info' => 'XL',
        'price' => 200000,
        'quantity' => 1,
        'total_price' => 200000,
    ]);

    $this->actingAs($this->user, 'web')->post(route('reviews.store', $this->product->id), [
        'rating' => 5,
        'comment' => 'Sản phẩm quá đẹp, vải mát và lên form cực chuẩn!',
    ])->assertSessionHas('success');

    $review = Review::where('product_id', $this->product->id)->first();
    expect($review->rating)->toBe(5);

    $this->actingAs($this->admin, 'admin')->put(route('admin.reviews.reply', $review->id), [
        'admin_reply' => 'Dạ ATINO cảm ơn bạn rất nhiều ạ!',
    ])->assertSessionHas('success');

    expect($review->fresh()->admin_reply)->toBe('Dạ ATINO cảm ơn bạn rất nhiều ạ!');
});

// ==========================================
// 10. QUẢN LÝ KHO HÀNG ADMIN (INVENTORY)
// ==========================================
test('admin có thể nhập kho, xuất kho, điều chỉnh tồn kho và cập nhật ngưỡng cảnh báo', function () {
    $initialStock = $this->variant->stock_quantity;

    $this->actingAs($this->admin, 'admin')->post(route('admin.inventory.import'), [
        'product_variant_id' => $this->variant->id,
        'quantity' => 20,
        'cost_price' => 150000,
        'supplier' => 'Xưởng May Hà Nội',
        'note' => 'Nhập bổ sung đợt 2',
    ])->assertRedirect(route('admin.inventory.index'));
    expect($this->variant->fresh()->stock_quantity)->toBe($initialStock + 20);

    $this->actingAs($this->admin, 'admin')->post(route('admin.inventory.export'), [
        'product_variant_id' => $this->variant->id,
        'quantity' => 5,
        'note' => 'Xuất làm mẫu chụp lookbook',
    ])->assertRedirect(route('admin.inventory.index'));
    expect($this->variant->fresh()->stock_quantity)->toBe($initialStock + 15);

    $this->actingAs($this->admin, 'admin')->post(route('admin.inventory.adjust'), [
        'product_variant_id' => $this->variant->id,
        'actual_quantity' => 60,
        'note' => 'Kiểm kê định kỳ tháng',
    ])->assertRedirect(route('admin.inventory.index'));
    expect($this->variant->fresh()->stock_quantity)->toBe(60);

    $this->actingAs($this->admin, 'admin')->post(route('admin.inventory.threshold', $this->variant->id), [
        'low_stock_threshold' => 12,
    ])->assertSessionHas('success');
    expect($this->variant->fresh()->low_stock_threshold)->toBe(12);
});

// ==========================================
// 11. QUẢN TRỊ SẢN PHẨM ADMIN (PRODUCTS)
// ==========================================
test('admin có thể tạo sản phẩm mới, nhân bản sản phẩm và bật tắt nổi bật', function () {
    $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), [
        'category_id' => $this->category->id,
        'name' => 'Quần Jean Slimfit Nam',
        'slug' => 'quan-jean-slimfit-nam',
        'sku' => 'JEAN-SLIM-01',
        'price' => 350000,
        'is_active' => 1,
    ])->assertRedirect();

    $newProduct = Product::where('sku', 'JEAN-SLIM-01')->first();
    expect($newProduct)->not->toBeNull();

    $this->actingAs($this->admin, 'admin')->put(route('admin.products.toggleFeatured', $newProduct->id))
        ->assertSessionHas('success');
    expect($newProduct->fresh()->is_featured)->toBeTrue();

    $this->actingAs($this->admin, 'admin')->post(route('admin.products.duplicate', $newProduct->id))
        ->assertRedirect(route('admin.products.index'));
    expect(Product::where('name', 'Bản sao - '.$newProduct->name)->exists())->toBeTrue();
});

// ==========================================
// 12. BẢN TIN KHUYẾN MÃI (NEWSLETTER)
// ==========================================
test('khách hàng có thể đăng ký nhận bản tin khuyến mãi thành công', function () {
    $this->postJson(route('newsletter.subscribe'), ['email' => 'nhantin@example.com'])
        ->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('subscribers', ['email' => 'nhantin@example.com']);
});

// ==========================================
// 13. SEO & TRANG CHÍNH SÁCH
// ==========================================
test('các đường dẫn sitemap, robots và chính sách hoạt động chuẩn xác', function () {
    $this->get(route('seo.sitemap'))->assertOk();
    $this->get(route('seo.robots'))->assertOk();
    $this->get(route('pages.return-policy'))->assertOk();
    $this->get(route('pages.buying-guide'))->assertOk();
});
