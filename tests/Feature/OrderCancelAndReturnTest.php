<?php

use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Size;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $userRole = Role::firstOrCreate(['name' => 'user'], ['description' => 'Khách hàng']);
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Quản trị viên']);

    $this->user = User::firstOrCreate(
        ['email' => 'client_order_test@example.com'],
        [
            'role_id' => $userRole->id,
            'name' => 'Khách Hàng Test',
            'phone' => '0981112299',
            'password' => bcrypt('password'),
            'status' => 'active',
            'phone_verified_at' => now(),
        ]
    );

    $this->admin = User::where('email', 'admin@example.com')->first();
    if (! $this->admin) {
        $this->admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Quản trị viên',
            'email' => 'admin@example.com',
            'phone' => '0901234567',
            'password' => bcrypt('123456'),
            'status' => 'active',
        ]);
    } else {
        $this->admin->update(['role_id' => $adminRole->id, 'status' => 'active']);
    }

    $this->category = Category::firstOrCreate(
        ['slug' => 'cat-order-test'],
        ['name' => 'Thời Trang Nam Test', 'is_active' => true]
    );

    $this->product = Product::firstOrCreate(
        ['slug' => 'sp-order-test'],
        [
            'category_id' => $this->category->id,
            'name' => 'Áo Thun Nam Cotton',
            'sku' => 'AO-COTTON-01',
            'price' => 199000,
            'is_active' => true,
        ]
    );

    $this->color = Color::firstOrCreate(['name' => 'Màu Đen'], ['code' => '#000000', 'is_active' => true]);
    $this->size = Size::firstOrCreate(['name' => 'Size L'], ['code' => 'L', 'is_active' => true]);

    $this->variant = ProductVariant::firstOrCreate(
        [
            'product_id' => $this->product->id,
            'sku' => 'AO-COTTON-01-L-DEN',
        ],
        [
            'color_id' => $this->color->id,
            'size_id' => $this->size->id,
            'stock_quantity' => 20,
            'price' => 199000,
            'is_active' => true,
        ]
    );
});

test('người dùng có thể hủy đơn hàng chờ xử lý kèm lý do và ghi chú', function () {
    $order = Order::create([
        'order_code' => 'ORD-CANCEL-01',
        'user_id' => $this->user->id,
        'recipient_name' => 'Khách Hàng Test',
        'recipient_phone' => '0981112233',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 199000,
        'discount_amount' => 0,
        'shipping_fee' => 30000,
        'total_amount' => 229000,
        'status' => 'pending',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $this->product->id,
        'product_variant_id' => $this->variant->id,
        'product_name' => $this->product->name,
        'variant_info' => 'Màu Đen / Size L',
        'price' => 199000,
        'quantity' => 2,
        'total_price' => 398000,
    ]);

    $initialStock = $this->variant->fresh()->stock_quantity;

    $response = $this->actingAs($this->user)
        ->post(route('orders.cancel', $order->id), [
            'reason' => 'Muốn đổi size / màu hoặc sản phẩm khác',
            'cancel_note' => 'Tôi muốn đổi sang size XL vì mặc L bị chật',
        ]);

    $response->assertSessionHas('success');
    expect($order->fresh()->status)->toBe('cancelled');

    // Kiểm tra kho đã được hoàn lại 2 cái
    expect($this->variant->fresh()->stock_quantity)->toBe($initialStock + 2);

    // Kiểm tra lịch sử cập nhật có lý do
    $lastHistory = $order->statusHistories()->latest()->first();
    expect($lastHistory->status)->toBe('cancelled');
    expect($lastHistory->note)->toContain('Muốn đổi size / màu hoặc sản phẩm khác');
    expect($lastHistory->note)->toContain('Tôi muốn đổi sang size XL');
});

