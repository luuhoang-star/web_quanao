@extends('layouts.app')

@section('title', 'Sự Kiện Flash Sale Giảm Giá Sâu - Atino Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active fw-bold text-danger" aria-current="page">Flash Sale Hot</li>
        </ol>
    </nav>

    <!-- Header Banner Flash Sale -->
    <div class="card border-0 bg-danger text-white rounded-4 p-4 mb-4 shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center z-1 position-relative">
            <div class="col-lg-7 mb-3 mb-lg-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2 fs-6"><i class="fa-solid fa-bolt me-1"></i> SỰ KIỆN HOT</span>
                    <span class="badge bg-white text-danger fw-bold px-3 py-2 fs-6">GIẢM ĐẾN 50%+</span>
                </div>
                <h2 class="fw-black display-6 mb-2">CHƯƠNG TRÌNH FLASH SALE VÀNG</h2>
                <p class="text-white-50 mb-0">Cơ hội săn ngay các sản phẩm thời trang cao cấp với giá ưu đãi cực sốc. Số lượng có hạn!</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                @if($flashSaleEndTime)
                    <div class="d-inline-block bg-black bg-opacity-30 rounded-4 p-3 border border-white border-opacity-25" id="flash-sale-page-countdown" data-endtime="{{ $flashSaleEndTime }}">
                        <div class="small text-warning font-weight-bold mb-1"><i class="fa-solid fa-clock me-1"></i> THỜI GIAN CÒN LẠI:</div>
                        <div class="d-flex align-items-center gap-2 fs-3 font-monospace fw-bold text-white justify-content-center">
                            <span class="bg-dark text-warning px-3 py-1 rounded-3" id="page-fs-hours">00</span>:
                            <span class="bg-dark text-warning px-3 py-1 rounded-3" id="page-fs-mins">00</span>:
                            <span class="bg-dark text-warning px-3 py-1 rounded-3" id="page-fs-secs">00</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Grid Flash Sale -->
    @if($flashSales->isNotEmpty())
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
            @foreach($flashSales as $fs)
                @php
                    $product = $fs->product;
                    if (!$product) continue;

                    $thumbnail = $product->images->where('is_thumbnail', true)->first() ?: $product->images->first();
                    $percentSold = $fs->quantity > 0 ? min(100, round(($fs->sold_quantity / $fs->quantity) * 100)) : 0;
                @endphp
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card transition-all">
                        <!-- Badge Sale -->
                        <div class="position-absolute top-0 start-0 m-3 z-2">
                            <span class="badge bg-danger fw-bold fs-6 shadow-sm">-{{ $fs->discount_percent }}%</span>
                        </div>

                        <!-- Image -->
                        <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none position-relative d-block overflow-hidden bg-light" style="padding-top: 120%;">
                            @if($thumbnail && $thumbnail->image_url)
                                <img src="{{ asset('storage/' . $thumbnail->image_url) }}" alt="{{ $product->name }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover transition-transform">
                            @else
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                    <i class="fa-solid fa-shirt fa-4x opacity-25"></i>
                                </div>
                            @endif
                        </a>

                        <!-- Body -->
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="text-muted small mb-1">{{ $product->category->name ?? 'Thời trang' }}</div>
                                <h6 class="card-title fw-bold text-dark text-truncate mb-2">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none text-dark hover-primary">{{ $product->name }}</a>
                                </h6>
                            </div>

                            <div>
                                <div class="d-flex align-items-baseline gap-2 mb-2">
                                    <span class="fs-5 fw-bold text-danger">{{ number_format($fs->sale_price) }}đ</span>
                                    <del class="small text-muted">{{ number_format($product->price) }}đ</del>
                                </div>

                                <!-- Progress bar -->
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between extra-small text-muted mb-1">
                                        <span>Đã bán {{ $fs->sold_quantity }}</span>
                                        <span>Còn {{ $fs->remaining_quantity }}</span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 6px;">
                                        <div class="progress-bar bg-danger rounded-pill" role="progressbar" style="width: {{ $percentSold }}%;" aria-valuenow="{{ $percentSold }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <a href="{{ route('shop.show', $product->slug) }}" class="btn btn-outline-danger btn-sm w-100 rounded-3 mt-3 fw-bold">
                                    <i class="fa-solid fa-bolt me-1"></i> MUA NGAY
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $flashSales->links() }}
        </div>
    @else
        <div class="text-center py-5 bg-light rounded-4 my-4">
            <i class="fa-solid fa-bolt-slash fa-4x text-muted mb-3 opacity-50"></i>
            <h5 class="fw-bold text-dark">Hiện chưa có đợt Flash Sale nào diễn ra</h5>
            <p class="text-muted mb-3">Vui lòng quay lại sau hoặc tham khảo các sản phẩm khác tại cửa hàng!</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fa-solid fa-store me-2"></i> Khám phá sản phẩm
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var $pageFsBox = $('#flash-sale-page-countdown');
        if ($pageFsBox.length) {
            var endTimeStr = $pageFsBox.data('endtime');
            if (endTimeStr) {
                var countDate = new Date(endTimeStr.replace(/-/g, "/")).getTime();
                var timerInterval = setInterval(function() {
                    var now = new Date().getTime();
                    var distance = countDate - now;

                    if (distance < 0) {
                        clearInterval(timerInterval);
                        $pageFsBox.html('<span class="text-warning fw-bold">Flash Sale đã kết thúc</span>');
                        return;
                    }

                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $('#page-fs-hours').text(hours < 10 ? '0' + hours : hours);
                    $('#page-fs-mins').text(minutes < 10 ? '0' + minutes : minutes);
                    $('#page-fs-secs').text(seconds < 10 ? '0' + seconds : seconds);
                }, 1000);
            }
        }
    });
</script>
@endpush
@endsection
