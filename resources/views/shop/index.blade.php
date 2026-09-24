@extends('layouts.app')

@section('title', $currentCollection ? 'Bộ Sưu Tập: ' . $currentCollection->name : ($isDiscount ? 'Flash Sale - Sản Phẩm Giảm Giá' : ($currentOutfit ? 'Outfit Gợi Ý: ' . ucfirst($currentOutfit) : 'Cửa Hàng Thời Trang - Tất Cả Sản Phẩm')))

@section('content')
<!-- BREADCRUMB & HEADER TITLE -->
<div class="bg-white p-3.5 p-md-4 rounded-4 shadow-sm mb-4 border">
    <div class="row align-items-center">
        <div class="col-md-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">Sản phẩm</a></li>
                    @if($currentCollection)
                        <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ $currentCollection->name }}</li>
                    @elseif($isDiscount)
                        <li class="breadcrumb-item active fw-semibold text-danger" aria-current="page">Flash Sale</li>
                    @elseif($currentOutfit)
                        <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Outfit: {{ ucfirst($currentOutfit) }}</li>
                    @endif
                </ol>
            </nav>
            <h3 class="fw-bold text-dark mb-0 fs-4 fs-md-3">
                @if($currentCollection)
                    <i class="fa-solid fa-layer-group text-primary me-2"></i>Bộ Sưu Tập: {{ $currentCollection->name }}
                @elseif($isDiscount)
                    <i class="fa-solid fa-bolt text-danger me-2"></i>Flash Sale - Sản Phẩm Đang Giảm Giá
                @elseif($currentOutfit)
                    <i class="fa-solid fa-vest text-warning me-2"></i>Outfit Gợi Ý: {{ ucfirst($currentOutfit) }}
                @else
                    <i class="fa-solid fa-store text-primary me-2"></i>Cửa Hàng Thời Trang
                @endif
            </h3>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            <span class="text-muted small" id="product-count-display">
                @if($products->total() > 0)
                    Hiển thị <strong id="count-first-item">{{ $products->firstItem() }}</strong>-<strong id="count-last-item">{{ $products->lastItem() }}</strong> / <strong id="count-total">{{ $products->total() }}</strong> sản phẩm
                @else
                    Chưa có sản phẩm phù hợp.
                @endif
            </span>
        </div>
    </div>
</div>

<!-- NÚT BỘ LỌC DÀNH CHO DỤNG CỤ DI ĐỘNG (MOBILE FILTER TRIGGER BUTTON) -->
<div class="d-lg-none mb-3">
    <button type="button" class="btn btn-dark w-100 py-2.5 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2 fw-semibold" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMobileFilter">
        <i class="fa-solid fa-sliders text-warning"></i>
        <span>Bộ Lọc Sản Phẩm</span>
        <span class="badge bg-warning text-dark rounded-pill px-2" id="mobile-filter-badge">{{ count($activeTagList ?? []) }}</span>
    </button>
</div>

<!-- MAIN CONTENT ROW -->
<div class="row g-4">
    <!-- CỘT TRÁI: BỘ LỌC DESKTOP (SIDEBAR FILTER) -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="sticky-top" style="top: 90px; z-index: 100;" id="shop-filter-desktop-container">
            @include('shop.components.filter-form', ['context' => 'desktop'])
        </div>
    </div>

    <!-- CỘT PHẢI: LƯỚI SẢN PHẨM & SẮP XẾP (PRODUCT GRID & SORT) -->
    <div class="col-lg-9">
        <!-- TOP BAR SẮP XẾP (SORT TOOLBAR) -->
        <div class="bg-white p-3 rounded-4 shadow-sm mb-3.5 border d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark small"><i class="fa-solid fa-arrow-down-short-wide me-1 text-primary"></i> Sắp xếp theo:</span>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <select name="sort" id="shop-sort-select" class="form-select form-select-sm" style="min-width: 200px;">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>✨ Mới nhất về</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>💵 Giá: Thấp đến Cao</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>💎 Giá: Cao đến Thấp</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>🔤 Tên: A - Z</option>
                </select>
            </div>
        </div>

        <!-- 1. CÁC TAGS LỌC ĐANG ÁP DỤNG (ACTIVE FILTER TAGS CONTAINER) -->
        <div id="active-tags-container">
            @include('shop.components.active-tags', ['activeTagList' => $activeTagList])
        </div>

        <!-- LOADING SPINNER OVERLAY -->
        <div id="filter-loading-spinner" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="text-muted small mt-2 fw-semibold">Đang cập nhật danh sách sản phẩm...</p>
        </div>

        <!-- 2. LƯỚI SẢN PHẨM (PRODUCT GRID CONTAINER) -->
        <div id="shop-product-grid-container">
            @include('shop.components.product-list', ['products' => $products, 'suggestedProducts' => $suggestedProducts ?? collect()])
        </div>

        <!-- 3. PHÂN TRANG (PAGINATION CONTAINER) -->
        <div id="pagination-container" class="d-flex justify-content-center mt-4">
            @if($products->hasPages())
                {{ $products->links() }}
            @endif
        </div>
    </div>
