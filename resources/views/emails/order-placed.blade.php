<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng #{{ $order->order_code }}</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; background: #ffffff; margin: 0 auto; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { color: #2563eb; margin: 0; }
        .info-box { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .table th { background: #f1f5f9; }
        .total-row { font-weight: bold; color: #dc2626; font-size: 16px; }
        .footer { text-align: center; font-size: 12px; color: #64748b; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>ATINO STORE</h2>
            <p style="margin: 5px 0 0; color: #64748b;">Xác Nhận Đơn Hàng Thành Công</p>
        </div>

        <p>Xin chào <strong>{{ $order->recipient_name }}</strong>,</p>
        <p>Cảm ơn bạn đã đặt hàng tại <strong>ATINO Store</strong>. Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn đã được ghi nhận trên hệ thống và đang được xử lý.</p>

        <div class="info-box">
            <h4 style="margin-top:0;">Thông tin người nhận:</h4>
            <p style="margin: 4px 0;"><strong>Họ tên:</strong> {{ $order->recipient_name }}</p>
            <p style="margin: 4px 0;"><strong>Số điện thoại:</strong> {{ $order->recipient_phone }}</p>
            <p style="margin: 4px 0;"><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
            @if($order->note)
                <p style="margin: 4px 0;"><strong>Ghi chú:</strong> {{ $order->note }}</p>
            @endif
        </div>

        <h4>Chi tiết đơn hàng:</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong><br>
                            <small style="color:#64748b;">{{ $item->variant_info }}</small>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price) }}đ</td>
                        <td>{{ number_format($item->total_price) }}đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: right; margin-bottom: 20px;">
            <p style="margin: 4px 0;">Tạm tính: <strong>{{ number_format($order->subtotal) }}đ</strong></p>
            @if($order->discount_amount > 0)
                <p style="margin: 4px 0; color: #16a34a;">Giảm giá: <strong>-{{ number_format($order->discount_amount) }}đ</strong></p>
            @endif
            <p style="margin: 4px 0;">Phí vận chuyển: <strong>{{ number_format($order->shipping_fee) }}đ</strong></p>
            <p class="total-row" style="margin: 8px 0;">Tổng cộng: {{ number_format($order->total_amount) }}đ</p>
        </div>

        <div class="footer">
            <p>Mọi thắc mắc vui lòng liên hệ Hotline: <strong>0973.797.151</strong> hoặc Email: luuvanhoang2k4@gmail.com</p>
            <p>© 2026 ATINO Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
