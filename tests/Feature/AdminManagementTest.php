<?php

use App\Models\Category;
use App\Models\Color;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Size;
use App\Models\User;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(['id' => 1], ['name' => 'admin', 'description' => 'Quản trị viên']);

    $this->admin = User::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'role_id' => $adminRole->id,
            'name' => 'Quản trị viên',
            'phone' => '0901234567',
            'password' => bcrypt('123456'),
            'status' => 'active',
        ]
    );
});

test('khách chưa đăng nhập truy cập trang admin bị chuyển hướng về login', function () {
    $response = $this->get('/admin');
    $response->assertRedirect(route('admin.login'));
});

test('admin đăng nhập truy cập thành công trang Dashboard', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Dashboard');
});

test('admin truy cập thành công trang Quản lý Sản phẩm', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Thêm Sản phẩm mới', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.create'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Danh mục', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.categories.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Thêm Danh mục', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.categories.create'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Màu sắc', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.colors.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Kích cỡ', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.sizes.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Kho hàng', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.inventory.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Lịch sử biến động Kho hàng', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.inventory.movements'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Đơn hàng', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Mã giảm giá', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.coupons.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Đánh giá', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.reviews.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Khách hàng', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.customers.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Hồ sơ cá nhân Admin (Profile)', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.profile.show'));
    $response->assertStatus(200);
    $response->assertSee('Thông Tin Cá Nhân Quản Trị Viên');
});

test('admin truy cập thành công trang Cấu hình hệ thống (Settings)', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.settings.index'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Lookbook', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.lookbooks.index'));
    $response->assertStatus(200);
    $response->assertSee('Lookbook');
});

test('admin truy cập thành công trang Thêm Lookbook mới', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.lookbooks.create'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Outfit Phối Sẵn', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.outfits.index'));
    $response->assertStatus(200);
    $response->assertSee('Outfit');
});

test('admin truy cập thành công trang Thêm Outfit mới', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.outfits.create'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Flash Sale', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.flash-sales.index'));
    $response->assertStatus(200);
    $response->assertSee('Flash Sale');
});

test('admin truy cập thành công trang Thêm Flash Sale mới', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.flash-sales.create'));
    $response->assertStatus(200);
});

