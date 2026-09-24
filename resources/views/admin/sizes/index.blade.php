@extends('layouts.admin')

@section('title', 'Quản Lý Kích Cỡ (Sizes)')
@section('page_title', 'Kích Cỡ Sản Phẩm')

@section('content')
<!-- 1. HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-ruler-combined text-primary"></i>Kích Cỡ Sản Phẩm
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-7 font-weight-semibold px-2.5 py-1 ms-1">
                {{ number_format($stats['total'] ?? $sizes->total()) }} kích cỡ
            </span>
        </h4>
        <p class="text-muted small mb-0">Quản lý kích cỡ sản phẩm hệ thống</p>
    </div>
    <div>
        <a href="{{ route('admin.sizes.create') }}" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm font-weight-medium">
            <i class="fa-solid fa-plus me-1"></i> Thêm Kích Cỡ Mới
        </a>
    </div>
</div>

<!-- 2. TABLE & FILTER TOOLBAR CARD -->
<div class="card admin-card border-0 shadow-sm rounded-3">
    <!-- FILTER BAR TOOLBAR (CARD HEADER) -->
    <div class="card-header bg-white py-3 border-bottom">
        <form id="sizeFilterForm" action="{{ route('admin.sizes.index') }}" method="GET" class="row g-2 align-items-center">
            @if(request('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
            @if(request('sort_dir')) <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}"> @endif
            @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif

            <!-- Search Input -->
            <div class="col-12 col-md">
                <div class="input-group">
                    <button type="submit" class="input-group-text bg-white text-muted border-end-0 ps-2.5 pe-2 btn" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <input type="text" name="search" class="form-control border-start-0 ps-1" placeholder="Tìm tên kích cỡ, mã code..." value="{{ request('search') }}" onkeydown="if(event.key==='Enter') this.form.submit();">
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="input-group-text bg-white text-muted border-start-0 pe-2" title="Xóa từ khóa">
                            <i class="fa-solid fa-xmark small"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Filter Loại Kích Cỡ -->
            <div class="col-12 col-sm-6 col-md-3">
                <select name="size_type" class="form-select" onchange="this.form.submit();">
                    <option value="">Tất cả loại kích cỡ</option>
                    @foreach(\App\Models\Size::TYPES as $key => $label)
                        <option value="{{ $key }}" {{ request('size_type') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Danh Mục Áp Dụng -->
            <div class="col-12 col-sm-6 col-md-3">
                <select name="category_id" class="form-select" onchange="this.form.submit();">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Trạng Thái -->
            <div class="col-12 col-sm-6 col-md-2">
                <select name="is_active" class="form-select" onchange="this.form.submit();">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('is_active') === '1' || request('is_active') === 'active' ? 'selected' : '' }}>Đang hiển thị</option>
                    <option value="0" {{ request('is_active') === '0' || request('is_active') === 'inactive' ? 'selected' : '' }}>Tạm ẩn</option>
                </select>
            </div>

            <!-- Nút Xóa Lọc -->
            @if(!empty($activeFilterCount) && $activeFilterCount > 0)
                <div class="col-12 col-sm-auto">
                    <a href="{{ route('admin.sizes.index') }}" class="btn btn-outline-danger px-2.5 d-flex align-items-center gap-1" title="Xóa tất cả bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Xóa lọc</span>
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- BULK ACTIONS BAR -->
    <form id="sizeBulkForm" action="{{ route('admin.sizes.bulkAction') }}" method="POST">
        @csrf
        <input type="hidden" name="action" id="size_bulk_action_input">

        <div id="sizeBulkBar" class="bg-primary-subtle border-bottom p-2.5 px-3 d-none align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-primary small">
                    <i class="fa-solid fa-check-double me-1"></i>Đã chọn <span id="sizeSelectedCount">0</span> kích cỡ
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-success bg-white" onclick="submitSizeBulk('enable')">
                    <i class="fa-solid fa-eye me-1"></i> Bật hiển thị
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary bg-white" onclick="submitSizeBulk('disable')">
                    <i class="fa-solid fa-eye-slash me-1"></i> Tạm ẩn
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="submitSizeBulk('delete')">
                    <i class="fa-solid fa-trash-can me-1"></i> Xóa kích cỡ đã chọn
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light extra-small">
                    <tr>
                        <th style="width: 36px;" class="ps-3">
                            <input type="checkbox" class="form-check-input" id="selectAllSizes">
                        </th>
                        <th style="width: 60px;">
                            @php $nextIdDir = ($sortBy === 'id' && $sortDir === 'asc') ? 'desc' : 'asc'; @endphp
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_dir' => $nextIdDir]) }}" class="text-dark text-decoration-none">
                                ID @if($sortBy === 'id') <i class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-primary"></i> @endif
                            </a>
                        </th>
                        <th>
                            @php $nextNameDir = ($sortBy === 'name' && $sortDir === 'asc') ? 'desc' : 'asc'; @endphp
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_dir' => $nextNameDir]) }}" class="text-dark text-decoration-none">
                                Tên kích cỡ @if($sortBy === 'name') <i class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-primary"></i> @endif
                            </a>
                        </th>
                        <th>Loại kích cỡ</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sizes as $size)
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" name="size_ids[]" value="{{ $size->id }}" class="form-check-input size-select">
                            </td>
                            <td class="fw-bold text-secondary">#{{ $size->id }}</td>
                            <td>
                                <span class="badge bg-dark text-white fs-6 px-3 py-1.5 rounded-3 font-weight-bold">
                                    {{ $size->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1">
                                    {{ $size->type_label }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.sizes.edit', $size->id) }}" class="btn btn-outline-primary btn-sm px-2.5 py-1" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm px-2.5 py-1 btn-delete-size" data-id="{{ $size->id }}" data-name="{{ $size->name }}" title="Xóa kích cỡ">
                                        <i class="fa-solid fa-trash-can me-1"></i>Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fa-solid fa-ruler-combined fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                <h6 class="fw-bold text-dark">Không tìm thấy kích cỡ nào</h6>
                                <p class="small text-muted mb-3">Thử thay đổi bộ lọc hoặc tạo kích cỡ mới.</p>
                                <a href="{{ route('admin.sizes.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus me-1"></i> Thêm Kích Cỡ Mới
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- 5. PAGINATION -->
    @if($sizes->total() > 0)
        <div class="card-footer bg-white border-top py-3 px-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $sizes->firstItem() }}–{{ $sizes->lastItem() }}</strong> trong tổng số <strong>{{ $sizes->total() }}</strong> kích cỡ
                </div>
                <div class="d-flex align-items-center gap-1.5 small text-muted">
                    <span>Xem:</span>
                    <select class="form-select form-select-sm" style="width: 75px;" onchange="window.location.href=this.value;">
                        @foreach([10, 15, 25, 50, 100] as $sizeOpt)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $sizeOpt]) }}" {{ $perPage == $sizeOpt ? 'selected' : '' }}>
                                {{ $sizeOpt }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                {{ $sizes->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Form Xóa Đơn Lẻ -->
@foreach($sizes as $size)
    <form id="delete-form-{{ $size->id }}" action="{{ route('admin.sizes.destroy', $size->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById('selectAllSizes');
        const checkboxes = document.querySelectorAll('.size-select');
        const bulkBar = document.getElementById('sizeBulkBar');
        const selectedCount = document.getElementById('sizeSelectedCount');

        function updateSizeBulkBar() {
            const checkedBoxes = document.querySelectorAll('.size-select:checked');
            const count = checkedBoxes.length;

            if (selectedCount) selectedCount.textContent = count;

            if (bulkBar) {
                if (count > 0) {
                    bulkBar.classList.remove('d-none');
                    bulkBar.classList.add('d-flex');
                } else {
                    bulkBar.classList.remove('d-flex');
                    bulkBar.classList.add('d-none');
                }
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateSizeBulkBar();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked && selectAll) selectAll.checked = false;
                updateSizeBulkBar();
            });
        });

        // Modal danh sách biến thể sử dụng Size
        $('.btn-show-size-variants').on('click', function() {
            const sizeId = $(this).data('size-id');
            const sizeName = $(this).data('size-name');

            Swal.fire({
                title: `Đang tải biến thể của Size "${sizeName}"...`,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: `{{ url('admin/sizes') }}/${sizeId}/variants`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let htmlContent = `
                            <div class="text-start mb-2 small text-muted">Các biến thể sản phẩm đang sử dụng Size <strong>"${sizeName}"</strong>:</div>
                            <div class="table-responsive" style="max-height: 280px;">
                                <table class="table table-sm align-middle text-start extra-small border">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản Phẩm</th>
                                            <th>Màu Sắc</th>
                                            <th>SKU</th>
                                            <th>Tồn Kho</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        response.data.forEach(function(v) {
                            const pName = v.product ? v.product.name : 'N/A';
                            const cName = v.color ? v.color.name : 'N/A';
                            const cHex = v.color ? (v.color.hex_code || '#000') : '#000';
                            htmlContent += `
                                <tr>
                                    <td class="fw-bold text-dark">${pName}</td>
                                    <td><span class="badge border text-dark" style="background-color: ${cHex};">🎨 ${cName}</span></td>
                                    <td><code>${v.sku || 'N/A'}</code></td>
                                    <td><span class="badge bg-light text-dark border">${v.stock_quantity} SP</span></td>
                                </tr>
                            `;
                        });
                        htmlContent += `</tbody></table></div>`;

                        Swal.fire({
                            title: `Biến thể đang dùng Size "${sizeName}" (${response.data.length})`,
                            html: htmlContent,
                            width: '650px',
                            confirmButtonColor: '#2563eb',
                            confirmButtonText: 'Đóng'
                        });
                    }
                }
            });
        });

        // Xem danh mục áp dụng
        $('.btn-show-categories').on('click', function() {
            const sizeName = $(this).data('name');
            const categories = $(this).data('categories');
            let htmlList = '<div class="d-flex flex-wrap gap-2 justify-content-center mt-2">';
            categories.forEach(function(c) {
                htmlList += `<span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 fs-6">📂 ${c}</span>`;
            });
            htmlList += '</div>';

            Swal.fire({
                title: `Danh mục áp dụng Size "${sizeName}"`,
                html: htmlList,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Đóng'
            });
        });

        // Chặn xóa khi size có biến thể
        $('.btn-blocked-delete').on('click', function() {
            const sizeName = $(this).data('name');
            const count = $(this).data('count');

            Swal.fire({
                title: 'Không thể xóa kích cỡ!',
                html: `Không thể xóa size <strong>"${sizeName}"</strong> vì đang được sử dụng bởi <strong>${count} biến thể sản phẩm</strong>.`,
                icon: 'warning',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Đã hiểu'
            });
        });

        // Xóa kích cỡ chưa sử dụng
        $('.btn-delete-size').on('click', function() {
            const sizeId = $(this).data('id');
            const sizeName = $(this).data('name');

            Swal.fire({
                title: 'Xác nhận xóa kích cỡ?',
                html: `Bạn có chắc chắn muốn xóa kích cỡ <strong>"${sizeName}"</strong>?<br><span class="text-muted small">Toàn bộ sản phẩm và biến thể đang dùng kích cỡ này sẽ được giữ nguyên an toàn (chuyển sang không gán size/freesize).</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#delete-form-${sizeId}`).submit();
                }
            });
        });
    });

    function submitSizeBulk(action) {
        const checkedBoxes = document.querySelectorAll('.size-select:checked');
        if (checkedBoxes.length === 0) {
            Swal.fire('Thông báo', 'Vui lòng chọn ít nhất 1 kích cỡ!', 'warning');
            return;
        }

        Swal.fire({
            title: 'Xác nhận thao tác?',
            text: action === 'delete' ? 'Hệ thống sẽ xóa các kích cỡ đã chọn. Các sản phẩm và biến thể liên quan vẫn được bảo toàn nguyên vẹn.' : 'Bạn có chắc chắn thực hiện thao tác hàng loạt?',
            icon: action === 'delete' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#dc3545' : '#2563eb',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('size_bulk_action_input').value = action;
                document.getElementById('sizeBulkForm').submit();
            }
        });
    }

    function toggleSizeStatus(element, e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const btn = $(element);
        const url = btn.data('url');

        btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT'
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    if (response.is_active) {
                        btn.html(`
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="fa-solid fa-circle-check me-1"></i>Hiển thị
                            </span>
                        `);
                    } else {
                        btn.html(`
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                <i class="fa-solid fa-eye-slash me-1"></i>Tạm ẩn
                            </span>
                        `);
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
                    Swal.fire('Lỗi!', response.message || 'Không thể đổi trạng thái.', 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Không thể kết nối đến máy chủ.';
                Swal.fire('Lỗi!', msg, 'error');
            }
        });
    }

    $(document).on('click', '.btn-toggle-size-status', function(e) {
        toggleSizeStatus(this, e);
    });
</script>
@endpush
