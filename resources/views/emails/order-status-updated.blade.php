<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật trạng thái đơn hàng #{{ $order->order_code }}</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; background: #ffffff; margin: 0 auto; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { color: #2563eb; margin: 0; }
        .status-badge { display: inline-block; padding: 6px 14px; background: #eff6ff; color: #1d4ed8; font-weight: bold; border-radius: 20px; font-size: 14px; margin-top: 5px; }
        .info-box { background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; font-size: 12px; color: #64748b; margin-top: 25px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>ATINO STORE</h2>
            <p style="margin: 5px 0 0; color: #64748b;">Thông Báo Cập Nhật Đơn Hàng</p>
        </div>

        <p>Xin chào <strong>{{ $order->recipient_name }}</strong>,</p>
        <p>Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn vừa có cập nhật trạng thái mới từ hệ thống:</p>

        <div style="text-align: center; margin: 20px 0;">
            <div class="status-badge">Trạng thái mới: {{ $statusLabel }}</div>
        </div>

        <div class="info-box">
            <p style="margin: 4px 0;"><strong>Mã đơn hàng:</strong> #{{ $order->order_code }}</p>
            <p style="margin: 4px 0;"><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
            <p style="margin: 4px 0;"><strong>Tổng thanh toán:</strong> <span style="color: #dc2626; font-weight: bold;">{{ number_format($order->total_amount) }}đ</span></p>
        </div>

        <p>Bạn có thể theo dõi chi tiết lịch sử đơn hàng bằng cách đăng nhập vào tài khoản cá nhân trên website ATINO Store.</p>

        <div class="footer">
            <p>Mọi thắc mắc xin liên hệ Hotline: <strong>0973.797.151</strong> hoặc Email: luuvanhoang2k4@gmail.com</p>
            <p>© 2026 ATINO Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