test('admin truy cập thành công trang Quản lý Biến thể của sản phẩm', function () {
    $category = Category::firstOrCreate(
        ['slug' => 'test-category-variant'],
        ['name' => 'Danh mục Variant Test', 'is_active' => true]
    );

    $product = Product::firstOrCreate(
        ['slug' => 'test-product-variant'],
        [
            'category_id' => $category->id,
            'name' => 'Sản phẩm Test Biến thể',
            'sku' => 'SKU-VAR-01',
            'price' => 300000,
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.variants.index', $product->id));
    $response->assertStatus(200);
    $response->assertSee($product->name);
});

test('admin truy cập thành công trang In Hóa Đơn & Phiếu Giao Hàng của đơn hàng', function () {
    $userRole = Role::firstOrCreate(['name' => 'user'], ['description' => 'Khách hàng']);
    $customer = User::create([
        'role_id' => $userRole->id,
        'name' => 'Khách mua hàng',
        'email' => 'buyer@example.com',
        'phone' => '0933445566',
        'password' => bcrypt('123456'),
        'status' => 'active',
    ]);

    $order = Order::create([
        'order_code' => 'ORD-ADMIN-INV-01',
        'user_id' => $customer->id,
        'recipient_name' => 'Khách mua hàng',
        'recipient_phone' => '0933445566',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 450000,
        'discount_amount' => 50000,
        'shipping_fee' => 30000,
        'total_amount' => 430000,
        'status' => 'confirmed',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_name' => 'Áo Thun Cao Cấp',
        'variant_info' => 'Màu: Trắng, Size: M',
        'quantity' => 2,
        'price' => 225000,
        'total_price' => 450000,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.invoice', $order->id));
    $response->assertStatus(200);
    $response->assertSee($order->order_code);
    $response->assertSee('HÓA ĐƠN BÁN HÀNG');
    $response->assertSee('ATINO');
});

test('admin có thể click đổi trạng thái biến thể sản phẩm thành công', function () {
    $category = Category::firstOrCreate(['slug' => 'test-cat-var'], ['name' => 'Danh mục test', 'is_active' => true]);
    $product = Product::firstOrCreate(
        ['sku' => 'TEST-VAR-PROD'],
        [
            'category_id' => $category->id,
            'name' => 'Sản phẩm có biến thể test',
            'slug' => 'san-pham-co-bien-the-test',
            'price' => 200000,
            'is_active' => true,
        ]
    );

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'TEST-VAR-PROD-M',
        'price' => 200000,
        'stock_quantity' => 10,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->putJson(route('admin.products.variants.toggleStatus', [$product->id, $variant->id]));
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'is_active' => false,
    ]);

    $this->assertDatabaseHas('product_variants', [
        'id' => $variant->id,
        'is_active' => false,
    ]);
});

test('admin có thể click đổi trạng thái kích cỡ thành công', function () {
    $size = Size::firstOrCreate(['name' => 'Size Test Toggle'], [
        'size_type' => 'OTHER',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->putJson(route('admin.sizes.toggleStatus', $size));
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'is_active' => false,
    ]);

    $this->assertDatabaseHas('sizes', [
        'id' => $size->id,
        'is_active' => false,
    ]);

    // Đổi lại lần 2
    $response2 = $this->actingAs($this->admin, 'admin')->putJson(route('admin.sizes.toggleStatus', $size));
    $response2->assertStatus(200);
    $response2->assertJson([
        'success' => true,
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('sizes', [
        'id' => $size->id,
        'is_active' => true,
    ]);
});

test('admin có thể thực hiện thao tác bật tắt hàng loạt trạng thái kích cỡ', function () {
    $size1 = Size::firstOrCreate(['name' => 'Size Bulk 1'], ['size_type' => 'OTHER', 'is_active' => true]);
    $size2 = Size::firstOrCreate(['name' => 'Size Bulk 2'], ['size_type' => 'OTHER', 'is_active' => true]);

    // Ẩn hàng loạt
    $response = $this->actingAs($this->admin, 'admin')->post(route('admin.sizes.bulkAction'), [
        'action' => 'disable',
        'size_ids' => [$size1->id, $size2->id],
    ]);
    $response->assertRedirect(route('admin.sizes.index'));
    expect($size1->fresh()->is_active)->toBeFalse();
    expect($size2->fresh()->is_active)->toBeFalse();

    // Bật hàng loạt
    $response2 = $this->actingAs($this->admin, 'admin')->post(route('admin.sizes.bulkAction'), [
        'action' => 'enable',
        'size_ids' => [$size1->id, $size2->id],
    ]);
    $response2->assertRedirect(route('admin.sizes.index'));
    expect($size1->fresh()->is_active)->toBeTrue();
    expect($size2->fresh()->is_active)->toBeTrue();
});

test('admin có thể click bật tắt nhanh trạng thái flash sale trả về json thành công', function () {
    $category = Category::firstOrCreate(['slug' => 'cat-fs-test'], ['name' => 'Cat FS', 'is_active' => true]);
    $product = Product::firstOrCreate(['slug' => 'prod-fs-test'], [
        'category_id' => $category->id,
        'name' => 'Prod FS Test',
        'sku' => 'SKU-FS-01',
        'price' => 200000,
        'is_active' => true,
    ]);

    $flashSale = FlashSale::create([
        'product_id' => $product->id,
        'sale_price' => 120000,
        'quantity' => 10,
        'sold_quantity' => 0,
        'start_time' => now()->subHour(),
        'end_time' => now()->addHours(2),
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->putJson(route('admin.flash-sales.toggleStatus', $flashSale->id));
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'is_active' => false,
    ]);
    expect($flashSale->fresh()->is_active)->toBeFalse();

    // Bật lại
    $response2 = $this->actingAs($this->admin, 'admin')->putJson(route('admin.flash-sales.toggleStatus', $flashSale->id));
    $response2->assertStatus(200);
    $response2->assertJson([
        'success' => true,
        'is_active' => true,
    ]);
    expect($flashSale->fresh()->is_active)->toBeTrue();
});

test('admin không thể chuyển đơn hàng chuyển khoản sang đang giao hàng nếu chưa thanh toán', function () {
    $order = Order::create([
        'order_code' => 'ORD-BANK-TEST-01',
        'user_id' => $this->admin->id,
        'recipient_name' => 'Khách Chuyển Khoản',
        'recipient_phone' => '0987654321',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 300000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
        'total_amount' => 300000,
        'status' => 'pending',
    ]);

    Payment::create([
        'order_id' => $order->id,
        'payment_method' => 'bank_transfer',
        'amount' => 300000,
        'payment_status' => 'pending',
    ]);

    // Thử chuyển sang shipping khi chưa thanh toán -> Bị từ chối
    $response = $this->actingAs($this->admin, 'admin')->put(route('admin.orders.updateStatus', $order->id), [
        'status' => 'shipping',
    ]);
    $response->assertSessionHas('error');
    expect($order->fresh()->status)->toBe('pending');

    // Admin xác nhận đã nhận chuyển khoản
    $payResponse = $this->actingAs($this->admin, 'admin')->put(route('admin.orders.updatePaymentStatus', $order->id), [
        'payment_status' => 'paid',
    ]);
    $payResponse->assertSessionHas('success');
    expect($order->fresh()->payment->payment_status)->toBe('paid');

    // Sau khi đã thanh toán -> Chuyển sang shipping thành công
    $shipResponse = $this->actingAs($this->admin, 'admin')->put(route('admin.orders.updateStatus', $order->id), [
        'status' => 'shipping',
    ]);
    $shipResponse->assertSessionHas('success');
    expect($order->fresh()->status)->toBe('shipping');
});

test('admin có thể xóa màu sắc và hệ thống bảo toàn sản phẩm biến thể an toàn', function () {
    $color = Color::firstOrCreate(['name' => 'Màu Test Modal', 'hex_code' => '#123456']);

    // 1. Khi màu chưa được gán vào biến thể nào -> Xóa thành công
    $deleteResponse = $this->actingAs($this->admin, 'admin')->deleteJson(route('admin.colors.destroy', $color->id));
    $deleteResponse->assertStatus(200)
        ->assertJson(['success' => true]);

    // 2. Tạo màu mới và gán vào 1 biến thể sản phẩm
    $colorInUse = Color::create(['name' => 'Màu Đang Dùng', 'hex_code' => '#654321']);
    $category = Category::firstOrCreate(['slug' => 'test-cat-color-usage'], ['name' => 'Danh mục màu test', 'is_active' => true]);
    $product = Product::firstOrCreate(
        ['sku' => 'TEST-COLOR-PROD'],
        [
            'category_id' => $category->id,
            'name' => 'Sản phẩm test màu sắc',
            'slug' => 'san-pham-test-mau-sac',
            'price' => 200000,
            'is_active' => true,
        ]
    );
    $size = Size::firstOrCreate(['name' => 'Size Test Col'], [
        'size_type' => 'OTHER',
        'is_active' => true,
    ]);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'color_id' => $colorInUse->id,
        'sku' => 'TEST-COLOR-USAGE-SKU',
        'price' => 100000,
        'stock_quantity' => 10,
        'is_active' => true,
    ]);

    // 3. Khi xóa màu đang được sử dụng -> Thành công 200, sản phẩm và biến thể vẫn còn nguyên (color_id về null)
    $deleteInUseResponse = $this->actingAs($this->admin, 'admin')->deleteJson(route('admin.colors.destroy', $colorInUse->id));
    $deleteInUseResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    expect(Product::where('id', $product->id)->exists())->toBeTrue();
    expect(ProductVariant::where('id', $variant->id)->exists())->toBeTrue();
    expect($variant->fresh()->color_id)->toBeNull();

    // Dọn dẹp dữ liệu test
    $variant->forceDelete();
    $product->forceDelete();
});

test('admin có thể xóa kích cỡ và hệ thống bảo toàn sản phẩm biến thể an toàn', function () {
    $size = Size::create(['name' => 'Size Test Delete', 'size_type' => 'OTHER', 'is_active' => true]);
    $category = Category::firstOrCreate(['slug' => 'test-cat-size-usage'], ['name' => 'Danh mục size test', 'is_active' => true]);
    $product = Product::firstOrCreate(
        ['sku' => 'TEST-SIZE-PROD'],
        [
            'category_id' => $category->id,
            'name' => 'Sản phẩm test size',
            'slug' => 'san-pham-test-size',
            'price' => 150000,
            'is_active' => true,
        ]
    );

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'size_id' => $size->id,
        'sku' => 'TEST-SIZE-USAGE-SKU',
        'price' => 150000,
        'stock_quantity' => 20,
        'is_active' => true,
    ]);

    // Khi xóa size đang được biến thể sử dụng -> Xóa thành công 200, sản phẩm và biến thể vẫn còn nguyên (size_id về null)
    $response = $this->actingAs($this->admin, 'admin')->delete(route('admin.sizes.destroy', $size->id));
    $response->assertRedirect(route('admin.sizes.index'));

    expect(Size::where('id', $size->id)->exists())->toBeFalse();
    expect(Product::where('id', $product->id)->exists())->toBeTrue();
    expect(ProductVariant::where('id', $variant->id)->exists())->toBeTrue();
    expect($variant->fresh()->size_id)->toBeNull();

    // Dọn dẹp
    $variant->forceDelete();
    $product->forceDelete();
});

