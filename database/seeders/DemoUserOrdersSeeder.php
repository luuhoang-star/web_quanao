<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoUserOrdersSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('order_returns')->truncate();
        DB::table('order_status_histories')->truncate();
        DB::table('payments')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        Schema::enableForeignKeyConstraints();

        $user2 = User::where('email', 'user@example.com')->first();
        $user4 = User::where('email', 'user@gmail.com')->first();
        $user5 = User::where('email', 'hoangnam@gmail.com')->first() ?? $user2;

        if (! $user2) {
            return;
        }

        $products = Product::with(['variants.color', 'variants.size'])->where('is_active', true)->get();
        if ($products->isEmpty()) {
            return;
        }

        $ordersData = [
            // 1. Đơn hoàn thành 1 - user@example.com
            [
                'user' => $user2,
                'order_code' => 'ATN-'.date('Ymd', strtotime('-12 days')).'-1001',
                'status' => 'completed',
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(9),
                'payment_method' => 'COD',
                'payment_status' => 'paid',
                'shipping_fee' => 0,
                'discount_amount' => 50000,
                'note' => 'Giao trong giờ hành chính giúp mình',
                'address' => 'Số 120 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội',
                'products_idx' => [0, 1], // Nike Dri-FIT + Zara Shirt
            ],
            // 2. Đơn hoàn thành 2 - user@gmail.com
            [
                'user' => $user4,
                'order_code' => 'ATN-'.date('Ymd', strtotime('-8 days')).'-1002',
                'status' => 'completed',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(5),
                'payment_method' => 'vnpay',
                'payment_status' => 'paid',
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'note' => 'Gọi trước khi giao 15 phút nhé',
                'address' => 'Số 45 Đường Lê Duẩn, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
                'products_idx' => [8, 9], // Đầm xòe hoa nhí + Sơ mi chiffon
            ],
            // 3. Đơn hoàn thành 3 - user@example.com (Đơn giá trị cao)
            [
                'user' => $user2,
                'order_code' => 'ATN-'.date('Ymd', strtotime('-5 days')).'-1003',
                'status' => 'completed',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'shipping_fee' => 0,
                'discount_amount' => 100000,
                'note' => 'Đóng gói quà tặng cẩn thận giùm shop nhé',
                'address' => 'Số 120 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội',
                'products_idx' => [4, 7], // Levi's 501 + Blazer nam
            ],
            // 4. Đơn đang giao hàng (shipping) - user@example.com
            [
                'user' => $user2,
                'order_code' => 'ATN-'.date('Ymd', strtotime('-2 days')).'-1004',
                'status' => 'shipping',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subHours(6),
                'payment_method' => 'COD',
                'payment_status' => 'pending',
                'shipping_fee' => 30000,
                'discount_amount' => 30000,
                'note' => 'Giao sáng thứ 7 này',
                'address' => 'Tầng 8, Tòa nhà Keangnam Landmark 72, Phường Mỹ Đình 2, Quận Nam Từ Liêm, Hà Nội',
                'products_idx' => [2, 17], // Polo Atino + Balo Adidas
            ],
            // 5. Đơn đã xác nhận (confirmed) - user@gmail.com
            [
                'user' => $user4,
                'order_code' => 'ATN-'.date('Ymd', strtotime('-1 day')).'-1005',
                'status' => 'confirmed',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subHours(12),
                'payment_method' => 'vnpay',
                'payment_status' => 'paid',
                'shipping_fee' => 0,
                'discount_amount' => 50000,
                'note' => null,
                'address' => 'Căn hộ Landmark 4, Vinhomes Central Park, Phường 22, Quận Bình Thạnh, TP. Hồ Chí Minh',
                'products_idx' => [14, 20], // Set Blazer nữ + Pajama lụa
            ],
            // 6. Đơn mới đặt chờ xử lý (pending) - user@example.com
            [
                'user' => $user2,
                'order_code' => 'ATN-'.date('Ymd').'-1006',
                'status' => 'pending',
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
                'payment_method' => 'COD',
                'payment_status' => 'pending',
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'note' => 'Kiểm tra kỹ size giúp mình',
                'address' => 'Số 120 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội',
                'products_idx' => [3, 23], // Áo khoác gió + Set 3 Boxer
            ],
            // 7. Đơn mới đặt chờ xử lý (pending) - Lê Hoàng Nam
            [
                'user' => $user5,
                'order_code' => 'ATN-'.date('Ymd').'-1007',
                'status' => 'pending',
                'created_at' => now()->subHour(),
                'updated_at' => now()->subHour(),
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'shipping_fee' => 0,
                'discount_amount' => 0,
                'note' => 'Chuyển khoản trong ngày',
                'address' => 'Số 88 Đường Bạch Đằng, Phường Thạch Thang, Quận Hải Châu, Đà Nẵng',
                'products_idx' => [15, 18], // Thắt lưng da + Ví da nam
            ],
            // 8. Đơn đã hủy (cancelled) - user@example.com
            [
                'user' => $user2,
                'order_code' => 'ATN-'.date('Ymd', strtotime('-15 days')).'-1008',
                'status' => 'cancelled',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(14),
                'payment_method' => 'COD',
                'payment_status' => 'failed',
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'note' => 'Đặt nhầm size nên xin phép hủy đặt lại',
                'address' => 'Số 120 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội',
                'products_idx' => [10], // Áo thun nữ Uniqlo
            ],
        ];

        foreach ($ordersData as $oData) {
            $u = $oData['user'];
            $subtotal = 0;
            $itemsToInsert = [];

            foreach ($oData['products_idx'] as $pIdx) {
                $product = $products->get($pIdx) ?? $products->first();
                $variant = $product->variants->first();

                $unitPrice = $product->sale_price ?: $product->price;
                $quantity = rand(1, 2);
                $totalItemPrice = $unitPrice * $quantity;
                $subtotal += $totalItemPrice;

                $colorName = $variant?->color?->name ?? 'Đen';
                $sizeName = $variant?->size?->name ?? 'M';

                $itemsToInsert[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_info' => "Màu: {$colorName} | Size: {$sizeName}",
                    'price' => $unitPrice,
                    'quantity' => $quantity,
                    'total_price' => $totalItemPrice,
                    'created_at' => $oData['created_at'],
                    'updated_at' => $oData['created_at'],
                ];
            }

            $totalAmount = max(0, $subtotal - $oData['discount_amount'] + $oData['shipping_fee']);

            $order = Order::create([
                'order_code' => $oData['order_code'],
                'user_id' => $u->id,
                'recipient_name' => $u->name,
                'recipient_phone' => $u->phone ?? '0987654321',
                'shipping_address' => $oData['address'],
                'subtotal' => $subtotal,
                'discount_amount' => $oData['discount_amount'],
                'shipping_fee' => $oData['shipping_fee'],
                'total_amount' => $totalAmount,
                'status' => $oData['status'],
                'note' => $oData['note'],
                'created_at' => $oData['created_at'],
                'updated_at' => $oData['updated_at'],
            ]);

            foreach ($itemsToInsert as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }

            // Tạo bản ghi Payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $oData['payment_method'],
                'transaction_code' => ($oData['payment_status'] === 'paid') ? 'TXN-'.strtoupper(uniqid()) : null,
                'amount' => $totalAmount,
                'payment_status' => $oData['payment_status'],
                'paid_at' => ($oData['payment_status'] === 'paid') ? $oData['created_at'] : null,
                'created_at' => $oData['created_at'],
                'updated_at' => $oData['updated_at'],
            ]);

            // Tạo timeline lịch sử trạng thái đơn hàng (Order Status Histories)
            $this->createStatusTimeline($order, $oData['status'], $oData['created_at'], $oData['updated_at']);
        }

        // Tạo 2 đơn yêu cầu đổi trả mẫu để trang Quản lý Đổi trả (Admin Returns) có dữ liệu
        $completedOrder = Order::where('status', 'completed')->first();
        if ($completedOrder) {
            DB::table('order_returns')->insert([
                [
                    'order_id' => $completedOrder->id,
                    'user_id' => $completedOrder->user_id,
                    'type' => 'exchange',
                    'reason' => 'Sản phẩm hơi chật, muốn đổi sang size lớn hơn',
                    'note' => 'Áo còn nguyên tem mác, chưa qua giặt ủi, nhờ shop hỗ trợ đổi từ size M sang size L giúp mình.',
                    'images' => json_encode(['products/ao-thun-nam-1.jpg']),
                    'bank_name' => null,
                    'bank_account_number' => null,
                    'bank_account_holder' => null,
                    'status' => 'pending',
                    'admin_note' => null,
                    'processed_by' => null,
                    'processed_at' => null,
                    'created_at' => now()->subDay(),
                    'updated_at' => now()->subDay(),
                ],
                [
                    'order_id' => $completedOrder->id,
                    'user_id' => $completedOrder->user_id,
                    'type' => 'return',
                    'reason' => 'Đặt nhầm màu sản phẩm',
                    'note' => 'Mình đặt nhầm màu áo sơ mi, muốn hoàn lại sản phẩm và nhận hoàn tiền qua STK ngân hàng.',
                    'images' => json_encode(['products/ao-so-mi-nam-1.jpg']),
                    'bank_name' => 'MB Bank',
                    'bank_account_number' => '0987654321',
                    'bank_account_holder' => 'NGUYEN VAN AN',
                    'status' => 'approved',
                    'admin_note' => 'Đã tiếp nhận yêu cầu và hẹn shipper thu hồi hàng ngày mai.',
                    'processed_by' => 1,
                    'processed_at' => now()->subHours(5),
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subHours(5),
                ],
            ]);
        }
    }

    /**
     * Tạo lịch sử trạng thái đơn hàng chân thực
     */
    private function createStatusTimeline(Order $order, string $status, $createdAt, $updatedAt): void
    {
        // 1. Luôn có bước Khởi tạo (pending)
        DB::table('order_status_histories')->insert([
            'order_id' => $order->id,
            'status' => 'pending',
            'note' => 'Đơn hàng được khởi tạo thành công trên hệ thống',
            'updated_by_type' => 'system',
            'updated_by_id' => null,
            'updated_by_name' => 'Hệ thống Atino',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        if ($status === 'pending') {
            return;
        }

        if ($status === 'cancelled') {
            DB::table('order_status_histories')->insert([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'note' => 'Khách hàng yêu cầu hủy đơn hàng: '.($order->note ?: 'Đổi ý'),
                'updated_by_type' => 'user',
                'updated_by_id' => $order->user_id,
                'updated_by_name' => $order->recipient_name,
                'created_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);

            return;
        }

        // Bước 2: Đã xác nhận (confirmed)
        $confirmedTime = $createdAt->copy()->addMinutes(30);
        DB::table('order_status_histories')->insert([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'note' => 'Nhân viên đã xác nhận đơn hàng và chuẩn bị đóng gói',
            'updated_by_type' => 'admin',
            'updated_by_id' => 1,
            'updated_by_name' => 'Quản trị viên Atino',
            'created_at' => $confirmedTime,
            'updated_at' => $confirmedTime,
        ]);

        if ($status === 'confirmed') {
            return;
        }

        // Bước 3: Đang giao hàng (shipping)
        $shippingTime = $createdAt->copy()->addDay();
        DB::table('order_status_histories')->insert([
            'order_id' => $order->id,
            'status' => 'shipping',
            'note' => 'Đơn hàng đã bàn giao cho đơn vị vận chuyển Viettel Post',
            'updated_by_type' => 'admin',
            'updated_by_id' => 1,
            'updated_by_name' => 'Quản trị viên Atino',
            'created_at' => $shippingTime,
            'updated_at' => $shippingTime,
        ]);

        if ($status === 'shipping') {
            return;
        }

        // Bước 4: Hoàn thành (completed)
        DB::table('order_status_histories')->insert([
            'order_id' => $order->id,
            'status' => 'completed',
            'note' => 'Giao hàng thành công. Khách hàng đã nhận kiện hàng nguyên vẹn.',
            'updated_by_type' => 'system',
            'updated_by_id' => null,
            'updated_by_name' => 'Viettel Post Tracking',
            'created_at' => $updatedAt,
            'updated_at' => $updatedAt,
        ]);
    }
}
