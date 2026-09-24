@extends('layouts.admin')

@section('title', 'Quản Lý Outfit Phối Sẵn')
@section('page_title', 'Quản Lý Outfit Phối Sẵn')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-layer-group text-warning me-2"></i>Danh Sách Outfit Phối Sẵn Gợi Ý
            </h4>
            <p class="text-muted small mb-0">Quản lý các bộ trang phục phối sẵn hiển thị trên trang chủ và trang chi tiết Outfit.</p>
        </div>
        <a href="{{ route('admin.outfits.create') }}" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Tạo Outfit Mới
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="card admin-card mb-4 border-0 shadow-sm rounded-3">
        <div class="card-body p-3">
            <form action="{{ route('admin.outfits.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Tìm theo tên outfit, badge, tagline..." value="{{ request('search') }}">
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
                        <a href="{{ route('admin.outfits.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                            <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Outfits Grid Cards -->
    <div class="row g-4 mb-4">
        @forelse($outfits as $item)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card admin-card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative d-flex flex-column justify-content-between">
                    <!-- Image Banner & Badge -->
                    <div class="position-relative overflow-hidden" style="height: 200px; background: {{ $item->gradient ?: 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)' }};">
                        <img src="{{ $item->hero_image_url }}" alt="{{ $item->name }}" class="w-100 h-100 opacity-75" style="object-fit: cover;">
                        
                        <div class="position-absolute top-0 start-0 end-0 p-3 d-flex justify-content-between align-items-start">
                            <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 extra-small fw-bold border border-secondary shadow-sm">
                                {{ $item->badge ?: 'OUTFIT' }}
                            </span>

                            <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill shadow-sm">
                                {{ $item->is_active ? 'Hiển thị' : 'Đã ẩn' }}
                            </span>
                        </div>

                        <div class="position-absolute bottom-0 start-0 end-0 p-3 text-white" style="background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(15,23,42,0.9) 100%);">
                            <h6 class="fw-bold text-white mb-0 text-truncate">{{ $item->name }}</h6>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3 flex-grow-1 d-flex flex-column justify-content-between">
                        <div>
                            @if($item->tagline)
                                <p class="text-muted extra-small mb-2 fst-italic text-truncate">{{ $item->tagline }}</p>
                            @endif

                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <span class="small text-secondary fw-semibold">
                                    <i class="fa-solid fa-shirt text-primary me-1"></i> {{ $item->products_count }} sản phẩm trong outfit
                                </span>
                                <span class="extra-small text-muted">
                                    <i class="fa-solid fa-sort me-1"></i> Thứ tự: {{ $item->sort_order }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                            <form action="{{ route('admin.outfits.toggleStatus', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-2.5" title="Đổi trạng thái">
                                    <i class="fa-solid {{ $item->is_active ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i>
                                    {{ $item->is_active ? 'Ẩn' : 'Hiện' }}
                                </button>
                            </form>

                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('admin.outfits.edit', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Sửa
                                </a>

                                <form action="{{ route('admin.outfits.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Outfit này?');">
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
                    <i class="fa-solid fa-layer-group fs-1 text-muted mb-3 opacity-50"></i>
                    <h5 class="fw-bold text-dark">Chưa có Outfit nào</h5>
                    <p class="text-muted small mb-3">Hãy tạo bộ Outfit phối sẵn đầu tiên để đề xuất cho khách hàng trên trang chủ.</p>
                    <div>
                        <a href="{{ route('admin.outfits.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="fa-solid fa-plus me-1"></i> Tạo Outfit Mới
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($outfits->hasPages())
        <div class="d-flex justify-content-center">
            {{ $outfits->links() }}
        </div>
    @endif
</div>
@endsection
