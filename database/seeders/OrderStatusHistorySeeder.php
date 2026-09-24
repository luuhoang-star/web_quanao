<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận đơn hàng',
            'processing' => 'Đang chuẩn bị hàng',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã giao hàng thành công',
            'cancelled' => 'Đã hủy đơn hàng',
            'returned' => 'Trả hàng / Hoàn tiền',
        ];

        foreach (Order::with('statusHistories')->get() as $order) {
            if ($order->statusHistories->isEmpty()) {
                // Mốc 1: Khởi tạo đơn
                $order->statusHistories()->create([
                    'status' => 'pending',
                    'note' => 'Đơn hàng đã được khởi tạo thành công trên hệ thống',
                    'updated_by_type' => 'system',
                    'updated_by_name' => 'Hệ thống',
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);

                // Mốc 2: Nếu trạng thái hiện tại khác pending
                if ($order->status !== 'pending') {
                    $label = $statusLabels[$order->status] ?? $order->status;
                    $order->statusHistories()->create([
                        'status' => $order->status,
                        'note' => "Cập nhật trạng thái đơn hàng thành: {$label}",
                        'updated_by_type' => 'admin',
                        'updated_by_id' => 1,
                        'updated_by_name' => 'Quản trị viên',
                        'created_at' => $order->updated_at,
                        'updated_at' => $order->updated_at,
                    ]);
                }
            }
        }
    }
}
