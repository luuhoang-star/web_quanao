<form action="{{ route('shop.index') }}" method="GET" class="shop-filter-form">
    <!-- Preserved Hidden Params -->
    @if(request()->filled('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif
    @if(request()->filled('category') && (!isset($categories) || $categories->isEmpty()))
        <input type="hidden" name="category" value="{{ request('category') }}">
    @endif
    @if(request()->filled('collection'))
        <input type="hidden" name="collection" value="{{ request('collection') }}">
    @endif
    @if(request()->filled('outfit'))
        <input type="hidden" name="outfit" value="{{ request('outfit') }}">
    @endif
    @if(request()->filled('tag'))
        <input type="hidden" name="tag" value="{{ request('tag') }}">
    @endif
    @if(request()->filled('discount'))
        <input type="hidden" name="discount" value="{{ request('discount') }}">
    @endif
    @if(request()->filled('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif

    @php
        $ctx = $context ?? 'desktop';
        $currentCatSlug = request('category', '');
        $hasCategories = isset($categories) && $categories->isNotEmpty();
        $hasPrice = $hasAvailablePriceFilter ?? (($priceCounts['all'] ?? 0) > 0 || !empty($priceRange));
        $hasColor = $hasAvailableColorFilter ?? (collect($colors)->contains(fn($c) => ($c->products_count ?? 0) > 0) || !empty($selectedColors));
        $hasSize = $hasAvailableSizeFilter ?? (collect($sizes)->contains(fn($s) => ($s->products_count ?? 0) > 0) || !empty($selectedSizes));
        $hasAnyFilterGroup = $hasCategories || $hasPrice || $hasColor || $hasSize;
    @endphp

    <style>
        .filter-group-header {
            cursor: pointer;
            user-select: none;
            padding: 4px 0;
            border-radius: 6px;
            transition: color 0.15s ease-in-out;
        }
        .filter-group-header:hover {
            color: var(--bs-primary) !important;
        }
        .filter-group-header .chevron-icon {
            transition: transform 0.25s ease-in-out;
        }
        .filter-group-header.collapsed .chevron-icon {
            transform: rotate(-90deg);
        }
        /* Dual Range Slider Styling */
        .dual-range-wrapper {
            position: relative;
            height: 32px;
            display: flex;
            align-items: center;
        }
        .dual-range-track {
            position: absolute;
            width: 100%;
            height: 6px;
            background-color: #e2e8f0;
            border-radius: 4px;
            z-index: 1;
        }
        .dual-range-progress {
            position: absolute;
            height: 6px;
            background-color: #0f172a;
            border-radius: 4px;
            z-index: 2;
        }
        .dual-range-input {
            position: absolute;
            width: 100%;
            height: 6px;
            background: none;
            pointer-events: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            z-index: 3;
            margin: 0;
        }
        .dual-range-input::-webkit-slider-thumb {
            height: 18px;
            width: 18px;
            border-radius: 50%;
            background: #0f172a;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.35);
            pointer-events: auto;
            -webkit-appearance: none;
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        .dual-range-input::-webkit-slider-thumb:hover {
            transform: scale(1.2);
            background: #1e293b;
        }
        .dual-range-input::-moz-range-thumb {
            height: 18px;
            width: 18px;
            border-radius: 50%;
            background: #0f172a;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.35);
            pointer-events: auto;
            cursor: pointer;
        }
    </style>

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-sliders me-2 text-warning"></i>Bộ Lọc Sản Phẩm
            </h6>
            <div class="d-flex align-items-center gap-2">
                @if($hasAnyFilterGroup)
                    <button type="button" class="btn btn-link p-0 text-secondary text-decoration-none extra-small btn-toggle-all-collapse" data-context="{{ $ctx }}" title="Thu gọn hoặc mở rộng tất cả mục lọc">
                        <i class="fa-solid fa-arrows-up-down me-1"></i><span class="toggle-text">Thu gọn</span>
                    </button>
                @endif
                <button type="button" class="btn btn-link p-0 text-danger text-decoration-none extra-small btn-reset-all-filters fw-semibold {{ (empty($activeTagList) && empty($currentCatSlug)) ? 'd-none' : '' }}">
                    <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc
                </button>
            </div>
        </div>
        <div class="card-body p-3.5">

            @if(!$hasAnyFilterGroup)
                <div class="text-center py-4 text-muted small">
                    <i class="fa-solid fa-filter-circle-xmark fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                    Không có bộ lọc khả dụng cho danh mục này.
                </div>
            @endif

            <!-- 1. DANH MỤC SẢN PHẨM (COLLAPSIBLE) -->
            @if($hasCategories)
                <div class="filter-group mb-3">
                    <div class="filter-group-header d-flex align-items-center justify-content-between mb-1.5" data-bs-toggle="collapse" data-bs-target="#collapseCat_{{ $ctx }}" aria-expanded="true" aria-controls="collapseCat_{{ $ctx }}">
                        <span class="font-weight-bold text-dark small">
                            <i class="fa-solid fa-layer-group me-1.5 text-primary"></i> <strong>1. Danh Mục Sản Phẩm</strong>
                        </span>
                        <i class="fa-solid fa-chevron-down extra-small text-muted chevron-icon"></i>
                    </div>

                    <div class="collapse show" id="collapseCat_{{ $ctx }}">
                        <div class="d-flex flex-column gap-1.5 small pt-1" style="max-height: 240px; overflow-y: auto;">
                            <!-- Tất cả danh mục -->
                            <div class="form-check custom-filter-radio">
                                <input class="form-check-input filter-input-category cursor-pointer" type="radio" name="category" id="cat_all_{{ $ctx }}" value="" {{ empty($currentCatSlug) ? 'checked' : '' }}>
                                <label class="form-check-label cursor-pointer text-dark d-flex align-items-center justify-content-between w-100" for="cat_all_{{ $ctx }}">
                                    <span class="fw-semibold">Tất cả sản phẩm</span>
                                </label>
                            </div>

                            @foreach($categories as $pCat)
                                <div class="form-check custom-filter-radio mt-1">
                                    <input class="form-check-input filter-input-category cursor-pointer" type="radio" name="category" id="cat_{{ $pCat->id }}_{{ $ctx }}" value="{{ $pCat->slug }}" {{ $currentCatSlug === $pCat->slug ? 'checked' : '' }}>
                                    <label class="form-check-label cursor-pointer text-dark d-flex align-items-center justify-content-between w-100 fw-bold" for="cat_{{ $pCat->id }}_{{ $ctx }}">
                                        <span>{{ $pCat->name }}</span>
                                    </label>
                                </div>
                                @if($pCat->children && $pCat->children->isNotEmpty())
                                    <div class="ms-3 ps-2 border-start d-flex flex-column gap-1 my-1">
                                        @foreach($pCat->children as $subCat)
                                            <div class="form-check custom-filter-radio">
                                                <input class="form-check-input filter-input-category cursor-pointer" type="radio" name="category" id="cat_{{ $subCat->id }}_{{ $ctx }}" value="{{ $subCat->slug }}" {{ $currentCatSlug === $subCat->slug ? 'checked' : '' }}>
                                                <label class="form-check-label cursor-pointer text-muted d-flex align-items-center justify-content-between w-100" for="cat_{{ $subCat->id }}_{{ $ctx }}">
                                                    <span>{{ $subCat->name }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr class="my-3 text-muted opacity-25">
            @endif

            <!-- 2. MỨC GIÁ MUA SẮM (DUAL RANGE SLIDER 2 ĐẦU KÉO & PRESETS) -->
            @if($hasPrice)
                @php
                    $curMinPrice = isset($minPrice) && $minPrice !== null ? (int)$minPrice : 0;
                    $curMaxPrice = isset($maxPrice) && $maxPrice !== null ? (int)$maxPrice : 2000000;
                    $minLimit = $minPriceLimit ?? 0;
                    $maxLimit = $maxPriceLimit ?? 2000000;
                @endphp
                <div class="filter-group mb-3">
                    <div class="filter-group-header d-flex align-items-center justify-content-between mb-1.5" data-bs-toggle="collapse" data-bs-target="#collapsePrice_{{ $ctx }}" aria-expanded="true" aria-controls="collapsePrice_{{ $ctx }}">
                        <span class="font-weight-bold text-dark small">
                            <i class="fa-solid fa-money-bill-wave me-1.5 text-success"></i> <strong>2. Mức Giá Mua Sắm</strong>
                        </span>
                        <i class="fa-solid fa-chevron-down extra-small text-muted chevron-icon"></i>
                    </div>

                    <div class="collapse show" id="collapsePrice_{{ $ctx }}">
                        <div class="pt-2 pb-1">
                            <!-- Dual Range Slider Container -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="extra-small text-muted">Khoảng giá kéo:</span>
                                <span class="extra-small fw-bold text-dark font-monospace" id="slider_price_display_{{ $ctx }}">
                                    {{ number_format($curMinPrice) }}đ - {{ number_format($curMaxPrice) }}đ
                                </span>
                            </div>

                            <div class="dual-range-wrapper mb-2">
                                <div class="dual-range-track"></div>
                                <div class="dual-range-progress" id="slider_progress_{{ $ctx }}"></div>
                                <input type="range" class="dual-range-input range-slider-min" 
                                       id="slider_min_{{ $ctx }}" 
                                       data-context="{{ $ctx }}"
                                       min="{{ $minLimit }}" max="{{ $maxLimit }}" step="20000" 
                                       value="{{ $curMinPrice }}">
                                <input type="range" class="dual-range-input range-slider-max" 
                                       id="slider_max_{{ $ctx }}" 
                                       data-context="{{ $ctx }}"
                                       min="{{ $minLimit }}" max="{{ $maxLimit }}" step="20000" 
                                       value="{{ $curMaxPrice }}">
                            </div>

                            <!-- Input số tiền và nút áp dụng -->
                            <div class="d-flex align-items-center gap-1.5 mb-3">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="min_price" 
                                           id="input_min_{{ $ctx }}" 
                                           data-context="{{ $ctx }}"
                                           class="form-control form-control-sm text-center font-monospace extra-small px-1 filter-input-min" 
                                           placeholder="Từ" 
                                           min="{{ $minLimit }}" max="{{ $maxLimit }}" step="10000"
                                           value="{{ $curMinPrice > 0 ? $curMinPrice : '' }}">
                                </div>
                                <span class="text-muted extra-small">-</span>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="max_price" 
                                           id="input_max_{{ $ctx }}" 
                                           data-context="{{ $ctx }}"
                                           class="form-control form-control-sm text-center font-monospace extra-small px-1 filter-input-max" 
                                           placeholder="Đến" 
                                           min="{{ $minLimit }}" max="{{ $maxLimit }}" step="10000"
                                           value="{{ $curMaxPrice < $maxLimit ? $curMaxPrice : '' }}">
                                </div>
                                <button type="button" class="btn btn-dark btn-sm px-2.5 py-1 btn-apply-price-slider flex-shrink-0" data-context="{{ $ctx }}" title="Áp dụng mức giá">
                                    <i class="fa-solid fa-arrow-right extra-small"></i>
                                </button>
                            </div>

                            <div class="border-top pt-2">
                                <span class="extra-small text-muted fw-semibold d-block mb-1.5">Hoặc chọn nhanh theo mốc:</span>
                                <div class="d-flex flex-column gap-1.5 small">
                                    <!-- Tất cả mức giá -->
                                    <div class="form-check custom-filter-radio">
                                        <input class="form-check-input filter-input-price cursor-pointer" type="radio" name="price_range" id="price_all_{{ $ctx }}" value="" {{ (empty($priceRange) && empty($minPrice) && empty($maxPrice)) ? 'checked' : '' }}>
                                        <label class="form-check-label cursor-pointer text-dark d-flex align-items-center justify-content-between w-100" for="price_all_{{ $ctx }}">
                                            <span>Tất cả mức giá</span>
                                            <span class="badge bg-light text-muted border rounded-pill">{{ $priceCounts['all'] ?? 0 }}</span>
                                        </label>
                                    </div>

                                    <!-- Dưới 300.000đ -->
                                    @php
                                        $cUnder300 = $priceCounts['under_300'] ?? 0;
                                        $disUnder300 = ($cUnder300 === 0 && $priceRange !== 'under_300');
                                    @endphp
                                    <div class="form-check custom-filter-radio {{ $disUnder300 ? 'opacity-40 pe-none' : '' }}">
                                        <input class="form-check-input filter-input-price cursor-pointer" type="radio" name="price_range" id="price_under_300_{{ $ctx }}" value="under_300" {{ $priceRange == 'under_300' ? 'checked' : '' }} {{ $disUnder300 ? 'disabled' : '' }}>
                                        <label class="form-check-label {{ $disUnder300 ? 'text-muted' : 'cursor-pointer text-dark' }} d-flex align-items-center justify-content-between w-100" for="price_under_300_{{ $ctx }}">
                                            <span>Dưới 300.000đ</span>
                                            <span class="badge {{ $disUnder300 ? 'bg-light text-muted-50 border-0' : 'bg-light text-muted border' }} rounded-pill">{{ $cUnder300 }}</span>
                                        </label>
                                    </div>

                                    <!-- 300.000đ - 500.000đ -->
                                    @php
                                        $c300500 = $priceCounts['300_500'] ?? 0;
                                        $dis300500 = ($c300500 === 0 && $priceRange !== '300_500');
                                    @endphp
                                    <div class="form-check custom-filter-radio {{ $dis300500 ? 'opacity-40 pe-none' : '' }}">
                                        <input class="form-check-input filter-input-price cursor-pointer" type="radio" name="price_range" id="price_300_500_{{ $ctx }}" value="300_500" {{ $priceRange == '300_500' ? 'checked' : '' }} {{ $dis300500 ? 'disabled' : '' }}>
                                        <label class="form-check-label {{ $dis300500 ? 'text-muted' : 'cursor-pointer text-dark' }} d-flex align-items-center justify-content-between w-100" for="price_300_500_{{ $ctx }}">
                                            <span>300.000đ - 500.000đ</span>
                                            <span class="badge {{ $dis300500 ? 'bg-light text-muted-50 border-0' : 'bg-light text-muted border' }} rounded-pill">{{ $c300500 }}</span>
                                        </label>
                                    </div>

                                    <!-- 500.000đ - 1.000.000đ -->
                                    @php
                                        $c5001000 = $priceCounts['500_1000'] ?? 0;
                                        $dis5001000 = ($c5001000 === 0 && $priceRange !== '500_1000');
                                    @endphp
                                    <div class="form-check custom-filter-radio {{ $dis5001000 ? 'opacity-40 pe-none' : '' }}">
                                        <input class="form-check-input filter-input-price cursor-pointer" type="radio" name="price_range" id="price_500_1000_{{ $ctx }}" value="500_1000" {{ $priceRange == '500_1000' ? 'checked' : '' }} {{ $dis5001000 ? 'disabled' : '' }}>
                                        <label class="form-check-label {{ $dis5001000 ? 'text-muted' : 'cursor-pointer text-dark' }} d-flex align-items-center justify-content-between w-100" for="price_500_1000_{{ $ctx }}">
                                            <span>500.000đ - 1.000.000đ</span>
                                            <span class="badge {{ $dis5001000 ? 'bg-light text-muted-50 border-0' : 'bg-light text-muted border' }} rounded-pill">{{ $c5001000 }}</span>
                                        </label>
                                    </div>

                                    <!-- Trên 1.000.000đ -->
                                    @php
                                        $cOver1000 = $priceCounts['over_1000'] ?? 0;
                                        $disOver1000 = ($cOver1000 === 0 && $priceRange !== 'over_1000');
                                    @endphp
                                    <div class="form-check custom-filter-radio {{ $disOver1000 ? 'opacity-40 pe-none' : '' }}">
                                        <input class="form-check-input filter-input-price cursor-pointer" type="radio" name="price_range" id="price_over_1000_{{ $ctx }}" value="over_1000" {{ $priceRange == 'over_1000' ? 'checked' : '' }} {{ $disOver1000 ? 'disabled' : '' }}>
                                        <label class="form-check-label {{ $disOver1000 ? 'text-muted' : 'cursor-pointer text-dark' }} d-flex align-items-center justify-content-between w-100" for="price_over_1000_{{ $ctx }}">
                                            <span>Trên 1.000.000đ</span>
                                            <span class="badge {{ $disOver1000 ? 'bg-light text-muted-50 border-0' : 'bg-light text-muted border' }} rounded-pill">{{ $cOver1000 }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2. MÀU SẮC (MULTI-SELECT SWATCHES & COLLAPSIBLE) -->
            @if($hasColor)
                @if($hasPrice)
                    <hr class="my-3 text-muted opacity-25">
                @endif
                <div class="filter-group mb-3">
                    <div class="filter-group-header d-flex align-items-center justify-content-between mb-1.5" data-bs-toggle="collapse" data-bs-target="#collapseColor_{{ $ctx }}" aria-expanded="true" aria-controls="collapseColor_{{ $ctx }}">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="font-weight-bold text-dark small">
                                <i class="fa-solid fa-palette me-1.5 text-primary"></i> <strong>3. Màu Sắc</strong>
                            </span>
                            <span class="extra-small text-muted fw-normal">(Chọn nhiều)</span>
                        </div>
                        <i class="fa-solid fa-chevron-down extra-small text-muted chevron-icon"></i>
                    </div>

                    <div class="collapse show" id="collapseColor_{{ $ctx }}">
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            @foreach($colors as $cl)
                                @php
                                    $isSelected = in_array($cl->id, $selectedColors);
                                    $cCount = $cl->products_count ?? 0;
                                    $isDisabled = ($cCount === 0 && !$isSelected);
                                @endphp
                                <label class="color-swatch-pill border rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5 transition-all {{ $isDisabled ? 'disabled opacity-40 pe-none border-dashed bg-light text-muted cursor-not-allowed' : ($isSelected ? 'active border-primary bg-primary-subtle text-primary fw-bold shadow-2xs cursor-pointer' : 'bg-white text-dark hover-border-primary cursor-pointer') }}"
                                       title="{{ $isDisabled ? 'Màu '.$cl->name.' (0 sản phẩm)' : 'Chọn Màu '.$cl->name.' ('.$cCount.' sản phẩm)' }}">
                                    <input type="checkbox" name="colors[]" value="{{ $cl->id }}" class="d-none filter-input-color" {{ $isSelected ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                                    
                                    <span class="swatch-circle rounded-circle border d-inline-flex align-items-center justify-content-center text-white" style="width: 14px; height: 14px; background-color: {{ $cl->hex_code ?? '#cccccc' }}; font-size: 0.55rem;">
                                        @if($isSelected)
                                            <i class="fa-solid fa-check"></i>
                                        @endif
                                    </span>

                                    <span class="small">{{ $cl->name }}</span>
                                    <span class="extra-small {{ $isSelected ? 'text-primary' : 'text-muted' }}">({{ $cCount }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 4. KÍCH THƯỚC (MULTI-SELECT SIZE & COLLAPSIBLE) -->
            @if($hasSize)
                @if($hasPrice || $hasColor)
                    <hr class="my-3 text-muted opacity-25">
                @endif
                <div class="filter-group mb-2">
                    <div class="filter-group-header d-flex align-items-center justify-content-between mb-1.5" data-bs-toggle="collapse" data-bs-target="#collapseSize_{{ $ctx }}" aria-expanded="true" aria-controls="collapseSize_{{ $ctx }}">
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="font-weight-bold text-dark small">
                                <i class="fa-solid fa-ruler-combined me-1.5 text-danger"></i> <strong>4. Kích Thước (Size)</strong>
                            </span>
                            <span class="extra-small text-muted fw-normal">(Chọn nhiều)</span>
                        </div>
                        <i class="fa-solid fa-chevron-down extra-small text-muted chevron-icon"></i>
                    </div>

                    <div class="collapse show" id="collapseSize_{{ $ctx }}">
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            @foreach($sizes as $sz)
                                @php
                                    $isSelected = in_array($sz->id, $selectedSizes);
                                    $sCount = $sz->products_count ?? 0;
                                    $isDisabled = ($sCount === 0 && !$isSelected);
                                @endphp
                                <label class="size-button-pill border rounded-3 px-3 py-1.5 d-inline-flex align-items-center gap-1 transition-all {{ $isDisabled ? 'disabled opacity-40 bg-light text-muted pe-none border-dashed cursor-not-allowed' : ($isSelected ? 'active bg-dark text-white fw-bold shadow-xs border-dark cursor-pointer' : 'bg-white text-dark hover-bg-light cursor-pointer') }}" 
                                       title="{{ $isDisabled ? 'Size '.$sz->name.' (0 sản phẩm)' : 'Chọn Size '.$sz->name.' ('.$sCount.' sản phẩm)' }}">
                                    <input type="checkbox" name="sizes[]" value="{{ $sz->id }}" class="d-none filter-input-size" {{ $isSelected ? 'checked' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                                    
                                    <span class="fw-semibold small">{{ $sz->name }}</span>
                                    <span class="extra-small {{ $isSelected ? 'text-white-50' : 'text-muted' }}">({{ $sCount }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</form>
