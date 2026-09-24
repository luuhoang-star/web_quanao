@extends('layouts.app')

@section('title', 'Quên Mật Khẩu - ATINO Store')

@push('styles')
<style>
    .forgot-card-wrapper {
        max-width: 450px;
        margin: 0 auto;
    }

    .forgot-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
        overflow: hidden;
    }

    .forgot-card-header {
        background-color: #0f172a !important;
        padding: 1.25rem 1.5rem !important;
        text-align: center;
    }

    .forgot-icon-circle {
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

    .forgot-input-group .input-group-text {
        background-color: #ffffff;
        border-color: #cbd5e1;
        color: #64748b;
        padding-left: 0.85rem;
        padding-right: 0.85rem;
    }

    .forgot-input-control {
        border-color: #cbd5e1 !important;
        font-size: 0.875rem !important;
        padding: 0.6rem 0.85rem !important;
        color: #0f172a !important;
        transition: all 0.2s ease !important;
    }

    .forgot-input-control:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08) !important;
        outline: none !important;
    }

    .btn-forgot-submit {
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

    .btn-forgot-submit:hover:not(:disabled) {
        background-color: #1e293b !important;
        border-color: #1e293b !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.18) !important;
    }

    .btn-forgot-submit:disabled {
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
    <div class="forgot-card-wrapper">
        <div class="card forgot-card">
            <!-- Compact Header -->
            <div class="forgot-card-header">
                <div class="forgot-icon-circle">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h5 class="fw-bold text-white mb-1 fs-5">Quên Mật Khẩu?</h5>
                <span class="extra-small text-white opacity-75">Nhập địa chỉ Gmail của bạn để nhận link đặt lại mật khẩu</span>
            </div>

            <div class="card-body p-3.5 p-md-4">
                <!-- Session Alerts -->
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 extra-small mb-3" role="alert">
                        <i class="fa-solid fa-circle-check me-1.5"></i>{{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('simulated_reset_url'))
                    <div class="alert alert-info rounded-3 extra-small mb-3 border-info">
                        <div class="fw-bold mb-1"><i class="fa-solid fa-envelope-circle-check me-1"></i> [DEV TEST] Link khôi phục Gmail:</div>
                        <div class="text-truncate extra-small bg-white p-2 rounded border mb-2 select-all">
                            <code>{{ session('simulated_reset_url') }}</code>
                        </div>
                        <a href="{{ session('simulated_reset_url') }}" class="btn btn-sm btn-dark w-100 rounded-pill fw-bold extra-small">
                            <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Mở link đặt lại mật khẩu ngay
                        </a>
                    </div>
                @endif

                <form id="formForgotPassword" action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email" class="form-label extra-small fw-semibold text-secondary mb-1">Địa chỉ Email / Gmail <span class="text-danger">*</span></label>
                        <div class="input-group forgot-input-group">
                            <span class="input-group-text border-end-0 rounded-start-3"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" class="form-control forgot-input-control rounded-end-3 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="vi-du: emailcua-ban@gmail.com" required autofocus>
                        </div>
                        @error('email')
                            <div class="text-danger extra-small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-forgot-submit" id="btnSubmitForgot">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Gửi link khôi phục qua Email</span>
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="text-center pt-2 border-top mt-3">
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formForgotPassword');
        const btnSubmit = document.getElementById('btnSubmitForgot');

        if (form && btnSubmit) {
            form.addEventListener('submit', function() {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi email...';
            });
        }
    });
</script>
@endpush
@endsection
