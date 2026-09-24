@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Kích Cỡ')
@section('page_title', 'Chỉnh Sửa Kích Cỡ')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card admin-card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Chỉnh Sửa Kích Cỡ: {{ $size->name }}</h6>
                <a href="{{ route('admin.sizes.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.sizes.update', $size->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Tên kích cỡ -->
                    <div class="mb-3">
                        <label for="name" class="form-label font-weight-medium text-dark">Tên Kích Cỡ (Size) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase font-weight-bold @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $size->name) }}" required autofocus>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Loại kích cỡ -->
                    <div class="mb-3">
                        <label for="size_type" class="form-label font-weight-medium text-dark">Loại Kích Cỡ <span class="text-danger">*</span></label>
                        <select class="form-select @error('size_type') is-invalid @enderror" id="size_type" name="size_type" required>
                            @foreach(\App\Models\Size::TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('size_type', $size->size_type) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted extra-small">Chọn loại: Size Chữ (Áo/Váy), Size Số (Quần) hoặc Kích cỡ tự do.</div>
                        @error('size_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Thứ Tự Hiển Thị -->
                    <div class="mb-3">
                        <label for="sort_order" class="form-label font-weight-medium text-dark">Thứ Tự Hiển Thị</label>
                        <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $size->sort_order) }}">
                        <div class="form-text text-muted extra-small">Số nhỏ hơn sẽ ưu tiên hiển thị trước.</div>
                        @error('sort_order')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Trạng Thái Hiển Thị -->
                    <div class="mb-4">
                        <label class="form-label font-weight-medium text-dark d-block">Trạng Thái</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $size->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label font-weight-medium text-dark" for="is_active">Cho phép hiển thị và sử dụng</label>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTONS -->
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('admin.sizes.index') }}" class="btn btn-light border px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Cập Nhật Kích Cỡ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
