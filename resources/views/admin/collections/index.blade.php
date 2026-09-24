@extends('layouts.admin')

@section('title', 'Quản Lý Bộ Sưu Tập')
@section('page_title', 'Danh Sách Bộ Sưu Tập')

@section('content')
<!-- HEADER ACTION BAR -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="fa-solid fa-folder-open me-2 text-primary"></i>Quản Lý Bộ Sưu Tập (Collections)
        </h4>
        <p class="text-muted small mb-0">Quản lý các bộ sưu tập thời trang hiển thị trên Banner Trang Chủ & Trang Cửa Hàng</p>
    </div>
    <div>
        <a href="{{ route('admin.collections.create') }}" class="btn btn-primary font-weight-bold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Thêm Bộ Sưu Tập Mới
        </a>
    </div>
</div>

<!-- ALERT MESSAGES -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- FILTER BAR -->
<div class="card admin-card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.collections.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <div class="input-group">
                    <button type="submit" class="input-group-text bg-white border-end-0 text-muted btn ps-2.5 pe-2" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <input type="text" name="keyword" class="form-control border-start-0 ps-1" placeholder="Tìm theo tên bộ sưu tập, nhãn badge..." value="{{ request('keyword') }}" onkeydown="if(event.key==='Enter') this.form.submit();">
                    @if(request('keyword'))
                        <a href="{{ request()->fullUrlWithQuery(['keyword' => null]) }}" class="input-group-text bg-white text-muted border-start-0 pe-2" title="Xóa từ khóa">
                            <i class="fa-solid fa-xmark small"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit();">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hiển thị (Active)</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm ẩn (Inactive)</option>
                </select>
            </div>
            @if(request()->hasAny(['keyword', 'status']))
                <div class="col-12 col-sm-auto">
                    <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-danger px-2.5 d-flex align-items-center gap-1" title="Xóa lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Xóa lọc</span>
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- PREVIEW DEMO BANNER CARDS -->
<div class="mb-4">
    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-eye me-2 text-primary"></i>Xem Trước Giao Diện Banner Trên Trang Chủ</h6>
    <div class="row g-3">
        @forelse($collections as $col)
            <div class="col-md-6 col-lg-3">
                <div class="position-relative overflow-hidden rounded-4 shadow-sm border" style="height: 220px; background: url('{{ $col->image_url }}') center/cover no-repeat;">
                    <div class="position-absolute inset-0 p-3 d-flex flex-column justify-content-end text-white" style="background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(15,23,42,0.85) 100%); top: 0; bottom: 0; left: 0; right: 0;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge bg-warning text-dark fw-bold rounded-pill extra-small">{{ $col->badge_text ?: 'BST' }}</span>
                            <span class="badge {{ $col->is_active ? 'bg-success' : 'bg-secondary' }} extra-small">
                                {{ $col->is_active ? 'Hiển thị' : 'Đang ẩn' }}
                            </span>
                        </div>
                        <h6 class="fw-bold text-white mb-0 text-truncate">{{ $col->name }}</h6>
                        <span class="text-white-50 extra-small text-truncate">{{ $col->subtitle ?: 'Bộ sưu tập độc quyền' }}</span>
                        <div class="mt-2 pt-2 border-top border-white-10 d-flex justify-content-between align-items-center extra-small">
                            <span class="text-white-50"><i class="fa-solid fa-shirt me-1"></i>{{ $col->products_count }} sản phẩm</span>
                            <a href="{{ route('admin.collections.edit', $col->id) }}" class="btn btn-sm btn-light py-0 px-2 rounded-pill fw-bold text-dark" style="font-size: 0.75rem;">Sửa</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-muted small">Chưa có bộ sưu tập nào.</div>
        @endforelse
    </div>
</div>

<!-- COLLECTION TABLE LIST -->
<div class="card admin-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list me-2 text-primary"></i>Danh Sách Chi Tiết Các Bộ Sưu Tập</h6>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light extra-small">
                <tr>
                    <th style="width: 50px;" class="ps-3">Thứ tự</th>
                    <th style="width: 90px;">Hình ảnh</th>
                    <th>Tên Bộ Sưu Tập</th>
                    <th>Nhãn Badge</th>
                    <th>Mô tả phụ (Subtitle)</th>
                    <th class="text-center">Sản phẩm</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-end pe-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $collection)
                    <tr>
                        <td class="ps-3 fw-bold text-muted">#{{ $collection->sort_order }}</td>
                        <td>
                            <img src="{{ $collection->image_url }}" alt="{{ $collection->name }}" class="rounded-3 border object-fit-cover" style="width: 60px; height: 60px;" onerror="this.src='https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=600&q=80';">
                        </td>
                        <td>
                            <a href="{{ route('admin.collections.edit', $collection->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                {{ $collection->name }}
                            </a>
                            <small class="text-muted font-monospace">slug: {{ $collection->slug }}</small>
                        </td>
                        <td>
                            @if($collection->badge_text)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold">
                                    {{ $collection->badge_text }}
                                </span>
                            @else
                                <span class="text-muted extra-small">Không có</span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted">{{ $collection->subtitle ?: '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info border border-info-subtle font-weight-bold">
                                <i class="fa-solid fa-shirt me-1"></i>{{ $collection->products_count }} SP
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.collections.toggleStatus', $collection->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm border-0 p-0" title="Bấm để đổi trạng thái">
                                    @if($collection->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-eye me-1"></i>Hiển thị</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><i class="fa-solid fa-eye-slash me-1"></i>Đang ẩn</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.collections.edit', $collection->id) }}#section-choose-products" class="btn btn-outline-info" title="Tự chọn sản phẩm cho bộ sưu tập này">
                                    <i class="fa-solid fa-shirt me-1"></i>Chọn SP ({{ $collection->products_count }})
                                </a>
                                <a href="{{ route('admin.collections.edit', $collection->id) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                </a>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $collection->id }}" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>

                            <!-- DELETE CONFIRMATION MODAL -->
                            <div class="modal fade" id="deleteModal{{ $collection->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content text-start">
                                        <div class="modal-header border-0 pb-0">
                                            <h6 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Xác nhận xóa</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body py-3">
                                            Bạn có chắc chắn muốn xóa bộ sưu tập <strong>"{{ $collection->name }}"</strong>?
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Hủy</button>
                                            <form action="{{ route('admin.collections.destroy', $collection->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger px-3">Xóa ngay</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 text-secondary d-block"></i>
                            Chưa có bộ sưu tập nào trong hệ thống. Bấm <strong>"Thêm Bộ Sưu Tập Mới"</strong> để khởi tạo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($collections->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $collections->links() }}
        </div>
    @endif
</div>
@endsection
