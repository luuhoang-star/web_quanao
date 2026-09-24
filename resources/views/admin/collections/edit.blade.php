@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Bộ Sưu Tập - ' . $collection->name)
@section('page_title', 'Chỉnh Sửa Bộ Sưu Tập')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb extra-small">
                <li class="breadcrumb-item"><a href="{{ route('admin.collections.index') }}" class="text-decoration-none">Bộ Sưu Tập</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa #{{ $collection->id }}</li>
            </ol>
        </nav>

        <div class="card admin-card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Chỉnh Sửa Bộ Sưu Tập Banner</h6>
                    <span class="text-muted small">Cập nhật thông tin bộ sưu tập: <strong>{{ $collection->name }}</strong></span>
                </div>
                <a href="{{ route('admin.collections.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.collections.update', $collection->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- LEFT COLUMN: THÔNG TIN CHÍNH -->
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="name" class="form-label font-weight-medium text-dark">Tên Bộ Sưu Tập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $collection->name) }}" required autofocus>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="badge_text" class="form-label font-weight-medium text-dark">Nhãn Thẻ (Badge Text)</label>
                                    <input type="text" class="form-control @error('badge_text') is-invalid @enderror" id="badge_text" name="badge_text" value="{{ old('badge_text', $collection->badge_text) }}" placeholder="Ví dụ: BST MÙA HÈ, MINIMALIST...">
                                    <span class="form-text extra-small text-muted">Hiển thị ở dạng ô huy hiệu trên góc ảnh banner</span>
                                    @error('badge_text')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="subtitle" class="form-label font-weight-medium text-dark">Tiêu Đề Phụ (Subtitle)</label>
                                    <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle', $collection->subtitle) }}" placeholder="Ví dụ: Thoáng mát & Thanh lịch">
                                    <span class="form-text extra-small text-muted">Dòng mô tả ngắn bên dưới tiêu đề chính</span>
                                    @error('subtitle')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label font-weight-medium text-dark">Mã Đường Dẫn (Slug)</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $collection->slug) }}" placeholder="summer-2026">
                                @error('slug')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label font-weight-medium text-dark">Mô Tả Chi Tiết</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Mô tả phong cách, chất liệu hoặc điểm đặc sắc của bộ sưu tập này...">{{ old('description', $collection->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: HÌNH ẢNH BANNER & CẤU HÌNH -->
                        <div class="col-md-5">
                            <div class="card bg-light border-0 p-3 rounded-3 mb-4">
                                <label class="form-label font-weight-bold text-dark mb-2">Hình Ảnh Banner Bộ Sưu Tập</label>
                                
                                <ul class="nav nav-pills nav-fill mb-3 extra-small" id="imageTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active py-1.5" id="file-tab" data-bs-toggle="tab" data-bs-target="#file-panel" type="button" role="tab">
                                            <i class="fa-solid fa-upload me-1"></i>Tải File Ảnh
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link py-1.5" id="url-tab" data-bs-toggle="tab" data-bs-target="#url-panel" type="button" role="tab">
                                            <i class="fa-solid fa-link me-1"></i>Đường Dẫn URL
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content" id="imageTabContent">
                                    <div class="tab-pane fade show active" id="file-panel" role="tabpanel">
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" onchange="previewFile(this);">
                                        @error('image')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="tab-pane fade" id="url-panel" role="tabpanel">
                                        <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url" name="image_url" value="{{ old('image_url', (str_starts_with($collection->image ?? '', 'http') ? $collection->image : '')) }}" placeholder="https://images.unsplash.com/..." oninput="previewUrl(this.value);" onchange="previewUrl(this.value);" onpaste="setTimeout(()=>previewUrl(this.value), 50);">
                                        @error('image_url')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- IMAGE PREVIEW BOX -->
                                <div class="mt-3 text-center">
                                    <span class="extra-small text-muted d-block mb-2">Khung xem trước banner:</span>
                                    <div class="position-relative overflow-hidden rounded-4 shadow-sm border bg-dark mx-auto" style="height: 200px; width: 100%;">
                                        <img id="img-preview" src="{{ $collection->image_url }}" alt="{{ $collection->name }}" class="w-100 h-100 object-fit-cover">
                                        <div class="position-absolute p-3 d-flex flex-column justify-content-end text-white text-start" style="background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(15,23,42,0.85) 100%); inset: 0;">
                                            <span class="badge bg-warning text-dark fw-bold mb-1 align-self-start rounded-pill extra-small" id="badge-preview">{{ $collection->badge_text ?: 'BST' }}</span>
                                            <h6 class="fw-bold text-white mb-0" id="title-preview">{{ $collection->name }}</h6>
                                            <span class="text-white-50 extra-small" id="sub-preview">{{ $collection->subtitle ?: 'Mô tả ngắn' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light border-0 p-3 rounded-3 mb-4">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label font-weight-medium text-dark">Thứ Tự Hiển Thị</label>
                                    <input type="number" min="0" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $collection->sort_order) }}">
                                    <span class="form-text extra-small text-muted">Số nhỏ hơn sẽ ưu tiên xếp trước (1, 2, 3...)</span>
                                </div>

                                <div class="form-check form-switch ms-1">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $collection->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-medium text-dark" for="is_active">Cho phép hiển thị bộ sưu tập này</label>
                                </div>
                            </div>
                        </div>

                        <!-- FULL WIDTH: BẢNG CHỌN SẢN PHẨM THUỘC BỘ SƯU TẬP -->
                        <div class="col-12" id="section-choose-products">
                            <div class="card border-0 bg-light p-3.5 rounded-3">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">
                                            <i class="fa-solid fa-shirt me-2 text-primary"></i>Chọn Sản Phẩm Thuộc Bộ Sưu Tập Này
                                        </h6>
                                        <span class="text-muted extra-small">Tích chọn các sản phẩm bạn muốn gán vào bộ sưu tập "{{ $collection->name }}"</span>
                                    </div>
                                    <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 shadow-xs" id="selected-count-badge">
                                        Đã chọn: {{ count(old('product_ids', $selectedProductIds)) }} sản phẩm
                                    </span>
                                </div>

                                <!-- Thanh lọc: Tìm kiếm, Danh mục -->
                                <div class="row g-2 mb-2">
                                    <div class="col-md-7">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                                            <input type="text" class="form-control border-start-0 ps-0" id="productSearchInput" placeholder="Tìm kiếm nhanh tên, SKU sản phẩm...">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-folder-tree"></i></span>
                                            <select class="form-select border-start-0" id="productCategoryFilter">
                                                <option value="">-- Tất cả danh mục --</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->parent_id ? '— ' . $cat->name : $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Thao tác chọn nhanh & số lượng hiển thị -->
                                <div class="d-flex align-items-center justify-content-between mb-2 px-1 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5 extra-small rounded-2" id="btnSelectAllVisible">
                                            <i class="fa-solid fa-check-double me-1"></i>Chọn tất cả đang hiển thị
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 extra-small rounded-2" id="btnUnselectAllVisible">
                                            <i class="fa-solid fa-xmark me-1"></i>Bỏ chọn tất cả đang hiển thị
                                        </button>
                                    </div>
                                    <div class="text-muted extra-small" id="visible-count-text">
                                        Hiển thị: <strong id="visibleCount" class="text-dark">{{ $products->count() }}</strong> / {{ $products->count() }} sản phẩm
                                    </div>
                                </div>

                                <div class="table-responsive bg-white rounded border" style="max-height: 380px; overflow-y: auto;">
                                    <table class="table align-middle table-hover mb-0 extra-small">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width: 45px;" class="text-center">
                                                    <input class="form-check-input cursor-pointer" type="checkbox" id="checkAllMaster" title="Chọn / Bỏ chọn tất cả sản phẩm đang hiển thị">
                                                </th>
                                                <th style="width: 60px;">Hình ảnh</th>
                                                <th>Tên sản phẩm</th>
                                                <th>Danh mục</th>
                                                <th>Giá bán</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productTableBody">
                                            @forelse($products as $product)
                                                @php
                                                    $thumb = $product->images->where('is_thumbnail', true)->first() ?? $product->images->first();
                                                    $isAttached = in_array($product->id, old('product_ids', $selectedProductIds));
                                                @endphp
                                                <tr class="product-row"
                                                    data-category-id="{{ $product->category_id ?? '' }}"
                                                    data-product-name="{{ Str::lower($product->name) }}"
                                                    data-product-sku="{{ Str::lower($product->sku) }}">
                                                    <td class="text-center">
                                                        <input class="form-check-input product-checkbox cursor-pointer" type="checkbox" name="product_ids[]" value="{{ $product->id }}" id="prod_{{ $product->id }}" {{ $isAttached ? 'checked' : '' }}>
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
                                                        <span class="text-muted extra-small">SKU: {{ $product->sku }}</span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 extra-small text-secondary rounded-pill btn-filter-cat" data-category-id="{{ $product->category_id ?? '' }}" title="Nhấn để lọc nhanh danh mục này">
                                                            <i class="fa-solid fa-folder me-1 extra-small text-muted"></i>{{ $product->category?->name ?? 'Không có' }}
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-danger">{{ number_format($product->price) }}đ</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">Chưa có sản phẩm nào đang hoạt động.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTONS -->
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top mt-3">
                        <a href="{{ route('admin.collections.index') }}" class="btn btn-light border px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Cập Nhật Bộ Sưu Tập
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('img-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewUrl(url) {
        if (url && url.trim() !== '') {
            document.getElementById('img-preview').src = url.trim();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const badgeInput = document.getElementById('badge_text');
        const subInput = document.getElementById('subtitle');

        if (nameInput) {
            nameInput.addEventListener('input', function() {
                document.getElementById('title-preview').innerText = this.value || 'Tên bộ sưu tập';
            });
        }

        if (badgeInput) {
            badgeInput.addEventListener('input', function() {
                document.getElementById('badge-preview').innerText = this.value || 'BST';
            });
        }

        if (subInput) {
            subInput.addEventListener('input', function() {
                document.getElementById('sub-preview').innerText = this.value || 'Mô tả ngắn';
            });
        }

        const urlInput = document.getElementById('image_url');
        if (urlInput && urlInput.value) {
            const urlTabBtn = document.getElementById('url-tab');
            if (urlTabBtn) {
                const tab = new bootstrap.Tab(urlTabBtn);
                tab.show();
                previewUrl(urlInput.value);
            }
        }

        // --- BỘ LỌC VÀ CHỌN SẢN PHẨM CHO BỘ SƯU TẬP ---
        const searchInput = document.getElementById('productSearchInput');
        const categoryFilter = document.getElementById('productCategoryFilter');
        const countBadge = document.getElementById('selected-count-badge');
        const visibleCountEl = document.getElementById('visibleCount');
        const checkAllMaster = document.getElementById('checkAllMaster');
        const btnSelectAllVisible = document.getElementById('btnSelectAllVisible');
        const btnUnselectAllVisible = document.getElementById('btnUnselectAllVisible');
        const productRows = document.querySelectorAll('#productTableBody .product-row');
        const catButtons = document.querySelectorAll('.btn-filter-cat');

        function updateCount() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            if (countBadge) {
                countBadge.innerText = `Đã chọn: ${checkedCount} sản phẩm`;
            }
            updateMasterCheckbox();
        }

        function updateMasterCheckbox() {
            if (!checkAllMaster) return;
            const visibleRows = Array.from(productRows).filter(row => row.style.display !== 'none');
            if (visibleRows.length === 0) {
                checkAllMaster.checked = false;
                checkAllMaster.indeterminate = false;
                return;
            }
            const visibleCheckboxes = visibleRows.map(row => row.querySelector('.product-checkbox'));
            const checkedVisibleCount = visibleCheckboxes.filter(cb => cb.checked).length;

            if (checkedVisibleCount === visibleRows.length) {
                checkAllMaster.checked = true;
                checkAllMaster.indeterminate = false;
            } else if (checkedVisibleCount > 0) {
                checkAllMaster.checked = false;
                checkAllMaster.indeterminate = true;
            } else {
                checkAllMaster.checked = false;
                checkAllMaster.indeterminate = false;
            }
        }

        function filterProducts() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedCat = categoryFilter ? categoryFilter.value : '';

            let visibleCount = 0;

            productRows.forEach(row => {
                const name = row.dataset.productName || '';
                const sku = row.dataset.productSku || '';
                const catId = row.dataset.categoryId || '';

                // Điều kiện từ khóa
                const matchesKeyword = !query || name.includes(query) || sku.includes(query);

                // Điều kiện danh mục
                const matchesCat = !selectedCat || catId === selectedCat;

                if (matchesKeyword && matchesCat) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCountEl) {
                visibleCountEl.innerText = visibleCount;
            }

            updateMasterCheckbox();
        }

        // Event listeners cho các bộ lọc
        if (searchInput) {
            searchInput.addEventListener('input', filterProducts);
        }
        if (categoryFilter) {
            categoryFilter.addEventListener('change', filterProducts);
        }

        // Click badge danh mục trên dòng sản phẩm để lọc nhanh danh mục đó
        catButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const catId = this.dataset.categoryId;
                if (categoryFilter) {
                    categoryFilter.value = catId;
                    filterProducts();
                }
            });
        });

        // Checkbox từng sản phẩm
        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                updateCount();
            });
        });

        // Checkbox Master
        if (checkAllMaster) {
            checkAllMaster.addEventListener('change', function() {
                const isChecked = this.checked;
                productRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const cb = row.querySelector('.product-checkbox');
                        if (cb) cb.checked = isChecked;
                    }
                });
                updateCount();
            });
        }

        // Nút chọn tất cả đang hiển thị
        if (btnSelectAllVisible) {
            btnSelectAllVisible.addEventListener('click', function() {
                productRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const cb = row.querySelector('.product-checkbox');
                        if (cb) cb.checked = true;
                    }
                });
                updateCount();
            });
        }

        // Nút bỏ chọn tất cả đang hiển thị
        if (btnUnselectAllVisible) {
            btnUnselectAllVisible.addEventListener('click', function() {
                productRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const cb = row.querySelector('.product-checkbox');
                        if (cb) cb.checked = false;
                    }
                });
                updateCount();
            });
        }

        updateCount();
    });
</script>
@endsection
