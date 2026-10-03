<!-- Header Navbar Partial (Logo Left, Main Menu with Subcategory Dropdowns Center, Search Pill & Cart Right) -->
@php
    $headerNavCategories = \App\Models\Category::whereNull('parent_id')
        ->where('is_active', true)
        ->with(['children' => function($q) {
            $q->where('is_active', true)->orderBy('sort_order', 'asc');
        }])
        ->orderBy('sort_order', 'asc')
        ->get();
@endphp

<style>
    /* Header Dropdown Menu Styling (Cầu nối Hover & Giữ Click mượt tuyệt đối) */
    .header-nav-dropdown {
        position: relative;
        padding-bottom: 0.75rem;
        margin-bottom: -0.75rem;
    }
    
    .header-nav-link {
        color: #0f172a;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.5rem 0.85rem;
        border-radius: 6px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        cursor: pointer;
    }

    .header-nav-link:hover, 
    .header-nav-link.active, 
    .header-nav-dropdown:hover .header-nav-link,
    .header-nav-dropdown.is-open .header-nav-link {
        color: #4f46e5;
        background-color: #f1f5f9;
    }

    .header-sub-menu {
        opacity: 0;
        visibility: hidden;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 220px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.15);
        border: 1px solid #e2e8f0;
        padding: 0.6rem;
        z-index: 1050;
        margin-top: 0;
        transform: translateY(6px);
        transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
        transition-delay: 0.2s; /* GIỮ MENU KHI RỜI CHUỘT TRONG 0.2 GIÂY ĐỂ KHÔNG BỊ TẮT ĐỘT NGỘT */
    }

    /* Vùng đệm rơ-le trong suốt (Hover Bridge) nối liền Link và Dropdown */
    .header-sub-menu::before {
        content: "";
        position: absolute;
        top: -15px;
        left: 0;
        width: 100%;
        height: 15px;
        background: transparent;
    }

    .header-nav-dropdown:hover .header-sub-menu,
    .header-nav-dropdown.is-open .header-sub-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        transition-delay: 0s; /* MỞ NGAY LẬP TỨC KHI RÊ CHUỘT HOẶC BẤM */
    }

    .header-sub-item {
        color: #334155;
        text-decoration: none;
        font-size: 0.835rem;
        font-weight: 500;
        padding: 0.55rem 0.85rem;
        border-radius: 8px;
        display: block;
        transition: all 0.15s ease;
    }

    .header-sub-item:hover {
        color: #4f46e5;
        background-color: #f1f5f9;
        font-weight: 600;
    }

    .header-search-box {
        width: 280px;
        transition: width 0.2s ease;
    }
    @media (max-width: 991.98px) {
        .header-search-box {
            width: 200px;
        }
    }
    @media (max-width: 575.98px) {
        .header-search-box {
            width: 150px;
        }
    }
</style>

<!-- Top Promo Announcement Bar -->
<div class="promo-top-bar text-center">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-none d-md-flex align-items-center gap-3">
            <span><i class="fa-solid fa-truck-fast me-1 text-warning"></i> Miễn phí giao hàng cho đơn từ 499k</span>
            <span>|</span>
            <span><i class="fa-solid fa-rotate-left me-1 text-info"></i> Đổi trả dễ dàng 30 ngày</span>
        </div>
        <div class="mx-auto mx-md-0 fw-semibold">
            🔥 ĐÓN BỘ SƯU TẬP MỚI 2026 - GIẢM ĐẾN 50% HÔM NAY!
        </div>
        <div class="d-none d-md-flex align-items-center gap-3">
            <a href="tel:0973797151" class="text-white text-decoration-none small"><i class="fa-solid fa-phone me-1"></i> 0973.797.151</a>
        </div>
    </div>
</div>

