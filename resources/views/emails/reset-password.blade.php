<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - ATINO Store</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
        }
        .email-wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background-color: #0f172a;
            padding: 32px 24px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .email-body {
            padding: 40px 32px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .text {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn-reset {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }
        .alert-box {
            background-color: #f1f5f9;
            border-left: 4px solid #0f172a;
            padding: 16px;
            border-radius: 8px;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }
        .link-fallback {
            word-break: break-all;
            font-size: 13px;
            color: #2563eb;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div style="padding: 40px 16px;">
        <div class="email-wrapper">
            <!-- Header -->
            <div class="email-header">
                <h1>ATINO STORE</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <h2 class="greeting">Xin chào, {{ $user->name }}! 👋</h2>
                <p class="text">
                    Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản <strong>{{ $user->email }}</strong> tại hệ thống thời trang ATINO.
                </p>
                <p class="text">
                    Vui lòng bấm vào nút bên dưới để tiến hành thiết lập lại mật khẩu mới cho tài khoản của bạn:
                </p>

                <div class="btn-container">
                    <a href="{{ $resetUrl }}" class="btn-reset" target="_blank">🔒 Đặt Lại Mật Khẩu Tức Thì</a>
                </div>

                <div class="alert-box">
                    <strong>⏰ Lưu ý quan trọng:</strong> Link xác thực này sẽ tự động hết hạn sau <strong>15 phút</strong> kể từ khi thư này được gửi đi và chỉ được sử dụng một lần duy nhất. Nếu bạn không gửi yêu cầu này, vui lòng bỏ qua email và mật khẩu của bạn vẫn hoàn toàn an toàn.
                </div>

                <p class="text" style="font-size: 13px; color: #94a3b8; margin-bottom: 8px;">
                    Nếu nút bấm trên không hoạt động, bạn hãy sao chép và dán liên kết bên dưới vào trình duyệt web:
                </p>
                <p class="link-fallback">
                    <a href="{{ $resetUrl }}" target="_blank">{{ $resetUrl }}</a>
                </p>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p style="margin: 0 0 8px 0;"><strong>ATINO Fashion Store</strong> — Thời Trang Nam Nữ Phong Cách Tối Giản</p>
                <p style="margin: 0;">Hotline hỗ trợ: 0973.797.151 | Email: luuvanhoang2k4@gmail.com | Website: quanao.test</p>
            </div>
        </div>
    </div>
</body>
</html>
