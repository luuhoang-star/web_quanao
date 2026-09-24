<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    /**
     * Gửi tin nhắn thông báo tùy chỉnh tới Telegram.
     */
    public static function sendMessage(string $message): bool
    {
        $settings = Cache::get('site_settings', []);
        $token = $settings['telegram_bot_token'] ?? env('TELEGRAM_BOT_TOKEN');
        $chatId = $settings['telegram_chat_id'] ?? env('TELEGRAM_CHAT_ID');

        if (empty($token) || empty($chatId)) {
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$token}/sendMessage";
            $response = Http::timeout(5)->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Telegram notification error: '.$response->body());

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Thông báo khi có đơn hàng mới phát sinh trên hệ thống.
     */
    public static function notifyNewOrder(Order $order): bool
    {
        $settings = Cache::get('site_settings', []);
        if (isset($settings['telegram_notify_order']) && ! $settings['telegram_notify_order']) {
            return false;
        }

        $order->loadMissing(['items', 'payment']);
        $totalFormatted = number_format($order->total_amount).'đ';
        $paymentMethod = match ($order->payment?->payment_method ?? 'COD') {
            'bank_transfer' => 'Chuyển khoản VietQR',
            'momo' => 'Ví MoMo',
            default => 'Tiền mặt khi nhận (COD)',
        };

        $itemsSummary = '';
        foreach ($order->items as $index => $item) {
            $num = $index + 1;
            $itemsSummary .= "  {$num}. {$item->product_name} ({$item->variant_info}) x{$item->quantity}\n";
        }

        $message = "🛍️ <b>CÓ ĐƠN HÀNG MỚI TẠI ATINO STORE!</b>\n"
            ."━━━━━━━━━━━━━━━━━━━━\n"
            ."🔖 <b>Mã đơn:</b> #<code>{$order->order_code}</code>\n"
            ."👤 <b>Khách hàng:</b> {$order->recipient_name}\n"
            ."📞 <b>Số điện thoại:</b> {$order->recipient_phone}\n"
            ."📍 <b>Địa chỉ:</b> {$order->shipping_address}\n"
            ."💳 <b>Thanh toán:</b> {$paymentMethod}\n"
            ."💰 <b>Tổng tiền:</b> <b>{$totalFormatted}</b>\n"
            ."📦 <b>Chi tiết món hàng:</b>\n"
            .$itemsSummary
            .'⏰ <b>Thời gian:</b> '.now()->format('d/m/Y H:i:s')."\n"
            ."━━━━━━━━━━━━━━━━━━━━\n"
            .'👉 <a href="'.route('admin.orders.show', $order->id).'">Bấm vào đây để duyệt đơn ngay</a>';

        return self::sendMessage($message);
    }

    /**
     * Thông báo khi khách hàng gửi yêu cầu đổi/trả hàng.
     */
    public static function notifyReturnRequest(OrderReturn $return): bool
    {
        $settings = Cache::get('site_settings', []);
        if (isset($settings['telegram_notify_return']) && ! $settings['telegram_notify_return']) {
            return false;
        }

        $order = $return->order;
        $orderCode = $order ? $order->order_code : 'N/A';

        $message = "⚠️ <b>YÊU CẦU ĐỔI / TRẢ HÀNG MỚI!</b>\n"
            ."━━━━━━━━━━━━━━━━━━━━\n"
            ."🔖 <b>Mã đơn hàng:</b> #<code>{$orderCode}</code>\n"
            ."📋 <b>Hình thức:</b> {$return->type_label}\n"
            ."👤 <b>Khách hàng:</b> {$return->user?->name} ({$return->contact_phone})\n"
            ."📝 <b>Lý do:</b> {$return->reason_label}\n"
            ."💬 <b>Mô tả:</b> {$return->customer_note}\n"
            .'⏰ <b>Thời gian gửi:</b> '.now()->format('d/m/Y H:i:s')."\n"
            ."━━━━━━━━━━━━━━━━━━━━\n"
            .'👉 <a href="'.route('admin.returns.show', $return->id).'">Bấm vào đây để kiểm tra yêu cầu</a>';

        return self::sendMessage($message);
    }
}
