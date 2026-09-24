<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Atino - Thời Trang Nam Nữ Phong Cách Tối Giản')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SEO & Social Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Atino - Cửa hàng thời trang nam nữ phong cách tối giản, hiện đại, thanh lịch. Mua sắm quần áo thời trang cao cấp với giá tốt nhất.')">
    <meta name="keywords" content="@yield('meta_keywords', 'thời trang nam, thời trang nữ, áo thun, áo sơ mi, quần jean, áo polo, thời trang atino, quần áo cao cấp')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- OpenGraph Social Meta Tags -->
    <meta property="og:site_name" content="Atino Fashion Store">
    <meta property="og:title" content="@yield('title', 'Atino - Thời Trang Nam Nữ Phong Cách Tối Giản')">
    <meta property="og:description" content="@yield('meta_description', 'Cửa hàng thời trang cao cấp Atino. Giao hàng toàn quốc, miễn phí đổi trả trong 30 ngày.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Atino - Thời Trang Nam Nữ Phong Cách Tối Giản')">
    <meta name="twitter:description" content="@yield('meta_description', 'Cửa hàng thời trang cao cấp Atino. Giao hàng toàn quốc.')">

    <!-- Schema.org JSON-LD Structured Data cho Google -->
    <script type="application/ld+json">
    {
        "\u0040context": "https://schema.org",
        "\u0040type": "Organization",
        "name": "Atino Fashion Store",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "contactPoint": {
            "\u0040type": "ContactPoint",
            "telephone": "0973797151",
            "contactType": "customer service"
        }
    }
    </script>

    <!-- Tách CSS & JS riêng biệt -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <!-- 1. Main Header Navbar Component -->
    @include('partials.header')

    <!-- 3. Main Content Container -->
    <main>
        <div class="container my-4">
            <!-- Flash Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Cart Drawer Partial (Offcanvas Right) -->
    @include('partials.cart-drawer')

    <!-- 4. Footer Component -->
    @include('partials.footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        window.currentDrawerItems = [];

        // Hàm nạp dữ liệu Giỏ hàng thực tế từ Server vào Cart Drawer
        window.reloadCartDrawer = function() {
            $.ajax({
                url: '{{ route("cart.json") }}',
                method: 'GET',
                success: function(res) {
                    if (!res.success) return;

                    var count = res.count || 0;
                    $('#cart-count-badge').text(count);
                    $('#drawer-cart-count').text(count);
                    $('#drawer-subtotal-price').text(res.total || '0đ');

                    window.currentDrawerItems = res.items || [];

                    if (count === 0 || !res.items || res.items.length === 0) {
                        $('#drawer-cart-empty').removeClass('d-none');
                        $('#drawer-cart-list').addClass('d-none').empty();
                    } else {
                        $('#drawer-cart-empty').addClass('d-none');
                        var listContainer = $('#drawer-cart-list').removeClass('d-none').empty();

                        var fallbackPhotos = [
                            'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=400&q=80',
                            'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=400&q=80',
                            'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=400&q=80',
                            'https://images.unsplash.com/photo-1562157873-818bc0726f68?auto=format&fit=crop&w=400&q=80'
                        ];

                        res.items.forEach(function(item, index) {
                            var imgUrl = item.image || fallbackPhotos[item.product_id % fallbackPhotos.length];
                            var itemHtml = `
                                <div class="d-flex align-items-center gap-2.5 p-2.5 bg-light rounded-3 border border-light position-relative mb-2 shadow-2xs">
                                    <img src="${imgUrl}" alt="${item.name}" class="rounded-2 object-fit-cover flex-shrink-0" style="width: 58px; height: 68px;" onerror="this.onerror=null; this.src='${fallbackPhotos[index % fallbackPhotos.length]}';">
                                    <div class="flex-grow-1 min-w-0 pe-3">
                                        <h6 class="fw-bold text-dark mb-1 small text-truncate" title="${item.name}">${item.name}</h6>
                                        <div class="d-flex align-items-center gap-1.5 mb-1.5">
                                            <span class="badge bg-dark text-white extra-small">${item.variant}</span>
                                            <span class="fw-semibold text-muted extra-small">${item.price}</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="cart-qty-pill" style="transform: scale(0.85); transform-origin: left center;">
                                                <button type="button" class="qty-btn" onclick="window.updateDrawerQty('${item.id}', -1)"><i class="fa-solid fa-minus"></i></button>
                                                <input type="number" class="qty-input" value="${item.quantity}" readonly>
                                                <button type="button" class="qty-btn" onclick="window.updateDrawerQty('${item.id}', 1)"><i class="fa-solid fa-plus"></i></button>
                                            </div>
                                            <span class="fw-extrabold text-danger small">${item.subtotal}</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-light btn-sm rounded-circle p-1 text-danger border-0 position-absolute top-0 end-0 m-1" onclick="window.removeDrawerItem('${item.id}')" title="Xóa">
                                        <i class="fa-solid fa-xmark extra-small"></i>
                                    </button>
                                </div>
                            `;
                            listContainer.append(itemHtml);
                        });
                    }
                }
            });
        };

        // Hàm cập nhật số lượng trong Drawer
        window.updateDrawerQty = function(itemId, delta) {
            var item = window.currentDrawerItems ? window.currentDrawerItems.find(function(i) { return i.id == itemId; }) : null;
            var currentQty = item ? parseInt(item.quantity) : 1;
            var newQty = currentQty + delta;
            if (newQty <= 0) {
                window.removeDrawerItem(itemId);
                return;
            }
            $.ajax({
                url: '/cart/' + itemId,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: newQty
                },
                success: function() {
                    window.reloadCartDrawer();
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Không thể cập nhật';
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, showConfirmButton: false, timer: 2000 });
                }
            });
        };

        // Hàm xóa món khỏi Drawer
        window.removeDrawerItem = function(itemId) {
            $.ajax({
                url: '/cart/' + itemId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    window.reloadCartDrawer();
                }
            });
        };

        $(document).ready(function() {
            // Nạp dữ liệu Cart Drawer ngay khi mở trang
            window.reloadCartDrawer();

            // Nạp lại dữ liệu khi mở Offcanvas Cart Drawer
            var drawerEl = document.getElementById('cartDrawer');
            if (drawerEl) {
                drawerEl.addEventListener('show.bs.offcanvas', function () {
                    window.reloadCartDrawer();
                });
            }

            // Lắng nghe sự kiện click nút "Thêm vào giỏ hàng"
            $(document).on('click', '.btn-add-to-cart', function(e) {
                e.preventDefault();
                var btn = $(this);
                var productId = btn.data('id');
                if (!productId) return;

                var qty = 1;
                var variantId = null;

                // Nếu nhấn từ trang chi tiết sản phẩm hoặc form có số lượng/biến thể
                var form = $('#add-to-cart-form');
                if (form.length > 0) {
                    var formQty = form.find('input[name="quantity"]').val();
                    if (formQty && parseInt(formQty) > 0) {
                        qty = parseInt(formQty);
                    }
                    var formVar = form.find('input[name="product_variant_id"]').val();
                    if (formVar) {
                        variantId = formVar;
                    }
                }

                var originalHtml = btn.html();
                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang thêm...');

                $.ajax({
                    url: '{{ route("cart.store") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        product_variant_id: variantId,
                        quantity: qty
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html(originalHtml);
                        
                        // Nạp lại Cart Drawer từ server
                        window.reloadCartDrawer();

                        // Mở Cart Drawer tự động từ lề phải
                        if (drawerEl) {
                            var bsDrawer = bootstrap.Offcanvas.getOrCreateInstance(drawerEl);
                            bsDrawer.show();
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(originalHtml);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Có lỗi xảy ra khi thêm vào giỏ hàng.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            });


            // Lắng nghe sự kiện click nút Yêu Thích (.btn-toggle-wishlist)
            $(document).on('click', '.btn-toggle-wishlist', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var btn = $(this);
                var productId = btn.data('id');
                if (!productId) return;

                $.ajax({
                    url: '/wishlist/' + productId + '/toggle',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            var icon = btn.find('i');
                            if (response.added) {
                                icon.removeClass('fa-regular text-muted').addClass('fa-solid text-danger');
                                btn.addClass('active');
                            } else {
                                icon.removeClass('fa-solid text-danger').addClass('fa-regular text-muted');
                                btn.removeClass('active');
                            }

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'warning',
                                title: 'Vui lòng đăng nhập để lưu sản phẩm yêu thích!',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            setTimeout(function() {
                                window.location.href = "{{ route('login') }}";
                            }, 1200);
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Có lỗi xảy ra, vui lòng thử lại.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
