@extends('layouts.admin')

@section('title', 'Tạo Lookbook Mới')
@section('page_title', 'Tạo Lookbook Mới (@ATINO_STYLE)')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-plus-circle text-success me-2"></i>Thêm Mới Lookbook
            </h4>
            <p class="text-muted small mb-0">Tạo mới hình ảnh phối đồ Lookbook và chọn các sản phẩm xuất hiện trong ảnh.</p>
        </div>
        <a href="{{ route('admin.lookbooks.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Có lỗi xảy ra, vui lòng kiểm tra lại:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.lookbooks.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Left Side: Basic Info & Image Upload -->
            <div class="col-lg-5">
                <div class="card admin-card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-image text-primary me-2"></i>Hình Ảnh & Thông Tin Lookbook</h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Tiêu đề Lookbook -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold text-dark small">Tiêu đề Lookbook <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Ví dụ: Lookbook: Năng Động Thể Thao" value="{{ old('title') }}" required>
                        </div>

                        <!-- Phụ đề -->
                        <div class="mb-3">
                            <label for="subtitle" class="form-label fw-semibold text-dark small">Phụ đề mô tả</label>
                            <input type="text" name="subtitle" id="subtitle" class="form-control" placeholder="Ví dụ: Phong cách phối đồ thể thao hiện đại" value="{{ old('subtitle') }}">
                        </div>

                        <!-- Chữ trên nút Shop (Default: Xem & Mua X Sản Phẩm) -->
                        <div class="mb-3">
                            <label for="button_text" class="form-label fw-semibold text-dark small">Nội dung nút bấm (Tùy chọn)</label>
                            <input type="text" name="button_text" id="button_text" class="form-control" placeholder="Mặc định: Xem & Mua X Sản Phẩm" value="{{ old('button_text') }}">
                            <span class="extra-small text-muted">Nếu để trống, hệ thống sẽ tự động hiển thị "Xem & Mua X Sản Phẩm" dựa theo số sản phẩm chọn.</span>
                        </div>

                        <!-- Link Instagram -->
                        <div class="mb-3">
                            <label for="instagram_url" class="form-label fw-semibold text-dark small"><i class="fa-brands fa-instagram text-danger me-1"></i>Link bài viết Instagram (Tùy chọn)</label>
                            <input type="url" name="instagram_url" id="instagram_url" class="form-control" placeholder="https://instagram.com/p/..." value="{{ old('instagram_url') }}">
                        </div>

                        <!-- Tải ảnh lên HOẶC URL -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Hình ảnh Lookbook <span class="text-danger">*</span></label>
                            
                            <ul class="nav nav-pills nav-fill mb-2 small" id="imageTypeTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-1 rounded-pill" id="file-tab" data-bs-toggle="tab" data-bs-target="#file-upload" type="button" role="tab">Tải File Ảnh</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 rounded-pill" id="url-tab" data-bs-toggle="tab" data-bs-target="#url-input" type="button" role="tab">Nhập Đường Dẫn URL</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="imageTypeTabContent">
                                <div class="tab-pane fade show active p-2 border rounded-3 bg-light" id="file-upload" role="tabpanel">
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                                </div>
                                <div class="tab-pane fade p-2 border rounded-3 bg-light" id="url-input" role="tabpanel">
                                    <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://images.unsplash.com/..." value="{{ old('image_url') }}" oninput="previewUrl(this.value)">
                                </div>
                            </div>

                            <!-- Preview Box -->
                            <div class="mt-3 text-center">
                                <div class="border rounded-3 p-2 bg-light d-inline-block position-relative" style="width: 100%; max-height: 260px; overflow: hidden;">
                                    <img id="img-preview" src="https://via.placeholder.com/600x400?text=Xem+Truoc+Anh+Lookbook" alt="Preview" class="rounded-2 w-100" style="height: 220px; object-fit: cover;">
                                </div>
                            </div>
                        </div>

                        <!-- Thứ tự & Trạng thái -->
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="sort_order" class="form-label fw-semibold text-dark small">Thứ tự hiển thị</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-input-label fw-bold text-dark small ms-1" for="is_active">Cho phép hiển thị</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Select Products to Attach -->
            <div class="col-lg-7">
                <div class="card admin-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-shirt text-warning me-2"></i>Chọn Sản Phẩm Xuất Hiện Trong Ảnh Lookbook
                        </h6>
                        <span class="badge bg-primary rounded-pill px-3 py-1.5 fs-7" id="selected-count-badge">Đã chọn: {{ count(old('product_ids', [])) }} sản phẩm</span>
                    </div>
                    <div class="card-body p-3">
                        <!-- Thanh Lọc: Tìm kiếm & Danh mục -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="text" id="product-search-input" class="form-control border-start-0 ps-0" placeholder="Tìm kiếm nhanh tên, SKU sản phẩm...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-folder-tree"></i></span>
                                    <select class="form-select border-start-0" id="product-category-filter">
                                        <option value="">-- Tất cả danh mục --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->parent_id ? '— ' . $cat->name : $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Số lượng hiển thị -->
                        <div class="d-flex align-items-center justify-content-end mb-2 px-1">
                            <div class="text-muted extra-small" id="visible-count-text">
                                Hiển thị: <strong id="visibleCount" class="text-dark">{{ $products->count() }}</strong> / {{ $products->count() }} sản phẩm
                            </div>
                        </div>

                        <div class="table-responsive rounded-3 border" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0" id="products-selection-table">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="text-center" style="width: 45px;">
                                            <input class="form-check-input cursor-pointer" type="checkbox" id="checkAllMaster" title="Chọn / Bỏ chọn tất cả sản phẩm đang hiển thị">
                                        </th>
                                        <th style="width: 60px;">Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        @php
                                            $thumb = $product->images->where('is_thumbnail', true)->first() ?? $product->images->first();
                                        @endphp
                                        <tr class="product-row"
                                            data-category-id="{{ $product->category_id ?? '' }}"
                                            data-product-name="{{ Str::lower($product->name) }}"
                                            data-product-sku="{{ Str::lower($product->sku ?? '') }}">
                                            <td class="text-center">
                                                <input class="form-check-input product-checkbox cursor-pointer" type="checkbox" name="product_ids[]" value="{{ $product->id }}" id="prod_{{ $product->id }}" {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                @if($thumb && $thumb->image_url)
                                                    @php
                                                        $imgSrc = str_starts_with($thumb->image_url, 'http') ? $thumb->image_url : asset('storage/' . $thumb->image_url);
                                                    @endphp
                                                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="rounded-2 border object-fit-cover" style="width: 44px; height: 44px;">
                                                @else
                                                    <div class="bg-light border rounded-2 d-flex align-items-center justify-content-center text-muted" style="width: 44px; height: 44px;" title="Chưa có ảnh sản phẩm">
                                                        <i class="fa-solid fa-shirt text-secondary"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <label for="prod_{{ $product->id }}" class="fw-bold text-dark small mb-0 cursor-pointer text-decoration-none d-block">
                                                    {{ $product->name }}
                                                </label>
                                                <span class="extra-small text-muted">SKU: {{ $product->sku ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 extra-small text-secondary rounded-pill btn-filter-cat" data-category-id="{{ $product->category_id ?? '' }}" title="Nhấn để lọc nhanh danh mục này">
                                                    <i class="fa-solid fa-folder me-1 extra-small text-muted"></i>{{ $product->category?->name ?? 'Không phân loại' }}
                                                </button>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-danger small">
                                                    {{ number_format($product->sale_price ?: $product->price) }}đ
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Không tìm thấy sản phẩm nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.lookbooks.index') }}" class="btn btn-light rounded-pill px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Lookbook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewUrl(url) {
        if (url.trim() !== '') {
            document.getElementById('img-preview').src = url;
        }
    }

    $(document).ready(function() {
        const searchInput = $('#product-search-input');
        const categoryFilter = $('#product-category-filter');
        const countBadge = $('#selected-count-badge');
        const visibleCountEl = $('#visibleCount');
        const checkAllMaster = $('#checkAllMaster');
        const productRows = $('.product-row');

        function updateSelectedCount() {
            const count = $('.product-checkbox:checked').length;
            countBadge.text('Đã chọn: ' + count + ' sản phẩm');
            updateMasterCheckbox();
        }

        function updateMasterCheckbox() {
            const visibleRows = productRows.filter(function() {
                return $(this).css('display') !== 'none';
            });
            if (visibleRows.length === 0) {
                checkAllMaster.prop('checked', false).prop('indeterminate', false);
                return;
            }
            const checkedVisible = visibleRows.find('.product-checkbox:checked').length;
            if (checkedVisible === visibleRows.length) {
                checkAllMaster.prop('checked', true).prop('indeterminate', false);
            } else if (checkedVisible > 0) {
                checkAllMaster.prop('checked', false).prop('indeterminate', true);
            } else {
                checkAllMaster.prop('checked', false).prop('indeterminate', false);
            }
        }

        function filterProducts() {
            const query = (searchInput.val() || '').toLowerCase().trim();
            const selectedCat = categoryFilter.val() || '';

            let visibleCount = 0;

            productRows.each(function() {
                const name = ($(this).data('product-name') || '').toString();
                const sku = ($(this).data('product-sku') || '').toString();
                const catId = ($(this).data('category-id') || '').toString();

                const matchesKeyword = !query || name.indexOf(query) > -1 || sku.indexOf(query) > -1;
                const matchesCat = !selectedCat || catId === selectedCat;

                if (matchesKeyword && matchesCat) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            if (visibleCountEl.length) {
                visibleCountEl.text(visibleCount);
            }

            updateMasterCheckbox();
        }

        searchInput.on('input keyup', filterProducts);
        categoryFilter.on('change', filterProducts);

        // Click badge danh muc tren san pham de loc nhanh
        $(document).on('click', '.btn-filter-cat', function(e) {
            e.preventDefault();
            const catId = $(this).data('category-id');
            categoryFilter.val(catId).trigger('change');
        });

        // Checkbox tung san pham
        $(document).on('change', '.product-checkbox', function() {
            updateSelectedCount();
        });

        // Checkbox Master
        checkAllMaster.on('change', function() {
            const isChecked = $(this).is(':checked');
            productRows.filter(':visible').find('.product-checkbox').prop('checked', isChecked);
            updateSelectedCount();
        });


        updateSelectedCount();
    });
</script>
@endpush
