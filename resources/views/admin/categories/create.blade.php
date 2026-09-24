@extends('layouts.admin')

@section('title', 'Thêm Danh Mục Mới')
@section('page_title', 'Thêm Danh Mục')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card admin-card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-folder-plus me-2 text-primary"></i>Tạo Danh Mục Sản Phẩm Mới
                </h6>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3 mb-3">
                        <!-- Tên danh mục -->
                        <div class="col-md-6">
                            <label for="name" class="form-label font-weight-medium">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Đồ ngủ Satin, Áo sơ mi nam, Đồ thể thao..." required autofocus>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Đường dẫn SEO (Slug) -->
                        <div class="col-md-6">
                            <label for="slug" class="form-label font-weight-medium">Đường dẫn SEO (Slug)</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="Tự động sinh từ tên nếu để trống">
                            @error('slug')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Cấp Phân Danh Mục (Cha / Con) -->
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label for="parent_id" class="form-label font-weight-bold text-dark mb-1">
                            <i class="fa-solid fa-sitemap me-1 text-primary"></i> Cấp Phân Danh Mục
                        </label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="">📁 Tạo làm Danh Mục Gốc (Danh Mục Cha)</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $selectedParentId ?? '') == $parent->id ? 'selected' : '' }}>
                                    ↳ Trở thành Danh Mục Con của: {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted extra-small mt-2">
                            <i class="fa-solid fa-circle-info me-1 text-info"></i>
                            Nếu giữ nguyên <b>"Tạo làm Danh Mục Gốc"</b>, mục này sẽ là Danh Mục Cha cấp cao nhất.
                        </div>
                        @error('parent_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-light border px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Danh Mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