test('admin truy cập danh sách sản phẩm hiển thị đầy đủ table khi có sản phẩm', function () {
    $category = Category::firstOrCreate(['slug' => 'test-cat-prod-list'], ['name' => 'Danh mục test', 'is_active' => true]);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Sản phẩm Test Table',
        'slug' => 'san-pham-test-table',
        'sku' => 'TEST-PROD-TABLE',
        'price' => 200000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.index'));
    $response->assertStatus(200);
    $response->assertSee('Sản phẩm Test Table');

    $product->forceDelete();
});

test('admin có thể bật tắt trạng thái ẩn/hiện sản phẩm qua ajax', function () {
    $category = Category::firstOrCreate(['slug' => 'test-cat-prod-toggle'], ['name' => 'Danh mục test toggle', 'is_active' => true]);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Sản phẩm Test Toggle',
        'slug' => 'san-pham-test-toggle',
        'sku' => 'TEST-PROD-TOGGLE',
        'price' => 200000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->putJson(route('admin.products.toggleStatus', $product->id));
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'is_active' => false,
    ]);

    expect($product->fresh()->is_active)->toBeFalse();

    $product->forceDelete();
});

test('admin có thể xem chi tiết yêu cầu đổi trả', function () {
    $order = Order::create([
        'order_code' => 'ORD-RETURN-SHOW',
        'user_id' => $this->admin->id,
        'recipient_name' => 'Khách Return',
        'recipient_phone' => '0901234567',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 200000,
        'total_amount' => 230000,
        'status' => 'completed',
    ]);

    $return = OrderReturn::create([
        'order_id' => $order->id,
        'user_id' => $this->admin->id,
        'type' => 'return',
        'reason' => 'Không vừa size',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.returns.show', $return->id));
    $response->assertStatus(200);
    $response->assertSee('ORD-RETURN-SHOW');
});

test('admin có thể truy cập form create và edit màu sắc', function () {
    $color = Color::create([
        'name' => 'Màu Test Form',
        'hex_code' => '#AABBCC',
        'code' => 'TEST_COLOR',
        'is_active' => true,
    ]);

    $responseCreate = $this->actingAs($this->admin, 'admin')->get(route('admin.colors.create'));
    $responseCreate->assertStatus(200);

    $responseEdit = $this->actingAs($this->admin, 'admin')->get(route('admin.colors.edit', $color->id));
    $responseEdit->assertStatus(200);

    $color->delete();
});
