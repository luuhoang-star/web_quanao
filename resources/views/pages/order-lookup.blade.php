@extends('layouts.app')

@section('title', 'Tra Cứu Đơn Hàng - Atino Store')
@section('meta_description', 'Tra cứu trạng thái vận chuyển và thông tin chi tiết đơn hàng của bạn tại Atino chỉ bằng mã đơn hàng hoặc số điện thoại.')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Tra cứu đơn hàng</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Header Search Box Card -->
            <div class="card border-0 rounded-4 bg-light shadow-sm p-4 p-md-5 mb-4 text-center">
                <span class="badge bg-dark text-white px-3 py-2 rounded-pill text-uppercase mb-3 mx-auto" style="letter-spacing: 0.05em; font-size: 0.75rem;">Theo Dõi Đơn Hàng</span>
                <h1 class="fw-bold text-dark display-6 mb-3">Tra Cứu Tiến Trình Giao Hàng</h1>
                <p class="text-muted mx-auto mb-4" style="max-width: 550px; line-height: 1.6;">
                    Nhập <strong>Mã đơn hàng</strong> (ví dụ: ORD-...) hoặc <strong>Số điện thoại</strong> đặt hàng để kiểm tra tiến trình xử lý và lộ trình vận chuyển.
                </p>

                <!-- Search Form -->
                <form action="{{ route('pages.order-lookup') }}" method="GET" class="mx-auto w-100" style="max-width: 580px;">
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border bg-white p-1">
                        <span class="input-group-text bg-white border-0 ps-3 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" value="{{ $keyword }}" required class="form-control border-0 shadow-none px-2" placeholder="Nhập mã đơn hàng hoặc số điện thoại..." style="font-size: 0.95rem;">
                        <button class="btn btn-dark rounded-pill px-4 fw-bold" type="submit">
                            Tra Cứu
                        </button>
                    </div>
                </form>
            </div>

            <!-- Kết Quả Tìm Kiếm -->
            @if($keyword !== '')
                @if($orders->isNotEmpty())
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fa-solid fa-box-open text-primary me-2"></i>Tìm thấy {{ $orders->count() }} đơn hàng phù hợp:
                        </h5>

                        <div class="d-flex flex-column gap-4">
                            @foreach($orders as $order)
                                @php
                                    $statusMap = [
                                        'pending' => ['bg' => 'bg-warning-subtle text-warning border-warning-subtle', 'text' => 'Chờ xử lý', 'icon' => 'fa-clock'],
                                        'confirmed' => ['bg' => 'bg-info-subtle text-info border-info-subtle', 'text' => 'Đã xác nhận', 'icon' => 'fa-check'],
                                        'processing' => ['bg' => 'bg-primary-subtle text-primary border-primary-subtle', 'text' => 'Đang đóng gói', 'icon' => 'fa-box'],
                                        'shipping' => ['bg' => 'bg-primary-subtle text-primary border-primary-subtle', 'text' => 'Đang giao hàng', 'icon' => 'fa-truck-fast'],
                                        'completed' => ['bg' => 'bg-success-subtle text-success border-success-subtle', 'text' => 'Giao thành công', 'icon' => 'fa-circle-check'],
                                        'cancelled' => ['bg' => 'bg-danger-subtle text-danger border-danger-subtle', 'text' => 'Đã hủy đơn', 'icon' => 'fa-circle-xmark'],
                                    ];
                                    $st = $statusMap[$order->status] ?? ['bg' => 'bg-secondary-subtle text-secondary', 'text' => $order->status, 'icon' => 'fa-info-circle'];
                                @endphp

                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                                    <div class="card-header bg-light py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <div>
                                            <span class="text-muted small">Mã đơn:</span>
                                            <strong class="text-dark fs-6 ms-1">#{{ $order->order_code }}</strong>
                                            <span class="text-muted small ms-3"><i class="fa-regular fa-calendar me-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <span class="badge {{ $st['bg'] }} px-3 py-1.5 rounded-pill fw-semibold border">
                                            <i class="fa-solid {{ $st['icon'] }} me-1"></i>{{ $st['text'] }}
                                        </span>
                                    </div>

                                    <div class="card-body p-4">
                                        <!-- Thông tin giao hàng -->
                                        <div class="row g-3 mb-4 pb-3 border-bottom small">
                                            <div class="col-md-6">
                                                <div class="text-muted mb-1"><i class="fa-solid fa-user me-2"></i>Người nhận: <strong class="text-dark">{{ $order->recipient_name }}</strong></div>
                                                <div class="text-muted"><i class="fa-solid fa-phone me-2"></i>Số điện thoại: <strong class="text-dark">{{ $order->recipient_phone }}</strong></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-muted mb-1"><i class="fa-solid fa-location-dot me-2"></i>Địa chỉ: <strong class="text-dark">{{ $order->shipping_address }}</strong></div>
                                                <div class="text-muted"><i class="fa-solid fa-credit-card me-2"></i>Thanh toán: <strong class="text-dark">{{ strtoupper($order->payment?->payment_method ?? 'COD') }} ({{ $order->payment?->status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }})</strong></div>
                                            </div>
                                        </div>

                                        <!-- Danh sách sản phẩm -->
                                        <div class="d-flex flex-column gap-3 mb-3">
                                            @foreach($order->items as $item)
                                                @php
                                                    $product = $item->productVariant?->product;
                                                    $imgUrl = $product?->primaryImage?->display_url ?? 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=150&q=80';
                                                @endphp
                                                <div class="d-flex align-items-center justify-content-between gap-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ $imgUrl }}" alt="Ảnh sản phẩm" class="rounded-3 object-fit-cover border" style="width: 55px; height: 55px;">
                                                        <div>
                                                            <div class="fw-semibold text-dark small mb-1">{{ $product?->name ?? 'Sản phẩm' }}</div>
                                                            <div class="extra-small text-muted">
                                                                Màu: {{ $item->productVariant?->color?->name ?? '-' }} | Size: {{ $item->productVariant?->size?->name ?? '-' }} | SL: x{{ $item->quantity }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="fw-bold text-dark small text-end">
                                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Tổng tiền -->
                                        <div class="bg-light p-3 rounded-3 d-flex justify-content-between align-items-center mt-3">
                                            <span class="text-muted small">Tổng thanh toán:</span>
                                            <span class="text-danger fw-black fs-5">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- Không tìm thấy -->
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white mb-5">
                        <div class="text-muted mb-3">
                            <i class="fa-solid fa-box-open fa-3x opacity-40"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Không Tìm Thấy Đơn Hàng Nào</h5>
                        <p class="text-muted small mx-auto mb-4" style="max-width: 450px;">
                            Không tìm thấy dữ liệu đơn hàng khớp với từ khóa <strong>"{{ $keyword }}"</strong>. Vui lòng kiểm tra lại mã đơn hàng hoặc số điện thoại.
                        </p>
                        <a href="tel:0973797151" class="btn btn-outline-dark rounded-pill px-4 btn-sm mx-auto">
                            <i class="fa-solid fa-headset me-2"></i>Liên Hệ Hỗ Trợ 0973.797.151
                        </a>
                    </div>
                @endif
            @endif

            <!-- Mẹo Tra Cứu -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Mẹo Nhỏ Giúp Bạn Tra Cứu Dễ Hơn:</h6>
                <ul class="text-muted small mb-0 ps-3 d-flex flex-column gap-2">
                    <li>Mã đơn hàng có dạng <strong>ORD-YYYYMMDD-XXXX</strong> được gửi trong tin nhắn xác nhận sau khi bạn hoàn tất đặt hàng.</li>
                    <li>Bạn có thể nhập chính xác số điện thoại đặt hàng để xem danh sách các đơn hàng gần đây nhất.</li>
                    <li>Nếu bạn đã có tài khoản thành viên Atino, bạn có thể <a href="{{ route('orders.index') }}" class="text-primary fw-semibold">Đăng nhập tài khoản</a> để quản lý đầy đủ lịch sử đơn hàng.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
