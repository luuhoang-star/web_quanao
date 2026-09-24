@extends('layouts.admin')

@section('title', 'Chi Tiết Khách Hàng - ' . $customer->name)
@section('page_title', 'Chi Tiết Khách Hàng')

@section('content')
<!-- Header thông tin khách hàng -->
<div class="card admin-card mb-4">
    <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm" style="width: 64px; height: 64px;">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h5 class="fw-bold text-dark mb-0">{{ $customer->name }}</h5>
                    @if($customer->status === 'banned')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">Đã bị khóa</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Hoạt động</span>
                    @endif
                </div>
                <div class="small text-muted">
                    <span><i class="fa-regular fa-envelope me-1"></i>{{ $customer->email }}</span> | 
                    <span><i class="fa-solid fa-phone me-1"></i>{{ $customer->phone ?? 'Chưa cập nhật' }}</span> | 
                    <span>Tham gia: {{ $customer->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Trở về danh sách
            </a>
            <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')
                @if($customer->status === 'banned')
                    <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản này?')">
                        <i class="fa-solid fa-lock-open me-1"></i> Mở Khóa Tài Khoản
                    </button>
                @else
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">
                        <i class="fa-solid fa-user-lock me-1"></i> Khóa Tài Khoản
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Thống kê mua sắm -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Tổng Số Đơn Hàng</span>
                <h3 class="fw-bold mb-0 text-dark">{{ $customer->orders_count }} đơn</h3>
            </div>
            <div class="stat-card-icon bg-primary-subtle text-primary">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Tổng Tiền Đã Chi Tiêu</span>
                <h3 class="fw-bold mb-0 text-danger">{{ number_format($totalSpent) }}đ</h3>
            </div>
            <div class="stat-card-icon bg-success-subtle text-success">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách đơn hàng đã mua -->
<div class="card admin-card">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Lịch Sử Mua Hàng Của {{ $customer->name }}</h6>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light extra-small">
                <tr>
                    <th class="ps-3">Mã Đơn Hàng</th>
                    <th>Ngày Đặt</th>
                    <th>Người Nhận</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái Đơn</th>
                    <th class="text-end pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->orders as $order)
                    @php
                        $statusBadges = [
                            'pending' => ['text' => 'Chờ xử lý', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
                            'confirmed' => ['text' => 'Đã xác nhận', 'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
                            'processing' => ['text' => 'Đang chuẩn bị hàng', 'class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
                            'shipping' => ['text' => 'Đang giao hàng', 'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
                            'completed' => ['text' => 'Giao thành công', 'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
                            'cancelled' => ['text' => 'Đã hủy', 'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
                            'returned' => ['text' => 'Trả hàng / Hoàn tiền', 'class' => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle'],
                        ];
                        $badge = $statusBadges[$order->status] ?? ['text' => $order->status_label, 'class' => 'bg-secondary'];
                    @endphp
                    <tr>
                        <td class="ps-3 fw-bold">#{{ $order->order_code }}</td>
                        <td class="small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="fw-bold text-dark mb-0">{{ $order->recipient_name }}</div>
                            <span class="extra-small text-muted">{{ $order->recipient_phone }}</span>
                        </td>
                        <td class="fw-bold text-danger">{{ number_format($order->total_amount) }}đ</td>
                        <td>
                            <span class="badge {{ $badge['class'] }} px-3 py-2">
                                {{ $badge['text'] }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i> Xem đơn
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-box-open fa-2x mb-2 text-secondary d-block"></i>
                            Khách hàng này chưa có đơn hàng nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
