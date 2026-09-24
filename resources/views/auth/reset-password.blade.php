@extends('layouts.app')

@section('title', 'Đặt Lại Mật Khẩu - ATINO Store')

@push('styles')
<style>
    .reset-card-wrapper {
        max-width: 450px;
        margin: 0 auto;
    }

    .reset-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
        overflow: hidden;
    }

    .reset-card-header {
        background-color: #0f172a !important;
        padding: 1.25rem 1.5rem !important;
        text-align: center;
    }

    .reset-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        margin-bottom: 0.5rem;
    }

    .reset-input-group .input-group-text {
        background-color: #ffffff;
        border-color: #cbd5e1;
        color: #64748b;
        padding-left: 0.85rem;
        padding-right: 0.85rem;
    }

    .reset-input-control {
        border-color: #cbd5e1 !important;
        font-size: 0.875rem !important;
        padding: 0.6rem 0.85rem !important;
        color: #0f172a !important;
        transition: all 0.2s ease !important;
    }

    .reset-input-control:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08) !important;
        outline: none !important;
    }

    .reset-input-control[readonly] {
        background-color: #f8fafc !important;
        color: #64748b !important;
        cursor: not-allowed;
    }

    .btn-toggle-pwd {
        border-color: #cbd5e1 !important;
        background-color: #ffffff !important;
        color: #64748b !important;
        padding: 0.6rem 0.85rem !important;
        transition: all 0.2s ease !important;
    }

    .btn-toggle-pwd:hover {
        background-color: #f8fafc !important;
        color: #0f172a !important;
    }

    .pwd-requirement-item {
        font-size: 0.775rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        transition: color 0.2s ease;
    }

    .pwd-requirement-item.valid {
        color: #10b981;
        font-weight: 600;
    }

    .btn-reset-submit {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border: 1px solid #0f172a !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        padding: 0.65rem 1.25rem !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.4rem !important;
        width: 100% !important;
    }

    .btn-reset-submit:hover:not(:disabled) {
        background-color: #1e293b !important;
        border-color: #1e293b !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.18) !important;
    }

    .btn-reset-submit:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .back-link {
        color: #64748b;
        font-size: 0.825rem;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: #0f172a;
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-md-5">
    <div class="reset-card-wrapper">
        <div class="card reset-card">
            <!-- Compact Header -->
            <div class="reset-card-header">
                <div class="reset-icon-circle">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h5 class="fw-bold text-white mb-1 fs-5">Đặt Lại Mật Khẩu</h5>
                <span class="extra-small text-white opacity-75">Tạo mật khẩu mới an toàn cho tài khoản của bạn</span>
            </div>

            <div class="card-body p-3.5 p-md-4">
                <!-- Session Status / Errors -->
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 extra-small mb-3" role="alert">
                        <i class="fa-solid fa-circle-check me-1.5"></i>{{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 extra-small mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="formResetPassword" action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Address (Readonly) -->
                    <div class="mb-3">
                        <label for="email" class="form-label extra-small fw-semibold text-secondary mb-1">Địa chỉ Email <span class="text-danger">*</span></label>
                        <div class="input-group reset-input-group">
                            <span class="input-group-text border-end-0 rounded-start-3"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" class="form-control reset-input-control rounded-end-3 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $email) }}" required readonly placeholder="email@example.com">
                        </div>
                        @error('email')
                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label extra-small fw-semibold text-secondary mb-1">Mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group reset-input-group">
                            <span class="input-group-text border-end-0 rounded-start-3"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control reset-input-control border-end-0 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Nhập ít nhất 6 ký tự..." required autofocus>
                            <button type="button" class="btn btn-toggle-pwd rounded-end-3" onclick="togglePasswordVisibility('password', this)" title="Hiện/Ẩn mật khẩu">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label extra-small fw-semibold text-secondary mb-1">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="input-group reset-input-group">
                            <span class="input-group-text border-end-0 rounded-start-3"><i class="fa-solid fa-check-double"></i></span>
                            <input type="password" class="form-control reset-input-control border-end-0" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu mới..." required>
                            <button type="button" class="btn btn-toggle-pwd rounded-end-3" onclick="togglePasswordVisibility('password_confirmation', this)" title="Hiện/Ẩn mật khẩu">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Realtime Password Requirements Indicator -->
                    <div class="bg-light p-2.5 rounded-3 mb-4 border border-light-subtle">
                        <div class="pwd-requirement-item mb-1" id="req-length">
                            <i class="fa-regular fa-circle-check" id="icon-length"></i>
                            <span>Mật khẩu chứa ít nhất 6 ký tự</span>
                        </div>
                        <div class="pwd-requirement-item" id="req-match">
                            <i class="fa-regular fa-circle-check" id="icon-match"></i>
                            <span>Mật khẩu xác nhận trùng khớp</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-reset-submit" id="btnSubmitReset">
                            <i class="fa-solid fa-key"></i>
                            <span>Đặt lại mật khẩu</span>
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="back-link">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle Password Visibility (Hiện / Ẩn Mật khẩu)
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye', 'fa-regular');
            icon.classList.add('fa-eye-slash', 'fa-solid');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash', 'fa-solid');
            icon.classList.add('fa-eye', 'fa-regular');
        }
    }

    // Realtime Validation cho Password & Confirm Password
    document.addEventListener('DOMContentLoaded', function() {
        const pwdInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const reqLength = document.getElementById('req-length');
        const iconLength = document.getElementById('icon-length');
        const reqMatch = document.getElementById('req-match');
        const iconMatch = document.getElementById('icon-match');
        const form = document.getElementById('formResetPassword');
        const btnSubmit = document.getElementById('btnSubmitReset');

        function validateForm() {
            const pwd = pwdInput.value;
            const confirm = confirmInput.value;

            // 1. Kiểm tra độ dài >= 6
            const isLengthValid = pwd.length >= 6;
            if (isLengthValid) {
                reqLength.classList.add('valid');
                iconLength.className = 'fa-solid fa-circle-check text-success';
            } else {
                reqLength.classList.remove('valid');
                iconLength.className = 'fa-regular fa-circle-check';
            }

            // 2. Kiểm tra khớp mật khẩu
            const isMatchValid = confirm.length > 0 && confirm === pwd;
            if (isMatchValid) {
                reqMatch.classList.add('valid');
                iconMatch.className = 'fa-solid fa-circle-check text-success';
            } else {
                reqMatch.classList.remove('valid');
                iconMatch.className = 'fa-regular fa-circle-check';
            }
        }

        pwdInput.addEventListener('input', validateForm);
        confirmInput.addEventListener('input', validateForm);

        // Loading State khi submit Form
        if (form && btnSubmit) {
            form.addEventListener('submit', function() {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
            });
        }
    });
</script>
@endpush
@endsection
