@extends('layouts.app')

@section('title', 'Sản Phẩm Yêu Thích - Quần Áo Store')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm border small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">Cửa hàng</a></li>
        <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Sản phẩm yêu thích</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-heart text-danger me-2"></i>Danh Sách Yêu Thích</h3>
        <p class="text-muted small mb-0">Các mẫu quần áo thời trang bạn đã lưu để xem và mua sau</p>
    </div>
    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fs-6">
        {{ $wishlists->total() }} sản phẩm
    </span>
</div>

@if($wishlists->isEmpty())
    <!-- Trạng thái trống -->
    <div class="card border-0 shadow-sm rounded-4 bg-white text-center py-5">
        <div class="card-body p-5">
            <i class="fa-regular fa-heart fa-4x text-danger mb-3 opacity-50"></i>
            <h4 class="fw-bold text-dark">Danh sách yêu thích đang trống!</h4>
            <p class="text-muted small mb-4">Bạn chưa lưu sản phẩm nào. Hãy bấm biểu tượng trái tim <i class="fa-regular fa-heart text-danger"></i> trên sản phẩm yêu thích nhé.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-store me-2"></i> Khám phá sản phẩm ngay
            </a>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($wishlists as $item)
            @php
                $product = $item->product;
                $thumb = $product->images->where('is_thumbnail', true)->first() ?: $product->images->first();
                $hasDiscount = $product->sale_price && $product->sale_price < $product->price;
                $percent = $hasDiscount ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
            @endphp
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 rounded-4 shadow-sm product-card bg-white overflow-hidden position-relative">
                    <!-- Discount Badge -->
                    @if($hasDiscount)
                        <span class="position-absolute top-0 start-0 m-2 badge bg-danger fs-6 fw-bold shadow-sm z-1">
                            -{{ $percent }}%
                        </span>
                    @endif

                    <!-- Remove from Wishlist Form -->
                    <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" class="position-absolute top-0 end-0 m-2 z-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Xóa khỏi yêu thích" onclick="return confirm('Bạn có muốn xóa sản phẩm này khỏi danh sách yêu thích?')">
                            <i class="fa-solid fa-xmark text-danger"></i>
                        </button>
                    </form>

                    <!-- Image Thumbnail -->
                    <a href="{{ route('shop.show', $product->slug) }}" class="position-relative overflow-hidden d-block">
                        @if($thumb && $thumb->image_url)
                            <img src="{{ asset('storage/' . $thumb->image_url) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 240px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 240px;">
                                <i class="fa-solid fa-shirt fa-4x opacity-50"></i>
                            </div>
                        @endif
                    </a>

                    <!-- Card Body -->
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small">
                                    {{ $product->category->name ?? 'Thời trang' }}
                                </span>
                                @if($product->brand)
                                    <span class="extra-small text-muted fw-semibold">© {{ $product->brand->name }}</span>
                                @endif
                            </div>
                            <h6 class="fw-bold text-dark text-truncate mt-2 mb-1" title="{{ $product->name }}">
                                <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none text-dark hover-primary">{{ $product->name }}</a>
                            </h6>
                            <code class="extra-small text-muted d-block mb-2"><i class="fa-solid fa-barcode me-1"></i>{{ $product->sku }}</code>
                        </div>

                        <div class="mt-2">
                            <div class="d-flex align-items-baseline gap-2 mb-3">
                                @if($hasDiscount)
                                    <span class="fw-bold text-danger fs-5">{{ number_format($product->sale_price) }}đ</span>
                                    <del class="small text-muted">{{ number_format($product->price) }}đ</del>
                                @else
                                    <span class="fw-bold text-dark fs-5">{{ number_format($product->price) }}đ</span>
                                @endif
                            </div>

                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-dark btn-sm w-100 rounded-pill font-weight-medium">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Phân trang -->
    @if($wishlists->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $wishlists->links() }}
        </div>
    @endif
@endif
@endsection
