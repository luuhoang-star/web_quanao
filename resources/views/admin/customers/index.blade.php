@extends('layouts.admin')

@section('title', 'Quản Lý Khách Hàng')
@section('page_title', 'Quản Lý Khách Hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-users text-primary me-2"></i>Quản Lý Khách Hàng</h5>
        <p class="text-muted small mb-0">Quản lý danh sách tài khoản người mua hàng toàn hệ thống</p>
    </div>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Tổng Khách Hàng</span>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalCustomers }}</h3>
            </div>
            <div class="stat-card-icon bg-primary-subtle text-primary">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Đang Hoạt Động</span>
                <h3 class="fw-bold mb-0 text-success">{{ $activeCount }}</h3>
            </div>
            <div class="stat-card-icon bg-success-subtle text-success">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Tài Khoản Đã Khóa</span>
                <h3 class="fw-bold mb-0 text-danger">{{ $bannedCount }}</h3>
            </div>
            <div class="stat-card-icon bg-danger-subtle text-danger">
                <i class="fa-solid fa-user-slash"></i>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc tìm kiếm -->
<div class="card admin-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên, email hoặc SĐT... (Nhấn Enter để tìm)" value="{{ request('keyword') }}" onkeydown="if(event.key==='Enter') this.form.submit();">
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-4">
                <select name="status" class="form-select" onchange="this.form.submit();">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Đang hoạt động</option>
                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>🚫 Đã bị khóa</option>
                </select>
            </div>
            @if(request()->filled('keyword') || request()->filled('status'))
                <div class="col-12 col-md-auto ms-md-auto">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-danger d-flex align-items-center gap-1.5" title="Xóa bộ lọc">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Xóa lọc</span>
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Bảng danh sách khách hàng -->
<div class="card admin-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light extra-small">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Khách Hàng</th>
                    <th>Email / Số Điện Thoại</th>
                    <th>Ngày Đăng Ký</th>
                    <th>Tổng Đơn Hàng</th>
                    <th>Tổng Chi Tiêu</th>
                    <th>Trạng Thái</th>
                    <th class="text-end pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#{{ $customer->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark mb-0">
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-decoration-none text-dark hover-primary">{{ $customer->name }}</a>
                                    </div>
                                    <span class="badge bg-light text-dark border extra-small">User</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small text-dark"><i class="fa-regular fa-envelope me-1 text-muted"></i>{{ $customer->email }}</div>
                            @if($customer->phone)
                                <div class="extra-small text-muted"><i class="fa-solid fa-phone me-1 text-muted"></i>{{ $customer->phone }}</div>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $customer->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                {{ $customer->orders_count }} đơn hàng
                            </span>
                        </td>
                        <td class="fw-bold text-danger">
                            {{ number_format($customer->orders_sum_total_amount ?: 0) }}đ
                        </td>
                        <td>
                            @if($customer->status === 'banned')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Đã bị khóa</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Hoạt động</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    @if($customer->status === 'banned')
                                        <button type="submit" class="btn btn-outline-success" title="Mở khóa tài khoản" onclick="return confirm('Bạn có muốn mở khóa tài khoản này?')">
                                            <i class="fa-solid fa-lock-open"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-outline-danger" title="Khóa tài khoản" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản này?')">
                                            <i class="fa-solid fa-user-lock"></i>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-users-slash fa-2x mb-2 text-secondary d-block"></i>
                            Không tìm thấy khách hàng nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
