@extends('layouts.admin')

@section('title', 'Thêm Màu Sắc Mới')
@section('page_title', 'Thêm Màu Sắc')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card admin-card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-palette me-2 text-primary"></i>Tạo Màu Sắc Mới</h6>
                <a href="{{ route('admin.colors.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.colors.store') }}" method="POST">
                    @csrf

                    <!-- Tên màu sắc -->
                    <div class="mb-3">
                        <label for="name" class="form-label font-weight-medium text-dark">Tên Màu Sắc <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Đen, Trắng, Xanh Navy..." required autofocus>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mã ký hiệu (Code) -->
                    <div class="mb-3">
                        <label for="code" class="form-label font-weight-medium text-dark">Mã Ký Hiệu (Code)</label>
                        <input type="text" class="form-control text-uppercase @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="Ví dụ: BLACK, WHITE, NAVY...">
                        <div class="form-text text-muted extra-small">Mã định danh nội bộ (tùy chọn).</div>
                        @error('code')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mã màu HEX -->
                    <div class="mb-3">
                        <label for="hex_code" class="form-label font-weight-medium text-dark">Mã Màu HEX <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color border-end-0" id="colorPicker" value="{{ old('hex_code', '#000000') }}" title="Chọn màu" style="width: 50px;">
                            <input type="text" class="form-control font-monospace text-uppercase @error('hex_code') is-invalid @enderror" id="hex_code" name="hex_code" value="{{ old('hex_code', '#000000') }}" placeholder="#000000" required>
                        </div>
                        @error('hex_code')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Trạng Thái Kích Hoạt -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-medium text-dark" for="is_active">Kích hoạt hiển thị màu này</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.colors.index') }}" class="btn btn-light border px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fa-solid fa-save me-1"></i> Lưu Màu Sắc
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const picker = document.getElementById('colorPicker');
        const hexInput = document.getElementById('hex_code');
        if (picker && hexInput) {
            picker.addEventListener('input', function() {
                hexInput.value = this.value.toUpperCase();
            });
            hexInput.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    picker.value = this.value;
                }
            });
        }
    });
</script>
@endpush
