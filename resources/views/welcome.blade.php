@extends('layouts.app')

@section('title', 'Atino - Thời Trang Nam Nữ Phong Cách Tối Giản')

@push('styles')
<style>
    /* 1. Global Page Styles */
    body {
        background-color: #ffffff;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* 2. Collection Banner Cards */
    .collection-banner-card {
        position: relative;
        height: 280px;
        border-radius: 20px;
        overflow: hidden;
        background-color: #1e293b;
        background-size: cover;
        background-position: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .collection-banner-card:hover {
        transform: translateY(-4px);
    }

    .collection-banner-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(15,23,42,0.85) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1.5rem;
        color: #ffffff;
    }

    /* 3. Category Tab Pills & Dropdowns */
    .category-tab-dropdown {
        position: relative;
        display: inline-block;
    }

    .category-tab-btn {
        background: transparent;
        border: none;
        color: #475569;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.4rem 1.15rem;
        border-radius: 50rem;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .category-tab-btn:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }

    .category-tab-btn.active {
        background-color: #e0e7ff;
        color: #4f46e5;
        font-weight: 700;
    }

    .category-tab-sub-menu {
        opacity: 0;
        visibility: hidden;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 190px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.15);
        border: 1px solid #e2e8f0;
        padding: 0.5rem;
        z-index: 1050;
        margin-top: 0.35rem;
        transform: translateY(6px);
        transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
    }

    .category-tab-sub-menu::before {
        content: "";
        position: absolute;
        top: -10px;
        left: 0;
        width: 100%;
        height: 10px;
        background: transparent;
    }

    .category-tab-dropdown:hover .category-tab-sub-menu,
    .category-tab-dropdown.is-open .category-tab-sub-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .tab-sub-item {
        color: #334155;
        text-decoration: none;
        font-size: 0.835rem;
        font-weight: 500;
        padding: 0.5rem 0.8rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .tab-sub-item:hover,
    .tab-sub-item.active {
        color: #4f46e5;
        background-color: #f1f5f9;
        font-weight: 600;
    }

    /* 4. Product Card & Hover Effects */
    .atino-product-card {
        border: none;
        background: transparent;
        transition: transform 0.25s ease;
    }

    .atino-product-card:hover {
        transform: translateY(-4px);
    }

    .atino-img-container {
        position: relative;
        background-color: #f8fafc;
        border-radius: 14px;
        overflow: hidden;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .atino-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .atino-product-card:hover .atino-img-container img {
        transform: scale(1.05);
    }

    .badge-discount-red {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #dc2626;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        z-index: 2;
    }

    /* 5. Lookbook Card Hover */
    .lookbook-item {
        cursor: pointer;
        transition: transform 0.25s ease;
    }

    .lookbook-item:hover {
        transform: translateY(-3px);
    }

    .lookbook-item:hover img {
        transform: scale(1.04);
    }
</style>
@endpush

@section('content')
@php
    $realFashionPhotos = [
        'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1562157873-818bc0726f68?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1542272604-780c36856842?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?auto=format&fit=crop&w=800&q=80',
    ];

    $getProductImage = function($product) {
        return $product->image_url;
    };
@endphp

<div class="container py-3">

    {{-- ================= 1. HERO BANNER & BST MỚI ================= --}}
    <section class="mb-5">
        <div class="row g-4">
            @forelse($collections as $col)
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('shop.index', ['collection' => $col->slug]) }}" class="text-decoration-none d-block h-100">
                        <div class="collection-banner-card shadow-sm position-relative overflow-hidden rounded-4" style="background: url('{{ $col->image_url }}') center/cover no-repeat;">
                            <div class="collection-banner-overlay">
                                @if($col->badge_text)
                                    <span class="badge bg-warning text-dark fw-bold mb-1 align-self-start rounded-pill extra-small">{{ $col->badge_text }}</span>
                                @endif
                                <h5 class="fw-bold text-white mb-1">{{ $col->name }}</h5>
                                <span class="text-white-50 extra-small">{{ $col->subtitle ?: 'Bộ sưu tập độc quyền' }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('shop.index', ['collection' => 'summer-2026']) }}" class="text-decoration-none d-block h-100">
                        <div class="collection-banner-card shadow-sm position-relative overflow-hidden rounded-4" style="background: url('https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=600&q=80') center/cover no-repeat;">
                            <div class="collection-banner-overlay">
                                <span class="badge bg-warning text-dark fw-bold mb-1 align-self-start rounded-pill extra-small">BST MÙA HÈ</span>
                                <h5 class="fw-bold text-white mb-1">Summer 2026</h5>
                                <span class="text-white-50 extra-small">Thoáng mát & Thanh lịch</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ================= 2. FLASH SALE BOX (KHỐI SALE TẬP TRUNG) ================= --}}
    @if(!empty($siteSettings['home_show_flash_sale']))
    <section class="mb-5">
        <div class="p-3.5 p-md-4 rounded-4 shadow-sm" style="background: linear-gradient(180deg, #fff7f7 0%, #ffffff 100%); border: 1.5px solid #fed7d7;">
            {{-- Header Flash Sale --}}
            <div class="rounded-4 p-3.5 p-md-4 text-white mb-4 shadow-sm bg-dark d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-danger text-white fw-bold px-3 py-2 rounded-pill fs-6 shadow-xs">
                        <i class="fa-solid fa-bolt me-1"></i> {{ $siteSettings['home_flash_sale_badge'] ?? 'FLASH SALE' }}
                    </span>
                    <div>
                        <h5 class="fw-extrabold text-white mb-0 fs-5">{{ $siteSettings['home_flash_sale_title'] ?? 'Ưu Đãi Đặc Biệt - Săn Sale Giá Sốc' }}</h5>
                        <span class="text-white-50 extra-small">{{ $siteSettings['home_flash_sale_subtitle'] ?? 'Số lượng có hạn, nhanh tay sở hữu!' }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    {{-- Dynamic Countdown Timer --}}
                    <div class="d-flex align-items-center gap-1.5 fs-6 fw-bold">
                        <span class="bg-white text-dark rounded px-2 py-1 shadow-xs" id="flash-hours">00h</span> :
                        <span class="bg-white text-dark rounded px-2 py-1 shadow-xs" id="flash-minutes">00m</span> :
                        <span class="bg-white text-dark rounded px-2 py-1 shadow-xs" id="flash-seconds">00s</span>
                    </div>
                    <a href="{{ route('shop.flashSale') }}" class="btn btn-light btn-sm rounded-pill fw-bold px-3 py-2 text-dark shadow-xs">
                        Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            @if(isset($flashSaleItems) && $flashSaleItems->count() > 0)
                @if($flashSaleItems->count() === 1)
                    {{-- Trường hợp chỉ có 1 sản phẩm Flash Sale: Hiển thị dạng Deal Hot Hero Card --}}
                    @php
                        $singleFlash = $flashSaleItems->first();
                        $product = $singleFlash->product;
                        $percent = $singleFlash->discount_percent;
                        $remaining = $singleFlash->remaining_quantity;
                        $fallbackPhoto = $realFashionPhotos[$product->id % count($realFashionPhotos)];
                        $imgSrc = $getProductImage($product);
                    @endphp
                    <div class="row justify-content-center my-2">
                        <div class="col-lg-8 col-xl-7">
                            <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white p-3.5 p-md-4">
                                <div class="row g-4 align-items-center">
                                    <div class="col-md-5">
                                        <div class="position-relative atino-img-container rounded-3 overflow-hidden shadow-xs">
                                            @if($percent > 0)
                                                <span class="badge-discount-red">-{{ $percent }}%</span>
                                            @endif
                                            <a href="{{ route('shop.show', $product->slug) }}">
                                                <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="w-100 rounded-3" style="height: 270px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ $fallbackPhoto }}';">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small fw-bold px-2.5 py-1 rounded-pill mb-2">
                                            <i class="fa-solid fa-fire me-1"></i> SẢN PHẨM FLASH SALE HÔM NAY
                                        </span>
                                        <h5 class="fw-bold text-dark mb-2">
                                            <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none hover-text-danger">
                                                {{ $product->name }}
                                            </a>
                                        </h5>
                                        <div class="d-flex align-items-baseline gap-2 mb-3">
                                            <span class="fw-extrabold text-danger fs-4">{{ number_format($singleFlash->sale_price) }}đ</span>
                                            <del class="text-muted small">{{ number_format($product->price) }}đ</del>
                                        </div>
                                        <div class="p-2.5 bg-danger-subtle rounded-3 mb-3 extra-small text-danger fw-semibold d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-bolt fs-6"></i>
                                            <span>Chỉ còn lại <strong>{{ $remaining }}</strong> sản phẩm giá ưu đãi!</span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-dark btn-md rounded-pill fw-bold px-4 shadow-sm btn-add-to-cart flex-grow-1" data-id="{{ $product->id }}">
                                                <i class="fa-solid fa-bag-shopping me-2"></i> Thêm vào giỏ
                                            </button>
                                            <a href="{{ route('shop.show', $product->slug) }}" class="btn btn-outline-dark btn-md rounded-pill px-3 fw-bold">
                                                Chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Trường hợp có từ 2 sản phẩm Flash Sale trở lên: Hiển thị Grid chuẩn --}}
                    <div class="row g-3 g-md-4">
                        @foreach($flashSaleItems as $flashSale)
                            @php
                                $product = $flashSale->product;
                                if (!$product || !$product->is_active) continue;
                                $percent = $flashSale->discount_percent;
                                $remaining = $flashSale->remaining_quantity;
                                $fallbackPhoto = $realFashionPhotos[$product->id % count($realFashionPhotos)];
                                $imgSrc = $getProductImage($product);
                            @endphp
                            <div class="col-6 col-md-3 flash-sale-card-item">
                                <div class="card atino-product-card h-100 p-2.5 border shadow-sm bg-white">
                                    <div class="position-relative">
                                        <a href="{{ route('shop.show', $product->slug) }}" class="d-block text-decoration-none">
                                            <div class="atino-img-container">
                                                @if($percent > 0)
                                                    <span class="badge-discount-red">-{{ $percent }}%</span>
                                                @endif
                                                <img src="{{ $imgSrc }}" alt="{{ $product->name }}" style="height: 280px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ $fallbackPhoto }}';">
                                            </div>
                                        </a>
                                        <button type="button" class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm z-2 btn-toggle-wishlist d-flex align-items-center justify-content-center" data-id="{{ $product->id }}" title="Thêm vào yêu thích" style="width: 34px; height: 34px;">
                                            <i class="{{ (Auth::check() && Auth::user()->wishlists && Auth::user()->wishlists->contains('product_id', $product->id)) ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart text-muted' }}"></i>
                                        </button>
                                    </div>

                                    <div class="pt-3 px-1 d-flex flex-column justify-content-between flex-grow-1">
                                        <div>
                                            <span class="extra-small text-muted text-uppercase fw-semibold" style="font-size: 0.65rem;">
                                                {{ $product->category->name ?? 'Thời trang Atino' }}
                                            </span>
                                            <h6 class="card-title text-dark fw-bold small mb-1 text-truncate" title="{{ $product->name }}">
                                                <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none">
                                                    {{ $product->name }}
                                                </a>
                                            </h6>
                                            
                                            <div class="small text-danger fw-semibold mb-2 d-flex align-items-center gap-1" style="font-size: 0.725rem;">
                                                <i class="fa-solid fa-fire text-danger"></i>
                                                <span>Còn lại: <strong>{{ $remaining }}</strong> sản phẩm</span>
                                            </div>
                                        </div>

                                        <div class="pt-2 border-top border-light">
                                            <div class="d-flex align-items-baseline gap-2 mb-2">
                                                <span class="fw-extrabold text-danger fs-5 me-1">{{ number_format($flashSale->sale_price) }}đ</span>
                                                <del class="extra-small text-muted" style="font-size: 0.75rem;">{{ number_format($product->price) }}đ</del>
                                            </div>
                                            <button type="button" class="btn btn-dark btn-sm w-100 rounded-pill fw-bold shadow-sm btn-add-to-cart py-2" data-id="{{ $product->id }}">
                                                <i class="fa-solid fa-bag-shopping me-1"></i> Thêm vào giỏ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Phân trang Flash Sale chỉ hiển thị khi có trên 4 sản phẩm --}}
                    @if($flashSaleItems->count() > 4)
                        <div class="d-flex align-items-center justify-content-center gap-3 mt-4" id="flash-pagination-container">
                            <button type="button" class="btn btn-light rounded-circle shadow-sm border p-0 d-inline-flex align-items-center justify-content-center" id="btn-flash-prev" style="width: 40px; height: 40px;" title="Trang trước">
                                <i class="fa-solid fa-chevron-left text-dark small"></i>
                            </button>
                            
                            <span class="fw-bold text-dark fs-6 px-2" id="flash-page-indicator">1/2</span>
                            
                            <button type="button" class="btn btn-light rounded-circle shadow-sm border p-0 d-inline-flex align-items-center justify-content-center" id="btn-flash-next" style="width: 40px; height: 40px;" title="Trang tiếp">
                                <i class="fa-solid fa-chevron-right text-dark small"></i>
                            </button>
                        </div>
                    @endif
                @endif
            @else
                <div class="text-center py-4 bg-white rounded-4 border">
                    <i class="fa-solid fa-clock text-muted fs-3 mb-2"></i>
                    <p class="mb-0 text-muted fw-semibold">Hiện chưa có sản phẩm nào trong chương trình Flash Sale.</p>
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ================= 3. TOP 5 SẢN PHẨM BÁN CHẠY NHẤT ================= --}}
    <section class="mb-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-fire text-danger me-1"></i> BEST SELLERS
                    </span>
                    <h2 class="fw-extrabold text-dark mb-0" style="letter-spacing: -0.03em; font-size: 1.8rem;">
                        TOP 5 SẢN PHẨM BÁN CHẠY NHẤT
                    </h2>
                </div>
                <p class="text-muted small mb-0">Tự động thống kê dựa trên số lượng mua thực tế từ các đơn hàng đã giao thành công.</p>
            </div>

            <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm rounded-pill fw-bold px-3 py-2 text-nowrap align-self-start align-self-md-auto shadow-sm">
                Xem tất cả sản phẩm <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Product Grid: 5 sản phẩm hiển thị tự động trên 1 hàng ở desktop --}}
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3 g-md-3" id="home-product-grid">
            @forelse($featuredProducts ?? [] as $product)
                @php
                    $effectiveSalePrice = $product->effective_sale_price;
                    $hasDiscount = !is_null($effectiveSalePrice) && $effectiveSalePrice < $product->price;
                    $percent = $product->discount_percent;
                    $parentCatSlug = $product->parent_slug ?? 'all';
                    $childCatSlug = $product->child_slug ?? 'all';
                    $fallbackPhoto = $realFashionPhotos[$product->id % count($realFashionPhotos)];
                    $imgSrc = $getProductImage($product);
                @endphp
                
                <div class="col product-item-card" data-category="{{ $parentCatSlug }}" data-child-category="{{ $childCatSlug }}">
                    <div class="card atino-product-card h-100 p-2.5 border shadow-sm">
                        <div class="position-relative">
                            <a href="{{ route('shop.show', $product->slug) }}" class="d-block text-decoration-none">
                                <div class="atino-img-container">
                                    {{-- Huy hiệu thứ hạng Top Bán Chạy --}}
                                    @if($loop->iteration === 1)
                                        <span class="badge bg-warning text-dark fw-extrabold position-absolute top-0 start-0 m-2 z-2 shadow-sm" style="font-size: 0.7rem; padding: 0.3rem 0.55rem; border-radius: 6px; letter-spacing: 0.02em;">
                                            <i class="fa-solid fa-crown me-1 text-dark"></i>TOP 1
                                        </span>
                                    @elseif($loop->iteration === 2)
                                        <span class="badge bg-secondary-subtle text-dark fw-bold border position-absolute top-0 start-0 m-2 z-2 shadow-sm" style="font-size: 0.7rem; padding: 0.3rem 0.55rem; border-radius: 6px;">
                                            <i class="fa-solid fa-medal me-1 text-secondary"></i>TOP 2
                                        </span>
                                    @elseif($loop->iteration === 3)
                                        <span class="badge bg-danger-subtle text-danger fw-bold border border-danger-subtle position-absolute top-0 start-0 m-2 z-2 shadow-sm" style="font-size: 0.7rem; padding: 0.3rem 0.55rem; border-radius: 6px;">
                                            <i class="fa-solid fa-award me-1 text-danger"></i>TOP 3
                                        </span>
                                    @else
                                        <span class="badge bg-dark text-white fw-bold position-absolute top-0 start-0 m-2 z-2 shadow-sm" style="font-size: 0.7rem; padding: 0.3rem 0.55rem; border-radius: 6px;">
                                            TOP {{ $loop->iteration }}
                                        </span>
                                    @endif

                                    @if($hasDiscount && $percent > 0)
                                        <span class="badge-discount-red" style="top: 36px;">-{{ $percent }}%</span>
                                    @endif

                                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}" style="height: 260px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ $fallbackPhoto }}';">
                                </div>
                            </a>
                            <button type="button" class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm z-2 btn-toggle-wishlist d-flex align-items-center justify-content-center" data-id="{{ $product->id }}" title="Thêm vào yêu thích" style="width: 34px; height: 34px;">
                                <i class="{{ (Auth::check() && Auth::user()->wishlists && Auth::user()->wishlists->contains('product_id', $product->id)) ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart text-muted' }}"></i>
                            </button>
                        </div>

                        <div class="card-body px-2 pt-3 pb-1 d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted extra-small font-weight-medium text-uppercase tracking-wider" style="font-size: 0.7rem;">
                                        {{ $product->category->name ?? 'Thời trang Atino' }}
                                    </span>
                                </div>

                                <h6 class="card-title text-dark fw-bold small mb-1.5 text-truncate" title="{{ $product->name }}">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h6>

                                {{-- Số lượng đã bán thực tế từ đơn hoàn thành --}}
                                <div class="extra-small text-muted mb-2 d-flex align-items-center gap-1.5" style="font-size: 0.725rem;">
                                    <i class="fa-solid fa-bag-shopping text-success"></i>
                                    <span>Đã bán: <strong class="text-dark fw-bold">{{ number_format($product->total_sold) }}</strong> sản phẩm</span>
                                </div>
                            </div>

                            <div class="pt-2 border-top border-light">
                                <div class="d-flex align-items-baseline gap-2 mb-3">
                                    @if($hasDiscount)
                                        <span class="fw-extrabold text-danger fs-5 me-1">{{ number_format($effectiveSalePrice) }}đ</span>
                                        <del class="extra-small text-muted" style="font-size: 0.75rem;">{{ number_format($product->price) }}đ</del>
                                    @else
                                        <span class="fw-extrabold text-dark fs-5">{{ number_format($product->price) }}đ</span>
                                    @endif
                                </div>

                                <button type="button" class="btn btn-dark btn-md w-100 rounded-pill fw-bold shadow-sm btn-add-to-cart py-2" data-id="{{ $product->id }}">
                                    <i class="fa-solid fa-bag-shopping me-2"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5 bg-white rounded-4 shadow-sm">
                    <i class="fa-solid fa-box-open fs-1 mb-2 opacity-50"></i>
                    <p class="mb-0 fw-semibold">Chưa có dữ liệu sản phẩm bán chạy.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ================= 4. OUTFIT PHỐI SẴN GỢI Ý ================= --}}
    <section class="mb-5">
        <div class="p-4 p-md-4 rounded-4 shadow-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 extra-small fw-bold">
                            <i class="fa-solid fa-shirt me-1"></i> MIX & MATCH
                        </span>
                        <h4 class="fw-extrabold text-dark mb-0" style="letter-spacing: -0.02em;">OUTFIT PHỐI SẴN GỢI Ý</h4>
                    </div>
                    <p class="text-muted extra-small mb-0">Các bộ trang phục được stylist Atino gợi ý phối theo từng phong cách và dịp sử dụng.</p>
                </div>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold align-self-start align-self-md-auto shadow-xs">
                    Xem tất cả outfit <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-3">
                @forelse($outfits ?? [] as $outfitItem)
                    <div class="col-md-4 col-lg-3">
                        <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100 position-relative" style="background: url('{{ $outfitItem->hero_image_url }}') center/cover no-repeat; min-height: 250px;">
                            <div class="p-3.5 d-flex flex-column justify-content-between h-100 text-white" style="background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(15,23,42,0.85) 100%); min-height: 250px;">
                                <span class="badge bg-dark text-white rounded-pill px-2.5 py-1.5 extra-small align-self-start border border-secondary shadow-sm">
                                    {{ $outfitItem->badge ?: 'OUTFIT' }}
                                </span>
                                <div>
                                    <h6 class="fw-bold text-white mb-1">{{ $outfitItem->name }}</h6>
                                    @if($outfitItem->tagline)
                                        <span class="text-white-50 extra-small d-block mb-2 text-truncate">{{ $outfitItem->tagline }}</span>
                                    @endif
                                    <a href="{{ route('outfits.show', $outfitItem->slug) }}" class="btn btn-light btn-sm rounded-pill w-100 fw-bold text-dark mt-1 shadow-xs" style="font-size: 0.775rem;">
                                        Xem Chi Tiết Outfit <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4 bg-white rounded-4 border">
                        <p class="mb-0">Đang cập nhật các bộ outfit phối sẵn mới nhất...</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ================= 5. SERVICE HIGHLIGHTS ================= --}}
    @if(!empty($siteSettings['home_show_features']))
    <section class="mb-5">
        <div class="bg-white rounded-4 p-4 shadow-sm border border-light">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3 border-end-md">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="{{ $siteSettings['feature_1_icon'] ?? 'fa-solid fa-truck-fast' }} fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $siteSettings['feature_1_title'] ?? 'Miễn Phí Vận Chuyển' }}</h6>
                    <p class="text-muted extra-small mb-0">{{ $siteSettings['feature_1_desc'] ?? 'Cho tất cả đơn hàng từ 499.000đ toàn quốc' }}</p>
                </div>
                
                <div class="col-6 col-md-3 border-end-md">
                    <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="{{ $siteSettings['feature_2_icon'] ?? 'fa-solid fa-arrows-rotate' }} fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $siteSettings['feature_2_title'] ?? 'Đổi Trả 30 Ngày' }}</h6>
                    <p class="text-muted extra-small mb-0">{{ $siteSettings['feature_2_desc'] ?? 'Miễn phí đổi size & mẫu trong 30 ngày' }}</p>
                </div>

                <div class="col-6 col-md-3 border-end-md">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background-color: #f3e8ff; color: #9333ea;">
                        <i class="{{ $siteSettings['feature_3_icon'] ?? 'fa-solid fa-shield-halved' }} fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $siteSettings['feature_3_title'] ?? 'Thanh Toán An Toàn' }}</h6>
                    <p class="text-muted extra-small mb-0">{{ $siteSettings['feature_3_desc'] ?? 'Bảo mật 100% giao dịch VNPay, MoMo, COD' }}</p>
                </div>

                <div class="col-6 col-md-3">
                    <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                        <i class="{{ $siteSettings['feature_4_icon'] ?? 'fa-solid fa-headset' }} fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $siteSettings['feature_4_title'] ?? 'Hỗ Trợ 24/7' }}</h6>
                    <p class="text-muted extra-small mb-0">{{ $siteSettings['feature_4_desc'] ?? 'Tư vấn chọn size & giải đáp mọi thắc mắc' }}</p>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ================= 6. CUSTOMER REVIEWS SECTION ================= --}}
    <section class="mb-5">
        <div class="p-4 p-md-5 rounded-4 shadow-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
            <div class="text-center mb-4">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold extra-small text-uppercase mb-2">ĐÁNH GIÁ THỰC TẾ</span>
                <h2 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.03em; font-size: 2rem;">KHÁCH HÀNG NÓI VỀ ATINO</h2>
                <p class="text-muted small mb-0">Hơn 50,000+ khách hàng đã tin tưởng và hài lòng với chất lượng Atino.</p>
            </div>

            <div class="row g-4" id="customer-reviews-row">
                @forelse($customerReviews ?? [] as $rev)
                    @php
                        $revUser = $rev->user;
                        $userName = $revUser ? $revUser->name : 'Khách hàng Atino';
                        $userAvatar = $revUser ? $revUser->avatar_url : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=0f172a&color=ffffff&bold=true';
                        $rating = $rev->rating ?: 5;
                    @endphp
                    <div class="col-md-4 customer-review-card-item">
                        <div class="card h-100 border-0 rounded-4 p-4 shadow-sm bg-white d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">{{ $userName }}</h6>
                                        <span class="extra-small text-success fw-semibold"><i class="fa-solid fa-circle-check me-1"></i>Đã mua hàng</span>
                                    </div>
                                </div>

                                {{-- Rating Stars --}}
                                <div class="text-warning mb-2" style="font-size: 0.85rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                    @endfor
                                </div>

                                @if($rev->product)
                                    <div class="extra-small text-muted mb-2 text-truncate">
                                        <i class="fa-solid fa-bag-shopping me-1"></i> {{ $rev->product->name }}
                                    </div>
                                @endif

                                <p class="small text-secondary mb-0 leading-relaxed">
                                    "{{ $rev->comment }}"
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4 bg-white rounded-4 border">
                        <p class="mb-0">Chưa có đánh giá nào từ khách hàng.</p>
                    </div>
                @endforelse
            </div>

            {{-- Reviews Slider Pagination Controls (< 1/2 >) --}}
            @if(isset($customerReviews) && $customerReviews->count() > 3)
                <div class="d-flex align-items-center justify-content-center gap-3 mt-4" id="review-pagination-container">
                    <button type="button" class="btn btn-light rounded-circle shadow-sm border p-0 d-inline-flex align-items-center justify-content-center" id="btn-review-prev" style="width: 42px; height: 42px;" title="Xem các đánh giá trước">
                        <i class="fa-solid fa-chevron-left text-dark"></i>
                    </button>

                    <span class="fw-bold text-dark fs-6 px-2" id="review-page-indicator">1/2</span>

                    <button type="button" class="btn btn-light rounded-circle shadow-sm border p-0 d-inline-flex align-items-center justify-content-center" id="btn-review-next" style="width: 42px; height: 42px;" title="Xem tiếp các đánh giá khác">
                        <i class="fa-solid fa-chevron-right text-dark"></i>
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- ================= 7. SECTION @ATINO_STYLE (Shoppable Lookbook Grid) ================= --}}
    <section class="mb-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">
                <i class="fa-brands fa-instagram text-danger me-1"></i> @ATINO_STYLE / LOOKBOOK
            </h3>
            <p class="text-muted small mb-2">Nhấp vào ảnh để xem & mua các sản phẩm trong phối đồ #AtinoStyle</p>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa-brands fa-instagram me-1"></i> Theo dõi trên Instagram
            </a>
        </div>

        <div class="row g-3">
            @forelse($lookbooks ?? [] as $lb)
                @php
                    $prodCount = $lb->products ? $lb->products->count() : 0;
                    $buttonLabel = $lb->button_text ?: ("Xem & Mua " . $prodCount . " Sản Phẩm");
                @endphp
                <div class="col-6 col-md-3">
                    <div class="lookbook-item overflow-hidden rounded-3 shadow-sm position-relative group-hover" data-bs-toggle="modal" data-bs-target="#lookbookModal{{ $lb->id }}">
                        <img src="{{ $lb->image_url }}" alt="{{ $lb->title }}" class="w-100" style="height: 280px; object-fit: cover; transition: transform 0.3s ease;">
                        <div class="position-absolute bottom-0 start-0 end-0 p-2 bg-dark bg-opacity-75 text-white text-center small fw-bold">
                            <i class="fa-solid fa-bag-shopping me-1 text-warning"></i> {{ $buttonLabel }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    <p class="mb-0">Đang cập nhật các phối đồ Lookbook mới nhất...</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ================= 8. SHOPPABLE LOOKBOOK MODALS ================= --}}
    @foreach($lookbooks ?? [] as $lb)
        <div class="modal fade" id="lookbookModal{{ $lb->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                    <div class="modal-header border-bottom py-3">
                        <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-camera text-danger me-2"></i>{{ $lb->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-5">
                                <img src="{{ $lb->image_url }}" alt="{{ $lb->title }}" class="w-100 rounded-3 shadow-sm" style="height: 320px; object-fit: cover;">
                                @if($lb->subtitle)
                                    <p class="text-muted small mt-2 mb-0 text-center fst-italic">{{ $lb->subtitle }}</p>
                                @endif
                            </div>
                            <div class="col-md-7">
                                <h6 class="fw-bold text-dark mb-3">Sản phẩm xuất hiện trong ảnh này:</h6>
                                <div class="d-flex flex-column gap-3">
                                    @forelse($lb->products as $lbProduct)
                                        @php
                                            $lbPrice = $lbProduct->sale_price ?: $lbProduct->price;
                                        @endphp
                                        <div class="d-flex align-items-center justify-content-between p-2 border rounded-3 bg-light">
                                            <div>
                                                <h6 class="fw-semibold text-dark mb-0 small">{{ $lbProduct->name }}</h6>
                                                <span class="text-danger fw-bold small">{{ number_format($lbPrice) }}đ</span>
                                            </div>
                                            <a href="{{ route('shop.show', $lbProduct->slug) }}" class="btn btn-sm btn-dark rounded-pill px-3">
                                                <i class="fa-solid fa-cart-plus me-1"></i> Xem chi tiết
                                            </a>
                                        </div>
                                    @empty
                                        <p class="text-muted small">Chưa gắn sản phẩm nào cho Lookbook này.</p>
                                    @endforelse
                                </div>

                                @if($lb->instagram_url)
                                    <div class="mt-4 text-end">
                                        <a href="{{ $lb->instagram_url }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3">
                                            <i class="fa-brands fa-instagram me-1"></i> Xem trên Instagram
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        /**
         * 1. Flash Sale Real-time Countdown Timer Module
         */
        function initFlashSaleCountdown() {
            var endTimeStr = "{{ $flashSaleEndTime ?? '' }}";
            var endTime;

            if (endTimeStr) {
                endTime = new Date(endTimeStr.replace(/-/g, "/")).getTime();
            } else {
                var todayEnd = new Date();
                todayEnd.setHours(23, 59, 59, 999);
                endTime = todayEnd.getTime();
            }

            function tick() {
                var now = new Date().getTime();
                var diff = endTime - now;

                if (diff <= 0) {
                    $('#flash-hours').text('00h');
                    $('#flash-minutes').text('00m');
                    $('#flash-seconds').text('00s');
                    return;
                }

                var hours = Math.floor(diff / 3600000);
                var minutes = Math.floor((diff % 3600000) / 60000);
                var seconds = Math.floor((diff % 60000) / 1000);

                $('#flash-hours').text((hours < 10 ? '0' + hours : hours) + 'h');
                $('#flash-minutes').text((minutes < 10 ? '0' + minutes : minutes) + 'm');
                $('#flash-seconds').text((seconds < 10 ? '0' + seconds : seconds) + 's');
            }

            tick();
            setInterval(tick, 1000);
        }

        /**
         * 3. Flash Sale Slider Pagination Module
         */
        function initFlashSaleSlider() {
            var FLASH_PER_PAGE = 4;
            var flashPage = 1;

            function renderFlash() {
                var $items = $('.flash-sale-card-item');
                var total = $items.length;
                var totalPages = Math.ceil(total / FLASH_PER_PAGE) || 1;

                if (total <= FLASH_PER_PAGE) {
                    $('#flash-pagination-container').hide();
                } else {
                    $('#flash-pagination-container').show();
                }

                if (flashPage > totalPages) flashPage = totalPages;
                if (flashPage < 1) flashPage = 1;

                $items.hide();

                var start = (flashPage - 1) * FLASH_PER_PAGE;
                var end = start + FLASH_PER_PAGE;
                $items.slice(start, end).fadeIn(200);

                $('#flash-page-indicator').text(flashPage + '/' + totalPages);
                $('#btn-flash-prev').prop('disabled', flashPage <= 1).css('opacity', flashPage <= 1 ? '0.4' : '1');
                $('#btn-flash-next').prop('disabled', flashPage >= totalPages).css('opacity', flashPage >= totalPages ? '0.4' : '1');
            }

            if ($('.flash-sale-card-item').length > 0) {
                renderFlash();
            }

            $('#btn-flash-prev').on('click', function() {
                if (flashPage > 1) {
                    flashPage--;
                    renderFlash();
                }
            });

            $('#btn-flash-next').on('click', function() {
                flashPage++;
                renderFlash();
            });
        }

        /**
         * 4. Customer Reviews Slider Module
         */
        function initCustomerReviewsSlider() {
            var REVIEWS_PER_PAGE = 3;
            var reviewPage = 1;

            function renderReviews() {
                var $items = $('.customer-review-card-item');
                var total = $items.length;
                var totalPages = Math.ceil(total / REVIEWS_PER_PAGE) || 1;

                if (reviewPage > totalPages) reviewPage = totalPages;
                if (reviewPage < 1) reviewPage = 1;

                $items.hide();

                var start = (reviewPage - 1) * REVIEWS_PER_PAGE;
                var end = start + REVIEWS_PER_PAGE;
                $items.slice(start, end).fadeIn(200);

                $('#review-page-indicator').text(reviewPage + '/' + totalPages);
                $('#btn-review-prev').prop('disabled', reviewPage <= 1).css('opacity', reviewPage <= 1 ? '0.4' : '1');
                $('#btn-review-next').prop('disabled', reviewPage >= totalPages).css('opacity', reviewPage >= totalPages ? '0.4' : '1');
            }

            if ($('.customer-review-card-item').length > 0) {
                renderReviews();

                $('#btn-review-prev').on('click', function() {
                    if (reviewPage > 1) {
                        reviewPage--;
                        renderReviews();
                    }
                });

                $('#btn-review-next').on('click', function() {
                    var totalPages = Math.ceil($('.customer-review-card-item').length / REVIEWS_PER_PAGE) || 1;
                    if (reviewPage < totalPages) {
                        reviewPage++;
                        renderReviews();
                    }
                });
            }
        }

        // Initialize All Modules
        initFlashSaleCountdown();
        initFlashSaleSlider();
        initCustomerReviewsSlider();
    });
</script>
@endpush
