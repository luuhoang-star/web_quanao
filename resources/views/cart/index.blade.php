@extends('layouts.app')

@section('title', 'Giỏ Hàng Của Bạn - ATINO Store')

@section('content')
<!-- Checkout Progress Stepper -->
<div class="checkout-stepper">
    <div class="checkout-step-item active">
        <span class="step-num">1</span>
        <span>1. Chọn sản phẩm</span>
    </div>
    <div class="checkout-step-divider"></div>
    <div class="checkout-step-item">
        <span class="step-num">2</span>
        <span>2. Thanh toán & Giao hàng</span>
    </div>
    <div class="checkout-step-divider"></div>
    <div class="checkout-step-item">
        <span class="step-num">3</span>
        <span>3. Hoàn tất đơn hàng</span>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-cart-shopping text-dark"></i>
            <span>Giỏ Hàng Của Bạn</span>
            @if($cart->items->isNotEmpty())
                <span class="badge bg-dark text-white rounded-pill fs-6 fw-normal px-2.5 py-1">({{ $cart->items->sum('quantity') }})</span>
            @endif
        </h3>
        <p class="text-muted small mb-0">Quản lý và kiểm tra các món đồ thời trang bạn đã lựa chọn</p>
    </div>
    @if($cart->items->isNotEmpty())
        <form action="{{ route('cart.clear') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold extra-small" onclick="return confirm('Bạn có chắc chắn muốn làm trống toàn bộ giỏ hàng?')">
                <i class="fa-regular fa-trash-can me-1"></i> Làm trống giỏ hàng
            </button>
        </form>
    @endif
</div>

@if($cart->items->isEmpty())
    <!-- Trạng thái giỏ hàng trống -->
    <div class="card border-0 shadow-sm rounded-4 bg-white text-center py-5">
        <div class="card-body p-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-4 mb-3" style="width: 100px; height: 100px;">
                <i class="fa-solid fa-cart-flatbed fa-3x text-muted opacity-50"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Giỏ hàng của bạn đang trống!</h4>
            <p class="text-muted small mb-4">Hãy nhanh tay khám phá các bộ sưu tập thời trang nam nữ mới nhất của ATINO nhé.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                <i class="fa-solid fa-bag-shopping me-2"></i> Khám phá sản phẩm ngay
            </a>
        </div>
    </div>
