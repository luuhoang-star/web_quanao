@extends('layouts.app')

@section('title', 'Đăng ký tài khoản thành viên - ATINO Fashion')

@push('styles')
<style>
    .auth-page-wrapper {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 0;
    }

    .auth-luxury-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(15, 23, 42, 0.03);
    }

    .auth-visual-side {
        background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 60%, #0f172a 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 3.5rem 3rem;
    }

    .auth-visual-bg-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: radial-gradient(circle at 20% 30%, rgba(245, 158, 11, 0.15) 0%, transparent 40%),
                          radial-gradient(circle at 80% 80%, rgba(99, 102, 241, 0.2) 0%, transparent 50%);
        pointer-events: none;
    }

    .auth-visual-side::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60%;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.95), transparent);
        pointer-events: none;
    }

    .brand-logo-text {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        letter-spacing: -0.04em;
        font-size: 2.2rem;
        color: #ffffff;
    }

    .brand-quote {
        font-style: italic;
        color: rgba(255, 255, 255, 0.85);
        font-size: 1.05rem;
        line-height: 1.6;
    }

    .auth-feature-item {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 0.85rem;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .auth-feature-item i {
        color: #fbbf24;
        font-size: 1rem;
    }

    .auth-form-side {
        padding: 3.5rem 3.25rem;
        background: #ffffff;
    }

    .auth-form-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.03em;
        margin-bottom: 0.4rem;
    }

    .auth-form-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 1.75rem;
    }

    .auth-input-group {
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .auth-input-group:focus-within {
        border-color: #0f172a;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.08);
    }

    .auth-input-group.is-invalid-group {
        border-color: #e11d48;
        box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.1);
    }

    .auth-input-group .input-group-text {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding-left: 1.1rem;
        padding-right: 0.6rem;
    }

    .auth-input-group .form-control {
        background: transparent;
        border: none;
        padding: 0.8rem 0.75rem 0.8rem 0.25rem;
        font-size: 0.925rem;
        color: #0f172a;
        box-shadow: none !important;
    }

    .auth-input-group .form-control::placeholder {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .btn-toggle-pwd {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding: 0 1.1rem;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .btn-toggle-pwd:hover {
        color: #0f172a;
    }

    .btn-auth-submit {
        background: #0f172a;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 0.9rem 1.5rem;
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: 0.02em;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.25);
    }

    .btn-auth-submit:hover {
        background: #1e293b;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(15, 23, 42, 0.35);
    }

    .auth-footer-link {
        color: #0f172a;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .auth-footer-link:hover {
        color: #4f46e5;
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="auth-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="auth-luxury-card">
                    <div class="row g-0">
                        
                        <!-- ================= CỘT TRÁI: VISUAL SHOWCASE ================= -->
                        <div class="col-lg-5 d-none d-lg-flex auth-visual-side">
                            <div class="auth-visual-bg-overlay"></div>
                            
                            <!-- Top Brand Info -->
                            <div class="position-relative z-2">
                                <div class="d-flex align-items-center gap-2.5 mb-3">
                                    <span class="brand-logo-text">ATINO</span>
                                    <span class="badge bg-warning text-dark extra-small fw-bold px-2 py-0.5 rounded-pill">VIP CLUB</span>
                                </div>
                                <p class="brand-quote mb-0">
                                    "Gia nhập cộng đồng thời trang ATINO để tận hưởng những đặc quyền mua sắm cao cấp nhất."
                                </p>
                            </div>

                            <!-- Bottom Features & Member Perks -->
                            <div class="position-relative z-2 mt-5">
                                <div class="mb-4">
                                    <div class="auth-feature-item">
                                        <i class="fa-solid fa-gift"></i>
                                        <span>Tặng ngay mã giảm <strong>50.000đ</strong> cho đơn đầu tiên</span>
                                    </div>
                                    <div class="auth-feature-item">
                                        <i class="fa-solid fa-crown"></i>
                                        <span>Tích lũy điểm thưởng và nâng hạng VIP trọn đời</span>
                                    </div>
                                    <div class="auth-feature-item">
                                        <i class="fa-solid fa-bell"></i>
                                        <span>Nhận thông báo sớm các đợt Flash Sale độc quyền</span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-15 backdrop-blur">
                                    <div class="d-flex align-items-center gap-2 mb-1.5">
                                        <i class="fa-solid fa-shield-check text-warning fs-5"></i>
                                        <span class="extra-small text-white fw-bold">Bảo mật thông tin tuyệt đối 100%</span>
                                    </div>
                                    <p class="extra-small text-white text-opacity-75 mb-0">
                                        Mọi thông tin cá nhân và dữ liệu mua sắm của bạn đều được mã hóa theo tiêu chuẩn bảo mật cao nhất.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- ================= CỘT PHẢI: FORM ĐĂNG KÝ ================= -->
                        <div class="col-lg-7 auth-form-side">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div>
                                    <h2 class="auth-form-title">Tạo tài khoản mới ✨</h2>
                                    <p class="auth-form-subtitle mb-0">Chỉ mất 30 giây để hoàn tất đăng ký và nhận ưu đãi</p>
                                </div>
                                <a href="{{ route('home') }}" class="btn btn-sm btn-light rounded-circle p-2 d-none d-sm-flex align-items-center justify-content-center text-muted" title="Về trang chủ" style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-house"></i>
                                </a>
                            </div>

                            <form action="{{ route('register') }}" method="POST">
                                @csrf

                                <!-- Họ và tên -->
                                <div class="mb-3">
                                    <label for="name" class="form-label small fw-bold text-dark mb-1.5">Họ và tên của bạn <span class="text-danger">*</span></label>
                                    <div class="input-group auth-input-group @error('name') is-invalid-group @enderror">
                                        <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Nguyễn Văn A" required autofocus>
                                    </div>
                                    @error('name')
                                        <div class="text-danger extra-small mt-1 fw-semibold d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Email & Số điện thoại (2 Cột) -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label small fw-bold text-dark mb-1.5">Địa chỉ Email <span class="text-danger">*</span></label>
                                        <div class="input-group auth-input-group @error('email') is-invalid-group @enderror">
                                            <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" required>
                                        </div>
                                        @error('email')
                                            <div class="text-danger extra-small mt-1 fw-semibold d-flex align-items-center gap-1">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label small fw-bold text-dark mb-1.5">Số điện thoại <span class="text-danger">*</span></label>
                                        <div class="input-group auth-input-group @error('phone') is-invalid-group @enderror">
                                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0987654321" required>
                                        </div>
                                        @error('phone')
                                            <div class="text-danger extra-small mt-1 fw-semibold d-flex align-items-center gap-1">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Mật khẩu & Nhập lại Mật khẩu (2 Cột) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label small fw-bold text-dark mb-1.5">Mật khẩu <span class="text-danger">*</span></label>
                                        <div class="input-group auth-input-group @error('password') is-invalid-group @enderror">
                                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Tối thiểu 6 ký tự" required>
                                            <button type="button" class="btn-toggle-pwd" onclick="togglePasswordVisibility('password', this)" title="Ẩn/Hiện mật khẩu">
                                                <i class="fa-regular fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="text-danger extra-small mt-1 fw-semibold d-flex align-items-center gap-1">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                                <span>{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label small fw-bold text-dark mb-1.5">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                        <div class="input-group auth-input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-shield-halved"></i></span>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                                            <button type="button" class="btn-toggle-pwd" onclick="togglePasswordVisibility('password_confirmation', this)" title="Ẩn/Hiện mật khẩu">
                                                <i class="fa-regular fa-eye-slash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cam kết điều khoản -->
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="terms" required checked>
                                    <label class="form-check-label extra-small text-muted user-select-none" for="terms">
                                        Tôi đồng ý với <a href="#" class="text-dark fw-bold text-decoration-none">Điều khoản dịch vụ</a> và <a href="#" class="text-dark fw-bold text-decoration-none">Chính sách bảo mật</a> của ATINO.
                                    </label>
                                </div>

                                <!-- Nút Đăng Ký Chính -->
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-auth-submit d-flex align-items-center justify-content-center gap-2">
                                        <span>ĐĂNG KÝ TÀI KHOẢN NGAY</span>
                                        <i class="fa-solid fa-arrow-right fs-6"></i>
                                    </button>
                                </div>

                                <!-- Liên Kết Đăng Nhập -->
                                <div class="text-center pt-3 border-top">
                                    <p class="small text-muted mb-0">
                                        Đã có tài khoản thành viên? 
                                        <a href="{{ route('login') }}" class="auth-footer-link ms-1">
                                            Đăng nhập tại đây <i class="fa-solid fa-arrow-right ms-0.5"></i>
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
</script>
@endpush
