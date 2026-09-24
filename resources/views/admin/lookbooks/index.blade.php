@extends('layouts.admin')

@section('title', 'Quản Lý Lookbook')
@section('page_title', 'Quản Lý Lookbook (@ATINO_STYLE)')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-camera text-danger me-2"></i>Danh Sách Lookbook / Instagram Gallery
            </h4>
            <p class="text-muted small mb-0">Quản lý các hình ảnh phối đồ Lookbook hiển thị trên trang chủ và liên kết sản phẩm đi kèm.</p>
        </div>
        <a href="{{ route('admin.lookbooks.create') }}" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Thêm Lookbook Mới
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="card admin-card mb-4 border-0 shadow-sm rounded-3">
        <div class="card-body p-3">
            <form action="{{ route('admin.lookbooks.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Tìm theo tiêu đề, phụ đề..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select bg-light">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hiển thị</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã ẩn</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-3">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.lookbooks.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                            <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Lookbook Grid Cards -->
    <div class="row g-4 mb-4">
        @forelse($lookbooks as $item)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card admin-card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                    <!-- Image Preview -->
                    <div class="position-relative overflow-hidden" style="height: 240px; background-color: #0f172a;">
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-100 h-100" style="object-fit: cover;">
                        
                        <!-- Badges -->
                        <span class="position-absolute top-0 start-0 m-2 badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill shadow-sm">
                            <i class="fa-solid {{ $item->is_active ? 'fa-eye me-1' : 'fa-eye-slash me-1' }}"></i>
                            {{ $item->is_active ? 'Hiển thị' : 'Đã ẩn' }}
                        </span>

                        <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 text-warning rounded-pill shadow-sm">
                            <i class="fa-solid fa-sort me-1"></i> Thứ tự: {{ $item->sort_order }}
                        </span>

                        <div class="position-absolute bottom-0 start-0 end-0 p-2 bg-dark bg-opacity-75 text-white text-center small fw-bold">
                            <i class="fa-solid fa-bag-shopping me-1 text-warning"></i>
                            {{ $item->button_text ?: ('Xem & Mua ' . $item->products_count . ' Sản Phẩm') }}
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $item->title }}">
                                {{ $item->title }}
                            </h6>
                            @if($item->subtitle)
                                <p class="text-muted extra-small mb-2 text-truncate">{{ $item->subtitle }}</p>
                            @endif

                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="small text-secondary fw-semibold">
                                    <i class="fa-solid fa-shirt text-primary me-1"></i> {{ $item->products_count }} sản phẩm gắn kèm
                                </span>
                                @if($item->instagram_url)
                                    <a href="{{ $item->instagram_url }}" target="_blank" class="text-danger small" title="Xem trên Instagram">
                                        <i class="fa-brands fa-instagram fs-6"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                            <form action="{{ route('admin.lookbooks.toggleStatus', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-2.5" title="Đổi trạng thái hiển thị">
                                    <i class="fa-solid {{ $item->is_active ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>
                                    {{ $item->is_active ? 'Ẩn' : 'Hiện' }}
                                </button>
                            </form>

                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('admin.lookbooks.edit', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Sửa
                                </a>

                                <form action="{{ route('admin.lookbooks.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Lookbook này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;" title="Xóa">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card admin-card border-0 shadow-sm p-5 text-center rounded-4">
                    <i class="fa-solid fa-camera-retro fs-1 text-muted mb-3 opacity-50"></i>
                    <h5 class="fw-bold text-dark">Chưa có Lookbook nào</h5>
                    <p class="text-muted small mb-3">Hãy tạo Lookbook phối đồ đầu tiên để hiển thị trên trang chủ cửa hàng.</p>
                    <div>
                        <a href="{{ route('admin.lookbooks.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="fa-solid fa-plus me-1"></i> Thêm Lookbook Mới
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($lookbooks->hasPages())
        <div class="d-flex justify-content-center">
            {{ $lookbooks->links() }}
        </div>
    @endif
</div>
@endsection
