@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân - ATINO Store')

@push('styles')
<style>
    /* Styling chuyên biệt cho Hồ sơ cá nhân ATINO (Minimal Premium Fashion) */
    .profile-wrapper {
        max-width: 1140px;
        margin: 0 auto;
    }

    .profile-avatar-circle {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background-color: #0f172a;
        color: #ffffff;
        font-weight: 800;
        font-size: 1.35rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        flex-shrink: 0;
    }

    .profile-user-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03) !important;
    }



    .atino-nav-btn {
        color: #475569 !important;
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        padding: 0.75rem 1.1rem !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        text-align: left !important;
    }

    .atino-nav-btn i {
        color: #64748b !important;
        font-size: 0.95rem;
        transition: color 0.2s ease !important;
    }

    .atino-nav-btn:hover {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .atino-nav-btn:hover i {
        color: #0f172a !important;
    }

    .atino-nav-btn.active,
    .atino-nav-btn[aria-selected="true"] {
        background-color: #0f172a !important;
        border-color: #0f172a !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.18) !important;
    }

    .atino-nav-btn.active i,
    .atino-nav-btn[aria-selected="true"] i {
        color: #ffffff !important;
    }

    .atino-nav-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 50rem;
        background-color: #f1f5f9;
        color: #334155;
    }

    .atino-nav-btn.active .atino-nav-badge,
    .atino-nav-btn[aria-selected="true"] .atino-nav-badge {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
    }

    .profile-card-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 1rem 1.25rem !important;
    }

    .profile-section-heading {
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .profile-input-field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 0.55rem 0.85rem !important;
        font-size: 0.875rem !important;
        color: #0f172a !important;
        transition: all 0.2s ease !important;
    }

    .profile-input-field:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08) !important;
        outline: none !important;
    }

    .btn-fashion-dark {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border: 1px solid #0f172a !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        padding: 0.55rem 1.35rem !important;
        transition: all 0.2s ease !important;
        display: inline-flex !items-center !important;
        justify-content: center !important;
        gap: 0.4rem !important;
    }

    .btn-fashion-dark:hover {
        background-color: #1e293b !important;
        border-color: #1e293b !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15) !important;
    }

    .btn-fashion-outline {
        background-color: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        padding: 0.5rem 1.15rem !important;
        transition: all 0.2s ease !important;
        display: inline-flex !items-center !important;
        justify-content: center !important;
        gap: 0.4rem !important;
    }

    .btn-fashion-outline:hover {
        background-color: #f8fafc !important;
        border-color: #0f172a !important;
        color: #0f172a !important;
    }

    .profile-empty-card {
        background-color: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2.25rem 1.25rem;
        text-align: center;
    }

    .profile-empty-icon-circle {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background-color: #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        margin-bottom: 0.75rem;
    }

    /* Responsive Scrollable Tab menu trên Mobile & Tablet */
    @media (max-width: 991.98px) {
        .profile-nav-container {
            flex-direction: row !important;
            overflow-x: auto !important;
            padding-bottom: 0.5rem !important;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            flex-wrap: nowrap !important;
        }

        .profile-nav-container::-webkit-scrollbar {
            height: 3px;
        }
        .profile-nav-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .atino-nav-btn {
            flex-shrink: 0 !important;
            width: auto !important;
            padding: 0.55rem 0.9rem !important;
            white-space: nowrap !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container profile-wrapper py-3">
    @php
        $completedTotal = isset($orders) ? $orders->where('status', 'completed')->sum('total_amount') : 0;
        if ($completedTotal >= 8000000) {
            $tierName = 'Kim Cương (Diamond VIP)';
            $tierBadge = 'bg-dark text-warning border-warning';
            $tierIcon = 'fa-gem';
            $tierDiscount = '10%';
            $tierProgress = 100;
            $tierNextGoal = 'Hạng cao nhất';
        } elseif ($completedTotal >= 3000000) {
            $tierName = 'Hạng Vàng (Gold)';
            $tierBadge = 'bg-warning text-dark border-warning';
            $tierIcon = 'fa-crown';
            $tierDiscount = '5%';
            $tierProgress = round(($completedTotal / 8000000) * 100);
            $tierNextGoal = 'Cần tích lũy thêm ' . number_format(8000000 - $completedTotal) . 'đ để lên Kim Cương';
        } elseif ($completedTotal >= 1000000) {
            $tierName = 'Hạng Bạc (Silver)';
            $tierBadge = 'bg-secondary-subtle text-dark border-secondary';
            $tierIcon = 'fa-award';
            $tierDiscount = '3%';
            $tierProgress = round(($completedTotal / 3000000) * 100);
            $tierNextGoal = 'Cần tích lũy thêm ' . number_format(3000000 - $completedTotal) . 'đ để lên Hạng Vàng';
        } else {
            $tierName = 'Thành Viên Mới (Bronze)';
            $tierBadge = 'bg-light text-dark border-secondary-subtle';
            $tierIcon = 'fa-shield';
            $tierDiscount = '0%';
            $tierProgress = round(($completedTotal / 1000000) * 100);
            $tierNextGoal = 'Cần tích lũy thêm ' . number_format(1000000 - $completedTotal) . 'đ để lên Hạng Bạc';
        }
    @endphp

    <!-- Top Account Header Card & Loyalty Tier -->
    <div class="card profile-user-card border-0 mb-4 p-4 shadow-sm">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="profile-avatar-circle">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h4 class="fw-extrabold text-dark mb-0 fs-5" style="letter-spacing: -0.01em;">{{ $user->name }}</h4>
                            <span class="badge {{ $tierBadge }} border px-2.5 py-1 rounded-pill extra-small fw-bold">
                                <i class="fa-solid {{ $tierIcon }} me-1"></i> {{ $tierName }}
                            </span>
                        </div>
                        <div class="text-muted small">
                            <span><i class="fa-regular fa-envelope me-1"></i>{{ $user->email }}</span>
                            <span class="mx-1.5">•</span>
                            <span><i class="fa-solid fa-phone me-1"></i>{{ $user->phone ?? 'Chưa cập nhật SĐT' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loyalty Spending Bar -->
            <div class="col-lg-6">
                <div class="bg-light p-3 rounded-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-1.5 extra-small">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-coins text-warning me-1"></i> Đã chi tiêu: <strong class="text-primary">{{ number_format($completedTotal) }}đ</strong></span>
                        <span class="text-muted">{{ $tierNextGoal }}</span>
                    </div>
                    <div class="progress rounded-pill bg-white border" style="height: 8px;">
                        <div class="progress-bar bg-primary rounded-pill progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $tierProgress }}%;" aria-valuenow="{{ $tierProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Section with Left Nav Tabs & Right Content -->
    <div class="row g-4 mb-5">
        <!-- Left Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="nav profile-nav-container flex-column gap-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <button class="nav-link atino-nav-btn active" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                    <span><i class="fa-regular fa-user me-2"></i> Hồ sơ cá nhân</span>
                </button>

                <button class="nav-link atino-nav-btn" id="v-pills-addresses-tab" data-bs-toggle="pill" data-bs-target="#v-pills-addresses" type="button" role="tab" aria-controls="v-pills-addresses" aria-selected="false">
                    <span><i class="fa-solid fa-location-dot me-2"></i> Địa chỉ nhận hàng</span>
                    @if(isset($user->addresses) && $user->addresses->isNotEmpty())
                        <span class="atino-nav-badge ms-2">{{ $user->addresses->count() }}</span>
                    @endif
                </button>

                <button class="nav-link atino-nav-btn" id="v-pills-orders-tab" data-bs-toggle="pill" data-bs-target="#v-pills-orders" type="button" role="tab" aria-controls="v-pills-orders" aria-selected="false">
                    <span><i class="fa-solid fa-bag-shopping me-2"></i> Đơn hàng của tôi</span>
                    @if(isset($orders) && $orders->isNotEmpty())
                        <span class="atino-nav-badge ms-2">{{ $orders->count() }}</span>
                    @endif
                </button>

                <button class="nav-link atino-nav-btn" id="v-pills-wishlist-tab" data-bs-toggle="pill" data-bs-target="#v-pills-wishlist" type="button" role="tab" aria-controls="v-pills-wishlist" aria-selected="false">
                    <span><i class="fa-regular fa-heart me-2"></i> Danh sách yêu thích</span>
                    @if(isset($wishlists) && $wishlists->isNotEmpty())
                        <span class="atino-nav-badge ms-2">{{ $wishlists->count() }}</span>
                    @endif
                </button>

                <button class="nav-link atino-nav-btn" id="v-pills-coupons-tab" data-bs-toggle="pill" data-bs-target="#v-pills-coupons" type="button" role="tab" aria-controls="v-pills-coupons" aria-selected="false">
                    <span><i class="fa-solid fa-ticket me-2"></i> Kho mã giảm giá</span>
                </button>

                <button class="nav-link atino-nav-btn" id="v-pills-password-tab" data-bs-toggle="pill" data-bs-target="#v-pills-password" type="button" role="tab" aria-controls="v-pills-password" aria-selected="false">
                    <span><i class="fa-solid fa-shield-halved me-2"></i> Đổi mật khẩu</span>
                </button>

                <div class="my-2 border-top border-light d-none d-lg-block"></div>

                <form action="{{ route('logout') }}" method="POST" class="w-100">
                    @csrf
                    <button type="submit" class="nav-link atino-nav-btn text-danger border-0">
                        <span><i class="fa-solid fa-right-from-bracket me-2 text-danger"></i> Đăng xuất</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Content Tab Panes -->
        <div class="col-lg-9">
            <div class="tab-content" id="v-pills-tabContent">
                
                <!-- TAB 1: HỒ SƠ CÁ NHÂN -->
                <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                    <!-- Thông Tin Cá Nhân Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                        <div class="profile-card-header">
                            <h5 class="profile-section-heading"><i class="fa-regular fa-id-card me-2"></i>Thông tin cá nhân</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label small fw-semibold text-secondary">Họ và tên <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control profile-input-field @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label small fw-semibold text-secondary">Địa chỉ Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control profile-input-field @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="phone" class="form-label small fw-semibold text-secondary">Số điện thoại</label>
                                        <div class="input-group">
                                            <input type="tel" class="form-control profile-input-field @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0987654321">
                                            @if($user->isPhoneVerified())
                                                <span class="input-group-text bg-success-subtle text-success border-success-subtle fw-semibold small">
                                                    <i class="fa-solid fa-check-circle me-1"></i> Đã xác thực OTP
                                                </span>
                                            @else
                                                <a href="{{ route('otp.show') }}" class="btn btn-warning btn-sm fw-bold align-items-center d-flex">
                                                    Xác thực OTP ngay
                                                </a>
                                            @endif
                                        </div>
                                        @error('phone')
                                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4 pt-2">
                                    <button type="submit" class="btn-fashion-dark">
                                        <i class="fa-solid fa-check me-1"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- TAB: ĐỊA CHỈ NHẬN HÀNG -->
                <div class="tab-pane fade" id="v-pills-addresses" role="tabpanel" aria-labelledby="v-pills-addresses-tab">
                    <!-- Địa Chỉ Giao Hàng Card -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="profile-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h5 class="profile-section-heading"><i class="fa-solid fa-location-dot me-2"></i>Địa chỉ nhận hàng</h5>
                            <button type="button" class="btn-fashion-dark py-1.5 px-3 extra-small" data-bs-toggle="modal" data-bs-target="#modalAddAddress">
                                <i class="fa-solid fa-plus me-1"></i> Thêm địa chỉ mới
                            </button>
                        </div>
                        <div class="card-body p-4">
                            @if(isset($user->addresses) && $user->addresses->isNotEmpty())
                                <div class="row g-3">
                                    @foreach($user->addresses as $address)
                                        <div class="col-12">
                                            <div class="border rounded-3 p-3 bg-white shadow-xs position-relative hover-shadow-sm transition-all {{ $address->is_default ? 'border-dark' : 'border-light-subtle' }}">
                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                            <strong class="text-dark fs-6">{{ $address->recipient_name }}</strong>
                                                            <span class="text-muted small">| {{ $address->recipient_phone }}</span>
                                                            @if($address->is_default)
                                                                <span class="badge bg-dark text-white rounded-pill px-2.5 py-0.5 extra-small fw-semibold">
                                                                    Mặc định
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-secondary small mb-1">
                                                            <i class="fa-solid fa-house me-1.5 opacity-60"></i>{{ $address->street_address }}
                                                        </div>
                                                        <div class="text-muted extra-small">
                                                            <i class="fa-solid fa-map-pin me-1.5 opacity-60"></i>{{ $address->ward }}, {{ $address->district }}, {{ $address->province_city }}
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                        @if(!$address->is_default)
                                                            <form action="{{ route('user.addresses.setDefault', $address->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn-fashion-outline py-1 px-2.5 extra-small">
                                                                    Thiết lập mặc định
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <button type="button" class="btn btn-light btn-sm rounded-circle border shadow-xs btn-edit-address" 
                                                                data-id="{{ $address->id }}"
                                                                data-name="{{ $address->recipient_name }}"
                                                                data-phone="{{ $address->recipient_phone }}"
                                                                data-province="{{ $address->province_city }}"
                                                                data-district="{{ $address->district }}"
                                                                data-ward="{{ $address->ward }}"
                                                                data-street="{{ $address->street_address }}"
                                                                data-default="{{ $address->is_default ? '1' : '0' }}"
                                                                title="Chỉnh sửa địa chỉ" style="width: 34px; height: 34px;">
                                                            <i class="fa-solid fa-pen text-dark extra-small"></i>
                                                        </button>

                                                        <form action="{{ route('user.addresses.destroy', $address->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-light btn-sm rounded-circle border shadow-xs text-danger" title="Xóa địa chỉ" style="width: 34px; height: 34px;">
                                                                <i class="fa-solid fa-trash-can extra-small"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="profile-empty-card">
                                    <div class="profile-empty-icon-circle">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 fs-6">Bạn chưa lưu địa chỉ nhận hàng mặc định nào</h6>
                                    <p class="small text-muted mb-3" style="max-width: 420px; margin: 0 auto;">Thêm địa chỉ giao hàng để giúp quá trình thanh toán và nhận đơn diễn ra nhanh chóng hơn.</p>
                                    <button type="button" class="btn-fashion-dark py-2 px-4" data-bs-toggle="modal" data-bs-target="#modalAddAddress">
                                        <i class="fa-solid fa-plus me-1"></i> Thêm địa chỉ nhận hàng
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 2: ĐƠN HÀNG CỦA TÔI -->
                <div class="tab-pane fade" id="v-pills-orders" role="tabpanel" aria-labelledby="v-pills-orders-tab">
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="profile-card-header">
                            <h5 class="profile-section-heading"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử đơn hàng</h5>
                        </div>
                        <div class="card-body p-4">
                            @if(isset($orders) && $orders->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="fw-bold extra-small text-uppercase">Mã đơn hàng</th>
                                                <th class="fw-bold extra-small text-uppercase">Ngày đặt</th>
                                                <th class="fw-bold extra-small text-uppercase">Tổng tiền</th>
                                                <th class="fw-bold extra-small text-uppercase">Trạng thái</th>
                                                <th class="fw-bold extra-small text-uppercase text-end">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td class="fw-bold text-dark">#{{ $order->order_code }}</td>
                                                    <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="fw-bold text-dark">{{ number_format($order->total_amount) }}đ</td>
                                                    <td>
                                                        @php
                                                            $p = $order->payments->first();
                                                            $isPaidOrder = ($p && in_array($p->payment_status, ['paid', 'completed'])) || $order->status === 'completed';
                                                        @endphp
                                                        @if($order->status === 'completed')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i>Đã giao &amp; Thanh toán</span>
                                                        @elseif($order->status === 'cancelled')
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill">Đã hủy</span>
                                                        @else
                                                            @if($isPaidOrder)
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i>Đã thanh toán</span>
                                                            @else
                                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1 rounded-pill"><i class="fa-solid fa-clock me-1"></i>Chờ thanh toán (COD)</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('orders.show', $order->order_code) }}" class="btn-fashion-outline py-1 px-3 extra-small">
                                                            Chi tiết <i class="fa-solid fa-chevron-right ms-1 extra-small"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="profile-empty-card">
                                    <div class="profile-empty-icon-circle">
                                        <i class="fa-solid fa-box-open"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 fs-6">Chưa có đơn hàng nào</h6>
                                    <p class="small text-muted mb-3" style="max-width: 420px; margin: 0 auto;">Khám phá ngay bộ sưu tập thời trang mới nhất của ATINO để sắm cho mình những trang phục ưng ý.</p>
                                    <a href="{{ route('shop.index') }}" class="btn-fashion-dark py-2 px-4 text-decoration-none">
                                        Khám phá sản phẩm
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 3: SẢN PHẨM YÊU THÍCH -->
                <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel" aria-labelledby="v-pills-wishlist-tab">
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="profile-card-header d-flex align-items-center justify-content-between">
                            <h5 class="profile-section-heading"><i class="fa-regular fa-heart me-2"></i>Danh sách yêu thích</h5>
                        </div>
                        <div class="card-body p-4">
                            @if(isset($wishlists) && $wishlists->isNotEmpty())
                                <div class="row g-3">
                                    @foreach($wishlists as $item)
                                        @if($item->product)
                                            @php
                                                $prod = $item->product;
                                                $thumbSrc = $prod->image_url;
                                            @endphp
                                            <div class="col-6 col-md-4">
                                                <div class="card h-100 border p-2 rounded-3 text-center atino-product-card position-relative">
                                                    <button type="button" class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm z-2 btn-toggle-wishlist active d-flex align-items-center justify-content-center" data-id="{{ $prod->id }}" title="Xóa khỏi yêu thích" style="width: 32px; height: 32px;">
                                                        <i class="fa-solid fa-heart text-danger"></i>
                                                    </button>

                                                    <a href="{{ route('shop.show', $prod->slug) }}" class="d-block mb-2 overflow-hidden rounded-3">
                                                        <img src="{{ $thumbSrc }}" alt="{{ $prod->name }}" class="img-fluid" style="height: 180px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ $realPhoto }}';">
                                                    </a>
                                                    <h6 class="fw-bold text-dark small text-truncate mb-1">
                                                        <a href="{{ route('shop.show', $prod->slug) }}" class="text-dark text-decoration-none">
                                                            {{ $prod->name }}
                                                        </a>
                                                    </h6>
                                                    <div class="fw-extrabold text-dark mb-2">
                                                        {{ number_format($prod->sale_price ?: $prod->price) }}đ
                                                    </div>
                                                    <button type="button" class="btn-fashion-dark py-1.5 w-100 btn-add-to-cart extra-small" data-id="{{ $prod->id }}">
                                                        <i class="fa-solid fa-bag-shopping me-1"></i> Thêm vào giỏ
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="profile-empty-card">
                                    <div class="profile-empty-icon-circle">
                                        <i class="fa-regular fa-heart"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 fs-6">Danh sách yêu thích đang trống</h6>
                                    <p class="small text-muted mb-3" style="max-width: 420px; margin: 0 auto;">Nhấn vào biểu tượng trái tim ở các sản phẩm để lưu lại danh sách yêu thích của bạn.</p>
                                    <a href="{{ route('shop.index') }}" class="btn-fashion-dark py-2 px-4 text-decoration-none">
                                        Khám phá ngay
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 4: PHIẾU GIẢM GIÁ -->
                <div class="tab-pane fade" id="v-pills-coupons" role="tabpanel" aria-labelledby="v-pills-coupons-tab">
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="profile-card-header">
                            <h5 class="profile-section-heading"><i class="fa-solid fa-ticket me-2"></i>Kho phiếu giảm giá</h5>
                        </div>
                        <div class="card-body p-4">
                            @if(isset($coupons) && $coupons->isNotEmpty())
                                <div class="row g-3">
                                    @foreach($coupons as $coupon)
                                        <div class="col-12 col-md-6">
                                            <div class="coupon-card-ticket p-3 d-flex align-items-center justify-content-between shadow-xs">
                                                <div>
                                                    <span class="badge bg-dark fw-bold rounded-pill mb-1 px-3 py-1 extra-small">{{ $coupon->code }}</span>
                                                    <div class="fw-bold text-dark fs-6 mt-1">Giảm {{ number_format($coupon->discount_value) }}{{ $coupon->discount_type === 'percent' ? '%' : 'đ' }}</div>
                                                    <div class="extra-small text-muted">Hạn sử dụng: {{ \Carbon\Carbon::parse($coupon->end_date)->format('d/m/Y') }}</div>
                                                </div>
                                                <button type="button" class="btn btn-outline-dark btn-sm rounded-pill py-1 px-3 extra-small fw-semibold" onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); if(typeof Swal!=='undefined'){Swal.fire({toast:true,position:'top-end',icon:'success',title:'Đã chép mã {{ $coupon->code }}!',showConfirmButton:false,timer:2000});}else{alert('Đã chép mã {{ $coupon->code }}!');}">
                                                    <i class="fa-regular fa-copy me-1"></i> Chép mã
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="profile-empty-card">
                                    <div class="profile-empty-icon-circle">
                                        <i class="fa-solid fa-ticket"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 fs-6">Hiện chưa có phiếu giảm giá nào</h6>
                                    <p class="small text-muted mb-0">Các ưu đãi và voucher mới nhất sẽ hiển thị tại đây khi có chương trình khuyến mãi.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB 5: ĐỔI MẬT KHẨU -->
                <div class="tab-pane fade" id="v-pills-password" role="tabpanel" aria-labelledby="v-pills-password-tab">
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="profile-card-header">
                            <h5 class="profile-section-heading"><i class="fa-solid fa-shield-halved me-2"></i>Đổi mật khẩu</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('profile.changePassword') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label small fw-semibold text-secondary">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control profile-input-field @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="text-danger extra-small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label small fw-semibold text-secondary">Mật khẩu mới <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control profile-input-field @error('password') is-invalid @enderror" id="password" name="password" required>
                                        @error('password')
                                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label small fw-semibold text-secondary">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control profile-input-field" id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn-fashion-dark">
                                        <i class="fa-solid fa-key me-1"></i> Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- BANNER CUỐI TRANG -->
@include('partials.full_width_banner')

<!-- MODAL THÊM ĐỊA CHỈ MỚI -->
<div class="modal fade" id="modalAddAddress" tabindex="-1" aria-labelledby="modalAddAddressLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modalAddAddressLabel">
                    <i class="fa-solid fa-location-dot text-dark me-2"></i>Thêm Địa Chỉ Giao Hàng Mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">


                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="add_recipient_name" class="form-label small fw-semibold text-secondary">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" class="form-control profile-input-field" id="add_recipient_name" name="recipient_name" value="{{ old('recipient_name', $user->name) }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                        </div>

                        <div class="col-md-6">
                            <label for="add_recipient_phone" class="form-label small fw-semibold text-secondary">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control profile-input-field" id="add_recipient_phone" name="recipient_phone" value="{{ old('recipient_phone', $user->phone) }}" placeholder="Ví dụ: 0987654321" required>
                        </div>

                        <div class="col-md-4">
                            <label for="add_province_city" class="form-label small fw-semibold text-secondary">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                            <select class="form-select profile-input-field" id="add_province_city" name="province_city" required onchange="onProvinceChange('add')">
                                <option value="">-- Chọn Tỉnh / Thành --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="add_district" class="form-label small fw-semibold text-secondary">Quận / Huyện <span class="text-danger">*</span></label>
                            <select class="form-select profile-input-field" id="add_district" name="district" required onchange="onDistrictChange('add')">
                                <option value="">-- Chọn Quận / Huyện --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="add_ward" class="form-label small fw-semibold text-secondary">Phường / Xã <span class="text-danger">*</span></label>
                            <select class="form-select profile-input-field" id="add_ward" name="ward" required>
                                <option value="">-- Chọn Phường / Xã --</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="add_street_address" class="form-label small fw-semibold text-secondary">Địa chỉ chi tiết (Số nhà, Tên đường) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control profile-input-field" id="add_street_address" name="street_address" placeholder="Ví dụ: Số 123 Đường Xuân Thủy" required>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_default" id="add_is_default" value="1" {{ (isset($user->addresses) && $user->addresses->isEmpty()) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-dark small" for="add_is_default">Đặt làm địa chỉ nhận hàng mặc định</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn-fashion-outline px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-fashion-dark px-4">
                        <i class="fa-solid fa-save me-1"></i> Lưu địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SỬA ĐỊA CHỈ -->
<div class="modal fade" id="modalEditAddress" tabindex="-1" aria-labelledby="modalEditAddressLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark fs-6" id="modalEditAddressLabel">
                    <i class="fa-solid fa-pen-to-square text-dark me-2"></i>Chỉnh Sửa Địa Chỉ Giao Hàng
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAddress" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">


                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_recipient_name" class="form-label small fw-semibold text-secondary">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" class="form-control profile-input-field" id="edit_recipient_name" name="recipient_name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_recipient_phone" class="form-label small fw-semibold text-secondary">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control profile-input-field" id="edit_recipient_phone" name="recipient_phone" required>
                        </div>

                        <div class="col-md-4">
                            <label for="edit_province_city" class="form-label small fw-semibold text-secondary">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                            <select class="form-select profile-input-field" id="edit_province_city" name="province_city" required onchange="onProvinceChange('edit')">
                                <option value="">-- Chọn Tỉnh / Thành --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="edit_district" class="form-label small fw-semibold text-secondary">Quận / Huyện <span class="text-danger">*</span></label>
                            <select class="form-select profile-input-field" id="edit_district" name="district" required onchange="onDistrictChange('edit')">
                                <option value="">-- Chọn Quận / Huyện --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="edit_ward" class="form-label small fw-semibold text-secondary">Phường / Xã <span class="text-danger">*</span></label>
                            <select class="form-select profile-input-field" id="edit_ward" name="ward" required>
                                <option value="">-- Chọn Phường / Xã --</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="edit_street_address" class="form-label small fw-semibold text-secondary">Địa chỉ chi tiết (Số nhà, Tên đường) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control profile-input-field" id="edit_street_address" name="street_address" required>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_default" id="edit_is_default" value="1">
                                <label class="form-check-label fw-semibold text-dark small" for="edit_is_default">Đặt làm địa chỉ nhận hàng mặc định</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn-fashion-outline px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-fashion-dark px-4">
                        <i class="fa-solid fa-save me-1"></i> Cập nhật địa chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let vnAddressData = [];

    document.addEventListener('DOMContentLoaded', async function() {
        await loadVnAddressData();

        // Xử lý active tab theo session flash 'active_tab' hoặc URL hash (#addresses, #orders,...)
        const sessionTab = '{{ session("active_tab") }}';
        let initialTabSelector = null;

        if (sessionTab) {
            initialTabSelector = `#v-pills-${sessionTab}-tab`;
        } else if (window.location.hash) {
            const rawHash = window.location.hash.replace('#', '');
            const cleanHash = rawHash.replace('v-pills-', '');
            initialTabSelector = `#v-pills-${cleanHash}-tab`;
        }

        if (initialTabSelector) {
            const activeTabTrigger = document.querySelector(initialTabSelector);
            if (activeTabTrigger) {
                const tabInstance = new bootstrap.Tab(activeTabTrigger);
                tabInstance.show();
            }
        }

        // Lưu hash khi người dùng chủ động chuyển tab
        const allTabButtons = document.querySelectorAll('#v-pills-tab button[data-bs-toggle="pill"]');
        allTabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', function(event) {
                const targetId = event.target.getAttribute('data-bs-target');
                if (targetId) {
                    const cleanHash = targetId.replace('#v-pills-', '');
                    history.replaceState(null, null, '#' + cleanHash);
                }
            });
        });

        // Xử lý nạp dữ liệu Modal Sửa Địa Chỉ
        const modalEditElement = document.getElementById('modalEditAddress');
        const modalEdit = new bootstrap.Modal(modalEditElement);

        document.querySelectorAll('.btn-edit-address').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const phone = this.getAttribute('data-phone');
                const province = this.getAttribute('data-province');
                const district = this.getAttribute('data-district');
                const ward = this.getAttribute('data-ward');
                const street = this.getAttribute('data-street');
                const isDefault = this.getAttribute('data-default') === '1';

                const form = document.getElementById('formEditAddress');
                form.action = `/user/addresses/${id}`;

                document.getElementById('edit_recipient_name').value = name;
                document.getElementById('edit_recipient_phone').value = phone;

                // Set province
                const provSelect = document.getElementById('edit_province_city');
                provSelect.value = province;
                if (!provSelect.value && province) {
                    const opt = document.createElement('option');
                    opt.value = province;
                    opt.textContent = province;
                    provSelect.appendChild(opt);
                    provSelect.value = province;
                }
                onProvinceChange('edit');

                // Set district
                const distSelect = document.getElementById('edit_district');
                distSelect.value = district;
                if (!distSelect.value && district) {
                    const opt = document.createElement('option');
                    opt.value = district;
                    opt.textContent = district;
                    distSelect.appendChild(opt);
                    distSelect.value = district;
                }
                onDistrictChange('edit');

                // Set ward
                const wardSelect = document.getElementById('edit_ward');
                wardSelect.value = ward;
                if (!wardSelect.value && ward) {
                    const opt = document.createElement('option');
                    opt.value = ward;
                    opt.textContent = ward;
                    wardSelect.appendChild(opt);
                    wardSelect.value = ward;
                }

                document.getElementById('edit_street_address').value = street;
                document.getElementById('edit_is_default').checked = isDefault;

                modalEdit.show();
            });
        });
    });

    // Load dữ liệu 63 Tỉnh/Thành + Quận/Huyện + Phường/Xã từ Open API
    async function loadVnAddressData() {
        try {
            const res = await fetch('https://provinces.open-api.vn/api/?depth=3');
            if (res.ok) {
                vnAddressData = await res.json();
                initProvinceDropdowns();
                return;
            }
        } catch (e) {
            console.warn('API Tỉnh Thành không khả dụng, nạp danh sách mặc định:', e);
        }

        // Fallback danh sách 63 tỉnh thành mặc định nếu mất kết nối
        const fallbackProvinces = [
            "Hà Nội", "TP. Hồ Chí Minh", "Đà Nẵng", "Hải Phòng", "Cần Thơ", 
            "An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu", 
            "Bắc Ninh", "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước", 
            "Bình Thuận", "Cà Mau", "Cao Bằng", "Đắk Lắk", "Đắk Nông", 
            "Điện Biên", "Đồng Nai", "Đồng Tháp", "Gia Lai", "Hà Giang", 
            "Hà Nam", "Hà Tĩnh", "Hải Dương", "Hậu Giang", "Hòa Bình", 
            "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu", 
            "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", 
            "Nghệ An", "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", 
            "Quảng Bình", "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", 
            "Sóc Trăng", "Sơn La", "Tây Ninh", "Thái Bình", "Thái Nguyên", 
            "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh", "Tuyên Quang", 
            "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"
        ];
        vnAddressData = fallbackProvinces.map((p, idx) => ({ name: p, code: idx + 1, districts: [] }));
        initProvinceDropdowns();
    }

    function initProvinceDropdowns() {
        ['add', 'edit'].forEach(prefix => {
            const provSelect = document.getElementById(prefix + '_province_city');
            if (!provSelect) return;
            const currentVal = provSelect.value;
            provSelect.innerHTML = '<option value="">-- Chọn Tỉnh / Thành --</option>';
            vnAddressData.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.name;
                opt.textContent = p.name;
                provSelect.appendChild(opt);
            });
            if (currentVal) provSelect.value = currentVal;
        });
    }

    function onProvinceChange(prefix) {
        const provSelect = document.getElementById(prefix + '_province_city');
        const distSelect = document.getElementById(prefix + '_district');
        const wardSelect = document.getElementById(prefix + '_ward');

        distSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        const selectedProvName = provSelect.value;
        const provObj = vnAddressData.find(p => p.name === selectedProvName);

        if (provObj && provObj.districts && provObj.districts.length > 0) {
            provObj.districts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.name;
                opt.textContent = d.name;
                distSelect.appendChild(opt);
            });
        }
    }

    function onDistrictChange(prefix) {
        const provSelect = document.getElementById(prefix + '_province_city');
        const distSelect = document.getElementById(prefix + '_district');
        const wardSelect = document.getElementById(prefix + '_ward');

        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        const selectedProvName = provSelect.value;
        const selectedDistName = distSelect.value;

        const provObj = vnAddressData.find(p => p.name === selectedProvName);
        if (!provObj) return;

        const distObj = provObj.districts?.find(d => d.name === selectedDistName);
        if (distObj && distObj.wards && distObj.wards.length > 0) {
            distObj.wards.forEach(w => {
                const opt = document.createElement('option');
                opt.value = w.name;
                opt.textContent = w.name;
                wardSelect.appendChild(opt);
            });
        }
    }


</script>
@endpush
@endsection