</div>

<!-- MOBILE OFFCANVAS FILTER DRAWER (TRƯỢT TỪ DƯỚI LÊN CHO MOBILE) -->
<div class="offcanvas offcanvas-bottom rounded-top-4 h-75 d-lg-none" tabindex="-1" id="offcanvasMobileFilter" aria-labelledby="offcanvasMobileFilterLabel">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title fw-bold text-dark" id="offcanvasMobileFilterLabel">
            <i class="fa-solid fa-sliders text-warning me-2"></i>Bộ Lọc Sản Phẩm
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3 overflow-y-auto" id="shop-filter-mobile-container">
        @include('shop.components.filter-form', ['context' => 'mobile'])
    </div>
    <div class="offcanvas-footer p-3 bg-light border-top d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-reset-all-filters flex-fill">Xóa lọc</button>
        <button type="button" class="btn btn-dark flex-fill fw-bold" data-bs-dismiss="offcanvas">Xem kết quả</button>
    </div>
</div>

<!-- ================= QUICK VIEW MODAL (XEM NHANH SẢN PHẨM) ================= -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-white">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 small" id="quickViewModalLabel">
                    <i class="fa-regular fa-eye text-primary"></i>
                    <span>Xem Nhanh Sản Phẩm</span>
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 position-relative">
                <!-- Loading State -->
                <div id="quickview-loading" class="text-center py-5">
                    <div class="spinner-border text-dark" role="status"></div>
                    <p class="small text-muted mt-2">Đang tải thông tin sản phẩm...</p>
                </div>

                <!-- Content State -->
                <div id="quickview-content" class="d-none">
                    <div class="row g-4">
                        <div class="col-md-6 text-center">
                            <div class="border rounded-3 overflow-hidden bg-light p-2 mb-2">
                                <img id="qv-main-img" src="" alt="Product" class="img-fluid rounded-2" style="max-height: 320px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 id="qv-product-name" class="fw-bold text-dark mb-1"></h5>
                            <div class="small text-muted mb-2">Mã SKU: <strong id="qv-product-sku" class="font-monospace text-dark"></strong></div>
                            
                            <div class="d-flex align-items-baseline gap-2 mb-3">
                                <span id="qv-price" class="fs-4 fw-extrabold text-danger"></span>
                                <del id="qv-orig-price" class="text-muted small"></del>
                            </div>

                            <p id="qv-description" class="small text-muted mb-3 line-clamp-3"></p>

                            <!-- Color picker -->
                            <div id="qv-color-wrapper" class="mb-3 d-none">
                                <label class="form-label fw-bold text-dark extra-small mb-1.5">Màu sắc:</label>
                                <div id="qv-colors-list" class="d-flex gap-2 flex-wrap"></div>
                            </div>

                            <!-- Size picker -->
                            <div id="qv-size-wrapper" class="mb-3 d-none">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <label class="form-label fw-bold text-dark extra-small mb-0">Kích cỡ:</label>
                                    <a id="qv-size-guide-link" href="#" class="extra-small text-primary fw-bold text-decoration-none d-flex align-items-center gap-1">
                                        <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                                        <span>Xem tư vấn size</span>
                                    </a>
                                </div>
                                <div id="qv-sizes-list" class="d-flex gap-2 flex-wrap"></div>
                            </div>

                            <div class="d-flex align-items-center gap-2 mt-4">
                                <button type="button" id="qv-btn-add-cart" class="btn btn-dark rounded-pill fw-bold btn-sm px-4 py-2.5 flex-grow-1">
                                    <i class="fa-solid fa-cart-plus me-1.5"></i> Thêm vào giỏ
                                </button>
                                <a id="qv-detail-link" href="#" class="btn btn-outline-dark rounded-pill fw-semibold btn-sm px-3 py-2.5">
                                    Xem chi tiết <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var currentQvProduct = null;
        var qvSelectedColorId = null;
        var qvSelectedSizeId = null;

        // Xử lý click Xem Nhanh
        $(document).on('click', '.btn-quick-view', function(e) {
            e.preventDefault();
            var productId = $(this).data('id');
            if (!productId) return;

            var modalEl = document.getElementById('quickViewModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();

            $('#quickview-loading').removeClass('d-none');
            $('#quickview-content').addClass('d-none');

            $.ajax({
                url: '/shop/quick-view/' + productId,
                method: 'GET',
                success: function(res) {
                    if (!res.success) return;
                    currentQvProduct = res.product;
                    
                    $('#qv-main-img').attr('src', currentQvProduct.images[0]);
                    $('#qv-product-name').text(currentQvProduct.name);
                    $('#qv-product-sku').text(currentQvProduct.sku);
                    $('#qv-price').text(currentQvProduct.price);
                    if (currentQvProduct.original_price) {
                        $('#qv-orig-price').text(currentQvProduct.original_price).removeClass('d-none');
                    } else {
                        $('#qv-orig-price').addClass('d-none');
                    }
                    $('#qv-description').text(currentQvProduct.description);
                    $('#qv-detail-link').attr('href', currentQvProduct.url);
                    $('#qv-size-guide-link').attr('href', currentQvProduct.url);

                    // Colors
                    var $colorsList = $('#qv-colors-list').empty();
                    if (currentQvProduct.colors && currentQvProduct.colors.length > 0) {
                        $('#qv-color-wrapper').removeClass('d-none');
                        currentQvProduct.colors.forEach(function(c, idx) {
                            var activeCls = idx === 0 ? 'active' : '';
                            if (idx === 0) qvSelectedColorId = c.id;
                            $colorsList.append(`
                                <button type="button" class="color-pill-btn qv-color-btn ${activeCls}" data-id="${c.id}">
                                    <span class="rounded-circle border" style="width: 12px; height: 12px; display: inline-block; background-color: ${c.hex_code};"></span>
                                    ${c.name}
                                </button>
                            `);
                        });
                    } else {
                        $('#qv-color-wrapper').addClass('d-none');
                        qvSelectedColorId = null;
                    }

                    // Sizes & Tự động chọn size theo số đo cá nhân (Smart Fit Advisor)
                    var $sizesList = $('#qv-sizes-list').empty();
                    $('#qv-size-hint').remove();
                    var userPrefSize = null;
                    try {
                        var savedAdvisor = localStorage.getItem('atino_fit_advisor_data');
                        if (savedAdvisor) {
                            var parsed = JSON.parse(savedAdvisor);
                            userPrefSize = parsed.size;
                        }
                    } catch(e) {}

                    if (currentQvProduct.sizes && currentQvProduct.sizes.length > 0) {
                        $('#qv-size-wrapper').removeClass('d-none');
                        
                        var matchedIdx = -1;
                        if (userPrefSize) {
                            matchedIdx = currentQvProduct.sizes.findIndex(function(s) {
                                return s.name.toString().toUpperCase() === userPrefSize.toUpperCase();
                            });
                        }
                        var defaultActiveIdx = matchedIdx !== -1 ? matchedIdx : 0;

                        currentQvProduct.sizes.forEach(function(s, idx) {
                            var activeCls = idx === defaultActiveIdx ? 'active' : '';
                            if (idx === defaultActiveIdx) qvSelectedSizeId = s.id;
                            $sizesList.append(`
                                <button type="button" class="size-pill-btn qv-size-btn ${activeCls}" data-id="${s.id}" data-name="${s.name}">
                                    ${s.name}
                                </button>
                            `);
                        });

                        if (matchedIdx !== -1) {
                            $('#qv-size-wrapper').append(`
                                <div id="qv-size-hint" class="extra-small text-primary mt-1.5 fw-semibold d-flex align-items-center gap-1">
                                    <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
                                    <span>Tự động chọn <strong>Size ${userPrefSize}</strong> theo số đo cá nhân của bạn</span>
                                </div>
                            `);
                        }
                    } else {
                        $('#qv-size-wrapper').addClass('d-none');
                        qvSelectedSizeId = null;
                    }

                    $('#quickview-loading').addClass('d-none');
                    $('#quickview-content').removeClass('d-none');
                }
            });
        });

        // Click chọn màu trong Quick View
        $(document).on('click', '.qv-color-btn', function() {
            $('.qv-color-btn').removeClass('active');
            $(this).addClass('active');
            qvSelectedColorId = $(this).data('id');
        });

        // Click chọn size trong Quick View
        $(document).on('click', '.qv-size-btn', function() {
            $('.qv-size-btn').removeClass('active');
            $(this).addClass('active');
            qvSelectedSizeId = $(this).data('id');
        });

        // Bấm thêm vào giỏ từ Quick View
        $('#qv-btn-add-cart').on('click', function() {
            if (!currentQvProduct) return;
            var variantId = null;
            if (currentQvProduct.variants && currentQvProduct.variants.length > 0) {
                var matched = currentQvProduct.variants.find(function(v) {
                    var mColor = !qvSelectedColorId || v.color_id == qvSelectedColorId;
                    var mSize = !qvSelectedSizeId || v.size_id == qvSelectedSizeId;
                    return mColor && mSize;
                });
                if (matched) variantId = matched.id;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang thêm...');

            $.ajax({
                url: '{{ route("cart.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: currentQvProduct.id,
                    product_variant_id: variantId,
                    quantity: 1
                },
                success: function() {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus me-1.5"></i> Thêm vào giỏ');
                    var modalEl = document.getElementById('quickViewModal');
                    if (modalEl) {
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }
                    if (window.reloadCartDrawer) window.reloadCartDrawer();
                    var drawerEl = document.getElementById('cartDrawer');
                    if (drawerEl) {
                        var bsDrawer = bootstrap.Offcanvas.getOrCreateInstance(drawerEl);
                        bsDrawer.show();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus me-1.5"></i> Thêm vào giỏ');
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Có lỗi xảy ra', showConfirmButton: false, timer: 2000 });
                }
            });
        });

        // 1. HÀM TỔNG HỢP VÀ GỬI AJAX BỘ LỌC DỮ LIỆU
        function applyAjaxFilter(pageUrl) {
            $('#filter-loading-spinner').removeClass('d-none');
            $('#shop-product-grid-container').addClass('opacity-50');

            const $activeForm = $('.shop-filter-form:visible').length ? $('.shop-filter-form:visible') : $('.shop-filter-form').first();
            
            const priceRange = $activeForm.find('input[name="price_range"]:checked').val() || '';
            
            const selectedColors = [];
            $activeForm.find('input[name="colors[]"]:checked').each(function() {
                selectedColors.push($(this).val());
            });

            const selectedSizes = [];
            $activeForm.find('input[name="sizes[]"]:checked').each(function() {
                selectedSizes.push($(this).val());
            });

            const sortVal = $('#shop-sort-select').val() || 'latest';

            const baseUrl = pageUrl ? pageUrl : "{{ route('shop.index') }}";
            const urlObj = new URL(baseUrl, window.location.origin);
            const params = urlObj.searchParams;

            const currentUrlParams = new URLSearchParams(window.location.search);
            ['search', 'collection', 'outfit', 'tag', 'discount'].forEach(paramKey => {
                if (currentUrlParams.has(paramKey) && currentUrlParams.get(paramKey) !== '') {
                    params.set(paramKey, currentUrlParams.get(paramKey));
                }
            });

            // Lọc danh mục sản phẩm
            const selectedCategory = $activeForm.find('input[name="category"]:checked').val();
            if (typeof selectedCategory !== 'undefined') {
                if (selectedCategory !== '') {
                    params.set('category', selectedCategory);
                } else {
                    params.delete('category');
                }
            } else if (currentUrlParams.has('category') && currentUrlParams.get('category') !== '') {
                params.set('category', currentUrlParams.get('category'));
            }

            // Lọc mức giá (Dual Slider hoặc Radio preset)
            const minPrice = $activeForm.find('input[name="min_price"]').val();
            const maxPrice = $activeForm.find('input[name="max_price"]').val();

            if (minPrice && parseFloat(minPrice) > 0) {
                params.set('min_price', minPrice);
            } else {
                params.delete('min_price');
            }

            if (maxPrice && parseFloat(maxPrice) > 0 && parseFloat(maxPrice) < 2000000) {
                params.set('max_price', maxPrice);
            } else {
                params.delete('max_price');
            }

            if (minPrice || maxPrice) {
                params.delete('price_range');
            } else if (priceRange !== '') {
                params.set('price_range', priceRange);
            } else {
                params.delete('price_range');
            }

            params.delete('colors[]');
            params.delete('color_id');
            selectedColors.forEach(cid => {
                params.append('colors[]', cid);
            });

            params.delete('sizes[]');
            params.delete('size_id');
            selectedSizes.forEach(sid => {
                params.append('sizes[]', sid);
            });

            if (sortVal !== 'latest') {
                params.set('sort', sortVal);
            } else {
                params.delete('sort');
            }

            const ajaxUrl = urlObj.toString();
            window.history.pushState({ path: ajaxUrl }, '', ajaxUrl);

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#filter-loading-spinner').addClass('d-none');
                    $('#shop-product-grid-container').removeClass('opacity-50');

                    if (response.success) {
                        $('#shop-product-grid-container').html(response.products_html);
                        $('#active-tags-container').html(response.tags_html);
                        $('#pagination-container').html(response.pagination_html);

                        if (response.filter_desktop_html) {
                            $('#shop-filter-desktop-container').html(response.filter_desktop_html);
                        }
                        if (response.filter_mobile_html) {
                            $('#shop-filter-mobile-container').html(response.filter_mobile_html);
                        }

                        initDualRangeSliders();

                        if (response.total > 0) {
                            $('#product-count-display').html(`Hiển thị <strong id="count-first-item">${response.first_item}</strong>-<strong id="count-last-item">${response.last_item}</strong> / <strong id="count-total">${response.total}</strong> sản phẩm`);
                        } else {
                            $('#product-count-display').html('Chưa có sản phẩm phù hợp.');
                        }

                        $('#mobile-filter-badge').text(response.active_count);
                    }
                },
                error: function() {
                    $('#filter-loading-spinner').addClass('d-none');
                    $('#shop-product-grid-container').removeClass('opacity-50');
                }
            });
        }

        // Khởi tạo và đồng bộ thanh trượt Dual Range Slider
        function initDualRangeSliders() {
            ['desktop', 'mobile'].forEach(ctx => {
                const $minSlider = $(`#slider_min_${ctx}`);
                const $maxSlider = $(`#slider_max_${ctx}`);
                const $progress = $(`#slider_progress_${ctx}`);
                const $display = $(`#slider_price_display_${ctx}`);
                const $minInput = $(`#input_min_${ctx}`);
                const $maxInput = $(`#input_max_${ctx}`);

                if (!$minSlider.length || !$maxSlider.length) return;

                const maxLimit = 2000000;

                function updateTrack() {
                    let minVal = parseInt($minSlider.val()) || 0;
                    let maxVal = parseInt($maxSlider.val()) || maxLimit;

                    if (minVal > maxVal) {
                        const temp = minVal;
                        minVal = maxVal;
                        maxVal = temp;
                    }

                    const leftPercent = (minVal / maxLimit) * 100;
                    const rightPercent = 100 - (maxVal / maxLimit) * 100;

                    $progress.css({
                        left: leftPercent + '%',
                        right: rightPercent + '%'
                    });

                    $display.text(`${minVal.toLocaleString('vi-VN')}đ - ${maxVal.toLocaleString('vi-VN')}đ`);
                    $minInput.val(minVal > 0 ? minVal : '');
                    $maxInput.val(maxVal < maxLimit ? maxVal : '');
                }

                updateTrack();

                $minSlider.off('input').on('input', function() {
                    let minVal = parseInt($(this).val()) || 0;
                    let maxVal = parseInt($maxSlider.val()) || maxLimit;
                    if (minVal > maxVal) {
                        $(this).val(maxVal);
                    }
                    updateTrack();
                });

                $maxSlider.off('input').on('input', function() {
                    let minVal = parseInt($minSlider.val()) || 0;
                    let maxVal = parseInt($(this).val()) || maxLimit;
                    if (maxVal < minVal) {
                        $(this).val(minVal);
                    }
                    updateTrack();
                });
            });
        }

        initDualRangeSliders();

        // Xử lý nút áp dụng khoảng giá của slider
        $(document).on('click', '.btn-apply-price-slider', function() {
            const ctx = $(this).data('context') || 'desktop';
            const $form = $(this).closest('form');
            $form.find('input[name="price_range"]').prop('checked', false);
            applyAjaxFilter();
        });

        // Bắt các sự kiện lọc khi thay đổi radio / checkbox
        $(document).on('change', 'input[name="category"], input[name="colors[]"], input[name="sizes[]"], #shop-sort-select', function() {
            applyAjaxFilter();
        });

        // Khi người dùng click chọn mốc giá nhanh -> xóa min_price/max_price input và lọc
        $(document).on('change', 'input[name="price_range"]', function() {
            const $form = $(this).closest('form');
            $form.find('input[name="min_price"]').val('');
            $form.find('input[name="max_price"]').val('');
            applyAjaxFilter();
        });

        // Xóa tag
        $(document).on('click', '.btn-remove-filter-tag', function(e) {
            e.preventDefault();
            const type = $(this).data('type');
            const val = $(this).data('value');

            if (type === 'price_range' || type === 'price_slider') {
                $('input[name="price_range"]').prop('checked', false);
                $('input[name="price_range"][value=""]').prop('checked', true);
                $('input[name="min_price"]').val('');
                $('input[name="max_price"]').val('');
            } else if (type === 'color') {
                $(`input[name="colors[]"][value="${val}"]`).prop('checked', false);
            } else if (type === 'size') {
                $(`input[name="sizes[]"][value="${val}"]`).prop('checked', false);
            } else if (type === 'category') {
                $('input[name="category"]').prop('checked', false);
                $('input[name="category"][value=""]').prop('checked', true);
            }

            applyAjaxFilter();
        });

        // Reset bộ lọc
        $(document).on('click', '.btn-reset-all-filters, #btn-clear-all-filters-tag, #btn-reset-filters-empty', function(e) {
            e.preventDefault();
            $('input[name="category"]').prop('checked', false);
            $('input[name="category"][value=""]').prop('checked', true);
            $('input[name="price_range"]').prop('checked', false);
            $('input[name="price_range"][value=""]').prop('checked', true);
            $('input[name="min_price"]').val('');
            $('input[name="max_price"]').val('');
            $('input[name="colors[]"]').prop('checked', false);
            $('input[name="sizes[]"]').prop('checked', false);
            applyAjaxFilter();
        });

        // Phân trang
        $(document).on('click', '#pagination-container a', function(e) {
            e.preventDefault();
            const pageUrl = $(this).attr('href');
            if (pageUrl) {
                applyAjaxFilter(pageUrl);
                $('html, body').animate({ scrollTop: $('#product-count-display').offset().top - 100 }, 300);
            }
        });

        // Xử lý nút Thu gọn / Mở rộng tất cả bộ lọc
        $(document).on('click', '.btn-toggle-all-collapse', function(e) {
            e.preventDefault();
            const card = $(this).closest('.card');
            const collapses = card.find('.card-body .collapse');
            const headers = card.find('.card-body .filter-group-header');
            const textSpan = $(this).find('.toggle-text');
            const isAnyOpen = collapses.hasClass('show');

            collapses.each(function() {
                const bsCol = bootstrap.Collapse.getOrCreateInstance(this, { toggle: false });
                if (isAnyOpen) {
                    bsCol.hide();
                } else {
                    bsCol.show();
                }
            });

            if (isAnyOpen) {
                headers.addClass('collapsed').attr('aria-expanded', 'false');
                textSpan.text('Mở rộng');
            } else {
                headers.removeClass('collapsed').attr('aria-expanded', 'true');
                textSpan.text('Thu gọn');
            }
        });

        // Đồng bộ icon chevron khi người dùng toggle từng nhóm riêng lẻ
        $(document).on('show.bs.collapse', '.filter-group .collapse', function() {
            $(this).prev('.filter-group-header').removeClass('collapsed').attr('aria-expanded', 'true');
        });
        $(document).on('hide.bs.collapse', '.filter-group .collapse', function() {
            $(this).prev('.filter-group-header').addClass('collapsed').attr('aria-expanded', 'false');
        });
    });
</script>
@endpush
