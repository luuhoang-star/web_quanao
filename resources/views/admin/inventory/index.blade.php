@extends('layouts.admin')

@section('title', 'Quản Lý Kho Hàng & Tồn Kho - Admin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- PAGE TITLE & QUICK ACTIONS -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-primary"></i> Quản Lý Kho Hàng & Tồn Kho
                <span class="badge bg-primary-subtle text-primary border fs-6 rounded-pill px-3 py-1">
                    {{ number_format($stats['total_stock']) }} sản phẩm
                </span>
            </h4>
            <p class="text-muted small mb-0">Theo dõi tồn kho theo biến thể màu/size, nhập xuất tồn, kiểm kê và cảnh báo chạm ngưỡng.</p>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            <button type="button" class="btn btn-success shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importStockModal">
                <i class="fa-solid fa-file-import"></i> <span>Nhập Kho</span>
            </button>
            <button type="button" class="btn btn-outline-danger shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#exportStockModal">
                <i class="fa-solid fa-file-export"></i> <span>Xuất Kho Thủ Công</span>
            </button>
            <button type="button" class="btn btn-outline-warning text-dark shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                <i class="fa-solid fa-scale-balanced"></i> <span>Điều Chỉnh Kiểm Kê</span>
            </button>
            <a href="{{ route('admin.inventory.movements') }}" class="btn btn-outline-secondary shadow-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left"></i> <span>Lịch Sử Biến Động</span>
            </a>
            <a href="{{ route('admin.inventory.exportExcel') }}" class="btn btn-outline-success shadow-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> <span>Xuất Excel (CSV)</span>
            </a>
        </div>
    </div>

    <!-- 1. DASHBOARD TỔNG QUAN HÀNG TỒN (5 KPI CARDS) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Tổng SKU -->
        <div class="col-12 col-sm-6 col-xl-2-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-card-icon bg-primary-subtle text-primary rounded-3 flex-shrink-0">
                        <i class="fa-solid fa-barcode"></i>
                    </div>
                    <div>
                        <span class="text-muted extra-small fw-semibold text-uppercase d-block mb-1">Tổng SKU Biến Thể</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_skus']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Tổng tồn kho -->
        <div class="col-12 col-sm-6 col-xl-2-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-card-icon bg-success-subtle text-success rounded-3 flex-shrink-0">
                        <i class="fa-solid fa-cubes"></i>
                    </div>
                    <div>
                        <span class="text-muted extra-small fw-semibold text-uppercase d-block mb-1">Tổng Số Lượng Tồn</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_stock']) }} <small class="fs-6 fw-normal text-muted">SP</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: SKU Sắp Hết Hàng -->
        <div class="col-12 col-sm-6 col-xl-2-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-card-icon bg-warning-subtle text-warning rounded-3 flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <span class="text-muted extra-small fw-semibold text-uppercase d-block mb-1">Sắp Hết Hàng</span>
                        <h4 class="fw-bold mb-0 text-warning">{{ number_format($stats['low_stock_count']) }} <small class="fs-6 fw-normal text-muted">SKU</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: SKU Hết Hàng -->
        <div class="col-12 col-sm-6 col-xl-2-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-card-icon bg-danger-subtle text-danger rounded-3 flex-shrink-0">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div>
                        <span class="text-muted extra-small fw-semibold text-uppercase d-block mb-1">Hết Hàng Hoàn Toàn</span>
                        <h4 class="fw-bold mb-0 text-danger">{{ number_format($stats['out_of_stock_count']) }} <small class="fs-6 fw-normal text-muted">SKU</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5: Giá trị tồn kho ước tính -->
        <div class="col-12 col-sm-6 col-xl-2-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="stat-card-icon bg-info-subtle text-info rounded-3 flex-shrink-0">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div>
                        <span class="text-muted extra-small fw-semibold text-uppercase d-block mb-1">Giá Trị Tồn Kho</span>
                        <h5 class="fw-bold mb-0 text-dark">{{ number_format($stats['estimated_stock_value'], 0, ',', '.') }} <small class="fs-6 fw-normal text-muted">đ</small></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 2. BẢNG DANH SÁCH TỒN KHO & BỘ LỌC -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        
        <!-- TOOLBAR BỘ LỌC -->
        <div class="card-header bg-white py-3 border-bottom">
            <form action="{{ route('admin.inventory.index') }}" method="GET" class="row g-2 align-items-center">
                <!-- Search input -->
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Tìm theo Mã SKU, tên sản phẩm..." 
                               value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('admin.inventory.index', request()->except('search')) }}" class="input-group-text bg-light border-start-0 text-muted" title="Xóa tìm kiếm">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Lọc Danh Mục -->
                <div class="col-6 col-md-3 col-lg-2">
                    <select name="category_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">-- Tất cả danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Lọc Trạng thái tồn -->
                <div class="col-6 col-md-3 col-lg-2">
                    <select name="stock_status" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">-- Trạng thái tồn --</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Sắp hết hàng (< Ngưỡng)</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Hết hàng (= 0)</option>
                    </select>
                </div>

                <!-- Sắp xếp -->
                <div class="col-6 col-md-2 col-lg-2">
                    <select name="sort_by" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="stock_quantity" {{ $sortBy == 'stock_quantity' ? 'selected' : '' }}>Tồn kho: Ít ➔ Nhiều</option>
                        <option value="sku" {{ $sortBy == 'sku' ? 'selected' : '' }}>Mã SKU (A ➔ Z)</option>
                        <option value="cost_price" {{ $sortBy == 'cost_price' ? 'selected' : '' }}>Giá vốn: Cao ➔ Thấp</option>
                    </select>
                </div>

                <!-- Nút Submit / Reset -->
                <div class="col-6 col-md-2 col-lg-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    @if($activeFilterCount > 0)
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-secondary" title="Đặt lại bộ lọc">
                            <i class="fa-solid fa-rotate"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- BẢNG DỮ LIỆU -->
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light extra-small text-uppercase">
                    <tr>
                        <th style="width: 50px;" class="ps-3">STT</th>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Sản Phẩm & Biến Thể</th>
                        <th style="width: 140px;">SKU / Mã Vạch</th>
                        <th style="width: 110px;" class="text-center">Tồn Hiện Tại</th>
                        <th style="width: 100px;" class="text-center">Đã Đặt</th>
                        <th style="width: 110px;" class="text-center">Có Thể Bán</th>
                        <th style="width: 110px;" class="text-end">Giá Vốn</th>
                        <th style="width: 110px;" class="text-center">Ngưỡng Báo</th>
                        <th style="width: 110px;" class="text-center">Trạng Thái</th>
                        <th style="width: 130px;" class="text-end pe-3">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $index => $v)
                        @php
                            $reserved = $reservedStockMap[$v->id] ?? 0;
                            $available = max(0, $v->stock_quantity - $reserved);
                            $thumbImage = $v->product ? $v->product->image_url : null;
                        @endphp
                        <tr>
                            <td class="ps-3 text-muted small fw-semibold">
                                {{ ($variants->currentPage() - 1) * $variants->perPage() + $index + 1 }}
                            </td>
                            <td>
                                @if($thumbImage)
                                    <img src="{{ $thumbImage }}" class="rounded border object-fit-cover" width="44" height="44" alt="Product Image">
                                @else
                                    <div class="rounded border bg-light text-muted d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="fa-solid fa-shirt"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-1">
                                    {{ $v->product ? $v->product->name : 'N/A' }}
                                </div>
                                <div class="d-flex align-items-center gap-2 extra-small">
                                    @if($v->color)
                                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                            <span class="rounded-circle border" style="width: 8px; height: 8px; background-color: {{ $v->color->hex_code ?? '#cccccc' }};"></span>
                                            {{ $v->color->name }}
                                        </span>
                                    @endif
                                    @if($v->size)
                                        <span class="badge bg-dark text-white">Size {{ $v->size->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code class="fw-bold text-primary">{{ $v->sku ?? 'N/A' }}</code>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 {{ $v->stock_quantity <= 0 ? 'text-danger' : ($v->stock_quantity <= ($v->low_stock_threshold ?? 5) ? 'text-warning fw-bold' : 'text-dark') }}">
                                    {{ number_format($v->stock_quantity) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($reserved > 0)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">
                                        {{ $reserved }}
                                    </span>
                                @else
                                    <span class="text-muted small">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold {{ $available <= 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($available) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-dark small fw-semibold">
                                    {{ number_format($v->cost_price ?? 0, 0, ',', '.') }}đ
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-link p-0 text-decoration-none text-muted small btn-edit-threshold" 
                                        data-id="{{ $v->id }}" 
                                        data-threshold="{{ $v->low_stock_threshold ?? 5 }}" 
                                        title="Sửa ngưỡng cảnh báo">
                                    <span class="badge bg-light text-dark border"><i class="fa-solid fa-bell me-1 text-warning"></i> < {{ $v->low_stock_threshold ?? 5 }}</span>
                                </button>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $v->stock_status_badge }} rounded-pill px-2.5 py-1 extra-small fw-semibold">
                                    {{ $v->stock_status_label }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-success btn-quick-import" 
                                            data-id="{{ $v->id }}" 
                                            data-sku="{{ $v->sku }}" 
                                            data-name="{{ $v->product ? $v->product->name : '' }} ({{ $v->color ? $v->color->name : '' }} / {{ $v->size ? 'Size '.$v->size->name : '' }})" 
                                            data-cost="{{ $v->cost_price }}" 
                                            title="Nhập kho nhanh">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-quick-export" 
                                            data-id="{{ $v->id }}" 
                                            data-sku="{{ $v->sku }}" 
                                            data-name="{{ $v->product ? $v->product->name : '' }} ({{ $v->color ? $v->color->name : '' }} / {{ $v->size ? 'Size '.$v->size->name : '' }})" 
                                            data-stock="{{ $v->stock_quantity }}" 
                                            title="Xuất kho thủ công">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning text-dark btn-quick-adjust" 
                                            data-id="{{ $v->id }}" 
                                            data-sku="{{ $v->sku }}" 
                                            data-name="{{ $v->product ? $v->product->name : '' }} ({{ $v->color ? $v->color->name : '' }} / {{ $v->size ? 'Size '.$v->size->name : '' }})" 
                                            data-stock="{{ $v->stock_quantity }}" 
                                            title="Kiểm kê điều chỉnh">
                                        <i class="fa-solid fa-scale-balanced"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="fa-solid fa-boxes-stacked fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                <h6 class="fw-bold text-dark">Không tìm thấy dữ liệu tồn kho nào</h6>
                                <p class="small text-muted mb-3">Thử thay đổi bộ lọc hoặc thêm các biến thể sản phẩm mới.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($variants->total() > 0)
            <div class="card-footer bg-white py-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $variants->firstItem() }}-{{ $variants->lastItem() }}</strong> trong tổng số <strong>{{ number_format($variants->total()) }}</strong> biến thể tồn kho
                </div>
                <div>
                    {{ $variants->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- ================= MODAL NHẬP KHO ================= -->
<div class="modal fade" id="importStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-import me-2"></i>Phiếu Nhập Kho Sản Phẩm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.inventory.import') }}" method="POST" id="formImportStock">
                @csrf
                <div class="modal-body p-4">
                    <!-- KHỐI CHỌN SẢN PHẨM PHÂN CẤP THEO DANH MỤC -->
                    <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <span class="fw-bold small text-dark"><i class="fa-solid fa-layer-group me-1 text-success"></i> Bước 1 & 2: Chọn Sản Phẩm Cần Nhập</span>
                            <div class="input-group input-group-sm" style="max-width: 280px;">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted extra-small"></i></span>
                                <input type="text" class="form-control bg-white border-start-0 py-1" id="import_quick_search" placeholder="Gõ tìm nhanh SKU / Tên...">
                            </div>
                        </div>

                        <div class="row g-2">
                            <!-- Bước 1: Chọn Danh Mục -->
                            <div class="col-md-6">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">1. Danh Mục Sản Phẩm</label>
                                <select class="form-select form-select-sm" id="import_category_select">
                                    <option value="">-- Tất cả danh mục --</option>
                                    @foreach($categories->whereNull('parent_id') as $parentCat)
                                        <optgroup label="📂 {{ $parentCat->name }}">
                                            @foreach($parentCat->children as $subCat)
                                                <option value="{{ $subCat->id }}">{{ $subCat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bước 2: Chọn Sản Phẩm Cụ Thể -->
                            <div class="col-md-6">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">2. Chọn Sản Phẩm <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="import_product_select">
                                    <option value="">-- Chọn sản phẩm --</option>
                                </select>
                            </div>

                            <!-- Bước 3: Chọn Biến Thể (Màu sắc & Kích thước) -->
                            <div class="col-12 mt-2">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">3. Chọn Biến Thể (Màu & Size) Cần Nhập <span class="text-danger">*</span></label>
                                <select name="product_variant_id" class="form-select fw-semibold" id="import_product_variant_id" required>
                                    <option value="">-- Vui lòng chọn sản phẩm trước --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Khung Preview Thông Tin Biến Thể Đang Chọn -->
                        <div id="import_preview_card" class="mt-3 p-2.5 bg-white rounded-3 border d-none">
                            <div class="d-flex align-items-center gap-3">
                                <img id="import_preview_img" src="" alt="" class="rounded border object-fit-cover" style="width: 52px; height: 52px;">
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span id="import_preview_cat" class="badge bg-light text-dark border extra-small"></span>
                                        <code id="import_preview_sku" class="extra-small fw-bold text-success"></code>
                                    </div>
                                    <h6 id="import_preview_name" class="fw-bold text-dark mb-1 text-truncate small mt-0.5"></h6>
                                    <div class="d-flex align-items-center gap-3 extra-small">
                                        <span id="import_preview_variant" class="text-muted"></span>
                                        <span class="text-secondary">|</span>
                                        <span>Tồn kho hiện tại: <strong id="import_preview_stock" class="text-dark"></strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- THÔNG TIN ĐỢT NHẬP HÀNG -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số Lượng Nhập <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control form-control-lg text-success fw-bold" min="1" placeholder="Ví dụ: 50" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Giá Vốn Nhập (đ/SP)</label>
                            <input type="number" name="cost_price" id="import_cost_price" class="form-control form-control-lg" step="1000" min="0" placeholder="Ví dụ: 120000">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nhà Cung Cấp / Xưởng May</label>
                            <input type="text" name="supplier" class="form-control" placeholder="Ví dụ: Tổng kho thời trang Atino">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mã Chứng Từ / Số HĐ</label>
                            <input type="text" name="reference_code" class="form-control" placeholder="Ví dụ: PN-{{ date('Ymd') }}-01">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Ghi Chú Nhập Kho</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú đợt nhập hàng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fa-solid fa-check me-1"></i> Xác Nhận Nhập Kho</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL XUẤT KHO THỦ CÔNG ================= -->
<div class="modal fade" id="exportStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-export me-2"></i>Phiếu Xuất Kho Thủ Công</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.inventory.export') }}" method="POST" id="formExportStock">
                @csrf
                <div class="modal-body p-4">
                    <!-- KHỐI CHỌN SẢN PHẨM PHÂN CẤP -->
                    <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <span class="fw-bold small text-dark"><i class="fa-solid fa-layer-group me-1 text-danger"></i> Bước 1 & 2: Chọn Sản Phẩm Cần Xuất</span>
                            <div class="input-group input-group-sm" style="max-width: 280px;">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted extra-small"></i></span>
                                <input type="text" class="form-control bg-white border-start-0 py-1" id="export_quick_search" placeholder="Gõ tìm nhanh SKU / Tên...">
                            </div>
                        </div>

                        <div class="row g-2">
                            <!-- Bước 1: Chọn Danh Mục -->
                            <div class="col-md-6">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">1. Danh Mục Sản Phẩm</label>
                                <select class="form-select form-select-sm" id="export_category_select">
                                    <option value="">-- Tất cả danh mục --</option>
                                    @foreach($categories->whereNull('parent_id') as $parentCat)
                                        <optgroup label="📂 {{ $parentCat->name }}">
                                            @foreach($parentCat->children as $subCat)
                                                <option value="{{ $subCat->id }}">{{ $subCat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bước 2: Chọn Sản Phẩm Cụ Thể -->
                            <div class="col-md-6">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">2. Chọn Sản Phẩm <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="export_product_select">
                                    <option value="">-- Chọn sản phẩm --</option>
                                </select>
                            </div>

                            <!-- Bước 3: Chọn Biến Thể -->
                            <div class="col-12 mt-2">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">3. Chọn Biến Thể (Màu & Size) Cần Xuất <span class="text-danger">*</span></label>
                                <select name="product_variant_id" class="form-select fw-semibold" id="export_product_variant_id" required>
                                    <option value="">-- Vui lòng chọn sản phẩm trước --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Khung Preview Thông Tin Biến Thể -->
                        <div id="export_preview_card" class="mt-3 p-2.5 bg-white rounded-3 border d-none">
                            <div class="d-flex align-items-center gap-3">
                                <img id="export_preview_img" src="" alt="" class="rounded border object-fit-cover" style="width: 52px; height: 52px;">
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span id="export_preview_cat" class="badge bg-light text-dark border extra-small"></span>
                                        <code id="export_preview_sku" class="extra-small fw-bold text-danger"></code>
                                    </div>
                                    <h6 id="export_preview_name" class="fw-bold text-dark mb-1 text-truncate small mt-0.5"></h6>
                                    <div class="d-flex align-items-center gap-3 extra-small">
                                        <span id="export_preview_variant" class="text-muted"></span>
                                        <span class="text-secondary">|</span>
                                        <span>Tồn kho hiện tại: <strong id="export_preview_stock" class="text-danger"></strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- THÔNG TIN XUẤT KHO -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số Lượng Xuất <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control form-control-lg text-danger fw-bold" min="1" placeholder="Ví dụ: 5" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mã Chứng Từ / Số Phiếu</label>
                            <input type="text" name="reference_code" class="form-control form-control-lg" placeholder="Ví dụ: PX-{{ date('Ymd') }}-01">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Lý Do Xuất Kho <span class="text-danger">*</span></label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Hàng rách hỏng kiểm kê, trả lại xưởng may, xuất làm mẫu chụp hình..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold"><i class="fa-solid fa-check me-1"></i> Xác Nhận Xuất Kho</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL ĐIỀU CHỈNH KIỂM KÊ ================= -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-scale-balanced me-2"></i>Điều Chỉnh Kiểm Kê Kho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.inventory.adjust') }}" method="POST" id="formAdjustStock">
                @csrf
                <div class="modal-body p-4">
                    <!-- KHỐI CHỌN SẢN PHẨM PHÂN CẤP -->
                    <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <span class="fw-bold small text-dark"><i class="fa-solid fa-layer-group me-1 text-warning"></i> Bước 1 & 2: Chọn Sản Phẩm Kiểm Kê</span>
                            <div class="input-group input-group-sm" style="max-width: 280px;">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted extra-small"></i></span>
                                <input type="text" class="form-control bg-white border-start-0 py-1" id="adjust_quick_search" placeholder="Gõ tìm nhanh SKU / Tên...">
                            </div>
                        </div>

                        <div class="row g-2">
                            <!-- Bước 1: Chọn Danh Mục -->
                            <div class="col-md-6">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">1. Danh Mục Sản Phẩm</label>
                                <select class="form-select form-select-sm" id="adjust_category_select">
                                    <option value="">-- Tất cả danh mục --</option>
                                    @foreach($categories->whereNull('parent_id') as $parentCat)
                                        <optgroup label="📂 {{ $parentCat->name }}">
                                            @foreach($parentCat->children as $subCat)
                                                <option value="{{ $subCat->id }}">{{ $subCat->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bước 2: Chọn Sản Phẩm Cụ Thể -->
                            <div class="col-md-6">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">2. Chọn Sản Phẩm <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="adjust_product_select">
                                    <option value="">-- Chọn sản phẩm --</option>
                                </select>
                            </div>

                            <!-- Bước 3: Chọn Biến Thể -->
                            <div class="col-12 mt-2">
                                <label class="form-label extra-small text-muted fw-semibold mb-1">3. Chọn Biến Thể (Màu & Size) Cần Kiểm Kê <span class="text-danger">*</span></label>
                                <select name="product_variant_id" class="form-select fw-semibold" id="adjust_product_variant_id" required>
                                    <option value="">-- Vui lòng chọn sản phẩm trước --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Khung Preview Thông Tin Biến Thể -->
                        <div id="adjust_preview_card" class="mt-3 p-2.5 bg-white rounded-3 border d-none">
                            <div class="d-flex align-items-center gap-3">
                                <img id="adjust_preview_img" src="" alt="" class="rounded border object-fit-cover" style="width: 52px; height: 52px;">
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span id="adjust_preview_cat" class="badge bg-light text-dark border extra-small"></span>
                                        <code id="adjust_preview_sku" class="extra-small fw-bold text-dark"></code>
                                    </div>
                                    <h6 id="adjust_preview_name" class="fw-bold text-dark mb-1 text-truncate small mt-0.5"></h6>
                                    <div class="d-flex align-items-center gap-3 extra-small">
                                        <span id="adjust_preview_variant" class="text-muted"></span>
                                        <span class="text-secondary">|</span>
                                        <span>Tồn hệ thống: <strong id="adjust_preview_stock" class="text-primary"></strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- THÔNG TIN KIỂM KÊ -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Số Lượng Thực Tế Đếm Được Tại Kho <span class="text-danger">*</span></label>
                        <input type="number" name="actual_quantity" id="adjust_actual_quantity" class="form-control form-control-lg text-primary fw-bold" min="0" placeholder="Số thực tế đếm tại kho..." required>
                        <div class="form-text extra-small text-muted mt-1">Hệ thống sẽ tự động tính số lượng chênh lệch (thừa/thiếu) và tạo phiếu điều chỉnh tương ứng.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Lý Do Điều Chỉnh / Lệch Kho <span class="text-danger">*</span></label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Kiểm kê định kỳ, đếm thừa/thiếu do nhầm size..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning text-dark px-4 fw-bold"><i class="fa-solid fa-check me-1"></i> Lưu Điều Chỉnh</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL CẬP NHẬT NGƯỠNG CẢNH BÁO ================= -->
<div class="modal fade" id="thresholdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title fw-bold"><i class="fa-solid fa-bell text-warning me-1"></i> Ngưỡng Cảnh Báo Sắp Hết</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="formThreshold">
                @csrf
                <div class="modal-body py-3">
                    <p class="small text-muted mb-2">Khi số lượng tồn kho xuống bằng hoặc thấp hơn ngưỡng này, hệ thống sẽ bật cảnh báo đỏ/vàng.</p>
                    <label class="form-label fw-semibold small">Ngưỡng Tối Thiểu (Số lượng)</label>
                    <input type="number" name="low_stock_threshold" id="input_threshold_val" class="form-control fw-bold" min="0" required>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm">Lưu Ngưỡng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const inventoryProducts = @json($inventoryProducts);

    // Xây dựng map hỗ trợ tra cứu nhanh biến thể theo ID
    const variantLookup = {};
    inventoryProducts.forEach(prod => {
        prod.variants.forEach(v => {
            variantLookup[v.id] = {
                variant: v,
                product: prod
            };
        });
    });

    /**
     * Khởi tạo bộ chọn phân cấp: Danh mục -> Sản phẩm -> Biến thể
     */
    function setupHierarchicalSelector(prefix) {
        const $catSelect = $(`#${prefix}_category_select`);
        const $prodSelect = $(`#${prefix}_product_select`);
        const $varSelect = $(`#${prefix}_product_variant_id`);
        const $quickSearch = $(`#${prefix}_quick_search`);
        const $previewCard = $(`#${prefix}_preview_card`);

        // Đổ danh sách sản phẩm theo danh mục
        function populateProducts(catId = '') {
            $prodSelect.empty().append('<option value="">-- Chọn sản phẩm --</option>');
            $varSelect.empty().append('<option value="">-- Vui lòng chọn sản phẩm trước --</option>');
            $previewCard.addClass('d-none');

            const filteredProds = catId 
                ? inventoryProducts.filter(p => p.category_id == catId)
                : inventoryProducts;

            filteredProds.forEach(p => {
                $prodSelect.append(`<option value="${p.id}" data-name="${p.name.toLowerCase()}">${p.name}</option>`);
            });
        }

        // Đổ danh sách biến thể theo sản phẩm
        function populateVariants(prodId) {
            $varSelect.empty().append('<option value="">-- Chọn Màu sắc & Size --</option>');
            $previewCard.addClass('d-none');

            const prod = inventoryProducts.find(p => p.id == prodId);
            if (!prod || !prod.variants) return;

            prod.variants.forEach(v => {
                const stockBadge = v.stock_quantity <= 0 ? 'Hết hàng' : `Tồn: ${v.stock_quantity}`;
                $varSelect.append(
                    `<option value="${v.id}" data-cost="${v.cost_price}" data-stock="${v.stock_quantity}">
                        [${v.sku}] ${v.color_name} - Size ${v.size_name} (${stockBadge})
                    </option>`
                );
            });
        }

        // Cập nhật thẻ xem trước (Preview Card)
        function updatePreview(variantId) {
            if (!variantId || !variantLookup[variantId]) {
                $previewCard.addClass('d-none');
                return;
            }

            const item = variantLookup[variantId];
            const p = item.product;
            const v = item.variant;

            $(`#${prefix}_preview_img`).attr('src', p.image_url);
            $(`#${prefix}_preview_cat`).text(p.category_name);
            $(`#${prefix}_preview_sku`).text(v.sku);
            $(`#${prefix}_preview_name`).text(p.name);
            $(`#${prefix}_preview_variant`).html(`Màu: <strong>${v.color_name}</strong> | Size: <strong>${v.size_name}</strong>`);
            $(`#${prefix}_preview_stock`).text(`${v.stock_quantity} SP`);
            $previewCard.removeClass('d-none');

            // Tự động điền giá vốn nhập nếu là modal nhập
            if (prefix === 'import' && v.cost_price) {
                $('#import_cost_price').val(v.cost_price);
            }
            // Tự động điền số lượng kiểm kê nếu là modal điều chỉnh
            if (prefix === 'adjust') {
                $('#adjust_actual_quantity').val(v.stock_quantity);
            }
        }

        // Khởi tạo sản phẩm ban đầu
        populateProducts();

        // Bắt sự kiện chọn Danh Mục
        $catSelect.on('change', function() {
            populateProducts($(this).val());
        });

        // Bắt sự kiện chọn Sản Phẩm
        $prodSelect.on('change', function() {
            const prodId = $(this).val();
            if (prodId) {
                populateVariants(prodId);
            } else {
                $varSelect.empty().append('<option value="">-- Vui lòng chọn sản phẩm trước --</option>');
                $previewCard.addClass('d-none');
            }
        });

        // Bắt sự kiện chọn Biến Thể
        $varSelect.on('change', function() {
            updatePreview($(this).val());
        });

        // Bắt sự kiện gõ tìm nhanh (Quick Search)
        $quickSearch.on('input', function() {
            const keyword = $(this).val().trim().toLowerCase();
            if (!keyword) {
                populateProducts($catSelect.val());
                return;
            }

            // Tìm trong toàn bộ danh sách sản phẩm hoặc SKU
            $prodSelect.empty().append('<option value="">-- Kết quả tìm kiếm --</option>');
            $varSelect.empty().append('<option value="">-- Chọn biến thể phù hợp --</option>');

            let matchedProds = [];
            inventoryProducts.forEach(p => {
                const nameMatch = p.name.toLowerCase().includes(keyword);
                const skuMatch = p.variants.some(v => v.sku.toLowerCase().includes(keyword));
                if (nameMatch || skuMatch) {
                    matchedProds.push(p);
                    $prodSelect.append(`<option value="${p.id}">${p.name} (${p.category_name})</option>`);
                }
            });

            // Nếu chỉ có 1 sản phẩm khớp, tự động chọn và bung biến thể luôn
            if (matchedProds.length === 1) {
                $prodSelect.val(matchedProds[0].id);
                populateVariants(matchedProds[0].id);

                // Nếu từ khóa trùng đúng SKU nào, tự động chọn luôn SKU đó
                const exactVariant = matchedProds[0].variants.find(v => v.sku.toLowerCase().includes(keyword));
                if (exactVariant) {
                    $varSelect.val(exactVariant.id);
                    updatePreview(exactVariant.id);
                }
            }
        });

        // Hàm hỗ trợ chọn trực tiếp biến thể từ ngoài bảng
        return function selectSpecificVariant(variantId) {
            if (!variantId || !variantLookup[variantId]) return;

            const item = variantLookup[variantId];
            const p = item.product;
            const v = item.variant;

            // Đặt danh mục
            $catSelect.val(p.category_id);
            // Đổ sản phẩm theo danh mục và chọn sản phẩm
            populateProducts(p.category_id);
            $prodSelect.val(p.id);
            // Đổ biến thể và chọn biến thể
            populateVariants(p.id);
            $varSelect.val(v.id);
            // Cập nhật Preview Card
            updatePreview(v.id);
        };
    }

    $(document).ready(function() {
        // Cài đặt bộ chọn cho cả 3 modal
        const setImportVariant = setupHierarchicalSelector('import');
        const setExportVariant = setupHierarchicalSelector('export');
        const setAdjustVariant = setupHierarchicalSelector('adjust');

        // Nút Nhập Kho Nhanh (+) ở từng dòng trong bảng
        $('.btn-quick-import').on('click', function() {
            const variantId = $(this).data('id');
            setImportVariant(variantId);
            $('#importStockModal').modal('show');
        });

        // Nút Xuất Kho Nhanh (-) ở từng dòng trong bảng
        $('.btn-quick-export').on('click', function() {
            const variantId = $(this).data('id');
            setExportVariant(variantId);
            $('#exportStockModal').modal('show');
        });

        // Nút Kiểm Kê Nhanh ở từng dòng trong bảng
        $('.btn-quick-adjust').on('click', function() {
            const variantId = $(this).data('id');
            setAdjustVariant(variantId);
            $('#adjustStockModal').modal('show');
        });

        // Nút Sửa Ngưỡng Cảnh Báo
        $('.btn-edit-threshold').on('click', function() {
            const variantId = $(this).data('id');
            const threshold = $(this).data('threshold');
            
            $('#formThreshold').attr('action', `{{ url('admin/inventory') }}/${variantId}/threshold`);
            $('#input_threshold_val').val(threshold);
            $('#thresholdModal').modal('show');
        });
    });
</script>
@endpush
@endsection
