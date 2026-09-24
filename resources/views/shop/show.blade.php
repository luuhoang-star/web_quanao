@extends('layouts.app')

@section('title', $product->name . ' - Chi Tiết Sản Phẩm | Atino Store')
@section('meta_description', Str::limit(strip_tags($product->description ?? 'Mua ngay ' . $product->name . ' thời trang cao cấp tại Atino Store với giá tốt nhất.'), 160))
@section('og_type', 'product')
@section('og_image', $product->images->first() ? asset('storage/' . ($product->images->where('is_thumbnail', true)->first()->image_url ?? $product->images->first()->image_url)) : asset('images/logo.png'))

@push('styles')
<script type="application/ld+json">
{
    "\u0040context": "https://schema.org/",
    "\u0040type": "Product",
    "name": "{{ $product->name }}",
    "image": [
        "{{ asset('storage/' . ($product->images->where('is_thumbnail', true)->first()->image_url ?? $product->images->first()->image_url ?? '')) }}"
    ],
    "description": "{{ strip_tags($product->description ?? $product->name) }}",
    "sku": "{{ $product->sku }}",
    "offers": {
        "\u0040type": "Offer",
        "url": "{{ url()->current() }}",
        "priceCurrency": "VND",
        "price": "{{ $product->sale_price ?: $product->price }}",
        "availability": "https://schema.org/InStock",
        "itemCondition": "https://schema.org/NewCondition"
    }
}
</script>
<style>
    .product-main-gallery {
        position: relative;
        background: #f8fafc;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .product-main-img-wrapper {
        height: 480px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: zoom-in;
    }
    .product-main-img-wrapper img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    .product-main-img-wrapper:hover img {
        transform: scale(1.04);
    }
    .thumb-gallery-strip {
        scrollbar-width: thin;
    }
    .thumb-box {
        width: 72px;
        height: 72px;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .thumb-box.active, .thumb-box:hover {
        border-color: #0f172a !important;
        transform: translateY(-2px);
    }
    .thumb-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .color-pill-btn {
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 50rem;
        transition: all 0.2s ease;
    }
    .color-pill-btn:hover, .color-pill-btn.active {
        border-color: #0f172a !important;
        background: #0f172a !important;
        color: #ffffff !important;
    }
    .size-pill-btn {
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-size: 0.875rem;
        font-weight: 700;
        padding: 0.45rem 1.15rem;
        border-radius: 10px;
        min-width: 48px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .size-pill-btn:hover, .size-pill-btn.active {
        border-color: #0f172a !important;
        background: #0f172a !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }
    .size-pill-btn.disabled {
        opacity: 0.4;
        text-decoration: line-through;
        cursor: not-allowed;
    }
    .delivery-policy-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
    }
    .policy-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        font-size: 0.825rem;
    }
    .policy-item i {
        font-size: 1.1rem;
        color: #0f172a;
        margin-top: 2px;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm border small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">Sản phẩm</a></li>
        @if($product->category)
            <li class="breadcrumb-item"><a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="text-decoration-none text-muted">{{ $product->category->name }}</a></li>
        @endif
        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ Str::limit($product->name, 40) }}</li>
    </ol>
</nav>

