@extends('layouts.app')

@section('title', $outfitInfo['name'] . ' - Bộ Phối Đồ Thời Trang Atino')

@push('styles')
<style>
    .outfit-hero-card {
        background: {{ $outfitInfo['gradient'] }};
        border-radius: 24px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .outfit-badge-pill {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding: 0.4rem 1rem;
        border-radius: 50rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: inline-block;
    }

    .outfit-summary-box {
        background: #ffffff;
        border-radius: 20px;
        border: 2px solid #f1f5f9;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 20px;
    }

    .btn-add-all-outfit {
        background-color: #0f172a;
        color: #ffffff;
        font-weight: 700;
        border-radius: 50rem;
        padding: 0.85rem 1.5rem;
        transition: all 0.25s ease;
        border: none;
    }

    .btn-add-all-outfit:hover {
        background-color: #334155;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.25);
    }

    .btn-buy-now-outfit {
        background-color: #dc2626;
        color: #ffffff;
        font-weight: 700;
        border-radius: 50rem;
        padding: 0.85rem 1.5rem;
        transition: all 0.25s ease;
        border: none;
    }

    .btn-buy-now-outfit:hover {
        background-color: #b91c1c;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
    }

    .outfit-item-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .outfit-item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .outfit-thumb-img {
        font-size: 0;
        color: transparent;
    }
</style>
@endpush

@section('content')
<div class="container py-3">
    <!-- Breadcrumb Nav -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">Shop</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ $outfitInfo['name'] }}</li>
        </ol>
    </nav>

    <!-- 1. BANNER OUTFIT & MÔ TẢ PHONG CÁCH -->
    <section class="outfit-hero-card p-4 p-md-5 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="outfit-badge-pill mb-3">
                    <i class="fa-solid fa-sparkles me-1 text-warning"></i> {{ $outfitInfo['badge'] }}
                </span>
                <h1 class="display-5 fw-extrabold text-white mb-2" style="font-weight: 800; letter-spacing: -1px;">
                    {{ $outfitInfo['name'] }}
                </h1>
                <p class="text-warning fw-semibold fs-5 mb-3">
                    "{{ $outfitInfo['tagline'] }}"
                </p>
                <p class="text-white-50 leading-relaxed mb-4" style="max-width: 600px;">
                    {{ $outfitInfo['description'] }}
                </p>

                <!-- Thẻ items đi kèm trong outfit -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="small text-white-50 me-2 fw-semibold">Gồm có trong set:</span>
                    @foreach($outfitInfo['items_included'] as $item)
                        <span class="badge bg-white text-dark font-weight-medium rounded-pill px-3 py-2 shadow-sm">
                            <i class="fa-solid fa-check text-success me-1"></i> {{ $item }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-5">
                <div class="position-relative overflow-hidden rounded-4 shadow-lg border border-light border-opacity-25" style="height: 320px;">
                    <img src="{{ $outfitInfo['hero_image'] }}" alt="{{ $outfitInfo['name'] }}" class="w-100 h-100" style="object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-dark bg-opacity-50 text-white backdrop-blur">
                        <small class="d-block text-warning fw-bold">LOOKBOOK CHUẨN KHUYÊN DÙNG</small>
                        <span class="extra-small text-white-50">Thiết kế bởi đội ngũ Atino Style Specialist</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. DANH SÁCH SẢN PHẨM TRONG OUTFIT & TỔNG GIÁ -->
    <div class="row g-4 mb-5">
        <!-- Cột Trái: Danh sách sản phẩm thuộc Outfit với Checkbox -->
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-layer-group text-primary me-2"></i>Các Sản Phẩm Trong Outfit ({{ $products->count() }})
                    </h4>
                    <span class="text-muted small">Tùy chọn những món bạn thích hoặc mua trọn bộ để nhận ưu đãi</span>
                </div>
                
                <!-- Nút Chọn tất cả / Bỏ chọn tất cả -->
                <div class="form-check form-switch cursor-pointer bg-white px-3 py-2 rounded-pill border shadow-sm">
                    <input class="form-check-input cursor-pointer" type="checkbox" id="toggle-select-all-outfit" checked>
                    <label class="form-check-label fw-bold text-dark small cursor-pointer" for="toggle-select-all-outfit">
                        Chọn tất cả ({{ $products->count() }})
                    </label>
                </div>
            </div>

            <div class="row g-3">
                @foreach($products as $product)
                    @php
                        $thumb = $product->images->where('is_thumbnail', true)->first() ?: $product->images->first();
                        $hasDiscount = $product->sale_price && $product->sale_price < $product->price;
                        $finalPrice = $hasDiscount ? $product->sale_price : $product->price;
                        $percent = $hasDiscount ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;

                        // Lấy danh sách Size và Màu độc bản từ các biến thể
                        $availableSizes = $product->variants->pluck('size')->filter()->unique('id');
                        $availableColors = $product->variants->pluck('color')->filter()->unique('id');
                        $firstVariant = $product->variants->first();

                        $variantsArray = $product->variants->map(function ($v) {
                            return [
                                'id' => $v->id,
                                'size_id' => $v->size_id,
                                'color_id' => $v->color_id,
                                'price' => $v->price,
                                'sale_price' => $v->sale_price,
                            ];
                        })->values()->toArray();
                    @endphp
                    <div class="col-12 col-md-6">
                        <div class="card outfit-item-card h-100 p-3 position-relative"
                             data-product-id="{{ $product->id }}"
                             data-variants='@json($variantsArray)'>
                            
                            <!-- Checkbox Lựa Chọn Sản Phẩm -->
                            <div class="position-absolute top-0 start-0 m-3 z-2">
                                <input type="checkbox" 
                                       class="form-check-input outfit-item-checkbox cursor-pointer shadow-sm" 
                                       style="width: 22px; height: 22px;"
                                       data-id="{{ $product->id }}"
                                       data-variant-id="{{ $firstVariant ? $firstVariant->id : '' }}"
                                       data-name="{{ $product->name }}"
                                       data-original-price="{{ $product->price }}"
                                       data-final-price="{{ $finalPrice }}"
                                       checked>
                            </div>

                            <div class="row g-3 align-items-center ps-4">
                                <div class="col-4">
                                    <div class="position-relative rounded-3 overflow-hidden bg-light" style="aspect-ratio: 1/1;">
                                        @if($hasDiscount)
                                            <span class="position-absolute top-0 start-0 m-1 badge bg-danger extra-small fw-bold z-1">-{{ $percent }}%</span>
                                        @endif
                                        @php
                                            $imgSrc = $thumb ? $thumb->display_url : 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80';
                                        @endphp
                                        <img src="{{ $imgSrc }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-100 h-100 outfit-thumb-img" 
                                             style="object-fit: cover;"
                                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80';">
                                    </div>
                                </div>

                                <div class="col-8 d-flex flex-column justify-content-between h-100">
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary border extra-small mb-1">
                                            {{ $product->category->name ?? 'Thời trang' }}
                                        </span>
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $product->name }}">
                                            <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none hover-primary">
                                                {{ $product->name }}
                                            </a>
                                        </h6>
                                        
                                        <!-- Khối Chọn Size & Màu cho từng sản phẩm trong Outfit -->
                                        <div class="row g-1 my-2">
                                            @if($availableSizes->count() > 0)
                                                <div class="{{ $availableColors->count() > 0 ? 'col-6' : 'col-12' }}">
                                                    <select class="form-select form-select-sm outfit-size-select extra-small py-1 px-2 border-secondary-subtle">
                                                        @foreach($availableSizes as $sz)
                                                            <option value="{{ $sz->id }}">{{ $sz->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            @if($availableColors->count() > 0)
                                                <div class="{{ $availableSizes->count() > 0 ? 'col-6' : 'col-12' }}">
                                                    <select class="form-select form-select-sm outfit-color-select extra-small py-1 px-2 border-secondary-subtle">
                                                        @foreach($availableColors as $cl)
                                                            <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <div>
                                            @if($hasDiscount)
                                                <span class="fw-bold text-danger me-1">{{ number_format($product->sale_price) }}đ</span>
                                                <del class="extra-small text-muted">{{ number_format($product->price) }}đ</del>
                                            @else
                                                <span class="fw-bold text-dark">{{ number_format($product->price) }}đ</span>
                                            @endif
                                        </div>

                                        <button type="button" class="btn btn-sm btn-outline-dark rounded-circle btn-add-to-cart" data-id="{{ $product->id }}" title="Thêm riêng sản phẩm này">
                                            <i class="fa-solid fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cột Phải: Khối Tổng Giá & Nút Hành Động Dynamic (Action Summary Box) -->
        <div class="col-lg-4">
            <div class="outfit-summary-box p-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-receipt text-primary me-2"></i>Tổng Giá Trị Lựa Chọn
                </h5>

                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Đã chọn:</span>
                        <span class="fw-bold text-dark" id="outfit-selected-count-badge">{{ $products->count() }} / {{ $products->count() }} món</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Tổng giá gốc:</span>
                        <del class="text-muted small" id="outfit-original-price-display">{{ number_format($totalOriginalPrice) }}đ</del>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 text-success" id="outfit-savings-row">
                        <span class="small fw-semibold"><i class="fa-solid fa-tag me-1"></i>Tiết kiệm ưu đãi:</span>
                        <span class="fw-bold" id="outfit-savings-display">-{{ number_format($savingsAmount) }}đ</span>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between align-items-center pt-1">
                        <span class="fw-bold text-dark">Tổng tiền thanh toán:</span>
                        <span class="fs-4 fw-extrabold text-danger" id="outfit-final-price-display" style="font-weight: 800;">
                            {{ number_format($totalSalePrice) }}đ
                        </span>
                    </div>
                </div>

                <!-- Nút Thêm sản phẩm đã chọn vào giỏ -->
                <div class="d-grid gap-2 mb-3">
                    <button type="button" id="btn-add-selected-items" class="btn-add-all-outfit d-flex align-items-center justify-content-center gap-2">
                        <i class="fa-solid fa-cart-shopping"></i> <span id="btn-add-text">Thêm {{ $products->count() }} sản phẩm vào giỏ</span>
                    </button>
                    
                    <button type="button" id="btn-buy-now-selected" class="btn-buy-now-outfit d-flex align-items-center justify-content-center gap-2">
                        <i class="fa-solid fa-bolt"></i> <span id="btn-buy-text">Mua ngay {{ $products->count() }} sản phẩm</span>
                    </button>
                </div>

                <div class="text-center">
                    <small class="text-muted extra-small">
                        <i class="fa-solid fa-shield-check text-success me-1"></i> Miễn phí vận chuyển cho đơn hàng từ 500.000đ.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SECTION OUTFIT LIÊN QUAN -->
    @if(isset($relatedOutfits) && count($relatedOutfits) > 0)
        <section class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-shirt text-warning me-2"></i>Outfit Liên Quan Khác
                </h4>
                <a href="{{ route('shop.index') }}" class="text-primary small text-decoration-none fw-semibold">
                    Xem tất cả sản phẩm <i class="fa-solid fa-chevron-right ms-1"></i>
                </a>
            </div>

            <div class="row g-4">
                @foreach($relatedOutfits as $relOutfit)
                    @php
                        $relGradient = data_get($relOutfit, 'gradient') ?: 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)';
                        $relBadge = data_get($relOutfit, 'badge') ?: 'OUTFIT';
                        $relName = data_get($relOutfit, 'name');
                        $relDesc = data_get($relOutfit, 'description');
                        $relSlug = data_get($relOutfit, 'slug');
                    @endphp
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: {{ $relGradient }}; color: #ffffff;">
                            <div class="p-4 d-flex flex-column justify-content-between h-100">
                                <div>
                                    <span class="badge bg-white text-dark fw-bold mb-2">{{ $relBadge }}</span>
                                    <h5 class="fw-bold text-white mb-2">{{ $relName }}</h5>
                                    <p class="text-white-50 extra-small mb-3 leading-relaxed">{{ Str::limit($relDesc, 100) }}</p>
                                </div>

                                <a href="{{ route('outfits.show', $relSlug) }}" class="btn btn-light btn-sm rounded-pill font-weight-bold text-dark fw-bold">
                                    Khám phá Outfit này <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var totalAvailable = {{ $products->count() }};

        // Hàm định dạng tiền tệ VNĐ (ví dụ: 1200000 -> 1.200.000)
        function formatVND(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
        }

        // Tự động tính toán lại Tổng tiền theo Checkbox sản phẩm đã chọn
        function recalculateOutfitTotals() {
            var selectedCount = 0;
            var sumOriginal = 0;
            var sumFinal = 0;

            $('.outfit-item-checkbox:checked').each(function() {
                selectedCount++;
                sumOriginal += parseFloat($(this).data('original-price')) || 0;
                sumFinal += parseFloat($(this).data('final-price')) || 0;
            });

            var savings = sumOriginal - sumFinal;

            // Cập nhật giao diện real-time
            $('#outfit-selected-count-badge').text(selectedCount + ' / ' + totalAvailable + ' món');
            $('#outfit-original-price-display').text(formatVND(sumOriginal));
            $('#outfit-final-price-display').text(formatVND(sumFinal));

            if (savings > 0) {
                $('#outfit-savings-row').show();
                $('#outfit-savings-display').text('-' + formatVND(savings));
            } else {
                $('#outfit-savings-row').hide();
            }

            // Cập nhật nút bấm
            if (selectedCount > 0) {
                $('#btn-add-selected-items, #btn-buy-now-selected').prop('disabled', false);
                $('#btn-add-text').text('Thêm ' + selectedCount + ' sản phẩm vào giỏ');
                $('#btn-buy-text').text('Mua ngay ' + selectedCount + ' sản phẩm');
            } else {
                $('#btn-add-selected-items, #btn-buy-now-selected').prop('disabled', true);
                $('#btn-add-text').text('Vui lòng chọn ít nhất 1 sản phẩm');
                $('#btn-buy-text').text('Chưa chọn sản phẩm');
            }
        }

        // Lắng nghe sự kiện thay đổi Checkbox ở từng sản phẩm
        $(document).on('change', '.outfit-item-checkbox', function() {
            var checkedCount = $('.outfit-item-checkbox:checked').length;
            $('#toggle-select-all-outfit').prop('checked', checkedCount === totalAvailable);
            recalculateOutfitTotals();
        });

        // Lắng nghe sự kiện thay đổi Size / Màu ở từng sản phẩm
        $(document).on('change', '.outfit-size-select, .outfit-color-select', function() {
            var card = $(this).closest('.outfit-item-card');
            var sizeId = card.find('.outfit-size-select').val();
            var colorId = card.find('.outfit-color-select').val();
            var variants = card.data('variants') || [];

            var matchedVariant = variants.find(function(v) {
                var matchSize = !sizeId || v.size_id == sizeId;
                var matchColor = !colorId || v.color_id == colorId;
                return matchSize && matchColor;
            });

            var checkbox = card.find('.outfit-item-checkbox');
            if (matchedVariant) {
                checkbox.data('variant-id', matchedVariant.id);
            }
        });

        // Thêm danh sách sản phẩm đã chọn vào giỏ hàng (kèm Biến thể Size & Màu)
        function addSelectedProductsToCart(redirectToCheckout) {
            var selectedItems = [];
            $('.outfit-item-checkbox:checked').each(function() {
                selectedItems.push({
                    product_id: $(this).data('id'),
                    product_variant_id: $(this).data('variant-id') || null
                });
            });

            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Chưa chọn sản phẩm',
                    text: 'Vui lòng chọn ít nhất 1 sản phẩm trong Outfit để tiếp tục.'
                });
                return;
            }

            var addedCount = 0;
            var errorsCount = 0;

            $('#btn-add-selected-items, #btn-buy-now-selected').prop('disabled', true);
            $('#btn-add-text').html('<i class="fa-solid fa-spinner fa-spin"></i> Đang thêm vào giỏ...');

            selectedItems.forEach(function(item) {
                $.ajax({
                    url: '{{ route("cart.store") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: item.product_id,
                        product_variant_id: item.product_variant_id,
                        quantity: 1
                    },
                    success: function(response) {
                        addedCount++;
                        if (response.count !== undefined) {
                            $('#cart-count-badge').text(response.count);
                        }
                        if (addedCount + errorsCount === selectedItems.length) {
                            finishProcess();
                        }
                    },
                    error: function() {
                        errorsCount++;
                        if (addedCount + errorsCount === selectedItems.length) {
                            finishProcess();
                        }
                    }
                });
            });

            function finishProcess() {
                $('#btn-add-selected-items, #btn-buy-now-selected').prop('disabled', false);
                $('#btn-add-text').html('<i class="fa-solid fa-check text-success me-1"></i> Đã thêm ' + addedCount + ' sản phẩm!');

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Đã thêm ' + addedCount + ' sản phẩm đã chọn vào giỏ hàng!',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });

                setTimeout(function() {
                    recalculateOutfitTotals();
                }, 2000);

                if (redirectToCheckout) {
                    window.location.href = '{{ route("orders.checkout") }}';
                }
            }
        }

        // Click nút Thêm sản phẩm đã chọn
        $('#btn-add-selected-items').on('click', function(e) {
            e.preventDefault();
            addSelectedProductsToCart(false);
        });

        // Click nút Mua ngay sản phẩm đã chọn
        $('#btn-buy-now-selected').on('click', function(e) {
            e.preventDefault();
            addSelectedProductsToCart(true);
        });

        // Khởi chạy tính giá ngay khi tải trang
        recalculateOutfitTotals();
    });
</script>
@endpush
