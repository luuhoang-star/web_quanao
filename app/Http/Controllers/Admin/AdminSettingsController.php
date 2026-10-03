<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TelegramNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    /**
     * Mặc định các thông số cấu hình của hệ thống và trang chủ.
     */
    public static function getDefaultSettings(): array
    {
        return [
            // Thông tin chung & Vận chuyển
            'store_name' => 'Quần Áo Store - Thời Trang Nam ATINO',
            'hotline' => '0973797151',
            'email' => 'luuvanhoang2k4@gmail.com',
            'address' => 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội',
            'free_shipping_threshold' => 500000,
            'default_shipping_fee' => 30000,
            'maintenance_mode' => false,

            // Quản lý Flash Sale trang chủ
            'home_show_flash_sale' => true,
            'home_flash_sale_badge' => 'FLASH SALE',
            'home_flash_sale_title' => 'Ưu Đãi Đặc Biệt - Săn Sale Giá Sốc',
            'home_flash_sale_subtitle' => 'Số lượng có hạn, nhanh tay sở hữu!',

            // 4 Khối cam kết & Chính sách dịch vụ (Service Highlights)
            'home_show_features' => true,
            'feature_1_title' => 'Miễn Phí Vận Chuyển',
            'feature_1_desc' => 'Cho tất cả đơn hàng từ 499.000đ toàn quốc',
            'feature_1_icon' => 'fa-solid fa-truck-fast',

            'feature_2_title' => 'Đổi Trả 30 Ngày',
            'feature_2_desc' => 'Miễn phí đổi size & mẫu trong 30 ngày',
            'feature_2_icon' => 'fa-solid fa-arrows-rotate',

            'feature_3_title' => 'Thanh Toán An Toàn',
            'feature_3_desc' => 'Bảo mật 100% giao dịch VNPay, MoMo, COD',
            'feature_3_icon' => 'fa-solid fa-shield-halved',

            'feature_4_title' => 'Hỗ Trợ 24/7',
            'feature_4_desc' => 'Tư vấn chọn size & giải đáp mọi thắc mắc',
            'feature_4_icon' => 'fa-solid fa-headset',

            // Cấu hình Ngân hàng thụ hưởng & VietQR & MoMo
            'bank_code' => 'vietcombank',
            'bank_name' => 'Vietcombank (Ngoại thương Việt Nam)',
            'bank_account' => '9973797151',
            'bank_account_name' => 'LUU VAN HOANG',
            'bank_transfer_syntax' => 'TT',
            'momo_phone' => '0973797151',
            'momo_name' => 'LUU VAN HOANG',

            // Cấu hình Thông báo Telegram Bot
            'telegram_bot_token' => '',
            'telegram_chat_id' => '',
            'telegram_notify_order' => true,
            'telegram_notify_return' => true,

            // Chế độ MoMo: 'personal' (Ví MoMo cá nhân quét QR & SePay) hoặc 'gateway' (Cổng MoMo Business API)
            'momo_mode' => 'personal',

            // Cấu hình Cổng thanh toán MoMo Gateway (v2)
            'momo_gateway_partner_code' => 'MOMOBKUN20180529',
            'momo_gateway_access_key' => 'klm05TvNBzhg7h7j',
            'momo_gateway_secret_key' => 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa',
            'momo_gateway_env' => 'sandbox',

            // Cấu hình Webhook xác nhận thanh toán tự động (SePay / Casso / Ngân hàng)
            'webhook_api_key' => 'ATINO_SECURE_KEY_'.date('Y'),
            'auto_confirm_payment' => true,
        ];
    }

    /**
     * Hiển thị trang Cấu hình hệ thống & Nội dung trang chủ.
     */
    public function index(): View
    {
        $defaults = self::getDefaultSettings();
        $saved = Cache::get('site_settings', []);
        $settings = array_merge($defaults, is_array($saved) ? $saved : []);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Cập nhật Cấu hình hệ thống & Nội dung trang chủ.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'hotline' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'free_shipping_threshold' => ['required', 'numeric', 'min:0'],
            'default_shipping_fee' => ['required', 'numeric', 'min:0'],
            'maintenance_mode' => ['nullable', 'boolean'],

            // Flash Sale
            'home_flash_sale_badge' => ['nullable', 'string', 'max:50'],
            'home_flash_sale_title' => ['required', 'string', 'max:255'],
            'home_flash_sale_subtitle' => ['nullable', 'string', 'max:255'],

            // 4 Cam kết
            'feature_1_title' => ['required', 'string', 'max:100'],
            'feature_1_desc' => ['required', 'string', 'max:255'],
            'feature_2_title' => ['required', 'string', 'max:100'],
            'feature_2_desc' => ['required', 'string', 'max:255'],
            'feature_3_title' => ['required', 'string', 'max:100'],
            'feature_3_desc' => ['required', 'string', 'max:255'],
            'feature_4_title' => ['required', 'string', 'max:100'],
            'feature_4_desc' => ['required', 'string', 'max:255'],

            // Cấu hình Ngân hàng & MoMo
            'bank_code' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:100'],
            'bank_transfer_syntax' => ['nullable', 'string', 'max:20'],
            'momo_phone' => ['nullable', 'string', 'max:20'],
            'momo_name' => ['nullable', 'string', 'max:100'],

            // Cổng MoMo
            'momo_mode' => ['nullable', 'string', 'in:personal,gateway'],
            'momo_gateway_partner_code' => ['nullable', 'string', 'max:100'],
            'momo_gateway_access_key' => ['nullable', 'string', 'max:100'],
            'momo_gateway_secret_key' => ['nullable', 'string', 'max:150'],
            'momo_gateway_env' => ['nullable', 'string', 'in:sandbox,production'],
        ], [
            'store_name.required' => 'Vui lòng nhập tên cửa hàng.',
            'hotline.required' => 'Vui lòng nhập số hotline liên hệ.',
            'email.required' => 'Vui lòng nhập email cửa hàng.',
            'address.required' => 'Vui lòng nhập địa chỉ cửa hàng.',
            'home_flash_sale_title.required' => 'Vui lòng nhập tiêu đề chương trình Flash Sale.',
            'feature_1_title.required' => 'Vui lòng nhập tiêu đề cam kết 1.',
            'feature_2_title.required' => 'Vui lòng nhập tiêu đề cam kết 2.',
            'feature_3_title.required' => 'Vui lòng nhập tiêu đề cam kết 3.',
            'feature_4_title.required' => 'Vui lòng nhập tiêu đề cam kết 4.',
        ]);

        $current = Cache::get('site_settings', self::getDefaultSettings());
        if (! is_array($current)) {
            $current = self::getDefaultSettings();
        }

        $settings = array_merge($current, [
            'store_name' => $request->store_name,
            'hotline' => $request->hotline,
            'email' => $request->email,
            'address' => $request->address,
            'free_shipping_threshold' => (float) $request->free_shipping_threshold,
            'default_shipping_fee' => (float) $request->default_shipping_fee,
            'maintenance_mode' => $request->has('maintenance_mode'),

            // Flash Sale
            'home_show_flash_sale' => $request->has('home_show_flash_sale'),
            'home_flash_sale_badge' => $request->home_flash_sale_badge ?: 'FLASH SALE',
            'home_flash_sale_title' => $request->home_flash_sale_title,
            'home_flash_sale_subtitle' => $request->home_flash_sale_subtitle,

            // 4 Cam kết
            'home_show_features' => $request->has('home_show_features'),
            'feature_1_title' => $request->feature_1_title,
            'feature_1_desc' => $request->feature_1_desc,
            'feature_1_icon' => $request->feature_1_icon ?: 'fa-solid fa-truck-fast',

            'feature_2_title' => $request->feature_2_title,
            'feature_2_desc' => $request->feature_2_desc,
            'feature_2_icon' => $request->feature_2_icon ?: 'fa-solid fa-arrows-rotate',

            'feature_3_title' => $request->feature_3_title,
            'feature_3_desc' => $request->feature_3_desc,
            'feature_3_icon' => $request->feature_3_icon ?: 'fa-solid fa-shield-halved',

            'feature_4_title' => $request->feature_4_title,
            'feature_4_desc' => $request->feature_4_desc,
            'feature_4_icon' => $request->feature_4_icon ?: 'fa-solid fa-headset',

            // Cấu hình Ngân hàng & MoMo
            'bank_code' => $request->input('bank_code', 'vietcombank') ?: 'vietcombank',
            'bank_name' => $request->input('bank_name', 'Vietcombank') ?: 'Vietcombank',
            'bank_account' => $request->input('bank_account', '9973797151') ?: '9973797151',
            'bank_account_name' => strtoupper((string) ($request->input('bank_account_name', 'LUU VAN HOANG') ?: 'LUU VAN HOANG')),
            'bank_transfer_syntax' => $request->input('bank_transfer_syntax', 'TT') ?: 'TT',
            'momo_phone' => $request->input('momo_phone', '0973797151') ?: '0973797151',
            'momo_name' => strtoupper((string) ($request->input('momo_name', 'LUU VAN HOANG') ?: 'LUU VAN HOANG')),

            // Cấu hình Telegram
            'telegram_bot_token' => trim((string) $request->input('telegram_bot_token', '')),
            'telegram_chat_id' => trim((string) $request->input('telegram_chat_id', '')),
            'telegram_notify_order' => $request->has('telegram_notify_order'),
            'telegram_notify_return' => $request->has('telegram_notify_return'),

            // Cấu hình Webhook
            'webhook_api_key' => trim((string) $request->input('webhook_api_key', 'ATINO_SECURE_KEY')),
            'auto_confirm_payment' => $request->has('auto_confirm_payment'),

            // Cổng MoMo
            'momo_mode' => $request->input('momo_mode', 'personal') ?: 'personal',
            'momo_gateway_partner_code' => trim((string) $request->input('momo_gateway_partner_code', 'MOMOBKUN20180529')),
            'momo_gateway_access_key' => trim((string) $request->input('momo_gateway_access_key', 'klm05TvNBzhg7h7j')),
            'momo_gateway_secret_key' => trim((string) $request->input('momo_gateway_secret_key', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa')),
            'momo_gateway_env' => $request->input('momo_gateway_env', 'sandbox') ?: 'sandbox',
        ]);

        Cache::forever('site_settings', $settings);

        return back()->with('success', 'Cập nhật cấu hình hệ thống & nội dung trang chủ thành công!');
    }

    /**
     * Bắn tin nhắn thử nghiệm tới Telegram Bot.
     */
    public function testTelegram(Request $request): RedirectResponse
    {
        $testMsg = "🔔 <b>THÔNG BÁO THỬ NGHIỆM TỪ ATINO STORE!</b>\n"
            ."━━━━━━━━━━━━━━━━━━━━\n"
            ."Hệ thống kết nối Telegram Bot đang hoạt động hoàn hảo.\n"
            .'Thời gian: '.now()->format('d/m/Y H:i:s');

        $success = TelegramNotificationService::sendMessage($testMsg);

        if ($success) {
            return back()->with('success', 'Đã gửi tin nhắn thử nghiệm thành công tới Telegram Bot!');
        }

        return back()->with('error', 'Gửi tin nhắn thử nghiệm thất bại! Vui lòng kiểm tra lại Bot Token và Chat ID.');
    }
}