<!-- MAIN PRODUCT DETAIL CARD -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-5">
    <div class="card-body p-4 p-lg-5">
        <div class="row g-4 g-lg-5">
            <!-- ================= 1. GALLERY SECTION ================= -->
            <div class="col-lg-6">
                @php
                    $allImages = $product->images;
                    $thumbnail = $allImages->where('is_thumbnail', true)->first() ?: $allImages->first();
                @endphp

                <!-- Ảnh lớn hiển thị chính với nút phóng to -->
                <div class="product-main-gallery mb-3 shadow-sm">
                    @php
                        $effectiveSale = $product->effective_sale_price;
                        $hasDiscount = !is_null($effectiveSale) && $effectiveSale < $product->price;
                        $topBadgePercent = $hasDiscount ? round((($product->price - $effectiveSale) / $product->price) * 100) : 0;
                    @endphp
                    @if($hasDiscount)
                        <span class="position-absolute top-0 start-0 m-3 badge bg-danger fs-6 fw-bold z-1 shadow-sm px-2.5 py-1.5 rounded-pill">
                            -{{ $topBadgePercent }}%
                        </span>
                    @endif

                    <!-- Nút mở Lightbox xem toàn màn hình -->
                    <button type="button" class="btn btn-white btn-sm rounded-circle position-absolute top-0 end-0 m-3 shadow-sm border z-1" onclick="openImageLightbox()" title="Xem ảnh lớn">
                        <i class="fa-solid fa-expand text-dark"></i>
                    </button>

                    <div class="product-main-img-wrapper" onclick="openImageLightbox()">
                        @if($thumbnail && $thumbnail->image_url)
                            <img id="main-product-img" src="{{ asset('storage/' . $thumbnail->image_url) }}" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center text-muted" style="height: 480px;">
                                <i class="fa-solid fa-shirt fa-6x opacity-25"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Thư viện ảnh thu nhỏ (Gallery Thumbnails Strip) -->
                @if($allImages->count() > 1)
                    <div class="d-flex gap-2.5 overflow-auto py-1 thumb-gallery-strip" id="product-thumbnails-strip">
                        @foreach($allImages as $img)
                            <div class="thumb-box bg-white {{ $loop->first ? 'active' : '' }}" 
                                 data-image-id="{{ $img->id }}"
                                 data-color-id="{{ $img->color_id ?? '' }}"
                                 data-img-url="{{ asset('storage/' . $img->image_url) }}"
                                 onclick="changeMainImage('{{ asset('storage/' . $img->image_url) }}', this)">
                                <img src="{{ asset('storage/' . $img->image_url) }}" alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- ================= 2. PRODUCT INFORMATION SECTION ================= -->
            <div class="col-lg-6">
                <!-- Category badge & Social share -->
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <span class="badge bg-dark text-white rounded-pill px-3 py-1.5 extra-small fw-semibold">
                        {{ $product->category->name ?? 'Thời trang cao cấp' }}
                    </span>

                    <!-- Quick Share Tool -->
                    <div class="d-flex align-items-center gap-1.5">
                        <button type="button" class="btn btn-light btn-sm rounded-circle border p-1 px-2 extra-small text-muted hover-text-dark" onclick="copyProductLink()" title="Sao chép liên kết">
                            <i class="fa-regular fa-share-from-square"></i>
                        </button>
                    </div>
                </div>

                <!-- Product Name -->
                <h2 class="fw-extrabold text-dark mb-2" style="font-size: 1.65rem; letter-spacing: -0.02em;">{{ $product->name }}</h2>

                <!-- SKU & Rating Stars -->
                @php
                    $avgRating = $avgRating ?? $averageRating ?? 5.0;
                @endphp
                <div class="d-flex align-items-center gap-3 mb-3 text-muted small">
                    <span>Mã SKU: <strong class="font-monospace text-dark">{{ $product->sku }}</strong></span>
                    <span>|</span>
                    <div class="d-flex align-items-center gap-1 text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-{{ $i <= round($avgRating) ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor
                        <span class="text-dark fw-bold ms-1">{{ number_format($avgRating, 1) }}</span>
                        <a href="#reviews-section" class="text-muted text-decoration-none">({{ $reviewsCount }} đánh giá)</a>
                    </div>
                </div>

                <!-- Price Box & Flash Sale Banner -->
                @php
                    $flashSale = $product->activeFlashSale;
                    if ($flashSale) {
                        $activeUnitPrice = $flashSale->sale_price;
                        $activeOrigPrice = $product->price;
                        $discountPercent = $flashSale->discount_percent;
                    } else {
                        $activeUnitPrice = ($product->sale_price && $product->sale_price < $product->price) ? $product->sale_price : $product->price;
                        $activeOrigPrice = $product->price;
                        $discountPercent = ($product->sale_price && $product->sale_price < $product->price) ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
                    }
                @endphp

                @if($flashSale)
                    <div class="card border-0 bg-danger text-white rounded-4 p-3.5 mb-4 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div>
                                <span class="badge bg-warning text-dark fw-bold me-2"><i class="fa-solid fa-bolt me-1"></i>FLASH SALE</span>
                                <span class="small fw-semibold text-white-50">Giá ưu đãi có hạn</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 extra-small" id="flash-sale-countdown-box" data-endtime="{{ $flashSale->end_time->format('Y-m-d H:i:s') }}">
                                <span class="text-white-50 me-1">Kết thúc sau:</span>
                                <span class="bg-dark text-warning font-monospace px-2 py-1 rounded fw-bold" id="fs-hours">00</span>:
                                <span class="bg-dark text-warning font-monospace px-2 py-1 rounded fw-bold" id="fs-mins">00</span>:
                                <span class="bg-dark text-warning font-monospace px-2 py-1 rounded fw-bold" id="fs-secs">00</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-3">
                            <span class="fs-1 fw-extrabold text-warning" id="display-unit-price">{{ number_format($flashSale->sale_price) }}đ</span>
                            <del class="fs-5 text-white-50" id="display-original-price">{{ number_format($product->price) }}đ</del>
                            <span class="badge bg-warning text-dark font-weight-bold">-{{ $discountPercent }}%</span>
                        </div>
                    </div>
                @else
                    <div class="p-3.5 bg-light rounded-4 mb-4" id="product-price-box" data-unit-price="{{ $activeUnitPrice }}" data-original-price="{{ $activeOrigPrice }}">
                        <div class="d-flex align-items-baseline gap-3 flex-wrap">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="fs-1 fw-extrabold text-danger" id="display-unit-price">{{ number_format($product->sale_price) }}đ</span>
                                <del class="fs-5 text-muted" id="display-original-price">{{ number_format($product->price) }}đ</del>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">-{{ $discountPercent }}% OFF</span>
                            @else
                                <span class="fs-1 fw-extrabold text-dark" id="display-unit-price">{{ number_format($product->price) }}đ</span>
                            @endif
                        </div>
                        <div id="total-price-preview" class="mt-2 text-dark small d-none border-top pt-2">
                            <i class="fa-solid fa-calculator me-1 text-primary"></i> Tạm tính (<span id="total-qty-span">1</span> món): <span id="display-total-price" class="fs-4 fw-bold text-danger ms-1">0đ</span>
                        </div>
                    </div>
                @endif

                <!-- Product Variants (Màu sắc & Size nếu có) -->
                @if($product->variants->isNotEmpty())
                    @php
                        $colors = $product->variants->pluck('color')->filter()->unique('id');
                        $sizes = $product->variants->pluck('size')->filter()->unique('id');

                        $variantsArray = $product->variants->map(function ($v) {
                            return [
                                'id' => $v->id,
                                'size_id' => $v->size_id,
                                'color_id' => $v->color_id,
                                'price' => $v->price,
                                'sale_price' => $v->sale_price,
                                'stock_quantity' => $v->stock_quantity,
                            ];
                        })->values()->toArray();
                        $firstVariant = $product->variants->first();
                    @endphp

                    <div id="product-variants-container" data-variants='@json($variantsArray)' class="mb-4">
                        <!-- Color Picker -->
                        @if($colors->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small mb-2 d-flex align-items-center gap-1.5">
                                    <span>Màu sắc:</span>
                                    <span id="selected-color-name" class="text-primary fw-semibold">{{ $colors->first()->name ?? '' }}</span>
                                </label>
                                <div class="d-flex gap-2 flex-wrap" id="color-picker-group">
                                    @foreach($colors as $color)
                                        <button type="button" 
                                                class="color-pill-btn d-inline-flex align-items-center gap-2 {{ $loop->first ? 'active' : '' }}" 
                                                data-color-id="{{ $color->id }}"
                                                data-color-name="{{ $color->name }}">
                                            <span class="rounded-circle border" style="width: 14px; height: 14px; background-color: {{ $color->hex_code ?? '#000' }};"></span>
                                            <span>{{ $color->name }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Size Picker & Nút Hướng Dẫn Chọn Size Thông Minh -->
                        @if($sizes->isNotEmpty())
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                                    <label class="form-label fw-bold text-dark small mb-0 d-flex align-items-center gap-1.5">
                                        <span>Kích cỡ (Size):</span>
                                        <span id="selected-size-name" class="text-primary fw-semibold">{{ $sizes->first()->name ?? '' }}</span>
                                    </label>
                                    
                                    <!-- Nút Bật Modal Bảng Hướng Dẫn Chọn Size -->
                                    <button type="button" class="btn btn-link text-decoration-none p-0 extra-small fw-bold text-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#sizeGuideModal" id="btn-open-size-guide">
                                        <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                                        <span>Tư vấn chọn size thông minh</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small py-0.5 px-1.5 rounded-pill">Chuẩn 98%</span>
                                    </button>
                                </div>
                                <div class="d-flex gap-2 flex-wrap" id="size-picker-group">
                                    @foreach($sizes as $size)
                                        <button type="button" 
                                                class="size-pill-btn {{ $loop->first ? 'active' : '' }}" 
                                                data-size-id="{{ $size->id }}"
                                                data-size-name="{{ $size->name }}">
                                            {{ $size->name }}
                                        </button>
                                    @endforeach
                                </div>

                                <!-- Thanh Thông Báo Gợi Ý Size Thông Minh (Tự động hiển thị khi đã lưu số đo) -->
                                <div id="smart-size-hint-container" class="mt-2.5 p-2 px-3 bg-light rounded-3 border border-primary-subtle d-none">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2 extra-small text-dark">
                                            <span class="badge bg-primary rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                                <i class="fa-solid fa-sparkles text-white" style="font-size: 10px;"></i>
                                            </span>
                                            <span>
                                                Theo số đo (<span id="hint-user-specs" class="fw-semibold">170cm, 65kg</span>) phù hợp: 
                                                <strong class="text-primary fw-bold fs-6" id="hint-user-size">Size L</strong>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <button type="button" class="btn btn-sm btn-primary py-0 px-2 extra-small rounded-pill fw-bold" id="btn-hint-apply-size" onclick="applyHintSize()">
                                                Chọn Size này
                                            </button>
                                            <button type="button" class="btn btn-sm btn-link text-muted p-0 extra-small text-decoration-none" data-bs-toggle="modal" data-bs-target="#sizeGuideModal" title="Đổi số đo">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Thông báo tình trạng tồn kho của biến thể -->
                        <div id="variant-stock-status" class="extra-small mt-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                <i class="fa-solid fa-circle-check me-1"></i>Còn hàng (<strong id="stock-count-display">{{ $firstVariant->stock_quantity ?? 10 }}</strong> món có sẵn)
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Quantity, Add to Cart & Buy Now Form -->
                <form action="{{ route('cart.store') }}" method="POST" class="mb-4" id="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="product_variant_id" id="selected-variant-id" value="{{ isset($firstVariant) && $firstVariant ? $firstVariant->id : '' }}">
                    
                    <div class="mb-3.5">
                        <label class="form-label fw-bold text-dark small mb-1.5">Số Lượng Mua:</label>
                        <div class="cart-qty-pill">
                            <button type="button" class="qty-btn" onclick="decrementQty()"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" id="prod-qty" name="quantity" class="qty-input" value="1" min="1" max="99">
                            <button type="button" class="qty-btn" onclick="incrementQty()"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2.5 flex-wrap">
                        @php
                            $isWishlisted = Auth::check() && Auth::user()->wishlists->contains('product_id', $product->id);
                        @endphp
                        <button type="button" id="btn-add-to-cart-action" class="btn btn-outline-dark btn-lg flex-grow-1 rounded-pill fw-bold py-3 btn-add-to-cart" data-id="{{ $product->id }}">
                            <i class="fa-solid fa-cart-plus me-2"></i> Thêm Vào Giỏ Hàng
                        </button>
                        <button type="submit" name="buy_now" value="1" id="btn-buy-now-action" class="btn btn-dark btn-lg flex-grow-1 rounded-pill fw-bold py-3 shadow-sm">
                            <i class="fa-solid fa-bolt me-2"></i> MUA NGAY
                        </button>
                        <button type="button" class="btn btn-light btn-lg rounded-circle border btn-toggle-wishlist p-3" data-id="{{ $product->id }}" title="Lưu vào yêu thích" style="width: 52px; height: 52px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="fa-{{ $isWishlisted ? 'solid text-danger' : 'regular text-muted' }} fa-heart fs-5"></i>
                        </button>
                    </div>
                </form>

                <!-- 4. Delivery & Quality Assurance Card -->
                <div class="delivery-policy-card mb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="policy-item">
                                <i class="fa-solid fa-truck-fast text-dark"></i>
                                <div>
                                    <strong class="text-dark d-block">Giao hàng hỏa tốc</strong>
                                    <span class="text-muted">Nhận hàng trong 2 - 4 ngày làm việc</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="policy-item">
                                <i class="fa-solid fa-box-open text-dark"></i>
                                <div>
                                    <strong class="text-dark d-block">Đồng kiểm khi nhận</strong>
                                    <span class="text-muted">Được kiểm tra trước khi trả tiền</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="policy-item">
                                <i class="fa-solid fa-rotate-left text-dark"></i>
                                <div>
                                    <strong class="text-dark d-block">Đổi hàng trong 30 ngày</strong>
                                    <span class="text-muted">Đổi size hoặc mẫu nếu không vừa</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="policy-item">
                                <i class="fa-solid fa-shield-check text-dark"></i>
                                <div>
                                    <strong class="text-dark d-block">Chính hãng 100%</strong>
                                    <span class="text-muted">Chất liệu cao cấp, bảo hành may</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Mô tả & Bảo quản -->
                <ul class="nav nav-tabs" id="prodTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-dark" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Mô tả sản phẩm</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-muted" id="material-tab" data-bs-toggle="tab" data-bs-target="#material" type="button" role="tab">Chất liệu & Giặt ủi</button>
                    </li>
                </ul>
                <div class="tab-content p-3.5 border border-top-0 rounded-bottom-3 bg-light small text-secondary" id="prodTabContent">
                    <div class="tab-pane fade show active" id="desc" role="tabpanel">
                        {!! nl2br(e($product->description ?? 'Chưa có mô tả chi tiết cho sản phẩm này.')) !!}
                    </div>
                    <div class="tab-pane fade" id="material" role="tabpanel">
                        <p class="mb-2"><strong class="text-dark">Chất liệu:</strong> {{ $product->material ?? 'Vải cotton cao cấp thoáng mát, thấm hút mồ hôi tốt.' }}</p>
                        <p class="mb-0"><strong class="text-dark">Hướng dẫn bảo quản:</strong> {{ $product->care_instructions ?? 'Giặt ở nhiệt độ thường, không dùng chất tẩy mạnh, phơi nơi thoáng gió.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= 3. REVIEWS SECTION (ĐÁNH GIÁ & BÌNH LUẬN) ================= -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-5" id="reviews-section">
    <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-star text-warning"></i>
            <span>Đánh Giá Từ Khách Hàng ({{ $reviewsCount }})</span>
        </h5>
    </div>
    <div class="card-body p-4">
        <!-- Overview Rating Box -->
        <div class="row align-items-center mb-4 bg-light p-4 rounded-4">
            <div class="col-md-4 text-center border-end">
                <h1 class="display-3 fw-extrabold text-dark mb-0">{{ number_format($avgRating, 1) }}</h1>
                <div class="text-warning fs-5 mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-{{ $i <= round($avgRating) ? 'solid' : 'regular' }} fa-star"></i>
                    @endfor
                </div>
                <span class="text-muted small">Dựa trên {{ $reviewsCount }} lượt đánh giá</span>
            </div>
            <div class="col-md-8 ps-md-4 mt-3 mt-md-0">
                <h6 class="fw-bold text-dark mb-1.5">Trải nghiệm thực tế từ người mua</h6>
                <p class="small text-muted mb-0">100% đánh giá là từ khách hàng đã mua và trải nghiệm sản phẩm tại ATINO Store.</p>
            </div>
        </div>

        <!-- List Reviews -->
        <div class="space-y-4 mb-4">
            @forelse($product->reviews as $review)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width: 38px; height: 38px;">
                                {{ strtoupper(substr($review->user->name ?? 'K', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 small">{{ $review->user->name ?? 'Khách hàng ẩn danh' }}</h6>
                                <span class="extra-small text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="text-warning extra-small">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </div>
                    </div>
                    <p class="small text-dark mb-2">{{ $review->comment }}</p>
                    
                    <!-- Review Images -->
                    @if($review->images && $review->images->isNotEmpty())
                        <div class="d-flex gap-2 mb-2">
                            @foreach($review->images as $revImg)
                                <img src="{{ asset('storage/' . $revImg->image_url) }}" alt="Ảnh đánh giá" class="rounded-3 border object-fit-cover" style="width: 65px; height: 65px;">
                            @endforeach
                        </div>
                    @endif

                    <!-- Phản Hồi Từ Cửa Hàng (Nếu Có) -->
                    @if($review->admin_reply)
                        <div class="mt-2.5 p-3 rounded-3 bg-light border-start border-3 border-dark ms-2 ms-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge bg-dark text-white extra-small px-2.5 py-1 rounded-pill fw-bold">
                                    <i class="fa-solid fa-store me-1 text-warning"></i>Phản Hồi Từ ATINO Store
                                </span>
                                @if($review->replied_at)
                                    <span class="text-muted extra-small">{{ $review->replied_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                            <p class="mb-0 small text-secondary">{{ $review->admin_reply }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="fa-regular fa-comment-dots fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                    Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên trải nghiệm!
                </div>
            @endforelse
        </div>

        <!-- Form Gửi Đánh Giá Sản Phẩm (Chỉ người đã mua mới được gửi) -->
        <div class="mt-4 pt-4 border-top">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-pen-to-square text-dark me-2"></i>Viết Đánh Giá Của Bạn</h6>
            
            @if(Auth::check())
                @php
                    $canReview = \App\Models\Order::where('user_id', Auth::id())
                        ->where('status', 'completed')
                        ->whereHas('items', function ($q) use ($product) {
                            $q->where('product_id', $product->id)
                              ->orWhere('product_name', $product->name);
                        })
                        ->exists();
                @endphp

                @if($canReview)
                    <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-light p-3.5 rounded-4 border">
                        @csrf
                        <!-- Chọn số sao -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">Mức độ hài lòng <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 align-items-center">
                                @for($star = 5; $star >= 1; $star--)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="star-{{ $star }}" value="{{ $star }}" {{ $star === 5 ? 'checked' : '' }}>
                                        <label class="form-check-label text-warning font-weight-medium" for="star-{{ $star }}">
                                            {{ $star }} <i class="fa-solid fa-star"></i>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Nội dung bình luận -->
                        <div class="mb-3">
                            <label for="comment" class="form-label fw-bold small text-dark mb-1">Bình luận chi tiết <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm rounded-3 @error('comment') is-invalid @enderror" id="comment" name="comment" rows="3" placeholder="Chia sẻ cảm nhận của bạn về chất vải, form dáng, độ vừa vặn..." required minlength="5">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Upload hình ảnh đánh giá -->
                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold small text-dark mb-1">Ảnh thực tế sản phẩm (Tùy chọn)</label>
                            <input type="file" class="form-control form-control-sm rounded-3" id="images" name="images[]" accept="image/*" multiple>
                        </div>

                        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold btn-sm">
                            <i class="fa-solid fa-paper-plane me-1.5"></i> Gửi Đánh Giá
                        </button>
                    </form>
                @else
                    <div class="alert alert-light border rounded-3 p-3 small text-muted">
                        <i class="fa-solid fa-circle-info text-primary me-1.5"></i> Bạn cần mua và nhận hàng thành công sản phẩm này để có thể gửi đánh giá trải nghiệm thực tế.
                    </div>
                @endif
            @else
                <div class="alert alert-light border rounded-3 p-3 small text-muted">
                    <i class="fa-solid fa-user-lock text-dark me-1.5"></i> Vui lòng <a href="{{ route('login') }}" class="fw-bold text-dark">Đăng nhập</a> để viết đánh giá cho sản phẩm.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ================= MODAL 1: BẢNG HƯỚNG DẪN CHỌN SIZE & TÍNH SIZE THÔNG MINH (ATINO SMART FIT FINDER) ================= -->
@include('shop.components.size-advisor-modal', ['product' => $product, 'sizes' => $sizes ?? collect()])

<!-- ================= MODAL 2: LIGHTBOX XEM ẢNH PHÓNG TO ================= -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="btn btn-dark btn-sm rounded-circle position-absolute top-0 end-0 m-3 shadow z-3" data-bs-dismiss="modal" aria-label="Close" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-xmark fs-6"></i>
                </button>
                <img id="lightbox-main-img" src="{{ $thumbnail && $thumbnail->image_url ? asset('storage/' . $thumbnail->image_url) : '' }}" class="img-fluid rounded-4 shadow-lg bg-white" style="max-height: 85vh; width: auto;" alt="{{ $product->name }}">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var currentCalculatedSize = 'L';
    var productGalleryImages = [
        @foreach($allImages as $img)
            {
                id: {{ $img->id }},
                color_id: {{ $img->color_id ? $img->color_id : 'null' }},
                url: "{{ asset('storage/' . $img->image_url) }}",
                is_thumbnail: {{ $img->is_thumbnail ? 'true' : 'false' }}
            },
        @endforeach
    ];

    // Đổi ảnh hiển thị chính với hiệu ứng mượt mà
    function changeMainImage(imgUrl, element, triggerColorSync = true) {
        var mainImg = document.getElementById('main-product-img');
        var lightboxImg = document.getElementById('lightbox-main-img');
        if (mainImg && mainImg.src !== imgUrl) {
            mainImg.style.opacity = '0.35';
            setTimeout(function() {
                mainImg.src = imgUrl;
                mainImg.style.opacity = '1';
            }, 120);
        }
        if (lightboxImg) {
            lightboxImg.src = imgUrl;
        }
        document.querySelectorAll('.thumb-box').forEach(box => {
            box.classList.remove('active');
        });
        if (element) {
            element.classList.add('active');
            try {
                element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } catch (e) {}

            // Two-way interaction: nếu click thumbnail có gán màu sắc, tự động chọn nút màu tương ứng!
            if (triggerColorSync) {
                var colorId = element.getAttribute('data-color-id');
                if (colorId) {
                    var targetColorBtn = $(`.color-pill-btn[data-color-id="${colorId}"]`);
                    if (targetColorBtn.length && !targetColorBtn.hasClass('active')) {
                        targetColorBtn.trigger('click');
                    }
                }
            }
        }
    }

    // Mở Lightbox xem ảnh lớn
    function openImageLightbox() {
        var modalEl = document.getElementById('imageLightboxModal');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    var currentCalculatedSize = 'L';
    var currentCalculatedType = 'tops';

    // Đổi hiển thị giữa Bảng size Áo và Quần trong Tab 2
    function switchChartTable(type) {
        if (type === 'tops') {
            $('#chart-table-tops').removeClass('d-none');
            $('#chart-table-bottoms').addClass('d-none');
            $('#btn-show-top-chart').addClass('active');
            $('#btn-show-bottom-chart').removeClass('active');
        } else {
            $('#chart-table-tops').addClass('d-none');
            $('#chart-table-bottoms').removeClass('d-none');
            $('#btn-show-top-chart').removeClass('active');
            $('#btn-show-bottom-chart').addClass('active');
        }
    }

    // Thuật toán Tính Size Đa Chiều Chuẩn Xác
    function calculateSmartAdvisor() {
        var h = parseInt($('#fit-range-height').val()) || 170;
        var w = parseInt($('#fit-range-weight').val()) || 65;
        var fitPref = $('input[name="fit_preference"]:checked').val() || 'regular';
        var apparelType = $('input[name="fit_apparel_type"]:checked').val() || 'tops';
        currentCalculatedType = apparelType;

        $('#display-height-val').text(h);
        $('#display-weight-val').text(w);

        // 1. Tính chỉ số BMI & Phân loại vóc dáng
        var hM = h / 100;
        var bmi = (w / (hM * hM)).toFixed(1);
        var bmiText = 'Cân đối';
        if (bmi < 18.5) bmiText = 'Dáng gầy / Thanh mảnh';
        else if (bmi <= 22.9) bmiText = 'Vóc dáng cân đối chuẩn';
        else if (bmi <= 24.9) bmiText = 'Hơi đậm người';
        else bmiText = 'Thể trạng đậm / Đầy đặn';
        $('#fit-bmi-badge').text('BMI: ' + bmi + ' (' + bmiText + ')');

        var recommendedSize = 'L';
        var confidence = 95;
        var note = '';

        // 2. Thuật toán chọn size
        if (apparelType === 'tops') {
            // Quy đổi Áo (Tops: S, M, L, XL, XXL)
            if (h < 162 && w < 54) {
                recommendedSize = 'S';
                confidence = 94;
            } else if (h <= 169 && w <= 62) {
                recommendedSize = 'M';
                confidence = 96;
            } else if (h <= 176 && w <= 72) {
                recommendedSize = 'L';
                confidence = 98;
            } else if (h <= 182 && w <= 81) {
                recommendedSize = 'XL';
                confidence = 95;
            } else {
                recommendedSize = 'XXL';
                confidence = 93;
            }

            // Tinh chỉnh theo phong cách mặc mong muốn
            if (fitPref === 'slim') {
                if (recommendedSize === 'M' && (h <= 164 || w <= 56)) recommendedSize = 'S';
                else if (recommendedSize === 'L' && (h <= 171 || w <= 65)) recommendedSize = 'M';
                else if (recommendedSize === 'XL' && (h <= 177 || w <= 74)) recommendedSize = 'L';
                else if (recommendedSize === 'XXL' && (h <= 183 || w <= 83)) recommendedSize = 'XL';
            } else if (fitPref === 'loose') {
                if (recommendedSize === 'S') recommendedSize = 'M';
                else if (recommendedSize === 'M') recommendedSize = 'L';
                else if (recommendedSize === 'L') recommendedSize = 'XL';
                else if (recommendedSize === 'XL') recommendedSize = 'XXL';
            }

            var prefLabel = fitPref === 'slim' ? 'Ôm sát tôn dáng' : (fitPref === 'loose' ? 'Rộng rãi phóng khoáng (Oversize)' : 'Vừa vặn thoải mái');
            note = `Với chiều cao <strong>${h}cm</strong>, cân nặng <strong>${w}kg</strong> và sở thích mặc <strong>${prefLabel}</strong>, áo <strong>Size ${recommendedSize}</strong> sẽ mang đến phom dáng hoàn hảo nhất.`;
        } else {
            // Quy đổi Quần (Bottoms: 28, 29, 30, 31, 32, 33, 34)
            if (w <= 52 || (h <= 160 && w <= 54)) {
                recommendedSize = '28';
                confidence = 93;
            } else if (w <= 57) {
                recommendedSize = '29';
                confidence = 95;
            } else if (w <= 63) {
                recommendedSize = '30';
                confidence = 97;
            } else if (w <= 69) {
                recommendedSize = '31';
                confidence = 98;
            } else if (w <= 75) {
                recommendedSize = '32';
                confidence = 96;
            } else if (w <= 80) {
                recommendedSize = '33';
                confidence = 94;
            } else {
                recommendedSize = '34';
                confidence = 92;
            }

            if (fitPref === 'slim') {
                if (recommendedSize === '30' && w <= 60) recommendedSize = '29';
                else if (recommendedSize === '31' && w <= 66) recommendedSize = '30';
                else if (recommendedSize === '32' && w <= 72) recommendedSize = '31';
            } else if (fitPref === 'loose') {
                if (recommendedSize === '28') recommendedSize = '29';
                else if (recommendedSize === '29') recommendedSize = '30';
                else if (recommendedSize === '30') recommendedSize = '31';
                else if (recommendedSize === '31') recommendedSize = '32';
                else if (recommendedSize === '32') recommendedSize = '33';
                else if (recommendedSize === '33') recommendedSize = '34';
            }

            var prefLabelPants = fitPref === 'slim' ? 'Ôm gọn ống côn' : (fitPref === 'loose' ? 'Thoải mái ống đứng rộng' : 'Vừa vặn ống suông chuẩn');
            note = `Với thể trạng <strong>${w}kg</strong>, chiều cao <strong>${h}cm</strong> và phom <strong>${prefLabelPants}</strong>, quần <strong>Size ${recommendedSize}</strong> vừa vặn vòng eo và tôn chiều dài chân.`;
        }

        currentCalculatedSize = recommendedSize;

        // 3. Render lên giao diện Modal
        $('#fit-result-size-code').text(recommendedSize);
        $('#btn-apply-size-text').text('Size ' + recommendedSize);
        $('#fit-confidence-text').text(confidence + '% Phù Hợp');
        $('#fit-confidence-bar').css('width', confidence + '%').attr('aria-valuenow', confidence);
        $('#fit-recommendation-note').html('<i class="fa-solid fa-circle-check text-success me-1"></i> ' + note);

        var formName = fitPref === 'slim' ? 'Form Ôm Slim Fit' : (fitPref === 'loose' ? 'Form Rộng Oversize' : 'Form Chuẩn Regular Fit');
        $('#fit-result-fit-name').text(formName);

        // Kiểm tra xem sản phẩm có nút size này không
        var matchingBtn = $('.size-pill-btn').filter(function() {
            var sName = $(this).data('size-name').toString().trim().toUpperCase();
            return sName === recommendedSize.toUpperCase() || sName === ('SIZE ' + recommendedSize).toUpperCase();
        });

        if (matchingBtn.length > 0) {
            $('#fit-stock-warning').addClass('d-none');
            $('#btn-apply-recommended-size').prop('disabled', false).removeClass('btn-secondary').addClass('btn-warning');
        } else {
            $('#fit-stock-warning').removeClass('d-none').html('<i class="fa-solid fa-triangle-exclamation me-1"></i> Size ' + recommendedSize + ' hiện không có sẵn trong mẫu này. Vui lòng tham khảo size lân cận!');
        }

        // Highlight dòng tương ứng trong bảng quy đổi
        $('[data-size-row]').removeClass('table-warning table-primary fw-bold text-primary');
        $('[data-size-row="' + recommendedSize + '"]').addClass('table-warning fw-bold text-dark');
    }

    // Áp dụng chọn size vào sản phẩm từ Modal
    function applyAdvisorSize() {
        var targetBtn = $('.size-pill-btn').filter(function() {
            var sName = $(this).data('size-name').toString().trim().toUpperCase();
            return sName === currentCalculatedSize.toUpperCase() || sName === ('SIZE ' + currentCalculatedSize).toUpperCase();
        });

        if (targetBtn.length) {
            targetBtn.trigger('click');

            // Lưu cài đặt cá nhân vào localStorage nếu được chọn
            if ($('#fit-save-pref-checkbox').is(':checked')) {
                var advisorData = {
                    height: parseInt($('#fit-range-height').val()) || 170,
                    weight: parseInt($('#fit-range-weight').val()) || 65,
                    preference: $('input[name="fit_preference"]:checked').val() || 'regular',
                    apparelType: currentCalculatedType,
                    size: currentCalculatedSize
                };
                localStorage.setItem('atino_fit_advisor_data', JSON.stringify(advisorData));
                updateSmartHintBar(advisorData);
            }

            var modalEl = document.getElementById('sizeGuideModal');
            if (modalEl) {
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Đã tự động chọn Size ' + currentCalculatedSize,
                    showConfirmButton: false,
                    timer: 2200
                });
            }
        } else {
            alert('Size ' + currentCalculatedSize + ' hiện không có sẵn cho mẫu sản phẩm này.');
        }
    }

    // Áp dụng size từ thanh gợi ý nhanh trên trang chi tiết
    function applyHintSize() {
        if (currentCalculatedSize) {
            applyAdvisorSize();
        }
    }

    // Cập nhật thanh thông báo gợi ý thông minh trên trang chi tiết
    function updateSmartHintBar(data) {
        if (!data) return;
        $('#hint-user-specs').text(data.height + 'cm, ' + data.weight + 'kg');
        $('#hint-user-size').text('Size ' + currentCalculatedSize);
        $('#smart-size-hint-container').removeClass('d-none');
    }

    function calculateTotalPrice() {
        const input = document.getElementById('prod-qty');
        let qty = parseInt(input.value) || 1;
        if (qty < 1) {
            qty = 1;
            input.value = 1;
        }
        const priceBox = document.getElementById('product-price-box');
        if (!priceBox) return;

        const unitPrice = parseFloat(priceBox.getAttribute('data-unit-price')) || 0;
        const totalPrice = unitPrice * qty;

        const qtySpan = document.getElementById('total-qty-span');
        const totalDisplay = document.getElementById('display-total-price');
        const previewBox = document.getElementById('total-price-preview');

        if (qtySpan) qtySpan.textContent = qty;
        if (totalDisplay) totalDisplay.textContent = totalPrice.toLocaleString('vi-VN') + 'đ';

        if (previewBox) {
            if (qty > 1) {
                previewBox.classList.remove('d-none');
            } else {
                previewBox.classList.add('d-none');
            }
        }
    }

    function incrementQty() {
        const input = document.getElementById('prod-qty');
        input.value = parseInt(input.value || 1) + 1;
        calculateTotalPrice();
    }

    function decrementQty() {
        const input = document.getElementById('prod-qty');
        if (parseInt(input.value || 1) > 1) {
            input.value = parseInt(input.value) - 1;
            calculateTotalPrice();
        }
    }

    function copyProductLink() {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(window.location.href).then(function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Đã sao chép liên kết sản phẩm!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    alert('Đã sao chép liên kết sản phẩm!');
                }
            });
        }
    }

    $(document).ready(function() {
        $('#prod-qty').on('input change keyup', function() {
            calculateTotalPrice();
        });

        function updateSelectedVariant() {
            var activeColorBtn = $('.color-pill-btn.active');
            var activeSizeBtn = $('.size-pill-btn.active');

            var activeColorId = activeColorBtn.data('color-id');
            var activeSizeId = activeSizeBtn.data('size-id');

            var activeColorName = activeColorBtn.data('color-name');
            var activeSizeName = activeSizeBtn.data('size-name');

            if (activeColorName) $('#selected-color-name').text(activeColorName);
            if (activeSizeName) $('#selected-size-name').text(activeSizeName);

            var variants = $('#product-variants-container').data('variants') || [];

            var matchedVariant = variants.find(function(v) {
                var matchSize = !activeSizeId || v.size_id == activeSizeId;
                var matchColor = !activeColorId || v.color_id == activeColorId;
                return matchSize && matchColor;
            });

            if (matchedVariant) {
                $('#selected-variant-id').val(matchedVariant.id);

                var stock = matchedVariant.stock_quantity;
                if (stock > 0) {
                    $('#variant-stock-status').html(`
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                            <i class="fa-solid fa-circle-check me-1"></i>Còn hàng (<strong>${stock}</strong> món có sẵn)
                        </span>
                    `);
                    $('#btn-add-to-cart-action').prop('disabled', false).html('<i class="fa-solid fa-cart-plus me-2"></i> Thêm Vào Giỏ Hàng');
                    $('#btn-buy-now-action').prop('disabled', false).html('<i class="fa-solid fa-bolt me-2"></i> MUA NGAY');
                } else {
                    $('#variant-stock-status').html(`
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                            <i class="fa-solid fa-ban me-1"></i>Tạm hết hàng mẫu này
                        </span>
                    `);
                    $('#btn-add-to-cart-action').prop('disabled', true).html('<i class="fa-solid fa-ban me-2"></i> TẠM HẾT HÀNG');
                    $('#btn-buy-now-action').prop('disabled', true).html('<i class="fa-solid fa-ban me-2"></i> TẠM HẾT HÀNG');
                }

                var vPrice = (matchedVariant.sale_price && matchedVariant.sale_price > 0 && matchedVariant.sale_price < matchedVariant.price)
                    ? matchedVariant.sale_price
                    : matchedVariant.price;

                if (vPrice && vPrice > 0) {
                    $('#product-price-box').attr('data-unit-price', vPrice);
                    $('#display-unit-price').text(parseInt(vPrice).toLocaleString('vi-VN') + 'đ');
                    calculateTotalPrice();
                }
            } else {
                $('#selected-variant-id').val('');
            }

            // TỰ ĐỘNG THAY ĐỔI ẢNH THEO MÀU SẮC BIẾN THỂ ĐƯỢC CHỌN
            if (activeColorId && typeof productGalleryImages !== 'undefined' && productGalleryImages.length > 0) {
                var matchedImage = productGalleryImages.find(function(img) {
                    return img.color_id == activeColorId;
                });

                if (matchedImage) {
                    var targetThumb = document.querySelector(`.thumb-box[data-image-id="${matchedImage.id}"]`);
                    changeMainImage(matchedImage.url, targetThumb, false);
                } else {
                    // Nếu màu này chưa gán ảnh riêng, chuyển về ảnh thumbnail chính mặc định
                    var defaultThumb = productGalleryImages.find(function(img) { return img.is_thumbnail; }) || productGalleryImages[0];
                    if (defaultThumb) {
                        var defaultBox = document.querySelector(`.thumb-box[data-image-id="${defaultThumb.id}"]`);
                        if (defaultBox && !defaultBox.classList.contains('active')) {
                            changeMainImage(defaultThumb.url, defaultBox, false);
                        }
                    }
                }
            }
        }

        $(document).on('click', '.color-pill-btn', function() {
            $('.color-pill-btn').removeClass('active');
            $(this).addClass('active');
            updateSelectedVariant();
        });

        $(document).on('click', '.size-pill-btn', function() {
            $('.size-pill-btn').removeClass('active');
            $(this).addClass('active');
            updateSelectedVariant();
        });

        // Flash Sale Countdown
        var $fsBox = $('#flash-sale-countdown-box');
        if ($fsBox.length) {
            var endTimeStr = $fsBox.data('endtime');
            if (endTimeStr) {
                var countDate = new Date(endTimeStr.replace(/-/g, "/")).getTime();
                var timerInterval = setInterval(function() {
                    var now = new Date().getTime();
                    var distance = countDate - now;

                    if (distance < 0) {
                        clearInterval(timerInterval);
                        $fsBox.html('<span class="text-warning fw-bold">Flash Sale đã kết thúc</span>');
                        return;
                    }

                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $('#fs-hours').text(hours < 10 ? '0' + hours : hours);
                    $('#fs-mins').text(minutes < 10 ? '0' + minutes : minutes);
                    $('#fs-secs').text(seconds < 10 ? '0' + seconds : seconds);
                }, 1000);
            }
        }

        updateSelectedVariant();
        calculateTotalPrice();

        // Lắng nghe sự kiện thay đổi trên các thanh trượt & lựa chọn của Smart Advisor
        $('#fit-range-height, #fit-range-weight').on('input change', function() {
            calculateSmartAdvisor();
        });

        $('input[name="fit_preference"], input[name="fit_apparel_type"]').on('change', function() {
            calculateSmartAdvisor();
        });

        // Tự động khôi phục số đo từ localStorage nếu người dùng đã từng sử dụng
        try {
            var savedAdvisor = localStorage.getItem('atino_fit_advisor_data');
            if (savedAdvisor) {
                var parsedData = JSON.parse(savedAdvisor);
                if (parsedData.height) $('#fit-range-height').val(parsedData.height);
                if (parsedData.weight) $('#fit-range-weight').val(parsedData.weight);
                if (parsedData.preference) {
                    $(`input[name="fit_preference"][value="${parsedData.preference}"]`).prop('checked', true);
                }
                var defaultType = $('#sizeGuideModal').data('default-type') || 'tops';
                $(`input[name="fit_apparel_type"][value="${defaultType}"]`).prop('checked', true);
                
                calculateSmartAdvisor();
                updateSmartHintBar(parsedData);
            } else {
                calculateSmartAdvisor();
            }
        } catch (e) {
            calculateSmartAdvisor();
        }
    });
</script>
@endpush
