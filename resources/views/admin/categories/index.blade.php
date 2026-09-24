@extends('layouts.admin')

@section('title', 'Danh Mục Sản Phẩm')
@section('page_title', 'Danh Mục Sản Phẩm')

@section('content')
<style>
    .root-row {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .root-row:hover {
        background-color: #f1f5f9 !important;
    }
    .root-row.expanded-root {
        background-color: #eff6ff !important;
        border-left: 3px solid #2563eb !important;
    }
    .child-row {
        border-left: 3px solid #cbd5e1;
        background-color: #ffffff;
    }
    .child-row:hover {
        background-color: #f8fafc !important;
    }
    .icon-toggle {
        transition: transform 0.2s ease;
    }
</style>

<!-- 1. HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-folder-tree text-primary"></i>Danh Mục Sản Phẩm
        </h4>
        <p class="text-muted small mb-0">Quản lý danh mục sản phẩm (Click vào danh mục gốc để xem / ẩn các danh mục con)</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm font-weight-medium">
            <i class="fa-solid fa-plus me-1"></i> Thêm Danh Mục Mới
        </a>
    </div>
</div>

<!-- 2. SUMMARY KPI CARDS -->
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card admin-card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="h3 fw-bold text-dark mb-0 leading-none">{{ number_format($stats['total'] ?? 0) }}</div>
                    <span class="text-muted extra-small font-weight-medium text-uppercase tracking-wider mt-1 d-block">Tổng số danh mục</span>
                </div>
                <div class="stat-card-icon bg-primary-subtle text-primary rounded-3 p-2.5" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-folder-tree"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
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

    <div class="col-12 col-md-4">
        <div class="card admin-card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="h3 fw-bold text-info mb-0 leading-none">{{ number_format($stats['roots'] ?? 0) }}</div>
                    <span class="text-muted extra-small font-weight-medium text-uppercase tracking-wider mt-1 d-block">Danh mục gốc (Cha)</span>
                </div>
                <div class="stat-card-icon bg-info-subtle text-info rounded-3 p-2.5" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. FILTER BAR TOOLBAR -->
<div class="card admin-card border-0 shadow-sm rounded-3 mb-3">
    <div class="card-body p-2.5">
        <form id="catFilterForm" action="{{ route('admin.categories.index') }}" method="GET" class="row g-2 align-items-center">
            @if(request('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
            @if(request('sort_dir')) <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}"> @endif
            @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif

            <!-- Search Input -->
            <div class="col-12 col-md">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0 ps-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-1" placeholder="Tìm tên hoặc slug danh mục..." value="{{ request('search') }}" onkeydown="if(event.key==='Enter') this.form.submit();">
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="input-group-text bg-white text-muted border-start-0 pe-2 text-decoration-none" title="Xóa từ khóa">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Active Status Filter -->
            <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                <select name="is_active" class="form-select" onchange="this.form.submit();">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('is_active') === '1' || request('is_active') === 'active' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ request('is_active') === '0' || request('is_active') === 'inactive' ? 'selected' : '' }}>Đang tạm ẩn</option>
                </select>
            </div>

            <!-- Nút Xóa Lọc (Chỉ hiển thị khi đang lọc) -->
            @if(!empty($activeFilterCount) && $activeFilterCount > 0)
                <div class="col-12 col-sm-auto">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-danger px-2.5 d-flex align-items-center gap-1" title="Xóa tất cả bộ lọc (Đang kích hoạt {{ $activeFilterCount }} bộ lọc)">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Xóa lọc</span>
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- 4. TABLE & BULK ACTIONS -->
<div class="card admin-card border-0 shadow-sm rounded-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light extra-small">
                    <tr>
                        <th style="width: 90px;" class="text-center ps-3" title="Kéo thả để sắp xếp vị trí thứ tự">
                            Thứ Tự
                        </th>
                        <th>Tên Danh Mục</th>
                        <th>Trạng Thái</th>
                        <th class="text-end pe-3">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allCategories as $cat)
                        @php
                            $isRoot = !$cat->parent_id;
                            $cCount = $cat->children_count ?? $cat->children->count();
                            $pCount = $cat->products_count ?? 0;
                        @endphp
                        @if($isRoot)
                            <!-- DÒNG DANH MỤC GỐC (CHA) -->
                            <tr data-id="{{ $cat->id }}" 
                                data-is-root="1" 
                                data-parent-id="" 
                                data-name="{{ $cat->name }}" 
                                class="table-light-subtle root-row {{ !empty($activeFilterCount) ? 'expanded-root' : '' }}" 
                                title="Click vào dòng để xem / thu gọn danh mục con">
                                <td class="text-center drag-handle ps-3" style="cursor: grab;" title="Kéo thả để thay đổi số thứ tự">
                                    <div class="d-flex align-items-center justify-content-center gap-1.5">
                                        <i class="fa-solid fa-grip-vertical text-secondary"></i>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 cat-order-badge">Cha #{{ $cat->sort_order }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($cCount > 0)
                                            <button type="button" class="btn btn-sm btn-light border px-1.5 py-0.5 btn-toggle-tree shadow-xs" data-parent-id="{{ $cat->id }}" title="Click để xem / ẩn {{ $cCount }} danh mục con">
                                                <i class="fa-solid {{ !empty($activeFilterCount) ? 'fa-chevron-down' : 'fa-chevron-right' }} text-primary extra-small icon-toggle"></i>
                                            </button>
                                        @else
                                            <span style="display: inline-block; width: 26px;"></span>
                                        @endif
                                        <div class="rounded-2 bg-primary-subtle text-primary p-1.5 px-2">
                                            <i class="fa-solid fa-folder-tree"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="fw-bold text-dark fs-6">{{ $cat->name }}</span>
                                                <span class="badge {{ $cCount > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border' }} extra-small rounded-pill px-2 cat-child-count-badge-{{ $cat->id }}" data-count="{{ $cCount }}">
                                                    <span class="child-count-num">{{ $cCount }}</span> danh mục con
                                                </span>
                                                @if($pCount > 0)
                                                    <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 extra-small rounded-pill btn-manage-products shadow-xs" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-count="{{ $pCount }}" data-parent-id="" data-is-root="1" title="Quản lý {{ $pCount }} sản phẩm trong danh mục này">
                                                        <i class="fa-solid fa-boxes-stacked me-1"></i><span class="product-count-num-{{ $cat->id }}">{{ $pCount }}</span> SP
                                                    </button>
                                                @endif
                                            </div>
                                            <code class="extra-small text-muted bg-light px-1 py-0.5 rounded border">{{ $cat->slug }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <button type="button" class="btn btn-sm border-0 p-0 btn-toggle-cat-status" data-id="{{ $cat->id }}" data-url="{{ route('admin.categories.toggleStatus', $cat->id) }}" title="Click để đổi trạng thái Hiển thị / Ẩn">
                                        @if($cat->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                ● Hiển thị
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                ● Đang ẩn
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                <td class="text-end pe-3" onclick="event.stopPropagation();">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.categories.create', ['parent_id' => $cat->id]) }}" class="btn btn-outline-primary" title="Thêm danh mục con">
                                            <i class="fa-solid fa-plus me-1"></i>Thêm con
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <span class="cat-delete-btn-container-{{ $cat->id }}">
                                            @if($pCount > 0 || $cCount > 0)
                                                <button type="button" class="btn btn-outline-secondary btn-blocked-cat-delete"
                                                    data-id="{{ $cat->id }}"
                                                    data-name="{{ $cat->name }}"
                                                    data-products="{{ $pCount }}"
                                                    data-children="{{ $cCount }}"
                                                    data-parent-id=""
                                                    data-is-root="1"
                                                    title="Khóa xóa vì đang chứa sản phẩm hoặc danh mục con. Click để xem chi tiết">
                                                    <i class="fa-solid fa-lock text-warning"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-danger btn-delete-cat" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-parent-id="" data-is-root="1" title="Xóa danh mục">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <!-- DÒNG DANH MỤC CON -->
                            <tr data-id="{{ $cat->id }}" 
                                data-is-root="0" 
                                data-parent-id="{{ $cat->parent_id }}" 
                                data-name="{{ $cat->name }}" 
                                class="child-row child-row-parent-{{ $cat->parent_id }}" 
                                style="{{ empty($activeFilterCount) ? 'display: none;' : '' }}">
                                <td class="text-center drag-handle ps-3" style="cursor: grab;" title="Kéo thả để thay đổi số thứ tự">
                                    <div class="d-flex align-items-center justify-content-center gap-1.5">
                                        <i class="fa-solid fa-grip-vertical text-secondary"></i>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 cat-order-badge">#{{ $cat->sort_order }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 ps-4">
                                        <span class="text-secondary opacity-50 fw-bold" style="font-size: 1.1rem; line-height: 1;">↳</span>
                                        <div class="rounded-2 bg-light text-primary p-1 px-2 border">
                                            <i class="fa-solid fa-folder"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <a href="{{ route('admin.categories.edit', $cat->id) }}" class="fw-semibold text-dark text-decoration-none hover-primary mb-0.5 d-block">
                                                    {{ $cat->name }}
                                                </a>
                                                <button type="button" class="btn btn-sm {{ $pCount > 0 ? 'btn-outline-primary' : 'btn-outline-secondary opacity-75' }} py-0 px-2 extra-small rounded-pill btn-manage-products shadow-xs" 
                                                    data-id="{{ $cat->id }}" 
                                                    data-name="{{ $cat->name }}" 
                                                    data-count="{{ $pCount }}" 
                                                    data-parent-id="{{ $cat->parent_id }}" 
                                                    data-is-root="0" 
                                                    title="{{ $pCount > 0 ? 'Click để xem & xóa các sản phẩm trong danh mục này' : 'Danh mục chưa có sản phẩm' }}">
                                                    <i class="fa-solid fa-boxes-stacked me-1"></i><span class="product-count-num-{{ $cat->id }}">{{ $pCount }}</span> SP
                                                </button>
                                            </div>
                                            <code class="extra-small text-muted bg-light px-1 py-0.5 rounded border">{{ $cat->slug }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm border-0 p-0 btn-toggle-cat-status" data-id="{{ $cat->id }}" data-url="{{ route('admin.categories.toggleStatus', $cat->id) }}" title="Click để đổi trạng thái Hiển thị / Ẩn">
                                        @if($cat->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                ● Hiển thị
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                ● Đang ẩn
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <span class="cat-delete-btn-container-{{ $cat->id }}">
                                            @if($pCount > 0 || $cCount > 0)
                                                <button type="button" class="btn btn-outline-secondary btn-blocked-cat-delete"
                                                    data-id="{{ $cat->id }}"
                                                    data-name="{{ $cat->name }}"
                                                    data-products="{{ $pCount }}"
                                                    data-children="{{ $cCount }}"
                                                    data-parent-id="{{ $cat->parent_id }}"
                                                    data-is-root="0"
                                                    title="Khóa xóa vì đang chứa sản phẩm. Click để quản lý & xóa sản phẩm">
                                                    <i class="fa-solid fa-lock text-warning"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-danger btn-delete-cat" 
                                                    data-id="{{ $cat->id }}" 
                                                    data-name="{{ $cat->name }}" 
                                                    data-parent-id="{{ $cat->parent_id }}" 
                                                    data-is-root="0" 
                                                    title="Xóa danh mục">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                <h6 class="fw-bold text-dark">Không tìm thấy danh mục nào</h6>
                                <p class="small text-muted mb-3">Thử thay đổi bộ lọc hoặc tạo danh mục mới.</p>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus me-1"></i> Thêm Danh Mục Mới
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- 5. PAGINATION & ITEMS PER PAGE -->
    @if($allCategories->total() > 0)
        <div class="card-footer bg-white border-top py-3 px-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $allCategories->firstItem() }}–{{ $allCategories->lastItem() }}</strong> trong tổng số <strong>{{ $allCategories->total() }}</strong> danh mục
                </div>
                <div class="d-flex align-items-center gap-1.5 small text-muted">
                    <span>Xem:</span>
                    <select class="form-select form-select-sm" style="width: 75px;" onchange="window.location.href=this.value;">
                        @foreach([10, 15, 25, 50, 100] as $catOpt)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $catOpt]) }}" {{ request('per_page', 15) == $catOpt ? 'selected' : '' }}>
                                {{ $catOpt }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                {{ $allCategories->links() }}
            </div>
        </div>
    @endif
</div>

<!-- MODAL: QUẢN LÝ & XÓA SẢN PHẨM TRONG DANH MỤC -->
<div class="modal fade" id="categoryProductsModal" tabindex="-1" aria-labelledby="categoryProductsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-light border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-boxes-stacked fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="categoryProductsModalLabel">
                            Sản phẩm thuộc danh mục: <span id="modalCategoryName" class="text-primary"></span>
                        </h6>
                        <span class="extra-small text-muted">
                            Đang có <strong id="modalProductCountBadge" class="text-dark">0</strong> sản phẩm liên kết
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            
            <div class="modal-body p-3">
                <!-- Action bar trong modal: Tích chọn để xóa nhiều theo ý mình -->
                <div id="modalActionBar" class="d-flex align-items-center justify-content-between p-2.5 px-3 mb-3 bg-light rounded-2 border">
                    <div class="small text-muted d-flex align-items-center gap-1.5">
                        <i class="fa-solid fa-square-check text-primary"></i>
                        <span>Tích chọn sản phẩm muốn xóa khỏi danh mục này:</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger d-flex align-items-center gap-1.5 shadow-xs" id="btnDeleteSelectedProducts" disabled>
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Xóa sản phẩm đã chọn (<strong id="modalSelectedCount">0</strong>)</span>
                    </button>
                </div>

                <!-- Alert danh mục đã sạch sản phẩm -->
                <div id="modalEmptyAlert" class="alert alert-success d-none align-items-center justify-content-between p-3 mb-3 border-0 shadow-xs">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check fs-4 text-success"></i>
                        <div>
                            <div class="fw-bold text-success">Danh mục này hiện không còn sản phẩm nào!</div>
                            <div class="small text-success-emphasis">Bạn đã có thể xóa danh mục con này khỏi hệ thống.</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm px-3 shadow-xs" id="btnDeleteCatFromModal">
                        <i class="fa-solid fa-trash-can me-1"></i> Xóa danh mục này ngay
                    </button>
                </div>

                <!-- Bảng danh sách sản phẩm -->
                <div class="table-responsive border rounded-2" id="modalTableWrapper" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="modalProductsTable">
                        <thead class="table-light sticky-top small">
                            <tr>
                                <th style="width: 44px;" class="text-center">
                                    <input type="checkbox" class="form-check-input" id="modalSelectAllProducts" title="Chọn tất cả">
                                </th>
                                <th style="width: 55px;">Ảnh</th>
                                <th>Tên Sản Phẩm / SKU</th>
                                <th style="width: 130px;">Giá Bán</th>
                                <th style="width: 100px;">Trạng Thái</th>
                                <th style="width: 70px;" class="text-end pe-3">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="modalProductsTableBody">
                            <!-- Render bằng JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Loading State -->
                <div id="modalProductLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div class="text-muted small">Đang tải danh sách sản phẩm...</div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CHI TIẾT DANH MỤC CHA (KHI ĐANG CHỨA CÁC CON) -->
<div class="modal fade" id="categoryParentDetailModal" tabindex="-1" aria-labelledby="categoryParentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-light border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-folder-tree fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="categoryParentDetailModalLabel">
                            Danh mục Cha: <span id="parentModalCatName" class="text-primary"></span>
                        </h6>
                        <span class="extra-small text-muted">Chưa thể xóa vì đang chứa danh mục con</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-warning border-0 small mb-3">
                    <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Quy trình xóa danh mục cha:</div>
                    <ol class="mb-0 ps-3">
                        <li>Vào từng danh mục con bên dưới để xóa hết sản phẩm.</li>
                        <li>Sau khi hết sản phẩm, xóa các danh mục con đó.</li>
                        <li>Khi tất cả danh mục con đã được xóa hết, danh mục cha sẽ <strong>tự động mở khóa</strong> nút xóa cho bạn!</li>
                    </ol>
                </div>
                <div class="fw-semibold text-dark small mb-2">Các danh mục con bên trong:</div>
                <div id="parentModalChildList" class="list-group list-group-flush border rounded-2" style="max-height: 260px; overflow-y: auto;">
                    <!-- Render bằng JS -->
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Đã hiểu</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentModalCatId = null;
        let currentModalCatName = '';
        let currentModalParentId = null;
        let currentModalIsRoot = 0;

        const catModalElement = document.getElementById('categoryProductsModal');
        const parentModalElement = document.getElementById('categoryParentDetailModal');
        const catProductsModal = catModalElement ? new bootstrap.Modal(catModalElement) : null;
        const catParentModal = parentModalElement ? new bootstrap.Modal(parentModalElement) : null;

        // Hàm toggle mở / thu gọn danh mục con theo parentId
        function toggleCategoryTree(parentId) {
            const childRows = $(`.child-row-parent-${parentId}`);
            const rootRow = $(`tr.root-row[data-id="${parentId}"]`);
            const icon = rootRow.find('.icon-toggle');

            if (childRows.length === 0) return;

            if (childRows.first().is(':visible')) {
                childRows.hide();
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                rootRow.removeClass('expanded-root');
            } else {
                childRows.show();
                icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                rootRow.addClass('expanded-root');
            }
        }

        // Click vào dòng gốc (tr.root-row) để mở/thu gọn các danh mục con
        $(document).on('click', '.root-row', function(e) {
            if ($(e.target).closest('button, a, input, .drag-handle').length > 0) {
                return;
            }
            const parentId = $(this).data('id');
            toggleCategoryTree(parentId);
        });

        // Click trực tiếp vào nút mũi tên (.btn-toggle-tree)
        $(document).on('click', '.btn-toggle-tree', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const parentId = $(this).data('parent-id');
            toggleCategoryTree(parentId);
        });



        // Toggle trạng thái Ẩn/Hiện danh mục bằng AJAX
        $(document).on('click', '.btn-toggle-cat-status', function(e) {
            e.preventDefault();
            const btn = $(this);
            const url = btn.data('url');

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.is_active) {
                            btn.html('<span class="badge bg-success-subtle text-success border border-success-subtle">● Hiển thị</span>');
                        } else {
                            btn.html('<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">● Đang ẩn</span>');
                        }

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                    } else {
                        Swal.fire('Lỗi!', response.message || 'Không thể đổi trạng thái danh mục.', 'error');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Đã xảy ra lỗi khi đổi trạng thái danh mục!';
                    Swal.fire('Lỗi!', msg, 'error');
                }
            });
        });

        // ==========================================
        // 1. MỞ MODAL XEM & QUẢN LÝ SẢN PHẨM TRONG DANH MỤC
        // ==========================================
        function loadCategoryProducts(catId, catName, parentId, isRoot) {
            currentModalCatId = catId;
            currentModalCatName = catName;
            currentModalParentId = parentId || null;
            currentModalIsRoot = isRoot || 0;

            $('#modalCategoryName').text(catName);
            $('#modalProductCountBadge').text('...');
            $('#modalProductsTableBody').empty();
            $('#modalProductLoading').removeClass('d-none');
            $('#modalTableWrapper').addClass('d-none');
            $('#modalActionBar').addClass('d-none');
            $('#modalEmptyAlert').addClass('d-none');

            if (catProductsModal) catProductsModal.show();

            $.ajax({
                url: `{{ url('admin/categories') }}/${catId}/products`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#modalProductLoading').addClass('d-none');
                    if (response.success) {
                        renderProductModalList(response.products, response.count);
                    } else {
                        Swal.fire('Lỗi!', response.message || 'Không thể tải sản phẩm.', 'error');
                    }
                },
                error: function() {
                    $('#modalProductLoading').addClass('d-none');
                    Swal.fire('Lỗi!', 'Không thể tải danh sách sản phẩm từ máy chủ.', 'error');
                }
            });
        }

        function updateModalSelectedState() {
            const totalBoxes = $('.modal-product-checkbox').length;
            const checkedBoxes = $('.modal-product-checkbox:checked');
            const count = checkedBoxes.length;

            $('#modalSelectedCount').text(count);
            $('#btnDeleteSelectedProducts').prop('disabled', count === 0);

            const selectAll = $('#modalSelectAllProducts');
            if (totalBoxes > 0 && count === totalBoxes) {
                selectAll.prop('checked', true).prop('indeterminate', false);
            } else if (count > 0 && count < totalBoxes) {
                selectAll.prop('checked', false).prop('indeterminate', true);
            } else {
                selectAll.prop('checked', false).prop('indeterminate', false);
            }
        }

        // Sự kiện checkbox Chọn tất cả trong modal
        $(document).on('change', '#modalSelectAllProducts', function() {
            const isChecked = $(this).is(':checked');
            $('.modal-product-checkbox').prop('checked', isChecked);
            updateModalSelectedState();
        });

        // Sự kiện checkbox từng sản phẩm trong modal
        $(document).on('change', '.modal-product-checkbox', function() {
            updateModalSelectedState();
        });

        function renderProductModalList(products, count) {
            $('#modalProductCountBadge').text(count);
            const tbody = $('#modalProductsTableBody');
            tbody.empty();

            if (count === 0) {
                $('#modalTableWrapper').addClass('d-none');
                $('#modalActionBar').addClass('d-none');
                $('#modalEmptyAlert').removeClass('d-none').addClass('d-flex');
                updateCategoryRowUnlocked(currentModalCatId, currentModalCatName, currentModalParentId, currentModalIsRoot);
                updateModalSelectedState();
                return;
            }

            $('#modalEmptyAlert').addClass('d-none').removeClass('d-flex');
            $('#modalTableWrapper').removeClass('d-none');
            $('#modalActionBar').removeClass('d-none').addClass('d-flex');

            products.forEach(function(prod) {
                const tr = $(`
                    <tr id="modal-product-row-${prod.id}">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input modal-product-checkbox" value="${prod.id}">
                        </td>
                        <td>
                            <img src="${prod.image_url}" alt="${prod.name}" class="rounded border object-fit-cover" style="width: 44px; height: 44px;">
                        </td>
                        <td>
                            <div class="fw-semibold text-dark mb-0.5">${prod.name}</div>
                            <code class="extra-small text-muted bg-light px-1 py-0.5 rounded border">${prod.sku || 'Chưa có SKU'}</code>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">${prod.sale_price ? prod.sale_price : prod.price}</span>
                            ${prod.sale_price ? `<div class="extra-small text-muted text-decoration-line-through">${prod.price}</div>` : ''}
                        </td>
                        <td>
                            ${prod.is_active ? '<span class="badge bg-success-subtle text-success border">Hiển thị</span>' : '<span class="badge bg-secondary-subtle text-secondary border">Tạm ẩn</span>'}
                        </td>
                        <td class="text-end pe-3">
                            <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-delete-single-product" 
                                data-product-id="${prod.id}" 
                                data-product-name="${prod.name}" 
                                title="Xóa sản phẩm này">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `);
                tbody.append(tr);
            });

            updateModalSelectedState();
        }

        // Cập nhật DOM khi danh mục đã sạch sản phẩm -> Mở khóa nút xóa đỏ
        function updateCategoryRowUnlocked(catId, catName, parentId, isRoot) {
            // Cập nhật số sản phẩm trên dòng danh mục
            $(`.product-count-num-${catId}`).text('0');
            const prodBtn = $(`tr[data-id="${catId}"] .btn-manage-products`);
            prodBtn.removeClass('btn-outline-primary btn-outline-info').addClass('btn-outline-secondary opacity-75');

            // Cập nhật nút xóa sang Thùng rác đỏ
            const deleteContainer = $(`.cat-delete-btn-container-${catId}`);
            if (deleteContainer.length > 0) {
                deleteContainer.html(`
                    <button type="button" class="btn btn-outline-danger btn-delete-cat" 
                        data-id="${catId}" 
                        data-name="${catName}" 
                        data-parent-id="${parentId || ''}" 
                        data-is-root="${isRoot}" 
                        title="Xóa danh mục này">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                `);
            }
        }

        // Click nút xem & quản lý sản phẩm
        $(document).on('click', '.btn-manage-products', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const catId = $(this).data('id');
            const catName = $(this).data('name');
            const parentId = $(this).data('parent-id');
            const isRoot = $(this).data('is-root');
            loadCategoryProducts(catId, catName, parentId, isRoot);
        });

        // Click nút ổ khóa (khi bị chặn xóa)
        $(document).on('click', '.btn-blocked-cat-delete', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const catId = $(this).data('id');
            const catName = $(this).data('name');
            const pCount = parseInt($(this).data('products') || 0);
            const cCount = parseInt($(this).data('children') || 0);
            const parentId = $(this).data('parent-id');
            const isRoot = parseInt($(this).data('is-root') || 0);

            // Trường hợp 1: Danh mục đang có sản phẩm (ở con hoặc cha) -> Mở modal xóa sản phẩm
            if (pCount > 0) {
                loadCategoryProducts(catId, catName, parentId, isRoot);
                return;
            }

            // Trường hợp 2: Danh mục Cha đang chứa danh mục con -> Mở modal danh mục cha
            if (cCount > 0) {
                $('#parentModalCatName').text(catName);
                const list = $('#parentModalChildList');
                list.empty();

                const childRows = $(`.child-row-parent-${catId}`);
                if (childRows.length > 0) {
                    childRows.each(function() {
                        const childId = $(this).data('id');
                        const childName = $(this).data('name');
                        const childProdCount = parseInt($(this).find(`.product-count-num-${childId}`).text() || 0);

                        const item = $(`
                            <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                                <div>
                                    <div class="fw-semibold text-dark">↳ ${childName}</div>
                                    <span class="extra-small text-muted">
                                        <i class="fa-solid fa-boxes-stacked me-1"></i>${childProdCount} sản phẩm
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    ${childProdCount > 0 
                                        ? `<button type="button" class="btn btn-sm btn-outline-primary py-0.5 px-2 extra-small btn-manage-products" data-id="${childId}" data-name="${childName}" data-count="${childProdCount}" data-parent-id="${catId}" data-is-root="0"><i class="fa-solid fa-trash-can me-1"></i>Xóa sản phẩm</button>`
                                        : `<button type="button" class="btn btn-sm btn-outline-danger py-0.5 px-2 extra-small btn-delete-cat" data-id="${childId}" data-name="${childName}" data-parent-id="${catId}" data-is-root="0"><i class="fa-solid fa-trash-can me-1"></i>Xóa con này</button>`
                                    }
                                </div>
                            </div>
                        `);
                        list.append(item);
                    });
                } else {
                    list.html('<div class="p-3 text-center text-muted small">Không tìm thấy danh mục con nào.</div>');
                }

                // Tự động mở rộng cây con ngoài bảng để người dùng thấy
                toggleCategoryTree(catId);
                if (catParentModal) catParentModal.show();
            }
        });

        // ==========================================
        // 2. XÓA 1 SẢN PHẨM TRONG MODAL (AJAX)
        // ==========================================
        $(document).on('click', '.btn-delete-single-product', function(e) {
            e.preventDefault();
            const btn = $(this);
            const prodId = btn.data('product-id');
            const prodName = btn.data('product-name');

            Swal.fire({
                title: 'Xác nhận xóa sản phẩm?',
                html: `Bạn có chắc chắn muốn xóa sản phẩm <strong>"${prodName}"</strong> khỏi danh mục?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                    $.ajax({
                        url: `{{ url('admin/categories') }}/${currentModalCatId}/products/${prodId}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $(`#modal-product-row-${prodId}`).fadeOut(250, function() {
                                    $(this).remove();
                                    $('#modalProductCountBadge').text(response.remaining_products);
                                    $(`.product-count-num-${currentModalCatId}`).text(response.remaining_products);

                                    updateModalSelectedState();

                                    if (response.remaining_products === 0) {
                                        $('#modalTableWrapper').addClass('d-none');
                                        $('#modalActionBar').addClass('d-none');
                                        $('#modalEmptyAlert').removeClass('d-none').addClass('d-flex');
                                        updateCategoryRowUnlocked(currentModalCatId, currentModalCatName, currentModalParentId, currentModalIsRoot);
                                    }
                                });

                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                Toast.fire({ icon: 'success', title: response.message });
                            } else {
                                Swal.fire('Lỗi!', response.message || 'Không thể xóa sản phẩm.', 'error');
                                btn.prop('disabled', false).html('<i class="fa-solid fa-trash-can"></i>');
                            }
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Lỗi máy chủ khi xóa sản phẩm!';
                            Swal.fire('Lỗi!', msg, 'error');
                            btn.prop('disabled', false).html('<i class="fa-solid fa-trash-can"></i>');
                        }
                    });
                }
            });
        });

        // ==========================================
        // 3. TÍCH CHỌN ĐỂ XÓA NHIỀU SẢN PHẨM (AJAX)
        // ==========================================
        $('#btnDeleteSelectedProducts').on('click', function(e) {
            e.preventDefault();
            const checkedBoxes = $('.modal-product-checkbox:checked');
            const count = checkedBoxes.length;

            if (count === 0) {
                Swal.fire('Thông báo', 'Vui lòng tích chọn ít nhất 1 sản phẩm để xóa!', 'info');
                return;
            }

            const selectedIds = [];
            checkedBoxes.each(function() {
                selectedIds.push($(this).val());
            });

            const btn = $(this);

            Swal.fire({
                title: `Xóa ${count} sản phẩm đã chọn?`,
                html: `Bạn có chắc muốn xóa <strong>${count} sản phẩm</strong> đã chọn khỏi danh mục "<strong>${currentModalCatName}</strong>"?<br><span class="text-danger small">Hành động này sẽ gỡ các sản phẩm được chọn khỏi hệ thống.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Đồng ý xóa (${count} SP)`,
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Đang xóa...');

                    $.ajax({
                        url: `{{ url('admin/categories') }}/${currentModalCatId}/products/delete-selected`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_ids: selectedIds
                        },
                        dataType: 'json',
                        success: function(response) {
                            btn.prop('disabled', false).html('<i class="fa-solid fa-trash-can"></i> <span>Xóa sản phẩm đã chọn (<strong id="modalSelectedCount">0</strong>)</span>');
                            if (response.success) {
                                selectedIds.forEach(id => {
                                    $(`#modal-product-row-${id}`).remove();
                                });

                                $('#modalProductCountBadge').text(response.remaining_products);
                                $(`.product-count-num-${currentModalCatId}`).text(response.remaining_products);

                                updateModalSelectedState();

                                if (response.remaining_products === 0) {
                                    $('#modalTableWrapper').addClass('d-none');
                                    $('#modalActionBar').addClass('d-none');
                                    $('#modalEmptyAlert').removeClass('d-none').addClass('d-flex');

                                    updateCategoryRowUnlocked(currentModalCatId, currentModalCatName, currentModalParentId, currentModalIsRoot);
                                }

                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                Toast.fire({ icon: 'success', title: response.message });
                            } else {
                                Swal.fire('Lỗi!', response.message || 'Không thể xóa các sản phẩm đã chọn.', 'error');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html('<i class="fa-solid fa-trash-can"></i> <span>Xóa sản phẩm đã chọn (<strong id="modalSelectedCount">0</strong>)</span>');
                            updateModalSelectedState();
                            const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Lỗi khi xóa các sản phẩm!';
                            Swal.fire('Lỗi!', msg, 'error');
                        }
                    });
                }
            });
        });

        // Xóa danh mục ngay từ trong Modal khi đã hết sản phẩm
        $('#btnDeleteCatFromModal').on('click', function(e) {
            e.preventDefault();
            if (catProductsModal) catProductsModal.hide();
            executeDeleteCategory(currentModalCatId, currentModalCatName, currentModalParentId, currentModalIsRoot);
        });

        // ==========================================
        // 4. XÓA DANH MỤC (AJAX) & MỞ KHÓA DANH MỤC CHA REALTIME
        // ==========================================
        function executeDeleteCategory(catId, catName, parentId, isRoot) {
            Swal.fire({
                title: 'Xác nhận xóa danh mục?',
                html: `Bạn có chắc chắn muốn xóa danh mục "<strong>${catName}</strong>" khỏi hệ thống?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/categories') }}/${catId}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // 1. Ẩn và gỡ hàng khỏi bảng
                                $(`tr[data-id="${catId}"]`).fadeOut(300, function() {
                                    $(this).remove();
                                });

                                // 2. Nếu là danh mục con, cập nhật danh mục cha
                                if (response.parent_id) {
                                    const pId = response.parent_id;
                                    const remainingChildren = response.parent_remaining_children;

                                    // Cập nhật badge số con của cha
                                    const parentBadge = $(`.cat-child-count-badge-${pId}`);
                                    if (parentBadge.length > 0) {
                                        parentBadge.find('.child-count-num').text(remainingChildren);
                                        if (remainingChildren === 0) {
                                            parentBadge.removeClass('bg-primary-subtle text-primary border-primary-subtle').addClass('bg-secondary-subtle text-secondary border');
                                        }
                                    }

                                    // NẾU DANH MỤC CHA ĐÃ SẠCH HẾT CON VÀ HẾT SẢN PHẨM -> MỞ KHÓA NÚT XÓA ĐỎ CHO CHA!
                                    if (response.parent_can_delete) {
                                        const parentRow = $(`tr.root-row[data-id="${pId}"]`);
                                        const parentName = parentRow.data('name');
                                        $(`.cat-delete-btn-container-${pId}`).html(`
                                            <button type="button" class="btn btn-outline-danger btn-delete-cat" 
                                                data-id="${pId}" 
                                                data-name="${parentName}" 
                                                data-parent-id="" 
                                                data-is-root="1" 
                                                title="Xóa danh mục cha">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        `);

                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Đã mở khóa Danh Mục Cha!',
                                            html: `Tất cả danh mục con của "<strong>${parentName}</strong>" đã được xóa hết.<br>Bây giờ bạn có thể <strong>xóa danh mục cha</strong> này!`,
                                            confirmButtonColor: '#2563eb',
                                            confirmButtonText: 'Tuyệt vời'
                                        });
                                        return;
                                    }
                                }

                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                Toast.fire({ icon: 'success', title: response.message });
                            } else {
                                Swal.fire('Lỗi!', response.message || 'Không thể xóa danh mục.', 'error');
                            }
                        },
                        error: function(xhr) {
                            const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Không thể xóa danh mục vì vẫn còn dữ liệu liên kết!';
                            Swal.fire('Không thể xóa!', msg, 'warning');
                        }
                    });
                }
            });
        }

        // Bấm nút xóa danh mục (Thùng rác đỏ)
        $(document).on('click', '.btn-delete-cat', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const catId = $(this).data('id');
            const catName = $(this).data('name');
            const parentId = $(this).data('parent-id');
            const isRoot = $(this).data('is-root');
            executeDeleteCategory(catId, catName, parentId, isRoot);
        });

        // ==========================================
        // 5. KÉO THẢ SẮP XẾP THỨ TỰ DANH MỤC (SortableJS)
        // ==========================================
        const catTbody = document.querySelector('table tbody');
        if (catTbody) {
            new Sortable(catTbody, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'table-active',
                onEnd: function () {
                    const items = [];
                    let currentParentId = null;
                    let currentParentName = '';
                    const parentCounters = {};

                    catTbody.querySelectorAll('tr[data-id]').forEach((row) => {
                        const isRoot = row.getAttribute('data-is-root') === '1';
                        const catId = row.getAttribute('data-id');

                        if (isRoot) {
                            currentParentId = catId;
                            currentParentName = row.getAttribute('data-name');
                            parentCounters['root'] = (parentCounters['root'] || 0) + 1;
                            const order = parentCounters['root'];

                            row.setAttribute('data-parent-id', '');
                            const badge = row.querySelector('.cat-order-badge');
                            if (badge) {
                                badge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 cat-order-badge';
                                badge.textContent = 'Cha #' + order;
                            }

                            items.push({
                                id: catId,
                                parent_id: null,
                                sort_order: order
                            });
                        } else {
                            let parentId = currentParentId;
                            if (!parentId) {
                                parentId = row.getAttribute('data-parent-id') || null;
                            } else {
                                row.setAttribute('data-parent-id', parentId);
                            }

                            const pKey = parentId ? 'p_' + parentId : 'root';
                            parentCounters[pKey] = (parentCounters[pKey] || 0) + 1;
                            const order = parentCounters[pKey];

                            const badge = row.querySelector('.cat-order-badge');
                            if (badge) {
                                badge.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 cat-order-badge';
                                badge.textContent = '#' + order;
                            }

                            items.push({
                                id: catId,
                                parent_id: parentId,
                                sort_order: order
                            });
                        }
                    });

                    fetch("{{ route('admin.categories.reorder') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ items: items })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Đã lưu thứ tự và nhóm danh mục mới!'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error reordering categories:', error);
                    });
                }
            });
        }
    });
</script>
@endpush
