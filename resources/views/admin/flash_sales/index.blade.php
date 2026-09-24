@extends('layouts.admin')

@section('title', 'Quản lý Flash Sale')
@section('page_title', 'Quản Lý Flash Sale')

@section('content')
<div class="container-fluid">
    <!-- Header Page -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-bolt text-danger me-2"></i>Chương Trình Flash Sale
            </h4>
            <p class="text-muted small mb-0">Quản lý và thiết lập các đợt giảm giá chớp nhoáng theo thời gian thực.</p>
        </div>
        <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-danger px-4 rounded-pill fw-bold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i>Tạo đợt Flash Sale mới
        </a>
    </div>

    <!-- Stat Cards Overview (Clickable Filters) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.flash-sales.index', request()->except('status', 'page')) }}" class="text-decoration-none">
                <div class="admin-card p-3 d-flex align-items-center gap-3 transition-all {{ !request()->filled('status') ? 'border border-primary bg-primary bg-opacity-10 shadow-sm' : 'border' }}">
                    <div class="stat-card-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-semibold">Tổng Đợt Flash Sale</div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.flash-sales.index', array_merge(request()->except('page'), ['status' => 'ongoing'])) }}" class="text-decoration-none">
                <div class="admin-card p-3 d-flex align-items-center gap-3 transition-all {{ request('status') == 'ongoing' ? 'border border-success bg-success bg-opacity-10 shadow-sm' : 'border' }}">
                    <div class="stat-card-icon bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-semibold">Đang Diễn Ra</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($stats['ongoing']) }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.flash-sales.index', array_merge(request()->except('page'), ['status' => 'upcoming'])) }}" class="text-decoration-none">
                <div class="admin-card p-3 d-flex align-items-center gap-3 transition-all {{ request('status') == 'upcoming' ? 'border border-info bg-info bg-opacity-10 shadow-sm' : 'border' }}">
                    <div class="stat-card-icon bg-info bg-opacity-10 text-info">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-semibold">Sắp Diễn Ra</div>
                        <div class="fs-4 fw-bold text-info">{{ number_format($stats['upcoming']) }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.flash-sales.index', array_merge(request()->except('page'), ['status' => 'expired'])) }}" class="text-decoration-none">
                <div class="admin-card p-3 d-flex align-items-center gap-3 transition-all {{ request('status') == 'expired' ? 'border border-secondary bg-secondary bg-opacity-10 shadow-sm' : 'border' }}">
                    <div class="stat-card-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="fa-solid fa-power-off"></i>
                    </div>
                    <div>
                        <div class="text-muted extra-small text-uppercase fw-semibold">Đã Kết Thúc / Tắt</div>
                        <div class="fs-4 fw-bold text-secondary">{{ number_format($stats['expired'] + $stats['inactive']) }}</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="admin-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.flash-sales.index') }}" class="row g-3 align-items-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="Tìm kiếm theo tên sản phẩm, mã SKU... (ấn Enter để tìm)" value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('admin.flash-sales.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="input-group-text bg-light border-start-0 text-muted" title="Xóa từ khóa"><i class="fa-solid fa-xmark"></i></a>
                    @endif
                </div>
            </div>
            <div class="col-md-5 d-flex gap-2">
                <select name="status" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>⚡ Đang diễn ra</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>⏳ Sắp diễn ra</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>⌛ Đã kết thúc / Hết hàng</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>🚫 Đã tắt kích hoạt</option>
                </select>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-secondary px-3 text-nowrap d-flex align-items-center gap-1" title="Xóa toàn bộ bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Đặt lại</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Flash Sales List -->
    <div class="admin-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="ps-4">STT</th>
                        <th>Sản Phẩm</th>
                        <th>Giá Gốc</th>
                        <th>Giá Flash Sale</th>
                        <th>% Giảm</th>
                        <th>Số Lượng / Đã Bán</th>
                        <th>Khung Giờ Áp Dụng</th>
                        <th>Trạng Thái Giảm Giá</th>
                        <th style="width: 140px;">Kích Hoạt</th>
                        <th class="text-end pe-4" style="width: 160px;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flashSales as $index => $fs)
                        @php
                            $product = $fs->product;
                            $percent = $fs->discount_percent;
                            $isOngoing = $fs->is_ongoing;
                            $now = now();
                            $thumb = $product ? ($product->images->where('is_thumbnail', true)->first() ?? $product->images->first()) : null;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold text-muted small">{{ $flashSales->firstItem() + $index }}</td>
                            <td>
                                @if($product)
                                    <div class="d-flex align-items-center gap-3">
                                        @if($thumb)
                                            <img src="{{ $thumb->display_url }}" alt="{{ $product->name }}" class="rounded-3 border" style="width: 48px; height: 48px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px;">
                                                <i class="fa-solid fa-shirt text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 200px;" title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </a>
                                            <span class="badge bg-light text-secondary border extra-small">SKU: {{ $product->sku ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-danger small">Sản phẩm đã bị xóa</span>
                                @endif
                            </td>
                            <td class="text-muted small text-decoration-line-through">
                                {{ $product ? number_format($product->price) . 'đ' : '-' }}
                            </td>
                            <td class="fw-bold text-danger fs-6">
                                {{ number_format($fs->sale_price) }}đ
                            </td>
                            <td>
                                <span class="badge bg-danger text-white">-{{ $percent }}%</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small">Tối đa: {{ number_format($fs->quantity) }}</div>
                                <div class="small text-success">Đã bán: <strong>{{ number_format($fs->sold_quantity) }}</strong></div>
                                <div class="progress mt-1" style="height: 4px; width: 90px;">
                                    @php
                                        $soldRatio = $fs->quantity > 0 ? min(100, round(($fs->sold_quantity / $fs->quantity) * 100)) : 0;
                                    @endphp
                                    <div class="progress-bar bg-danger" style="width: {{ $soldRatio }}%;"></div>
                                </div>
                            </td>
                            <td class="small">
                                <div class="text-dark"><i class="fa-regular fa-calendar-check text-primary me-1"></i>Từ: {{ $fs->start_time ? $fs->start_time->format('d/m/Y H:i') : '-' }}</div>
                                <div class="text-muted"><i class="fa-regular fa-calendar-xmark text-danger me-1"></i>Đến: {{ $fs->end_time ? $fs->end_time->format('d/m/Y H:i') : '-' }}</div>
                            </td>
                            <td class="status-badge-cell">
                                @if(!$fs->is_active)
                                    <span class="badge bg-secondary text-white rounded-pill px-2 py-1"><i class="fa-solid fa-power-off me-1"></i>Đã tắt</span>
                                @elseif($isOngoing)
                                    <span class="badge bg-success text-white rounded-pill px-2 py-1"><i class="fa-solid fa-bolt me-1"></i>Đang diễn ra</span>
                                @elseif($now < $fs->start_time)
                                    <span class="badge bg-info text-dark rounded-pill px-2 py-1"><i class="fa-regular fa-clock me-1"></i>Sắp diễn ra</span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fa-solid fa-hourglass-end me-1"></i>Đã kết thúc</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm border-0 p-0 btn-toggle-flash-status" data-id="{{ $fs->id }}" data-url="{{ route('admin.flash-sales.toggleStatus', $fs->id) }}" onclick="toggleFlashStatus(this, event)" title="Click để Bật / Tắt kích hoạt nhanh" style="cursor: pointer;">
                                    @if($fs->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fs-7 font-weight-medium" style="cursor: pointer;">
                                            <i class="fa-solid fa-circle-check me-1"></i>Kích hoạt
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 fs-7 font-weight-medium" style="cursor: pointer;">
                                            <i class="fa-solid fa-circle-xmark me-1"></i>Tắt kích hoạt
                                        </span>
                                    @endif
                                </button>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <a href="{{ route('admin.flash-sales.edit', $fs->id) }}" class="btn btn-sm btn-outline-primary px-2" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                    </a>

                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-item px-2" data-url="{{ route('admin.flash-sales.destroy', $fs->id) }}" title="Xóa">
                                        <i class="fa-solid fa-trash me-1"></i>Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="fa-solid fa-bolt-lightning fs-1 opacity-25 mb-2"></i>
                                    <p class="mb-0 fw-semibold">Không tìm thấy đợt Flash Sale nào!</p>
                                    <small class="text-muted">Vui lòng thử lại với từ khóa hoặc bộ lọc khác.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($flashSales->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center">
                <span class="small text-muted">Hiển thị {{ $flashSales->firstItem() }} - {{ $flashSales->lastItem() }} trên {{ $flashSales->total() }} đợt Flash Sale</span>
                {{ $flashSales->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Hidden form for toggle status fallback -->
<form id="toggle-status-form" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="is_active" id="status-is-active" value="1">
</form>

<!-- Hidden form for single delete item -->
<form id="single-delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    function toggleFlashStatus(btnEl, e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const btn = $(btnEl);
        const url = btn.data('url');
        const row = btn.closest('tr');
        const badgeCell = row.find('.status-badge-cell');

        btn.prop('disabled', true).css('opacity', '0.6');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT'
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function(res) {
                btn.prop('disabled', false).css('opacity', '1');

                // Cập nhật nút trạng thái kích hoạt
                if (res.is_active) {
                    btn.html(`
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fs-7 font-weight-medium" style="cursor: pointer;">
                            <i class="fa-solid fa-circle-check me-1"></i>Kích hoạt
                        </span>
                    `);
                } else {
                    btn.html(`
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 fs-7 font-weight-medium" style="cursor: pointer;">
                            <i class="fa-solid fa-circle-xmark me-1"></i>Tắt kích hoạt
                        </span>
                    `);
                }

                // Cập nhật badge cột Trạng Thái Giảm Giá
                if (!res.is_active) {
                    badgeCell.html('<span class="badge bg-secondary text-white rounded-pill px-2 py-1"><i class="fa-solid fa-power-off me-1"></i>Đã tắt</span>');
                } else if (res.is_ongoing) {
                    badgeCell.html('<span class="badge bg-success text-white rounded-pill px-2 py-1"><i class="fa-solid fa-bolt me-1"></i>Đang diễn ra</span>');
                } else if (res.is_upcoming) {
                    badgeCell.html('<span class="badge bg-info text-dark rounded-pill px-2 py-1"><i class="fa-regular fa-clock me-1"></i>Sắp diễn ra</span>');
                } else {
                    badgeCell.html('<span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fa-solid fa-hourglass-end me-1"></i>Đã kết thúc</span>');
                }

                // Toast SweetAlert
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: res.message || 'Cập nhật trạng thái thành công!'
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).css('opacity', '1');
                let errMsg = 'Đã có lỗi xảy ra khi cập nhật.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: errMsg
                    });
                } else {
                    alert(errMsg);
                }
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Bật / Tắt kích hoạt Flash Sale nhanh 1-Click (AJAX delegation fallback)
        $(document).on('click', '.btn-toggle-flash-status', function(e) {
            toggleFlashStatus(this, e);
        });

        // Single delete confirmation handler
        document.querySelectorAll('.btn-delete-item').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: "Bạn có chắc chắn muốn xóa đợt Flash Sale này không?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Đồng ý xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('single-delete-form');
                        form.action = url;
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
