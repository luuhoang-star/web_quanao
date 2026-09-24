<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP xác thực tài khoản - ATINO Store</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            width: 100%;
            max-width: 540px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background-color: #0f172a;
            padding: 28px 24px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .email-body {
            padding: 36px 32px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .otp-box {
            background-color: #f1f5f9;
            border: 2px dashed #0f172a;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            display: inline-block;
            width: 80%;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 8px;
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
        }
        .alert-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 14px;
            border-radius: 8px;
            font-size: 13px;
            color: #991b1b;
            text-align: left;
            margin-top: 24px;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 20px 32px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
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
                <h2 class="greeting">Xin chào {{ $user->name }}! 👋</h2>
                <p class="text">
                    Cảm ơn bạn đã đăng ký tài khoản tại <strong>ATINO Store</strong>. Đây là mã OTP xác thực 6 số của bạn cho tài khoản Gmail <strong>{{ $user->email }}</strong>:
                </p>

                <div class="otp-box">
                    <span style="font-size: 12px; color: #64748b; font-weight: 600; text-uppercase; display: block; margin-bottom: 4px;">MÃ OTP XÁC THỰC 6 SỐ</span>
                    <div class="otp-code">{{ $otpCode }}</div>
                </div>

                <div class="alert-box">
                    <strong>⏰ Lưu ý an toàn:</strong> Mã OTP có hiệu lực trong <strong>5 phút</strong>. Tuyệt đối không chia sẻ mã này cho bất kỳ ai để bảo vệ tài khoản của bạn.
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p style="margin: 0 0 6px 0;"><strong>ATINO Fashion Store</strong> — Thời Trang Nam Nữ Phong Cách Tối Giản</p>
                <p style="margin: 0;">Hotline hỗ trợ: 0973.797.151 | Email: luuvanhoang2k4@gmail.com | Website: quanao.test</p>
            </div>
        </div>
    </div>
</body>
</html>