<header class="atino-header-sticky py-3">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <!-- 1. Logo Atino bên trái -->
            <a class="navbar-brand d-flex align-items-center gap-2 text-dark text-decoration-none" href="{{ route('home') }}">
                <span class="bg-dark text-white rounded-3 px-2 py-1 fw-black fs-4" style="font-weight: 900; letter-spacing: -1px;">A</span>
                <span class="fw-extrabold text-dark fs-3 tracking-tight" style="font-weight: 800; font-family: 'Plus Jakarta Sans', sans-serif;">ATINO</span>
            </a>

            <!-- 2. Menu danh mục ở giữa kèm Menu con xổ xuống (Dropdown) -->
            <nav class="d-none d-md-flex align-items-center gap-1 me-auto ms-4">
                @foreach($headerNavCategories as $parentCat)
                    <div class="header-nav-dropdown">
                        <!-- Link Danh mục chính -->
                        <a class="header-nav-link {{ request('category') === $parentCat->slug ? 'active' : '' }}" 
                           href="{{ route('shop.index', ['category' => $parentCat->slug]) }}"
                           data-has-children="{{ $parentCat->children->isNotEmpty() ? 'true' : 'false' }}">
                            <span>{{ $parentCat->name }}</span>
                            @if($parentCat->children->isNotEmpty())
                                <i class="fa-solid fa-chevron-down opacity-60" style="font-size: 0.65rem;"></i>
                            @endif
                        </a>

                        <!-- Dropdown Danh mục con xổ xuống -->
                        @if($parentCat->children->isNotEmpty())
                            <div class="header-sub-menu">
                                <!-- Link xem tất cả nhóm sản phẩm -->
                                <a href="{{ route('shop.index', ['category' => $parentCat->slug]) }}" class="header-sub-item text-primary fw-bold border-bottom pb-2 mb-1">
                                    <i class="fa-solid fa-layer-group me-1"></i> Tất cả {{ $parentCat->name }}
                                </a>

                                <!-- Các danh mục con cụ thể -->
                                @foreach($parentCat->children as $childCat)
                                    <a href="{{ route('shop.index', ['category' => $childCat->slug]) }}" class="header-sub-item">
                                        {{ $childCat->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>

            <!-- 3. Ô tìm kiếm bo tròn pill, User dropdown & Giỏ hàng góc phải -->
            <div class="d-flex align-items-center gap-2 gap-sm-3 ms-auto">
                <!-- Form tìm kiếm pill xám nhạt tích hợp Live Search -->
                <div class="position-relative header-search-box" id="header-search-wrapper">
                    <form action="{{ route('shop.index') }}" method="GET" class="d-flex align-items-center m-0">
                        <div class="position-relative w-100">
                            <span class="position-absolute start-0 top-50 translate-middle-y ms-3 text-muted" style="z-index: 5;">
                                <i class="fa-solid fa-magnifying-glass fs-6"></i>
                            </span>
                            <input type="text" id="header-search-input" name="search" autocomplete="off" class="form-control rounded-pill ps-5 pe-3 py-2 border-0 text-dark small search-input-minimal w-100" placeholder="Tìm kiếm..." value="{{ request('search') }}" style="background-color: #f1f5f9 !important;">
                            <span id="search-spinner" class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted spinner-border spinner-border-sm d-none" style="z-index: 5;"></span>
                        </div>
                    </form>

                    <!-- Dropdown Gợi Ý Tìm Kiếm Trực Tiếp (Live Search Dropdown) -->
                    <div id="header-live-search-dropdown" class="position-absolute bg-white rounded-4 shadow-lg border border-slate-200 p-2 mt-2 d-none" style="top: 100%; right: 0; width: 340px; max-width: calc(100vw - 2rem); z-index: 1060; max-height: 420px; overflow-y: auto;">
                        <div id="live-search-content"></div>
                    </div>
                </div>

                <!-- User Account Dropdown -->
                @if(Auth::guard('web')->check())
                    <div class="dropdown">
                        <a class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center border-0 shadow-sm" href="#" role="button" data-bs-toggle="dropdown" style="width: 40px; height: 40px;">
                            <i class="fa-regular fa-user fs-5 text-dark"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-4 p-2.5" style="min-width: 220px;">
                            <li>
                                <div class="px-2 py-1">
                                    <div class="fw-bold text-dark small text-truncate">{{ Auth::guard('web')->user()->name }}</div>
                                    <div class="text-muted extra-small text-truncate"><i class="fa-regular fa-envelope me-1"></i> {{ Auth::guard('web')->user()->email }}</div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 small" href="{{ route('profile.show') }}">
                                    <i class="fa-regular fa-id-badge me-2 text-primary"></i> Tài khoản của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 small" href="{{ route('orders.index') }}">
                                    <i class="fa-solid fa-receipt me-2 text-success"></i> Đơn hàng của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 small" href="{{ route('wishlist.index') }}">
                                    <i class="fa-regular fa-heart me-2 text-danger"></i> Sản phẩm yêu thích
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold rounded-3 py-2 small">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center border-0 shadow-sm text-dark" title="Đăng nhập" style="width: 40px; height: 40px;">
                        <i class="fa-regular fa-user fs-5"></i>
                    </a>
                @endif

                <!-- Icon Giỏ hàng góc phải (Trượt ra Cart Drawer) -->
                <button type="button" class="btn btn-dark rounded-circle p-2 position-relative d-flex align-items-center justify-content-center border-0 shadow-sm text-white" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" title="Xem Giỏ Hàng" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-bag-shopping fs-5"></i>
                    <span id="cart-count-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white fw-bold border border-2 border-white shadow-sm" style="font-size: 0.65rem;">
                        {{ $cartCount ?? 0 }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Menu Mobile Nav Bar cho màn hình nhỏ -->
        <div class="d-flex d-md-none align-items-center justify-content-between pt-2 border-top mt-2 overflow-auto" style="white-space: nowrap;">
            @foreach($headerNavCategories as $parentCat)
                <a class="text-dark text-decoration-none px-2 py-1 small fw-semibold" href="{{ route('shop.index', ['category' => $parentCat->slug]) }}">
                    {{ $parentCat->name }}
                </a>
            @endforeach
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            var searchWrapper = document.getElementById('header-search-wrapper');
            var searchDropdown = document.getElementById('header-live-search-dropdown');
            if (searchWrapper && searchDropdown && !searchWrapper.contains(e.target)) {
                searchDropdown.classList.add('d-none');
            }
        });

        // Xử lý Live Search với Debounce 250ms
        var searchInput = document.getElementById('header-search-input');
        var searchDropdown = document.getElementById('header-live-search-dropdown');
        var searchContent = document.getElementById('live-search-content');
        var searchSpinner = document.getElementById('search-spinner');
        var debounceTimer = null;

        if (searchInput && searchDropdown) {
            searchInput.addEventListener('input', function() {
                var query = this.value.trim();
                clearTimeout(debounceTimer);

                if (query.length < 1) {
                    searchDropdown.classList.add('d-none');
                    searchContent.innerHTML = '';
                    return;
                }

                if (searchSpinner) searchSpinner.classList.remove('d-none');

                debounceTimer = setTimeout(function() {
                    fetch('{{ route("shop.searchSuggestions") }}?q=' + encodeURIComponent(query))
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            if (searchSpinner) searchSpinner.classList.add('d-none');

                            if (data.success && data.products && data.products.length > 0) {
                                var html = '<div class="extra-small fw-bold text-muted px-2 py-1 mb-1 text-uppercase">Gợi ý sản phẩm (' + data.products.length + ')</div>';
                                html += '<div class="d-flex flex-column gap-1">';
                                
                                data.products.forEach(function(p) {
                                    var imgTag = p.image 
                                        ? '<img src="' + p.image + '" alt="' + p.name + '" class="rounded-2 object-fit-cover flex-shrink-0" style="width: 44px; height: 44px;">'
                                        : '<div class="bg-light rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;"><i class="fa-solid fa-shirt text-muted"></i></div>';

                                    var priceTag = p.has_discount
                                        ? '<div class="fw-bold text-danger extra-small">' + p.price + ' <del class="text-muted fw-normal" style="font-size: 0.7rem;">' + p.original_price + '</del></div>'
                                        : '<div class="fw-bold text-dark extra-small">' + p.price + '</div>';

                                    html += `
                                        <a href="${p.url}" class="d-flex align-items-center gap-2.5 p-2 rounded-3 text-decoration-none hover-bg-light transition-all">
                                            ${imgTag}
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-bold text-dark small text-truncate mb-0.5">${p.name}</div>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="extra-small text-muted">${p.category}</span>
                                                    ${priceTag}
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                });
                                html += '</div>';

                                if (data.total > data.products.length) {
                                    html += `
                                        <a href="${data.all_url}" class="btn btn-light btn-sm w-100 rounded-3 mt-2 fw-bold text-dark extra-small text-center py-2">
                                            Xem tất cả ${data.total} kết quả <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>
                                    `;
                                }

                                searchContent.innerHTML = html;
                                searchDropdown.classList.remove('d-none');
                            } else {
                                searchContent.innerHTML = '<div class="p-3 text-center text-muted extra-small"><i class="fa-regular fa-face-meh me-1"></i> Không tìm thấy sản phẩm phù hợp</div>';
                                searchDropdown.classList.remove('d-none');
                            }
                        })
                        .catch(function(err) {
                            if (searchSpinner) searchSpinner.classList.add('d-none');
                        });
                }, 250);
            });

            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 1 && searchContent.innerHTML.trim() !== '') {
                    searchDropdown.classList.remove('d-none');
                }
            });
        }
    });
</script>
