<div class="row g-3 g-md-4">
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
    @endphp

    @forelse($products as $product)
        @php
            $thumb = $product->images->where('is_thumbnail', true)->first() ?: $product->images->first();
            $effectiveSalePrice = $product->effective_sale_price;
            $hasDiscount = !is_null($effectiveSalePrice) && $effectiveSalePrice < $product->price;
            $percent = $product->discount_percent;
            $isWishlisted = Auth::check() && Auth::user()->wishlists->contains('product_id', $product->id);
            $realPhoto = $realFashionPhotos[$product->id % count($realFashionPhotos)];
            $imgSrc = ($thumb && $thumb->image_path) ? asset('storage/' . $thumb->image_path) : (($thumb && $thumb->image_url) ? asset('storage/' . $thumb->image_url) : $realPhoto);
        @endphp
        <div class="col-6 col-sm-6 col-md-4">
            <div class="card h-100 border-0 rounded-4 shadow-sm atino-product-card bg-white overflow-hidden position-relative">
                <!-- Discount Badge -->
                @if($hasDiscount)
                    <span class="position-absolute top-0 start-0 m-3 badge bg-danger fs-6 fw-bold shadow-sm z-1 px-3 py-2 rounded-3">
                        -{{ $percent }}%
                    </span>
                @endif

                <!-- Action Buttons: Wishlist & Quick View -->
                <div class="position-absolute top-0 end-0 m-2.5 d-flex gap-1.5 z-1">
                    <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm btn-quick-view p-0 d-flex align-items-center justify-content-center border" data-id="{{ $product->id }}" title="Xem nhanh" style="width: 34px; height: 34px;">
                        <i class="fa-regular fa-eye text-dark extra-small"></i>
                    </button>
                    <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm btn-toggle-wishlist p-0 d-flex align-items-center justify-content-center border" data-id="{{ $product->id }}" title="Thêm yêu thích" style="width: 34px; height: 34px;">
                        <i class="fa-{{ $isWishlisted ? 'solid text-danger' : 'regular text-secondary' }} fa-heart extra-small"></i>
                    </button>
                </div>

                <!-- Image Thumbnail -->
                <a href="{{ route('shop.show', $product->slug) }}" class="position-relative overflow-hidden d-block">
                    <img src="{{ $imgSrc }}" class="card-img-top" alt="{{ $product->name }}" style="height: 300px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ $realPhoto }}';">
                </a>

                <!-- Card Body -->
                <div class="card-body p-3.5 p-md-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small rounded-pill px-2.5 py-1">
                                {{ $product->category->name ?? 'Thời trang Atino' }}
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark text-truncate mb-2" title="{{ $product->name }}" style="font-size: 1rem;">
                            <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none text-dark hover-primary">{{ $product->name }}</a>
                        </h5>
                    </div>

                    <div class="pt-2 border-top border-light">
                        <div class="d-flex align-items-baseline gap-2 mb-3">
                            @if($hasDiscount)
                                <span class="fw-extrabold text-danger fs-5">{{ number_format($effectiveSalePrice) }}đ</span>
                                <del class="small text-muted">{{ number_format($product->price) }}đ</del>
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
        <!-- EMPTY STATE: KHÔNG TÌM THẤY SẢN PHẨM PHÙ HỢP VỚI BỘ LỌC -->
        <div class="col-12 text-center py-5 px-3 bg-white rounded-4 shadow-sm border">
            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3 p-3" style="width: 72px; height: 72px;">
                <i class="fa-solid fa-filter-circle-xmark fa-2x text-muted opacity-75"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Không tìm thấy sản phẩm phù hợp</h5>
            <p class="small text-muted mb-4 mx-auto" style="max-width: 440px;">
                Không có sản phẩm nào khớp với tất cả các tiêu chí lọc hiện tại của bạn. Vui lòng thử xóa bớt điều kiện lọc hoặc khám phá toàn bộ sản phẩm.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-2">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3.5 py-2 fw-semibold btn-reset-all-filters" id="btn-reset-filters-empty">
                    <i class="fa-solid fa-rotate-left me-1.5"></i> Xóa tất cả bộ lọc
                </button>
                <a href="{{ route('shop.index') }}" class="btn btn-dark btn-sm rounded-pill px-3.5 py-2 fw-semibold">
                    <i class="fa-solid fa-store me-1.5"></i> Xem tất cả sản phẩm
                </a>
            </div>
        </div>
    @endforelse

    <!-- BLOCK GỢI Ý KHI SỐ LƯỢNG SẢN PHẨM ÍT (< 6 SẢN PHẨM HOẶC EMPTY) -->
    @if(isset($suggestedProducts) && $suggestedProducts->isNotEmpty())
        <div class="col-12 mt-4 pt-3">
            <div class="bg-white p-3.5 p-md-4 rounded-4 shadow-sm border">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-2 border-bottom">
                    <div>
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                            <span>Gợi ý sản phẩm nổi bật khác</span>
                        </h6>
                        <span class="extra-small text-muted">Khám phá các mẫu thời trang mới nhất được yêu thích</span>
                    </div>
                    <a href="{{ route('shop.index') }}" class="small text-decoration-none fw-semibold text-primary d-inline-flex align-items-center gap-1">
                        <span>Xem toàn bộ cửa hàng</span>
                        <i class="fa-solid fa-arrow-right extra-small"></i>
                    </a>
                </div>

                <div class="row g-3">
                    @foreach($suggestedProducts as $sp)
                        @php
                            $sThumb = $sp->images->where('is_thumbnail', true)->first() ?: $sp->images->first();
                            $sEffectiveSalePrice = $sp->effective_sale_price;
                            $sHasDiscount = !is_null($sEffectiveSalePrice) && $sEffectiveSalePrice < $sp->price;
                            $sPercent = $sp->discount_percent;
                            $sIsWishlisted = Auth::check() && Auth::user()->wishlists->contains('product_id', $sp->id);
                            $sRealPhoto = $realFashionPhotos[$sp->id % count($realFashionPhotos)];
                            $sImgSrc = ($sThumb && $sThumb->image_path) ? asset('storage/' . $sThumb->image_path) : (($sThumb && $sThumb->image_url) ? asset('storage/' . $sThumb->image_url) : $sRealPhoto);
                        @endphp
                        <div class="col-6 col-md-3">
                            <div class="card h-100 border rounded-3 shadow-2xs atino-product-card bg-white overflow-hidden position-relative">
                                @if($sHasDiscount)
                                    <span class="position-absolute top-0 start-0 m-2 badge bg-danger extra-small fw-bold shadow-sm z-1 px-2 py-1 rounded-2">
                                        -{{ $sPercent }}%
                                    </span>
                                @endif

                                <div class="position-absolute top-0 end-0 m-2 d-flex gap-1 z-1">
                                    <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm btn-quick-view p-0 d-flex align-items-center justify-content-center border" data-id="{{ $sp->id }}" title="Xem nhanh" style="width: 28px; height: 28px;">
                                        <i class="fa-regular fa-eye text-dark extra-small"></i>
                                    </button>
                                </div>

                                <a href="{{ route('shop.show', $sp->slug) }}" class="position-relative overflow-hidden d-block">
                                    <img src="{{ $sImgSrc }}" class="card-img-top" alt="{{ $sp->name }}" style="height: 180px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ $sRealPhoto }}';">
                                </a>

                                <div class="card-body p-2.5 d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="badge bg-light text-muted extra-small rounded px-1.5 py-0.5 mb-1 text-truncate d-inline-block" style="max-width: 100%;">
                                            {{ $sp->category->name ?? 'Thời trang' }}
                                        </span>
                                        <h6 class="fw-semibold text-dark text-truncate mb-1.5" title="{{ $sp->name }}" style="font-size: 0.85rem;">
                                            <a href="{{ route('shop.show', $sp->slug) }}" class="text-decoration-none text-dark hover-primary">{{ $sp->name }}</a>
                                        </h6>
                                    </div>
                                    <div class="pt-1 border-top border-light d-flex align-items-center justify-content-between">
                                        <div>
                                            @if($sHasDiscount)
                                                <span class="fw-bold text-danger small">{{ number_format($sEffectiveSalePrice) }}đ</span>
                                            @else
                                                <span class="fw-bold text-dark small">{{ number_format($sp->price) }}đ</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('shop.show', $sp->slug) }}" class="btn btn-outline-dark btn-sm rounded-pill px-2 py-0.5 extra-small fw-semibold">
                                            Xem
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
