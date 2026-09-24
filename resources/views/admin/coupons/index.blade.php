@extends('layouts.admin')

@section('title', 'Quản Lý Mã Giảm Giá')
@section('page_title', 'Mã Giảm Giá (Coupons)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-ticket text-primary me-2"></i>Quản Lý Mã Giảm Giá</h5>
        <p class="text-muted small mb-0">Quản lý các chương trình ưu đãi, mã khuyến mãi và miễn phí vận chuyển</p>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary rounded-3">
        <i class="fa-solid fa-plus me-1"></i> Thêm Mã Mới
    </a>
</div>

<!-- Bộ lọc tìm kiếm -->
<div class="card admin-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.coupons.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo mã giảm giá (Code)..." value="{{ request('keyword') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="type" class="form-select">
                    <option value="">-- Tất cả loại giảm giá --</option>
                    <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>% Giảm phần trăm</option>
                    <option value="fixed_amount" {{ request('type') == 'fixed_amount' ? 'selected' : '' }}>💵 Giảm số tiền cố định</option>
                    <option value="free_shipping" {{ request('type') == 'free_shipping' ? 'selected' : '' }}>🚚 Miễn phí vận chuyển</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
                @if(request()->hasAny(['keyword', 'type']))
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-light border" title="Xóa bộ lọc"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Bảng danh sách mã giảm giá -->
<div class="card admin-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light extra-small">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Mã Coupon</th>
                    <th>Loại & Giá Trị Giảm</th>
                    <th>Đơn Tối Thiểu</th>
                    <th>Lượt Sử Dụng</th>
                    <th>Thời Gian Hiệu Lực</th>
                    <th>Trạng Thái</th>
                    <th class="text-end pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#{{ $coupon->id }}</td>
                        <td>
                            <code class="fs-6 fw-bold text-primary px-2 py-1 bg-primary-subtle rounded border border-primary-subtle">
                                <i class="fa-solid fa-ticket me-1"></i>{{ $coupon->code }}
                            </code>
                        </td>
                        <td>
                            @if($coupon->type === 'percent')
                                <span class="badge bg-warning text-dark border me-1">Giảm {{ number_format($coupon->value) }}%</span>
                                @if($coupon->max_discount_amount)
                                    <span class="extra-small text-muted d-block mt-1">Tối đa: {{ number_format($coupon->max_discount_amount) }}đ</span>
                                @endif
                            @elseif($coupon->type === 'fixed_amount')
                                <span class="badge bg-success text-white me-1">Giảm {{ number_format($coupon->value) }}đ</span>
                            @else
                                <span class="badge bg-info text-white me-1">Miễn phí ship</span>
                            @endif
                        </td>
                        <td class="small fw-semibold text-dark">
                            {{ number_format($coupon->min_order_value) }}đ
                        </td>
                        <td class="small">
                            <span class="fw-bold text-dark">{{ $coupon->used_count }}</span> / 
                            <span class="text-muted">{{ $coupon->usage_limit ?: '∞' }}</span>
                        </td>
                        <td class="extra-small text-muted">
                            <div><i class="fa-regular fa-calendar-check me-1 text-success"></i>{{ $coupon->start_date->format('d/m/Y') }}</div>
                            <div><i class="fa-regular fa-calendar-xmark me-1 text-danger"></i>{{ $coupon->end_date->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <button type="button" 
                                class="btn btn-sm border-0 p-0 btn-toggle-coupon-status" 
                                data-id="{{ $coupon->id }}" 
                                data-url="{{ route('admin.coupons.toggleStatus', $coupon->id) }}" 
                                title="Click để chuyển đổi trạng thái Bật (Đang chạy) / Tắt (Đang ẩn)"
                                style="cursor: pointer;">
                                @if($coupon->is_active && $coupon->end_date->isFuture())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">● Đang chạy</span>
                                @elseif(!$coupon->is_active)
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">● Đang ẩn</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">● Hết hạn</span>
                                @endif
                            </button>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-delete-coupon" data-id="{{ $coupon->id }}" data-code="{{ $coupon->code }}" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                            <form id="delete-form-{{ $coupon->id }}" action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-ticket fa-2x mb-2 text-secondary d-block"></i>
                            Chưa có mã giảm giá nào trong hệ thống.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($coupons->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $coupons->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.btn-delete-coupon').on('click', function() {
            const couponId = $(this).data('id');
            const couponCode = $(this).data('code');

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: `Bạn có chắc chắn muốn xóa mã giảm giá "${couponCode}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#delete-form-${couponId}`).submit();
                }
            });
        });

        // Click để chuyển đổi trạng thái Bật/Tắt coupon trực tiếp qua AJAX
        $(document).on('click', '.btn-toggle-coupon-status', function(e) {
            e.preventDefault();
            const btn = $(this);
            const url = btn.data('url');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.is_active) {
                            if (response.is_expired) {
                                btn.html('<span class="badge bg-danger-subtle text-danger border border-danger-subtle">● Hết hạn</span>');
                            } else {
                                btn.html('<span class="badge bg-success-subtle text-success border border-success-subtle">● Đang chạy</span>');
                            }
                        } else {
                            btn.html('<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">● Đang ẩn</span>');
                        }

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                    } else {
                        Swal.fire('Lỗi!', response.message || 'Không thể đổi trạng thái mã giảm giá.', 'error');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Đã xảy ra lỗi khi đổi trạng thái mã giảm giá!';
                    Swal.fire('Lỗi!', msg, 'error');
                }
            });
        });
    });
</script>
@endpush
