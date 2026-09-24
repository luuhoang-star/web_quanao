@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng')
@section('page_title', 'Quản Lý Đơn Hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-cart-flatbed text-primary me-2"></i>Quản Lý Đơn Hàng</h5>
        <p class="text-muted small mb-0">Theo dõi, xử lý và cập nhật tiến trình đơn hàng của khách hàng toàn hệ thống</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-sm btn-success rounded-pill px-3 py-2 fw-bold shadow-sm" title="Tải file Excel/CSV theo bộ lọc hiện tại">
            <i class="fa-solid fa-file-excel me-1.5"></i> Xuất Báo Cáo Excel / CSV
        </a>
    </div>
</div>

<!-- Nav tabs bộ lọc trạng thái cuộn ngang mượt mà không bị rớt dòng -->
<div class="card admin-card mb-4 border-0 shadow-sm">
    <div class="card-body p-2">
        <div class="d-flex flex-nowrap gap-2 overflow-x-auto py-1 order-status-tabs" style="scrollbar-width: thin;">
            <a href="{{ route('admin.orders.index', request()->except('status', 'page')) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ !request()->filled('status') ? 'btn-dark text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>Tất Cả</span>
                <span class="badge {{ !request()->filled('status') ? 'bg-white text-dark' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['all'] }}</span>
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'pending' ? 'btn-warning text-dark fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>⏳ Chờ Xử Lý</span>
                <span class="badge {{ request('status') == 'pending' ? 'bg-dark text-white' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['pending'] }}</span>
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'confirmed'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'confirmed' ? 'btn-info text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>✅ Đã Xác Nhận</span>
                <span class="badge {{ request('status') == 'confirmed' ? 'bg-white text-info' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['confirmed'] }}</span>
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'processing'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'processing' ? 'btn-secondary text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>📦 Đang Chuẩn Bị</span>
                <span class="badge {{ request('status') == 'processing' ? 'bg-white text-secondary' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['processing'] }}</span>
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'shipping'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'shipping' ? 'btn-primary text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>🚚 Đang Giao</span>
                <span class="badge {{ request('status') == 'shipping' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['shipping'] }}</span>
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'completed' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>🎉 Hoàn Thành</span>
                <span class="badge {{ request('status') == 'completed' ? 'bg-white text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['completed'] }}</span>
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'cancelled'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'cancelled' ? 'btn-danger text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>❌ Đã Hủy</span>
                <span class="badge {{ request('status') == 'cancelled' ? 'bg-white text-danger' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['cancelled'] }}</span>
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'returned'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'returned' ? 'btn-dark text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>↩️ Trả Hàng</span>
                <span class="badge {{ request('status') == 'returned' ? 'bg-white text-dark' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['returned'] }}</span>
            </a>
        </div>
    </div>
</div>

<!-- Bộ lọc tìm kiếm thông minh tự động lọc -->
<div class="card admin-card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center">
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0 bg-light" placeholder="Tìm theo mã đơn, người nhận, SĐT... (ấn Enter để tìm)" value="{{ request('keyword') }}">
                    @if(request('keyword'))
                        <a href="{{ route('admin.orders.index', request()->except('keyword', 'page')) }}" class="input-group-text bg-light border-start-0 text-muted" title="Xóa từ khóa"><i class="fa-solid fa-xmark"></i></a>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <select name="payment_method" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Phương thức thanh toán --</option>
                    <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>💵 COD (Tiền mặt)</option>
                    <option value="vnpay" {{ request('payment_method') == 'vnpay' ? 'selected' : '' }}>💳 VNPAY (Trực tuyến)</option>
                </select>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <select name="payment_status" class="form-select bg-light" onchange="this.form.submit()">
                    <option value="">-- Trạng thái thanh toán --</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>🟢 Đã thanh toán</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>🟡 Chưa thanh toán</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>🔵 Đã hoàn tiền</option>
                </select>

                @if(request()->anyFilled(['keyword', 'payment_method', 'payment_status', 'status']))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary px-3 text-nowrap d-flex align-items-center gap-1" title="Đặt lại toàn bộ bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Đặt lại</span>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Bảng danh sách đơn hàng -->
<div class="card admin-card overflow-hidden border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light extra-small">
                <tr>
                    <th class="ps-3" style="width: 140px;">Mã Đơn Hàng</th>
                    <th>Ngày Đặt</th>
                    <th>Khách Hàng / Người Nhận</th>
                    <th>Tổng Tiền & Thanh Toán</th>
                    <th style="min-width: 220px;">Trạng Thái Đơn Hàng</th>
                    <th class="text-end pe-3" style="width: 160px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $payment = $order->payment;
                        $paymentMethod = $payment ? strtoupper($payment->payment_method) : 'COD';
                        $paymentStatus = $payment ? $payment->payment_status : 'unpaid';

                        $statusBadgeClasses = [
                            'pending' => 'border-warning text-warning-emphasis bg-warning-subtle',
                            'confirmed' => 'border-info text-info-emphasis bg-info-subtle',
                            'processing' => 'border-secondary text-secondary-emphasis bg-secondary-subtle',
                            'shipping' => 'border-primary text-primary-emphasis bg-primary-subtle',
                            'completed' => 'border-success text-success-emphasis bg-success-subtle',
                            'cancelled' => 'border-danger text-danger-emphasis bg-danger-subtle',
                            'returned' => 'border-dark text-dark-emphasis bg-dark-subtle',
                        ];
                        $currentBadgeClass = $statusBadgeClasses[$order->status] ?? 'border-secondary bg-light text-dark';
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none text-primary fs-6" title="Xem chi tiết đơn">
                                #{{ $order->order_code }}
                            </a>
                        </td>
                        <td class="small text-muted">
                            <div><i class="fa-regular fa-calendar me-1"></i>{{ $order->created_at->format('d/m/Y') }}</div>
                            <div class="extra-small text-secondary">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-0">{{ $order->recipient_name }}</div>
                            <span class="extra-small text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $order->recipient_phone }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-danger fs-6 mb-1">{{ number_format($order->total_amount) }}đ</div>
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <span class="badge bg-light text-dark border extra-small">{{ $paymentMethod }}</span>
                                @if($paymentStatus === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small">
                                        <i class="fa-solid fa-circle-check me-1"></i>Đã TT
                                    </span>
                                @elseif($paymentStatus === 'refunded')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle extra-small">
                                        <i class="fa-solid fa-rotate-left me-1"></i>Đã hoàn tiền
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small">
                                        <i class="fa-regular fa-clock me-1"></i>Chưa TT
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <!-- Cập nhật trạng thái nhanh gọn (Tích hợp AJAX) -->
                            <div class="order-status-wrapper">
                                <select class="form-select form-select-sm fw-semibold order-status-select {{ $currentBadgeClass }}" 
                                        data-url="{{ route('admin.orders.updateStatus', $order->id) }}"
                                        data-order-code="{{ $order->order_code }}">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>✅ Đã xác nhận</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>📦 Đang chuẩn bị hàng</option>
                                    <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>🚚 Đang giao hàng</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>🎉 Giao thành công</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Đã hủy</option>
                                    <option value="returned" {{ $order->status == 'returned' ? 'selected' : '' }}>↩️ Trả hàng</option>
                                </select>
                                <div class="extra-small text-muted mt-1 status-updated-time" style="font-size: 0.72rem;">
                                    <i class="fa-regular fa-clock me-1"></i>Cập nhật: {{ $order->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-inline-flex gap-1.5">
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-dark px-2" title="In Hóa đơn / Phiếu gửi hàng">
                                    <i class="fa-solid fa-print me-1"></i>In
                                </a>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary px-2" title="Xem chi tiết đơn hàng">
                                    <i class="fa-solid fa-eye me-1"></i>Xem
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-cart-flatbed fa-2x mb-2 text-secondary d-block"></i>
                            <div class="fw-semibold">Không tìm thấy đơn hàng nào!</div>
                            <small class="text-muted">Vui lòng thử lại với bộ lọc hoặc từ khóa khác.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">Hiển thị {{ $orders->firstItem() }} - {{ $orders->lastItem() }} trên {{ $orders->total() }} đơn hàng</span>
            {{ $orders->links() }}
        </div>
    @endif
</div>

<!-- Form fallback cho trình duyệt không hỗ trợ JS -->
<form id="fallback-status-form" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="fallback-status-val">
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const statusClasses = {
            'pending': 'border-warning text-warning-emphasis bg-warning-subtle',
            'confirmed': 'border-info text-info-emphasis bg-info-subtle',
            'processing': 'border-secondary text-secondary-emphasis bg-secondary-subtle',
            'shipping': 'border-primary text-primary-emphasis bg-primary-subtle',
            'completed': 'border-success text-success-emphasis bg-success-subtle',
            'cancelled': 'border-danger text-danger-emphasis bg-danger-subtle',
            'returned': 'border-dark text-dark-emphasis bg-dark-subtle'
        };

        // Khởi tạo trạng thái hiện tại
        $('.order-status-select').each(function() {
            $(this).data('current-status', $(this).val());
        });

        // AJAX update status on change (Phản hồi tức thì)
        $('.order-status-select').on('change', function() {
            const select = $(this);
            const url = select.data('url');
            const newStatus = select.val();
            const previousStatus = select.data('current-status') || newStatus;
            const orderCode = select.data('order-code');
            const wrapper = select.closest('.order-status-wrapper');
            const timeContainer = wrapper.find('.status-updated-time');

            // Cập nhật màu sắc giao diện ngay lập tức (Optimistic UI)
            Object.values(statusClasses).forEach(cls => {
                cls.split(' ').forEach(c => select.removeClass(c));
            });
            if (statusClasses[newStatus]) {
                statusClasses[newStatus].split(' ').forEach(c => select.addClass(c));
            }

            select.prop('disabled', true).css('opacity', '0.7');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    status: newStatus
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(res) {
                    select.prop('disabled', false).css('opacity', '1');
                    select.data('current-status', newStatus);

                    // Cập nhật ngày giờ
                    if (res.updated_at) {
                        timeContainer.html('<i class="fa-regular fa-clock me-1"></i>Cập nhật: ' + res.updated_at);
                    }

                    // Toast thông báo thành công
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
                            title: res.message || ('Cập nhật đơn #' + orderCode + ' thành công!')
                        });
                    }
                },
                error: function(xhr) {
                    select.prop('disabled', false).css('opacity', '1');

                    // Hoàn tác về trạng thái cũ nếu xảy ra lỗi
                    select.val(previousStatus);
                    Object.values(statusClasses).forEach(cls => {
                        cls.split(' ').forEach(c => select.removeClass(c));
                    });
                    if (statusClasses[previousStatus]) {
                        statusClasses[previousStatus].split(' ').forEach(c => select.addClass(c));
                    }

                    let errMsg = 'Đã có lỗi xảy ra khi cập nhật trạng thái.';
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
        });
    });
</script>
@endpush