@else
    <!-- Thanh Thông Báo Freeship Thông Minh -->
    @php
        $freeshipThreshold = 500000;
        $isFreeship = $totalAmount >= $freeshipThreshold;
        $freeshipPercent = min(100, round(($totalAmount / $freeshipThreshold) * 100));
        $neededForFreeship = max(0, $freeshipThreshold - $totalAmount);
    @endphp

    <div class="freeship-banner shadow-sm">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 small">
                <i class="fa-solid fa-truck-fast text-success fs-5"></i>
                @if($isFreeship)
                    <span class="text-dark">
                        <strong class="text-success">Chúc mừng!</strong> Đơn hàng của bạn đã đủ điều kiện <strong>Miễn phí vận chuyển toàn quốc</strong>.
                    </span>
                @else
                    <span class="text-dark">
                        Mua thêm <strong class="text-danger">{{ number_format($neededForFreeship) }}đ</strong> nữa để được <strong class="text-success">Miễn phí giao hàng toàn quốc</strong>!
                    </span>
                @endif
            </div>
            <span class="extra-small fw-bold text-muted">{{ $freeshipPercent }}%</span>
        </div>
        <div class="freeship-progress">
            <div class="freeship-progress-bar" style="width: {{ $freeshipPercent }}%;"></div>
        </div>
    </div>

    <div class="row g-4">
        <!-- CỘT TRÁI: BẢNG SẢN PHẨM TRONG GIỎ -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light extra-small">
                            <tr>
                                <th class="ps-4 py-3">SẢN PHẨM</th>
                                <th class="text-center">ĐƠN GIÁ</th>
                                <th class="text-center">SỐ LƯỢNG</th>
                                <th class="text-end">THÀNH TIỀN</th>
                                <th class="text-center pe-4">XÓA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart->items as $item)
                                @php
                                    $product = $item->product;
                                    if (! $product) {
                                        continue;
                                    }
                                    $variant = $item->variant;
                                    $thumb = $product->images?->where('is_thumbnail', true)->first() ?: $product->images?->first();
                                    $subtotal = $item->price * $item->quantity;
                                @endphp
                                <tr>
                                    <!-- Sản phẩm -->
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($thumb && $thumb->image_url)
                                                <img src="{{ asset('storage/' . $thumb->image_url) }}" alt="{{ $product->name }}" class="rounded-3 border object-fit-cover flex-shrink-0" style="width: 68px; height: 68px;">
                                            @else
                                                <div class="bg-light border rounded-3 d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width: 68px; height: 68px;">
                                                    <i class="fa-solid fa-shirt fa-lg opacity-30"></i>
                                                </div>
                                            @endif

                                            <div class="min-w-0">
                                                <h6 class="fw-bold text-dark mb-1">
                                                    <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none text-dark hover-text-primary">
                                                        {{ $product->name }}
                                                    </a>
                                                </h6>

                                                <!-- Phân loại Màu & Size -->
                                                <div class="d-flex flex-wrap gap-1.5 align-items-center mb-1">
                                                    @if($variant && $variant->color)
                                                        <span class="badge bg-light text-secondary border extra-small">
                                                            <span class="d-inline-block rounded-circle me-1 border" style="width: 8px; height: 8px; background-color: {{ $variant->color->hex_code ?? '#000' }};"></span>
                                                            {{ $variant->color->name }}
                                                        </span>
                                                    @endif

                                                    @if($variant && $variant->size)
                                                        <span class="badge bg-dark text-white extra-small">Size: {{ $variant->size->name }}</span>
                                                    @endif
                                                </div>
                                                <span class="extra-small text-muted font-monospace"><i class="fa-solid fa-barcode me-1"></i>{{ $variant->sku ?? $product->sku }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Đơn giá -->
                                    <td class="text-center fw-semibold text-dark small">{{ number_format($item->price) }}đ</td>

                                    <!-- Form Cập nhật Số lượng với nút +/- -->
                                    <td class="text-center">
                                        <form id="cart-item-form-{{ $item->id }}" action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline-flex align-items-center justify-content-center">
                                            @csrf
                                            @method('PUT')
                                            <div class="cart-qty-pill">
                                                <button type="button" class="qty-btn" onclick="updateItemQuantity({{ $item->id }}, -1)">
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                                <input type="number" id="qty-input-{{ $item->id }}" name="quantity" class="qty-input" value="{{ $item->quantity }}" min="1" max="99" onchange="this.form.submit()">
                                                <button type="button" class="qty-btn" onclick="updateItemQuantity({{ $item->id }}, 1)">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>

                                    <!-- Thành tiền -->
                                    <td class="text-end fw-extrabold text-danger">{{ number_format($subtotal) }}đ</td>

                                    <!-- Xóa 1 sản phẩm -->
                                    <td class="text-center pe-4">
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-sm text-danger rounded-circle border p-1.5 hover-bg-danger hover-text-white transition-all" title="Xóa món này" onclick="return confirm('Bạn có muốn xóa {{ $product->name }} khỏi giỏ hàng?')">
                                                <i class="fa-solid fa-trash-can extra-small"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top p-3 px-4 d-flex justify-content-between align-items-center">
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-arrow-left me-1.5"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: TỔNG QUAN ĐƠN HÀNG (ORDER SUMMARY) -->
        <div class="col-lg-4">
            <div class="sticky-checkout-summary">
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-white py-3.5 px-4 border-bottom">
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2 fs-6">
                            <i class="fa-solid fa-receipt text-dark"></i>
                            <span>Tóm Tắt Đơn Hàng</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2.5 text-secondary small">
                            <span>Tạm tính tiền hàng:</span>
                            <span class="fw-bold text-dark fs-6">{{ number_format($totalAmount) }}đ</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 text-secondary small">
                            <span>Ước tính phí vận chuyển:</span>
                            @if($isFreeship)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Miễn phí (Freeship)</span>
                            @else
                                <span class="fw-semibold text-dark">30.000đ</span>
                            @endif
                        </div>

                        <hr class="my-3 text-muted">

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <div>
                                <span class="fw-bold text-dark fs-6 d-block">Tổng tiền dự kiến:</span>
                                <span class="extra-small text-muted">(Chưa tính mã giảm giá)</span>
                            </div>
                            <div class="text-end">
                                <span class="fs-3 fw-extrabold text-danger">{{ number_format($isFreeship ? $totalAmount : $totalAmount + 30000) }}đ</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('orders.checkout') }}" class="btn btn-dark btn-lg rounded-pill fw-bold py-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-credit-card"></i>
                                <span>TIẾN HÀNH THANH TOÁN</span>
                            </a>
                        </div>

                        <!-- Huy hiệu cam kết -->
                        <div class="trust-badges-grid">
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-shield-check"></i>
                                <span>Bảo mật đơn hàng</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-box-check"></i>
                                <span>Được đồng kiểm</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>Đổi trả 30 ngày</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-bolt"></i>
                                <span>Giao nhanh toàn quốc</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    function updateItemQuantity(itemId, delta) {
        var input = $('#qty-input-' + itemId);
        var currentVal = parseInt(input.val()) || 1;
        var newVal = currentVal + delta;
        if (newVal >= 1 && newVal <= 99) {
            input.val(newVal);
            $('#cart-item-form-' + itemId).submit();
        }
    }
</script>
@endpush
@endsection
