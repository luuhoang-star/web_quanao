@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Mã Giảm Giá')
@section('page_title', 'Chỉnh Sửa Mã Giảm Giá')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card admin-card shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Chỉnh Sửa Mã Giảm Giá: {{ $coupon->code }}</h6>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Mã Coupon -->
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label font-weight-medium">Mã Giảm Giá (Code) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase font-monospace fw-bold @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $coupon->code) }}" required>
                            @error('code')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Loại giảm giá -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label font-weight-medium">Loại Giảm Giá <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>% Giảm phần trăm</option>
                                <option value="fixed_amount" {{ old('type', $coupon->type) == 'fixed_amount' ? 'selected' : '' }}>💵 Giảm số tiền cố định (VNĐ)</option>
                                <option value="free_shipping" {{ old('type', $coupon->type) == 'free_shipping' ? 'selected' : '' }}>🚚 Miễn phí vận chuyển</option>
                            </select>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Giá trị giảm -->
                        <div class="col-md-6 mb-3">
                            <label for="value" class="form-label font-weight-medium">Giá trị giảm <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $coupon->value) }}" required>
                            @error('value')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Đơn hàng tối thiểu -->
                        <div class="col-md-6 mb-3">
                            <label for="min_order_value" class="form-label font-weight-medium">Đơn hàng tối thiểu (VNĐ)</label>
                            <input type="number" step="1000" min="0" class="form-control @error('min_order_value') is-invalid @enderror" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', $coupon->min_order_value) }}">
                            @error('min_order_value')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Giảm tối đa (Áp dụng cho giảm %) -->
                        <div class="col-md-6 mb-3">
                            <label for="max_discount_amount" class="form-label font-weight-medium">Giảm tối đa (Áp dụng cho %)</label>
                            <input type="number" step="1000" min="0" class="form-control @error('max_discount_amount') is-invalid @enderror" id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}">
                            @error('max_discount_amount')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Giới hạn số lượt sử dụng -->
                        <div class="col-md-6 mb-3">
                            <label for="usage_limit" class="form-label font-weight-medium">Giới hạn số lượt sử dụng</label>
                            <input type="number" min="1" class="form-control @error('usage_limit') is-invalid @enderror" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                            @error('usage_limit')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ngày bắt đầu -->
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label font-weight-medium">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $coupon->start_date->format('Y-m-d\TH:i')) }}" required>
                            @error('start_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ngày kết thúc -->
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label font-weight-medium">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $coupon->end_date->format('Y-m-d\TH:i')) }}" required>
                            @error('end_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-light border px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Cập Nhật Mã Giảm Giá
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
