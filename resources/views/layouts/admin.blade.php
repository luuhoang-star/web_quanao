<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bảng Quản Trị - Quần Áo Store')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- CSS / JS Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        :root {
            --admin-sidebar-width: 260px;
            --admin-bg: #f1f5f9;
            --admin-sidebar-bg: #0f172a;
            --admin-sidebar-hover: #1e293b;
            --admin-accent: #3b82f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--admin-bg);
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--admin-sidebar-width);
            height: 100vh;
            overflow-y: auto;
            background-color: var(--admin-sidebar-bg);
            color: #94a3b8;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        /* Tùy chỉnh thanh cuộn (scrollbar) cho Sidebar */
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }
        #sidebar::-webkit-scrollbar-track {
            background: var(--admin-sidebar-bg);
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 3px;
        }
        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        #sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 700;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        #sidebar .nav-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1rem 1.5rem 0.5rem;
            font-weight: 600;
        }

        #sidebar .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--admin-sidebar-hover);
            border-left-color: var(--admin-accent);
        }

        #sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        /* Main Layout Content */
        #main-content {
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Navbar Styling */
        .admin-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
        }

        /* Cards and Components */
        .admin-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--admin-sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Admin -->
    <aside id="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-shield-halved text-warning"></i>
            <span>QUẦN ÁO ADMIN</span>
        </div>

        <ul class="nav flex-column py-2">
            <li class="nav-header">Tổng Quan</li>
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            </li>

            <li class="nav-header">Quản Lý Cửa Hàng</li>
            <li class="nav-item">
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-shirt"></i> Sản Phẩm (Áo, Quần...)
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i> Danh Mục (Categories)
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.collections.index') }}" class="nav-link {{ request()->routeIs('admin.collections.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-folder-open"></i> Quản Lý Bộ Sưu Tập
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.colors.index') }}" class="nav-link {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-palette"></i> Quản Lý Màu Sắc
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.sizes.index') }}" class="nav-link {{ request()->routeIs('admin.sizes.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-ruler-combined"></i> Quản Lý Kích Cỡ
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.lookbooks.index') }}" class="nav-link {{ request()->routeIs('admin.lookbooks.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-camera"></i> Quản Lý Lookbook
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.outfits.index') }}" class="nav-link {{ request()->routeIs('admin.outfits.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Quản Lý Outfit Phối Sẵn
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked"></i> Quản Lý Kho Hàng
                    @php
                        $lowStockAlertCount = \App\Models\ProductVariant::where('stock_quantity', '<=', 5)->count();
                    @endphp
                    @if($lowStockAlertCount > 0)
                        <span class="badge bg-danger text-white float-end" title="{{ $lowStockAlertCount }} biến thể sắp/hết hàng">{{ $lowStockAlertCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-header">Kinh Doanh & Đơn Hàng</li>
            <li class="nav-item">
                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-flatbed"></i> Quản Lý Đơn Hàng
                    @php
                        $pendingCount = \App\Models\Order::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge bg-warning text-dark float-end">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.returns.index') }}" class="nav-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-rotate-left"></i> Yêu Cầu Đổi / Trả
                    @php
                        $pendingReturnCount = \App\Models\OrderReturn::where('status', 'pending')->count();
                    @endphp
                    @if($pendingReturnCount > 0)
                        <span class="badge bg-danger text-white float-end">{{ $pendingReturnCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.flash-sales.index') }}" class="nav-link {{ request()->routeIs('admin.flash-sales.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bolt text-danger"></i> Quản Lý Flash Sale
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-ticket"></i> Mã Giảm Giá (Coupons)
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-star"></i> Quản Lý Đánh Giá
                </a>
            </li>

            <li class="nav-header">Hệ Thống</li>
            <li class="nav-item">
                <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Quản Lý Khách Hàng
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.profile.show') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear"></i> Hồ Sơ Admin
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i> Cấu Hình Cửa Hàng
                </a>
            </li>
            <li class="nav-item mt-3">
                <a href="{{ route('home') }}" class="nav-link text-info">
                    <i class="fa-solid fa-arrow-left"></i> Về Trang Web
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Wrapper -->
    <div id="main-content">
        <!-- Top Navbar -->
        <header class="admin-navbar d-flex align-items-center justify-content-between sticky-top">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border d-lg-none" id="sidebar-toggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Bảng Điều Khiển')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Notifications badge -->
                <button class="btn btn-light border rounded-circle position-relative" title="Thông báo">
                    <i class="fa-regular fa-bell text-secondary"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </button>

                <!-- Admin Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user fa-2x text-slate"></i>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-bold text-dark small leading-none">{{ Auth::guard('admin')->user()->name }}</div>
                            <div class="text-muted extra-small" style="font-size: 0.75rem;">Administrator</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <span class="dropdown-item-text text-muted small">
                                <i class="fa-regular fa-envelope me-1"></i> {{ Auth::guard('admin')->user()->email }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile.show') }}">
                                <i class="fa-solid fa-user-gear me-2 text-primary"></i> Hồ sơ cá nhân
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                <i class="fa-solid fa-sliders me-2 text-warning"></i> Cấu hình cửa hàng
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất Admin
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="p-4 flex-grow-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 text-center text-muted small">
            <div class="container-fluid">
                <span>&copy; 2026 Quần Áo Store Admin Panel. Hỗ trợ hệ thống Laravel 12.</span>
            </div>
        </footer>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- SweetAlert2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Toggle Sidebar JS for Mobile -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#0f172a',
                    timer: 3500
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Thông báo!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#0f172a'
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
