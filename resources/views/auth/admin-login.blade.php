@extends('layouts.app')

@section('title', 'Đăng Nhập Quản Trị Viên (Admin Portal) - ATINO')

@push('styles')
<style>
    .admin-auth-wrapper {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 0;
    }

    .admin-auth-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15), 0 0 0 1px rgba(15, 23, 42, 0.03);
        max-width: 460px;
        width: 100%;
        margin: 0 auto;
    }

    .admin-auth-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 2.5rem 2rem 2rem 2rem;
        text-align: center;
        position: relative;
        color: #ffffff;
    }

    .admin-badge-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
        box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.4);
    }

    .admin-auth-body {
        padding: 2.5rem 2.25rem;
        background: #ffffff;
    }

    .admin-input-group {
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .admin-input-group:focus-within {
        border-color: #0f172a;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.08);
    }

    .admin-input-group .input-group-text {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding-left: 1.1rem;
        padding-right: 0.6rem;
    }

    .admin-input-group .form-control {
        background: transparent;
        border: none;
        padding: 0.85rem 0.75rem 0.85rem 0.25rem;
        font-size: 0.925rem;
        color: #0f172a;
        box-shadow: none !important;
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

    .btn-admin-submit {
        background: #0f172a;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 0.9rem 1.5rem;
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: 0.02em;
        transition: all 0.25s ease;
        box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.25);
    }

    .btn-admin-submit:hover {
        background: #1e293b;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(15, 23, 42, 0.35);
    }
</style>
@endpush

@section('content')
<div class="admin-auth-wrapper">
    <div class="container">
        <div class="admin-auth-card">
            <!-- Header Portal -->
            <div class="admin-auth-header">
                <div class="admin-badge-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h4 class="fw-extrabold text-white mb-1">HỆ THỐNG QUẢN TRỊ</h4>
                <p class="small text-white-50 mb-0">ATINO Control Center & Store Operations</p>
            </div>

            <!-- Body Form -->
            <div class="admin-auth-body">
                @if(auth()->check())
                    <div class="alert alert-warning border-0 rounded-3 p-3 extra-small mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info fs-6 text-warning"></i>
                        <span>Đang đăng nhập user: <strong>{{ auth()->user()->name }}</strong>. Đăng nhập Admin sẽ tự động chuyển phiên làm việc.</span>
                    </div>
                @endif

                <form action="{{ route('admin.processLogin') }}" method="POST">
                    @csrf

                    <!-- Email Admin -->
                    <div class="mb-3.5">
                        <label for="email" class="form-label small fw-bold text-dark mb-1.5">Email Quản Trị Viên <span class="text-danger">*</span></label>
                        <div class="input-group admin-input-group @error('email') is-invalid-group @enderror">
                            <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', 'admin@example.com') }}" placeholder="admin@example.com" required autofocus>
                        </div>
                        @error('email')
                            <div class="text-danger extra-small mt-1.5 fw-semibold">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mật Khẩu Admin -->
                    <div class="mb-3.5">
                        <label for="password" class="form-label small fw-bold text-dark mb-1.5">Mật Khẩu Bảo Mật <span class="text-danger">*</span></label>
                        <div class="input-group admin-input-group @error('password') is-invalid-group @enderror">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" value="123456" placeholder="••••••••" required>
                            <button type="button" class="btn-toggle-pwd" onclick="togglePasswordVisibility('password', this)" title="Ẩn/Hiện mật khẩu">
                                <i class="fa-regular fa-eye-slash"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger extra-small mt-1.5 fw-semibold">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ghi nhớ phiên -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" checked>
                        <label class="form-check-label extra-small text-muted user-select-none" for="remember">
                            Ghi nhớ phiên làm việc trên máy tính này
                        </label>
                    </div>

                    <!-- Nút Đăng Nhập Admin -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-admin-submit d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>ĐĂNG NHẬP TRANG QUẢN TRỊ</span>
                        </button>
                    </div>

                    <div class="text-center pt-3 border-top">
                        <a href="{{ route('home') }}" class="text-decoration-none small text-muted hover-underline">
                            <i class="fa-solid fa-arrow-left me-1"></i> Trở về Cửa Hàng
                        </a>
                    </div>
                </form>
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
