@extends('layouts.admin')

@section('title', 'Thông Tin Cá Nhân & Đổi Mật Khẩu Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-user-gear me-2 text-primary"></i>Thông Tin Cá Nhân Quản Trị Viên</h4>
        <p class="text-muted small mb-0">Quản lý thông tin tài khoản và cập nhật mật khẩu đăng nhập Admin</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- CỘT TRÁI: THÔNG TIN TÀI KHOẢN -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-id-card me-2 text-primary"></i>Thông Tin Tài Khoản</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label font-weight-medium text-dark">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label font-weight-medium text-dark">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label font-weight-medium text-dark">Số điện thoại liên hệ</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $admin->phone) }}" placeholder="0987654321">
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Thay Đổi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: ĐỔI MẬT KHẨU -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-key me-2 text-warning"></i>Đổi Mật Khẩu Đăng Nhập</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.profile.changePassword') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label font-weight-medium text-dark">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label font-weight-medium text-dark">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label font-weight-medium text-dark">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-warning px-4 rounded-pill fw-bold text-dark">
                        <i class="fa-solid fa-shield-halved me-2"></i>Đổi Mật Khẩu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