test('không thể hủy đơn hàng khi trạng thái không phải pending', function () {
    $order = Order::create([
        'order_code' => 'ORD-CANNOT-CANCEL',
        'user_id' => $this->user->id,
        'recipient_name' => 'Khách Hàng Test',
        'recipient_phone' => '0981112233',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 199000,
        'total_amount' => 229000,
        'status' => 'shipping',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('orders.cancel', $order->id), [
            'reason' => 'Đổi ý',
        ]);

    $response->assertSessionHas('error');
    expect($order->fresh()->status)->toBe('shipping');
});

test('người dùng có thể gửi yêu cầu đổi size/màu cho đơn đã hoàn thành', function () {
    Storage::fake('public');

    $order = Order::create([
        'order_code' => 'ORD-RETURN-01',
        'user_id' => $this->user->id,
        'recipient_name' => 'Khách Hàng Test',
        'recipient_phone' => '0981112233',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 199000,
        'total_amount' => 229000,
        'status' => 'completed',
    ]);

    $order->logStatusHistory('completed', 'Đơn hàng giao thành công');

    $file = UploadedFile::fake()->image('proof.jpg');

    $response = $this->actingAs($this->user)
        ->post(route('orders.requestReturn', $order->id), [
            'type' => 'exchange',
            'reason' => 'Mặc không vừa (bị chật / bị rộng)',
            'note' => 'Áo size L bị chật ngực, muốn đổi size XL',
            'images' => [$file],
        ]);

    $response->assertSessionHas('success');

    $return = OrderReturn::where('order_id', $order->id)->first();
    expect($return)->not->toBeNull();
    expect($return->type)->toBe('exchange');
    expect($return->status)->toBe('pending');
    expect($return->reason)->toBe('Mặc không vừa (bị chật / bị rộng)');
    expect($return->images)->toBeArray();
    expect(count($return->images))->toBe(1);
});

test('người dùng có thể gửi yêu cầu trả hàng hoàn tiền kèm số tài khoản', function () {
    $order = Order::create([
        'order_code' => 'ORD-RETURN-02',
        'user_id' => $this->user->id,
        'recipient_name' => 'Khách Hàng Test',
        'recipient_phone' => '0981112233',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 199000,
        'total_amount' => 229000,
        'status' => 'completed',
    ]);

    $order->logStatusHistory('completed', 'Đơn hàng giao thành công');

    $response = $this->actingAs($this->user)
        ->post(route('orders.requestReturn', $order->id), [
            'type' => 'return',
            'reason' => 'Sản phẩm bị lỗi (sứt chỉ, rách, bẩn, lỗi khóa kéo...)',
            'note' => 'Đường chỉ gấu áo bị tuột',
            'bank_name' => 'Vietcombank',
            'bank_account_number' => '0123456789',
            'bank_account_holder' => 'NGUYEN VAN A',
        ]);

    $response->assertSessionHas('success');

    $return = OrderReturn::where('order_id', $order->id)->first();
    expect($return)->not->toBeNull();
    expect($return->type)->toBe('return');
    expect($return->bank_name)->toBe('Vietcombank');
    expect($return->bank_account_number)->toBe('0123456789');
});

test('admin có thể chấp thuận yêu cầu đổi/trả hàng và tự động cập nhật', function () {
    $order = Order::create([
        'order_code' => 'ORD-ADMIN-APPROVE',
        'user_id' => $this->user->id,
        'recipient_name' => 'Khách Hàng Test',
        'recipient_phone' => '0981112233',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 199000,
        'total_amount' => 229000,
        'status' => 'completed',
    ]);

    $return = OrderReturn::create([
        'order_id' => $order->id,
        'user_id' => $this->user->id,
        'type' => 'return',
        'reason' => 'Hàng lỗi',
        'bank_name' => 'MB Bank',
        'bank_account_number' => '999999999',
        'bank_account_holder' => 'TEST',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->admin, 'admin')
        ->post(route('admin.returns.approve', $return->id), [
            'admin_note' => 'Đã tiếp nhận và đồng ý hoàn tiền cho khách',
        ]);

    $response->assertSessionHas('success');
    expect($return->fresh()->status)->toBe('approved');
    expect($return->fresh()->admin_note)->toBe('Đã tiếp nhận và đồng ý hoàn tiền cho khách');
    expect($order->fresh()->status)->toBe('returned');
});

test('admin có thể từ chối yêu cầu đổi/trả hàng kèm lý do rõ ràng', function () {
    $order = Order::create([
        'order_code' => 'ORD-ADMIN-REJECT',
        'user_id' => $this->user->id,
        'recipient_name' => 'Khách Hàng Test',
        'recipient_phone' => '0981112233',
        'shipping_address' => 'Hà Nội',
        'subtotal' => 199000,
        'total_amount' => 229000,
        'status' => 'completed',
    ]);

    $return = OrderReturn::create([
        'order_id' => $order->id,
        'user_id' => $this->user->id,
        'type' => 'exchange',
        'reason' => 'Không thích nữa',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->admin, 'admin')
        ->post(route('admin.returns.reject', $return->id), [
            'admin_note' => 'Sản phẩm đã bị cắt tem mác nên shop không hỗ trợ đổi',
        ]);

    $response->assertSessionHas('success');
    expect($return->fresh()->status)->toBe('rejected');
    expect($return->fresh()->admin_note)->toBe('Sản phẩm đã bị cắt tem mác nên shop không hỗ trợ đổi');
});
