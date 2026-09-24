<!-- 1. HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-shirt text-primary"></i>Sản Phẩm Thời Trang
        </h4>
        <p class="text-muted small mb-0">Quản lý sản phẩm, biến thể, tồn kho và trạng thái bán hàng</p>
    </div>
    <div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm font-weight-medium">
            <i class="fa-solid fa-plus me-1"></i> Thêm Sản Phẩm Mới
        </a>
    </div>
</div>

<!-- 2. KPI CARDS -->
<div class="row g-3 mb-3">
    <!-- KPI 1: Tổng sản phẩm -->
    <div class="col-6 col-md-3">
        <div class="card admin-card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="h3 fw-bold text-dark mb-0 leading-none">{{ number_format($stats['total'] ?? 0) }}</div>
                    <span class="text-muted extra-small font-weight-medium text-uppercase tracking-wider mt-1 d-block">Tổng sản phẩm</span>
                </div>
                <div class="stat-card-icon bg-primary-subtle text-primary rounded-3 p-2.5" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 2: Đang hiển thị -->
    <div class="col-6 col-md-3">
        <div class="card admin-card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="h3 fw-bold text-success mb-0 leading-none">{{ number_format($stats['active'] ?? 0) }}</div>
                    <span class="text-muted extra-small font-weight-medium text-uppercase tracking-wider mt-1 d-block">Đang hiển thị</span>
                </div>
                <div class="stat-card-icon bg-success-subtle text-success rounded-3 p-2.5" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-eye"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 3: Sắp hết hàng -->
    <div class="col-6 col-md-3">
        <div class="card admin-card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="h3 fw-bold text-warning mb-0 leading-none">{{ number_format($stats['low_stock'] ?? 0) }}</div>
                    <span class="text-muted extra-small font-weight-medium text-uppercase tracking-wider mt-1 d-block">Sắp hết hàng</span>
                </div>
                <div class="stat-card-icon bg-warning-subtle text-warning rounded-3 p-2.5" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 4: Hết hàng -->
    <div class="col-6 col-md-3">
        <div class="card admin-card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="h3 fw-bold text-danger mb-0 leading-none">{{ number_format($stats['out_of_stock'] ?? 0) }}</div>
                    <span class="text-muted extra-small font-weight-medium text-uppercase tracking-wider mt-1 d-block">Hết hàng</span>
                </div>
                <div class="stat-card-icon bg-danger-subtle text-danger rounded-3 p-2.5" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
            </div>
        </div>
    </div>
</div>
