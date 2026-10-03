@extends('layouts.admin')

@section('title', 'Quản Lý Màu Sắc (Colors)')
@section('page_title', 'Màu Sắc Sản Phẩm')

@section('content')
<!-- 1. HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-palette text-primary"></i>Màu Sắc Sản Phẩm
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-7 font-weight-semibold px-2.5 py-1 ms-1">
                {{ number_format($stats['total'] ?? $colors->total()) }} màu
            </span>
        </h4>
        <p class="text-muted small mb-0">Quản lý mã màu HEX và swatch hiển thị của sản phẩm thời trang</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm font-weight-medium" id="btn-open-create-modal">
            <i class="fa-solid fa-plus me-1"></i> Thêm Màu Mới
        </button>
    </div>
</div>

<!-- 2. TABLE & FILTER TOOLBAR CARD -->
<div class="card admin-card border-0 shadow-sm rounded-3">
    <!-- FILTER BAR TOOLBAR (CARD HEADER) -->
    <div class="card-header bg-white py-3 border-bottom">
        <form id="colorFilterForm" action="{{ route('admin.colors.index') }}" method="GET" class="row g-2 align-items-center">
            @if(request('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
            @if(request('sort_dir')) <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}"> @endif
            @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif

            <!-- Search Input -->
            <div class="col-12 col-md-8 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="🔍 Tìm tên màu hoặc mã HEX (#FF0000)... (Nhấn Enter để tìm)" value="{{ request('search') }}" onkeydown="if(event.key==='Enter') this.form.submit();">
                </div>
            </div>

            <!-- Nút Xóa lọc (khi đang lọc) -->
            @if(!empty($activeFilterCount) && $activeFilterCount > 0)
                <div class="col-12 col-md-4 col-lg-6 d-flex justify-content-md-end align-items-center">
                    <a href="{{ route('admin.colors.index') }}" class="btn btn-outline-danger px-3 d-flex align-items-center gap-1.5" title="Xóa bộ lọc">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Xóa lọc</span>
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- BULK ACTIONS BAR -->
    <form id="colorBulkForm" action="{{ route('admin.colors.bulkAction') }}" method="POST">
        @csrf
        <input type="hidden" name="action" id="color_bulk_action_input">

        <div id="colorBulkBar" class="bg-primary-subtle border-bottom p-2.5 px-3 d-none align-items-center justify-content-between">
            <span class="fw-bold text-primary small">
                <i class="fa-solid fa-check-double me-1"></i>Đã chọn <span id="colorSelectedCount">0</span> màu sắc
            </span>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-danger" onclick="submitColorBulk('delete')">
                    <i class="fa-solid fa-trash-can me-1"></i> Xóa màu đã chọn
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover" id="colors-table">
                <thead class="table-light extra-small">
                    <tr>
                        <th style="width: 36px;" class="ps-3">
                            <input type="checkbox" class="form-check-input" id="selectAllColors">
                        </th>
                        <th style="width: 60px;">
                            @php $nextIdDir = ($sortBy === 'id' && $sortDir === 'asc') ? 'desc' : 'asc'; @endphp
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_dir' => $nextIdDir]) }}" class="text-dark text-decoration-none">
                                ID @if($sortBy === 'id') <i class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-primary"></i> @endif
                            </a>
                        </th>
                        <th style="width: 90px;" class="text-center">Swatch</th>
                        <th>
                            @php $nextNameDir = ($sortBy === 'name' && $sortDir === 'asc') ? 'desc' : 'asc'; @endphp
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_dir' => $nextNameDir]) }}" class="text-dark text-decoration-none">
                                Tên Màu Sắc @if($sortBy === 'name') <i class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-primary"></i> @endif
                            </a>
                        </th>
                        <th>Mã HEX</th>
                        <th class="text-end pe-3" style="width: 110px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($colors as $color)
                        @php
                            $hex = strtoupper($color->hex_code ?? '#000000');
                            $isWhiteOrBright = in_array($hex, ['#FFFFFF', '#FFF', '#FDFDFD', '#FAFAFA', '#F8F9FA', '#F0F0F0']);
                        @endphp
                        <tr id="color-row-{{ $color->id }}">
                            <td class="ps-3">
                                <input type="checkbox" name="color_ids[]" value="{{ $color->id }}" class="form-check-input color-select">
                            </td>
                            <td class="fw-bold text-secondary">#{{ $color->id }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="d-inline-block rounded-circle shadow-xs" style="width: 32px; height: 32px; background-color: {{ $hex }}; border: {{ $isWhiteOrBright ? '2px solid #cbd5e1' : '1px solid rgba(0,0,0,0.15)' }};"></span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark color-name fs-6">{{ $color->name }}</span>
                            </td>
                            <td>
                                <code class="px-2 py-1 rounded bg-light border text-dark font-weight-bold color-hex">{{ $hex }}</code>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary btn-edit-color" data-id="{{ $color->id }}" title="Chỉnh sửa màu">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-delete-color" data-id="{{ $color->id }}" data-name="{{ $color->name }}" title="Xóa màu sắc">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa-solid fa-palette fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                <h6 class="fw-bold text-dark">Không tìm thấy màu sắc nào</h6>
                                <p class="small text-muted mb-3">Thử thay đổi bộ lọc hoặc thêm màu sắc mới.</p>
                                <button type="button" class="btn btn-primary btn-sm" onclick="$('#btn-open-create-modal').click();">
                                    <i class="fa-solid fa-plus me-1"></i> Thêm Màu Mới
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- 5. PAGINATION & ITEMS PER PAGE -->
    @if($colors->total() > 0)
        <div class="card-footer bg-white border-top py-3 px-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $colors->firstItem() }}–{{ $colors->lastItem() }}</strong> trong tổng số <strong>{{ $colors->total() }}</strong> màu sắc
                </div>
                <div class="d-flex align-items-center gap-1.5 small text-muted">
                    <span>Xem:</span>
                    <select class="form-select form-select-sm" style="width: 75px;" onchange="window.location.href=this.value;">
                        @foreach([15, 30, 50, 100] as $colorOpt)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $colorOpt]) }}" {{ $perPage == $colorOpt ? 'selected' : '' }}>
                                {{ $colorOpt }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                {{ $colors->links() }}
            </div>
        </div>
    @endif
</div>

<!-- MODAL THÊM & SỬA MÀU SẮC (AJAX) -->
<div class="modal fade" id="colorModal" tabindex="-1" aria-labelledby="colorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark" id="colorModalLabel"><i class="fa-solid fa-palette me-2 text-primary"></i>Thêm Màu Sắc Mới</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="colorAjaxForm">
                @csrf
                <input type="hidden" id="color_id" name="color_id">
                <input type="hidden" id="_method" name="_method" value="POST">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Tên màu sắc -->
                        <div class="col-12">
                            <label for="name" class="form-label font-weight-medium">Tên Màu Sắc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Ví dụ: Đen Tuyển, Trắng Kem, Navy..." required>
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>

                        <!-- Mã màu HEX & Color Picker Sync -->
                        <div class="col-12">
                            <label for="hex_code" class="form-label font-weight-medium">Mã Màu (HEX) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color border-end-0" id="color_picker" value="#000000" title="Chọn màu trực quan">
                                <input type="text" class="form-control font-monospace text-uppercase" id="hex_code" name="hex_code" value="#000000" placeholder="#000000" required>
                                <span class="input-group-text bg-light">
                                    <span id="preview_swatch" class="d-inline-block rounded-circle" style="width: 22px; height: 22px; background-color: #000000; border: 1px solid #cbd5e1;"></span>
                                </span>
                            </div>
                            <div class="invalid-feedback d-block" id="error-hex_code"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary px-4" id="btn-save-color">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. MODAL DANH SÁCH SẢN PHẨM ĐANG DÙNG MÀU NÀY -->
<div class="modal fade" id="modalColorUsage" tabindex="-1" aria-labelledby="modalColorUsageLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span id="usageModalSwatch" class="d-inline-block rounded-circle shadow-xs flex-shrink-0" style="width: 24px; height: 24px; background-color: #000; border: 1px solid rgba(0,0,0,0.2);"></span>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalColorUsageLabel">
                        Quản Lý Sản Phẩm Đang Dùng Màu: <span id="usageColorName" class="text-primary"></span> (<span id="usageColorHex" class="font-monospace text-secondary"></span>)
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Cảnh báo & Tóm tắt -->
                <div class="alert alert-info border-0 bg-info-subtle text-dark rounded-3 p-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info fs-5 text-info flex-shrink-0"></i>
                        <div class="small">
                            Màu sắc đang được liên kết với <strong><span id="usageModalProductTotal">0</span> sản phẩm</strong> (<span id="usageModalVariantTotal">0</span> biến thể).
                            Bạn có thể <strong>Bật/Tắt bán</strong>, <strong>Gỡ màu</strong> cho từng SP bên dưới hoặc <strong>Chuyển sang màu khác</strong> hàng loạt.
                        </div>
                    </div>
                </div>

                <!-- THANH CÔNG CỤ: CHUYỂN TOÀN BỘ SANG MÀU KHÁC (1 CLICK) -->
                <div class="card border border-primary-subtle bg-primary-subtle rounded-3 p-3 mb-3" id="transferColorBox">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <h6 class="fw-bold text-primary mb-1 d-flex align-items-center gap-1.5 fs-7">
                                <i class="fa-solid fa-shuffle"></i> Chuyển toàn bộ sản phẩm sang màu khác (1 Click)
                            </h6>
                            <span class="text-secondary extra-small">Chuyển toàn bộ các biến thể đang mang màu này sang màu đích mà không mất tồn kho.</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <select class="form-select form-select-sm bg-white" id="targetColorSelect" style="min-width: 200px;">
                                <option value="">-- Đang tải danh sách màu --</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary text-nowrap px-3 shadow-xs font-weight-medium" id="btnExecuteTransfer">
                                <i class="fa-solid fa-arrow-right-arrow-left me-1"></i>Chuyển ngay
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="usageLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Đang tải danh sách sản phẩm liên kết...</p>
                </div>

                <!-- Danh Sách Sản Phẩm -->
                <div id="usageContent" class="d-none">
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light extra-small text-uppercase">
                                <tr>
                                    <th style="width: 55px;" class="ps-3">Ảnh</th>
                                    <th>Tên Sản Phẩm & SKU</th>
                                    <th style="width: 130px;">Danh Mục</th>
                                    <th>Kích Cỡ (Size)</th>
                                    <th class="text-end pe-3" style="width: 250px;">Thao Tác Nhanh</th>
                                </tr>
                            </thead>
                            <tbody id="usageTableBody">
                                <!-- Render qua AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2.5 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-2xs" id="btnModalForceDelete">
                        <i class="fa-solid fa-trash-can me-1"></i> Xóa cưỡng chế màu này
                    </button>
                    <span class="extra-small text-muted d-none d-md-inline">• Thao tác xóa cưỡng chế sẽ xóa sạch toàn bộ biến thể màu này của các SP</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-border-primary:hover {
        border-color: #0d6efd !important;
        background-color: #e7f1ff !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.18) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const colorModal = new bootstrap.Modal(document.getElementById('colorModal'));
        const form = $('#colorAjaxForm');
        const selectAll = document.getElementById('selectAllColors');
        const checkboxes = document.querySelectorAll('.color-select');
        const bulkBar = document.getElementById('colorBulkBar');
        const selectedCount = document.getElementById('colorSelectedCount');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function updateColorBulkBar() {
            const checkedBoxes = document.querySelectorAll('.color-select:checked');
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
                updateColorBulkBar();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked && selectAll) selectAll.checked = false;
                updateColorBulkBar();
            });
        });

        function syncSwatchPreview(hexVal) {
            let cleanHex = hexVal.trim();
            if (!cleanHex.startsWith('#')) cleanHex = '#' + cleanHex;

            if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i.test(cleanHex)) {
                $('#color_picker').val(cleanHex);
                $('#preview_swatch').css('background-color', cleanHex);
            }
        }

        // Sync Color Picker & Hex Text Box
        $('#color_picker').on('input', function() {
            const val = $(this).val().toUpperCase();
            $('#hex_code').val(val);
            $('#preview_swatch').css('background-color', val);
        });

        $('#hex_code').on('input', function() {
            syncSwatchPreview($(this).val());
        });

        // 1. Mở Modal THÊM MỚI (CREATE)
        $('#btn-open-create-modal').on('click', function() {
            form[0].reset();
            $('#color_id').val('');
            $('#_method').val('POST');
            $('#color_picker').val('#000000');
            $('#hex_code').val('#000000');
            $('#preview_swatch').css('background-color', '#000000');
            $('#colorModalLabel').html('<i class="fa-solid fa-plus me-2 text-primary"></i>Thêm Màu Sắc Mới');
            clearValidationErrors();
            colorModal.show();
        });

        // 2. Nộp Form AJAX (STORE & UPDATE)
        form.on('submit', function(e) {
            e.preventDefault();
            clearValidationErrors();

            const colorId = $('#color_id').val();
            const isEdit = colorId !== '';
            const url = isEdit ? `{{ url('admin/colors') }}/${colorId}` : '{{ route("admin.colors.store") }}';
            const method = isEdit ? 'PUT' : 'POST';

            const formData = {
                name: $('#name').val(),
                hex_code: $('#hex_code').val(),
                _method: method
            };

            $('#btn-save-color').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang xử lý...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#btn-save-color').prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> Lưu Thay Đổi');
                    colorModal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    setTimeout(() => location.reload(), 800);
                },
                error: function(xhr) {
                    $('#btn-save-color').prop('disabled', false).html('<i class="fa-solid fa-floppy-disk me-1"></i> Lưu Thay Đổi');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#name').addClass('is-invalid');
                            $('#error-name').text(errors.name[0]);
                        }
                        if (errors.hex_code) {
                            $('#hex_code').addClass('is-invalid');
                            $('#error-hex_code').text(errors.hex_code[0]);
                        }
                    } else {
                        Swal.fire('Lỗi!', 'Có lỗi xảy ra khi xử lý dữ liệu.', 'error');
                    }
                }
            });
        });

        // 3. Click Nút CHỈNH SỬA (EDIT AJAX)
        $(document).on('click', '.btn-edit-color', function() {
            const colorId = $(this).data('id');
            clearValidationErrors();

            $.ajax({
                url: `{{ url('admin/colors') }}/${colorId}/edit`,
                type: 'GET',
                success: function(response) {
                    const color = response.data;
                    $('#color_id').val(color.id);
                    $('#name').val(color.name);
                    $('#hex_code').val(color.hex_code);
                    $('#color_picker').val(color.hex_code || '#000000');
                    $('#preview_swatch').css('background-color', color.hex_code || '#000000');
                    $('#_method').val('PUT');

                    $('#colorModalLabel').html('<i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Chỉnh Sửa Màu Sắc');
                    colorModal.show();
                },
                error: function() {
                    Swal.fire('Lỗi!', 'Không thể tải dữ liệu màu sắc.', 'error');
                }
            });
        });

        let activeUsageColorId = null;
        let activeUsageColorName = '';
        let activeUsageColorHex = '';
        const modalColorUsage = new bootstrap.Modal(document.getElementById('modalColorUsage'));

        // =========================================================================
        // 1. BẬT / TẮT TRẠNG THÁI KINH DOANH MÀU SẮC TRỰC TIẾP TRÊN BẢNG CHÍNH
        // =========================================================================
        $(document).on('click', '.btn-toggle-color-status', function() {
            const $btn = $(this);
            const url = $btn.data('url');

            $btn.prop('disabled', true);
            $.ajax({
                url: url,
                type: 'PUT',
                success: function(response) {
                    $btn.prop('disabled', false);
                    if (response.is_active) {
                        $btn.html(`
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Đang bán
                            </span>
                        `);
                    } else {
                        $btn.html(`
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-pause"></i> Tạm ngưng
                            </span>
                        `);
                    }
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                error: function() {
                    $btn.prop('disabled', false);
                    Swal.fire('Lỗi!', 'Không thể đổi trạng thái màu sắc.', 'error');
                }
            });
        });

        // =========================================================================
        // 2. HÀM MỞ MODAL QUẢN LÝ SẢN PHẨM ĐANG SỬ DỤNG MÀU
        // =========================================================================
        function openColorUsageModal(colorId, colorName, colorHex) {
            activeUsageColorId = colorId;
            activeUsageColorName = colorName;
            activeUsageColorHex = colorHex;

            $('#usageColorName').text(colorName);
            $('#usageColorHex').text(colorHex ? colorHex : '');
            $('#usageModalSwatch').css('background-color', colorHex || '#000000');
            $('#usageLoading').removeClass('d-none');
            $('#usageContent').addClass('d-none');
            $('#usageTableBody').empty();
            $('#targetColorSelect').html('<option value="">-- Đang tải danh sách màu --</option>');

            modalColorUsage.show();

            $.ajax({
                url: `{{ url('admin/colors') }}/${colorId}/variants`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#usageLoading').addClass('d-none');
                    $('#usageContent').removeClass('d-none');

                    if (response.success) {
                        $('#usageModalProductTotal').text(response.products_count);
                        $('#usageModalVariantTotal').text(response.variants_count);

                        // Đổ danh sách màu có thể chuyển đổi vào dropdown
                        let colorOptions = '<option value="">-- Chọn màu muốn chuyển sang --</option>';
                        if (response.available_colors && response.available_colors.length > 0) {
                            response.available_colors.forEach(function(c) {
                                colorOptions += `<option value="${c.id}">${c.name} (${c.hex_code})</option>`;
                            });
                        } else {
                            colorOptions = '<option value="">-- Không có màu khác --</option>';
                        }
                        $('#targetColorSelect').html(colorOptions);

                        if (response.products && response.products.length > 0) {
                            let rowsHtml = '';
                            response.products.forEach(function(p) {
                                let variantsHtml = '';
                                p.variants.forEach(function(v) {
                                    variantsHtml += `
                                        <a href="${v.edit_variant_url}" target="_blank" 
                                           class="badge bg-light text-dark border me-1.5 mb-1.5 font-weight-normal py-1.5 px-2 text-decoration-none shadow-2xs d-inline-flex align-items-center gap-1 hover-border-primary" 
                                           title="Bấm để sửa Size ${v.size_name} (kho ${v.stock_quantity}) ở tab mới"
                                           style="transition: all 0.15s ease;">
                                            <span>Size <strong class="text-primary">${v.size_name}</strong>:</span>
                                            <span class="badge ${v.stock_status_badge}">${v.stock_quantity} SP</span>
                                            <i class="fa-solid fa-pen-to-square text-primary ms-0.5" style="font-size: 0.7rem;"></i>
                                        </a>
                                    `;
                                });

                                let statusBtnHtml = p.all_active ? `
                                    <button type="button" class="btn btn-sm btn-outline-success py-1 px-2.5 extra-small rounded-pill btn-toggle-prod-color d-inline-flex align-items-center gap-1" data-url="${p.toggle_status_url}" data-name="${p.name}" title="Click để TẠM NGƯNG bán màu này cho riêng sản phẩm này">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Đang bán</span>
                                    </button>
                                ` : `
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 extra-small rounded-pill btn-toggle-prod-color d-inline-flex align-items-center gap-1" data-url="${p.toggle_status_url}" data-name="${p.name}" title="Click để BẬT lại bán màu này cho riêng sản phẩm này">
                                        <i class="fa-solid fa-circle-pause"></i>
                                        <span>Đang ẩn</span>
                                    </button>
                                `;

                                let detachBtnHtml = `
                                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2.5 extra-small rounded-pill btn-detach-prod-color d-inline-flex align-items-center gap-1" data-url="${p.detach_url}" data-name="${p.name}" title="Gỡ sạch các biến thể màu này khỏi sản phẩm này">
                                        <i class="fa-solid fa-trash-can"></i>
                                        <span>Gỡ màu</span>
                                    </button>
                                `;

                                rowsHtml += `
                                    <tr id="usage-prod-row-${p.id}">
                                        <td class="ps-3">
                                            <img src="${p.thumbnail}" alt="${p.name}" class="rounded-2 border object-fit-cover shadow-2xs" style="width: 44px; height: 44px;" onerror="this.src='https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80'">
                                        </td>
                                        <td>
                                            <a href="${p.edit_url}" target="_blank" class="fw-bold text-dark text-decoration-none hover-primary d-block" title="${p.name}">
                                                ${p.name}
                                            </a>
                                            <div class="small text-muted d-flex align-items-center gap-2 mt-0.5">
                                                <span>SKU: <code class="text-secondary">${p.sku}</code></span>
                                                <span>•</span>
                                                <span class="text-danger fw-semibold">${p.price_formatted}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                                ${p.category_name}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap align-items-center py-1">
                                                ${variantsHtml}
                                            </div>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="d-inline-flex align-items-center gap-1.5 flex-wrap justify-content-end">
                                                ${statusBtnHtml}
                                                ${detachBtnHtml}
                                                <a href="${p.variants_url}" target="_blank" class="btn btn-sm btn-light border py-1 px-2 extra-small rounded-pill text-secondary" title="Xem bảng biến thể ở tab mới">
                                                    <i class="fa-solid fa-layer-group text-primary"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });

                            $('#usageTableBody').html(rowsHtml);
                        } else {
                            $('#usageTableBody').html(`
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        <i class="fa-regular fa-circle-check text-success fs-4 d-block mb-1"></i>
                                        Tất cả sản phẩm đã được gỡ màu. Bạn có thể đóng popup và xóa màu sắc này!
                                    </td>
                                </tr>
                            `);
                        }
                    }
                },
                error: function() {
                    $('#usageLoading').addClass('d-none');
                    Swal.fire('Lỗi!', 'Không thể tải danh sách sản phẩm liên kết của màu sắc.', 'error');
                }
            });
        }

        // =========================================================================
        // 3. THAO TÁC 1-CLICK TRONG POPUP: BẬT / TẮT BÁN MÀU CHO RIÊNG 1 SẢN PHẨM
        // =========================================================================
        $(document).on('click', '.btn-toggle-prod-color', function() {
            const $btn = $(this);
            const url = $btn.data('url');

            $btn.prop('disabled', true);
            $.ajax({
                url: url,
                type: 'PUT',
                success: function(response) {
                    $btn.prop('disabled', false);
                    if (response.is_active) {
                        $btn.removeClass('btn-outline-secondary').addClass('btn-outline-success');
                        $btn.html('<i class="fa-solid fa-circle-check"></i><span>Đang bán</span>');
                        $btn.attr('title', 'Click để TẠM NGƯNG bán màu này cho riêng SP này');
                    } else {
                        $btn.removeClass('btn-outline-success').addClass('btn-outline-secondary');
                        $btn.html('<i class="fa-solid fa-circle-pause"></i><span>Đang ẩn</span>');
                        $btn.attr('title', 'Click để BẬT lại bán màu này cho riêng SP này');
                    }
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                error: function() {
                    $btn.prop('disabled', false);
                    Swal.fire('Lỗi!', 'Không thể thay đổi trạng thái bán của sản phẩm.', 'error');
                }
            });
        });

        // =========================================================================
        // 4. THAO TÁC 1-CLICK TRONG POPUP: GỠ MÀU KHỎI 1 SẢN PHẨM CỤ THỂ
        // =========================================================================
        $(document).on('click', '.btn-detach-prod-color', function() {
            const $btn = $(this);
            const url = $btn.data('url');
            const prodName = $btn.data('name');

            Swal.fire({
                title: 'Gỡ màu khỏi sản phẩm này?',
                html: `Bạn có chắc muốn gỡ màu <strong>"${activeUsageColorName}"</strong> khỏi sản phẩm <strong>"${prodName}"</strong>?<br><span class="text-muted small">Toàn bộ các kích cỡ có màu này của riêng sản phẩm này sẽ bị xóa. Các sản phẩm khác không bị ảnh hưởng.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý gỡ',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true);
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function(response) {
                            $btn.closest('tr').fadeOut(300, function() {
                                $(this).remove();
                                $('#usageModalProductTotal').text(response.remaining_products_count);
                                $('#usageModalVariantTotal').text(response.remaining_variants_count);

                                // Cập nhật badge trên bảng chính ngoài danh sách
                                const $mainRow = $(`#color-row-${activeUsageColorId}`);
                                if ($mainRow.length > 0) {
                                    if (response.remaining_products_count > 0) {
                                        $mainRow.find('.btn-view-color-usage').html(`
                                            <i class="fa-solid fa-shirt"></i>
                                            <span class="fw-semibold">${response.remaining_products_count} SP</span>
                                            <span class="badge bg-primary text-white rounded-pill ms-0.5" style="font-size: 0.68rem;">${response.remaining_variants_count} biến thể</span>
                                        `);
                                    } else {
                                        $mainRow.find('.btn-view-color-usage').parent().html(`
                                            <span class="badge bg-light text-muted border px-2.5 py-1.5 font-weight-medium">
                                                <i class="fa-regular fa-circle-check me-1 text-secondary"></i> Chưa dùng (0)
                                            </span>
                                        `);
                                        // Đổi nút xóa từ blocked sang delete-color
                                        $mainRow.find('.btn-delete-blocked').replaceWith(`
                                            <button type="button" class="btn btn-outline-danger btn-delete-color" data-id="${activeUsageColorId}" data-name="${activeUsageColorName}" title="Xóa màu sắc">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        `);
                                    }
                                }

                                if (response.remaining_products_count === 0) {
                                    $('#usageTableBody').html(`
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted small">
                                                <i class="fa-regular fa-circle-check text-success fs-3 d-block mb-1"></i>
                                                Tất cả sản phẩm đã được gỡ màu. Bạn có thể đóng popup và xóa màu sắc này!
                                            </td>
                                        </tr>
                                    `);
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: response.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            $btn.prop('disabled', false);
                            Swal.fire('Lỗi!', 'Không thể gỡ màu khỏi sản phẩm.', 'error');
                        }
                    });
                }
            });
        });

        // =========================================================================
        // 5. CÔNG CỤ CHUYỂN TOÀN BỘ SANG MÀU KHÁC HÀNG LOẠT (1-CLICK MERGE)
        // =========================================================================
        $('#btnExecuteTransfer').on('click', function() {
            const targetColorId = $('#targetColorSelect').val();
            const targetColorText = $('#targetColorSelect option:selected').text();

            if (!targetColorId) {
                Swal.fire('Thông báo', 'Vui lòng chọn màu sắc đích muốn chuyển sang!', 'warning');
                return;
            }

            Swal.fire({
                title: 'Xác nhận chuyển màu hàng loạt?',
                html: `Toàn bộ các sản phẩm đang mang màu <strong>"${activeUsageColorName}"</strong> sẽ được đổi sang màu <strong>"${targetColorText}"</strong>.<br><br><span class="text-muted small">Số lượng tồn kho, giá bán và dữ liệu đơn hàng sẽ được bảo lưu an toàn.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý chuyển ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Đang chuyển đổi màu...',
                        text: 'Vui lòng đợi trong giây lát',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `{{ url('admin/colors') }}/${activeUsageColorId}/transfer`,
                        type: 'POST',
                        data: {
                            target_color_id: targetColorId
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Chuyển đổi thành công!',
                                text: response.message,
                                confirmButtonText: 'Tải lại trang'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            const errorMsg = xhr.responseJSON?.message || 'Có lỗi xảy ra khi chuyển đổi màu.';
                            Swal.fire('Lỗi!', errorMsg, 'error');
                        }
                    });
                }
            });
        });

        // =========================================================================
        // 6. XỬ LÝ XÓA CƯỠNG CHẾ MÀU SẮC (FORCE DELETE)
        // =========================================================================
        function executeForceDeleteColor(colorId, colorName, pCount, vCount) {
            Swal.fire({
                title: 'XÓA CƯỠNG CHẾ MÀU NÀY?',
                html: `<div class="text-danger fw-bold mb-2">⚠️ CẢNH BÁO NGUY HIỂM:</div>
                       Màu <strong>"${colorName}"</strong> đang có <strong>${pCount} sản phẩm</strong> (${vCount} biến thể).<br><br>
                       Khi xóa cưỡng chế, hệ thống sẽ <strong>xóa vĩnh viễn màu sắc này và toàn bộ ${vCount} biến thể liên kết</strong> của tất cả các sản phẩm trên!<br><br>
                       <span class="text-muted small">Thao tác này KHÔNG THỂ HOÀN TÁC. Bạn có chắc chắn muốn tiếp tục?</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tôi hiểu, Xóa sạch tất cả',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Đang dọn dẹp dữ liệu...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `{{ url('admin/colors') }}/${colorId}?force=1`,
                        type: 'DELETE',
                        success: function(response) {
                            modalColorUsage.hide();
                            $(`#color-row-${colorId}`).fadeOut(400, function() {
                                $(this).remove();
                            });
                            Swal.fire({
                                icon: 'success',
                                title: 'Đã xóa cưỡng chế!',
                                text: response.message,
                                timer: 2500,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            const errorMsg = xhr.responseJSON?.message || 'Không thể xóa cưỡng chế màu sắc này.';
                            Swal.fire('Lỗi!', errorMsg, 'error');
                        }
                    });
                }
            });
        }

        // Bấm nút Xóa cưỡng chế từ trong Modal
        $('#btnModalForceDelete').on('click', function() {
            const pCount = $('#usageModalProductTotal').text();
            const vCount = $('#usageModalVariantTotal').text();
            executeForceDeleteColor(activeUsageColorId, activeUsageColorName, pCount, vCount);
        });

        // Bấm nút Xem danh sách sản phẩm
        $(document).on('click', '.btn-view-color-usage', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const hex = $(this).data('hex');
            openColorUsageModal(id, name, hex);
        });

        // Click nút XÓA thông minh ở màu đang có sản phẩm (3 lựa chọn linh hoạt)
        $(document).on('click', '.btn-delete-blocked', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const hex = $(this).data('hex');
            const pCount = $(this).data('products-count');
            const vCount = $(this).data('variants-count');

            Swal.fire({
                title: `Màu "${name}" đang được sử dụng!`,
                html: `Màu này đang được dùng trong <strong>${pCount} sản phẩm</strong> (${vCount} biến thể).<br><br>Bạn muốn xử lý như thế nào?`,
                icon: 'warning',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: '#2563eb',
                denyButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa-solid fa-list-check me-1"></i> Xem & Gỡ từng sản phẩm',
                denyButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Xóa cưỡng chế màu này',
                cancelButtonText: 'Đóng'
            }).then((result) => {
                if (result.isConfirmed) {
                    openColorUsageModal(id, name, hex);
                } else if (result.isDenied) {
                    executeForceDeleteColor(id, name, pCount, vCount);
                }
            });
        });

        // Xóa màu sắc (An toàn: Giữ nguyên sản phẩm và biến thể)
        $(document).on('click', '.btn-delete-color', function() {
            const colorId = $(this).data('id');
            const colorName = $(this).data('name');

            Swal.fire({
                title: 'Xác nhận xóa màu sắc?',
                html: `Bạn có chắc muốn xóa màu <strong>"${colorName}"</strong>?<br><span class="text-muted small">Toàn bộ sản phẩm và biến thể đang dùng màu này sẽ được giữ nguyên an toàn (không bị xóa).</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/colors') }}/${colorId}`,
                        type: 'DELETE',
                        success: function(response) {
                            $(`#color-row-${colorId}`).fadeOut(400, function() {
                                $(this).remove();
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Đã xóa!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Không thể xóa màu sắc này.';
                            Swal.fire('Lỗi!', errorMsg, 'error');
                        }
                    });
                }
            });
        });

        function clearValidationErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }
    });

    function submitColorBulk(action) {
        const checkedBoxes = document.querySelectorAll('.color-select:checked');
        if (checkedBoxes.length === 0) {
            Swal.fire('Thông báo', 'Vui lòng chọn ít nhất 1 màu sắc!', 'warning');
            return;
        }

        Swal.fire({
            title: 'Xác nhận thao tác?',
            text: action === 'delete' ? 'Hệ thống sẽ xóa các màu sắc đã chọn. Các sản phẩm và biến thể liên quan vẫn được bảo toàn nguyên vẹn.' : 'Bạn có chắc chắn thực hiện thao tác hàng loạt?',
            icon: action === 'delete' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#dc3545' : '#2563eb',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('color_bulk_action_input').value = action;
                document.getElementById('colorBulkForm').submit();
            }
        });
    }
</script>
@endpush
