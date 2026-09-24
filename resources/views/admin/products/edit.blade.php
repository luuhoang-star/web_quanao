@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Sản Phẩm')
@section('page_title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card admin-card shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Chỉnh Sửa: {{ $product->name }}
                    </h5>
                    <code class="text-muted small">SKU: {{ $product->sku }}</code>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- SECTION A: THÔNG TIN CƠ BẢN -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="fa-solid fa-circle-info text-primary me-2"></i>A. THÔNG TIN CƠ BẢN
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label font-weight-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="sku" class="form-label font-weight-medium">Mã SKU</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                                @error('sku')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="category_id" class="form-label font-weight-medium">Danh mục sản phẩm <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->parent_id ? '📂 ' : '📁 ' }}{{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION B: GIÁ & BÁN HÀNG -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="fa-solid fa-tag text-primary me-2"></i>B. GIÁ & BÁN HÀNG
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label font-weight-medium">Giá gốc (VNĐ) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="1000" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sale_price" class="form-label font-weight-medium">Giá khuyến mãi (VNĐ - Bỏ trống nếu không giảm)</label>
                                <div class="input-group">
                                    <input type="number" step="1000" min="0" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" placeholder="290000">
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('sale_price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION C: THAY ĐỔI / TẢI THÊM HÌNH ẢNH -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3" id="product-images">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="fa-solid fa-images text-primary me-2"></i>C. HÌNH ẢNH SẢN PHẨM
                            </h6>
                            <span class="text-muted extra-small">
                                <i class="fa-solid fa-circle-info me-1 text-primary"></i>Phân biệt rõ Ảnh Đại Diện và Thư Viện Ảnh Chi Tiết
                            </span>
                        </div>

                        <div class="row g-4">
                            <!-- 1. ẢNH ĐẠI DIỆN CHÍNH (THUMBNAIL) -->
                            <div class="col-md-6">
                                <div class="bg-white p-3 rounded-3 border h-100 d-flex flex-column justify-content-between shadow-xs">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label for="image" class="form-label fw-bold text-dark mb-0">
                                                <i class="fa-solid fa-image text-primary me-1"></i> 1. Ảnh Đại Diện Chính (Thumbnail)
                                            </label>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small">Ảnh Bìa / 1 ảnh</span>
                                        </div>

                                        <div class="text-muted small mb-3" style="font-size: 0.8rem; line-height: 1.45;">
                                            <div class="mb-1"><strong>• Hiển thị tại:</strong> Ảnh đầu tiên khách nhìn thấy trên <em>Trang chủ, Danh mục sản phẩm, Thẻ tìm kiếm & Giỏ hàng</em>.</div>
                                            <div><strong>• Quy cách:</strong> Mỗi sản phẩm chỉ có <strong>duy nhất 1 ảnh đại diện chính</strong> (khuyên dùng ảnh tỉ lệ vuông hoặc 3:4, chụp tổng thể rõ nét).</div>
                                        </div>

                                        @if($thumbnail)
                                            <div class="d-flex align-items-center gap-3 p-2 bg-light rounded border mb-3">
                                                <img src="{{ $thumbnail->display_url }}" alt="{{ $product->name }}" class="rounded border shadow-sm bg-white" style="width: 72px; height: 72px; object-fit: cover;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80';">
                                                <div>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small mb-1">
                                                        <i class="fa-solid fa-circle-check me-1"></i>Đang sử dụng làm ảnh chính
                                                    </span>
                                                    <div class="text-muted extra-small">Tải ảnh mới bên dưới nếu bạn muốn thay thế ảnh này.</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <label class="form-label extra-small text-muted mb-1 font-weight-medium">
                                            {{ $thumbnail ? 'Chọn ảnh mới để thay thế ảnh đại diện:' : 'Tải lên ảnh đại diện:' }}
                                        </label>
                                        <input type="file" class="form-control form-control-sm @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                        @error('image')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                        <!-- Khung Xem Trước Ảnh Đại Diện Mới (Preview) -->
                                        <div id="thumbnail-preview-container" class="mt-3 p-2.5 bg-light rounded-3 border d-none">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="extra-small fw-bold text-primary">
                                                    <i class="fa-solid fa-eye me-1"></i>Xem trước ảnh đại diện mới sẽ thay thế:
                                                </span>
                                                <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 extra-small" id="btn-remove-thumbnail" title="Bỏ chọn ảnh này">
                                                    <i class="fa-solid fa-xmark me-1"></i>Hủy chọn
                                                </button>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <img id="thumbnail-preview-img" src="" alt="Thumbnail Preview" class="rounded-3 border shadow-sm bg-white object-fit-cover" style="width: 110px; height: 110px;">
                                                <div class="small text-muted" style="font-size: 0.8rem;">
                                                    <div class="fw-semibold text-dark text-truncate mb-1" id="thumbnail-file-name" style="max-width: 200px;">tên-file.jpg</div>
                                                    <div id="thumbnail-file-size" class="text-secondary extra-small mb-1">0 KB</div>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small">
                                                        <i class="fa-solid fa-circle-check me-1"></i>Sẵn sàng cập nhật
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. THƯ VIỆN ẢNH CHI TIẾT (GALLERY) -->
                            <div class="col-md-6">
                                <div class="bg-white p-3 rounded-3 border h-100 d-flex flex-column justify-content-between shadow-xs">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label for="gallery" class="form-label fw-bold text-dark mb-0">
                                                <i class="fa-solid fa-photo-film text-success me-1"></i> 2. Thư Viện Ảnh Chi Tiết (Gallery)
                                            </label>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle extra-small">Ảnh phụ / Chọn nhiều</span>
                                        </div>

                                        <div class="text-muted small mb-3" style="font-size: 0.8rem; line-height: 1.45;">
                                            <div class="mb-1"><strong>• Hiển thị tại:</strong> Xuất hiện bên trong <em>Trang chi tiết sản phẩm</em> khi khách click vào xem món đồ.</div>
                                            <div><strong>• Công dụng:</strong> Chụp các góc khác của trang phục: <em>mặt trước, mặt sau, cận cảnh chất vải, tem mác, mẫu mặc phối đồ</em>.</div>
                                        </div>

                                        <div class="p-2.5 bg-light rounded border mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="small text-dark font-weight-medium">
                                                    <i class="fa-solid fa-layer-group text-secondary me-1.5"></i>Số lượng ảnh chi tiết hiện có:
                                                </span>
                                                <span class="badge bg-dark rounded-pill">{{ $galleryImages->count() }} ảnh</span>
                                            </div>
                                            @if($galleryImages->isNotEmpty())
                                                <div class="text-muted extra-small mt-1">Bạn có thể xem trước hoặc xóa từng ảnh chi tiết ở bảng danh sách bên dưới form.</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label extra-small text-muted mb-1 font-weight-medium">
                                            Tải thêm các ảnh chi tiết mới vào bộ sưu tập:
                                        </label>
                                        <input type="file" class="form-control form-control-sm @error('gallery.*') is-invalid @enderror" id="gallery" name="gallery[]" accept="image/*" multiple>
                                        <div class="form-text text-muted extra-small mt-1">
                                            <i class="fa-solid fa-lightbulb text-warning me-1"></i>Mẹo: Giữ phím <strong>Ctrl</strong> (Windows) hoặc <strong>Cmd</strong> (Mac) để bấm chọn nhiều ảnh cùng lúc.
                                        </div>
                                        @error('gallery.*')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                        <!-- Khung Xem Trước Thư Viện Ảnh Mới Sẽ Tải Thêm (Preview) -->
                                        <div id="gallery-preview-container" class="mt-3 p-2.5 bg-light rounded-3 border d-none">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="extra-small fw-bold text-success">
                                                    <i class="fa-solid fa-images me-1"></i>Xem trước ảnh mới sẽ tải thêm (<span id="gallery-preview-count">0</span> ảnh):
                                                </span>
                                                <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 extra-small" id="btn-clear-gallery" title="Xóa tất cả ảnh vừa chọn">
                                                    <i class="fa-solid fa-trash-can me-1"></i>Xóa hết
                                                </button>
                                            </div>
                                            <div id="gallery-preview-grid" class="d-flex flex-wrap gap-2" style="max-height: 220px; overflow-y: auto;">
                                                <!-- Dynamic items -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION D: THUỘC TÍNH & MÔ TẢ -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="fa-solid fa-list-check text-primary me-2"></i>D. THUỘC TÍNH THỜI TRANG & MÔ TẢ
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="material" class="form-label font-weight-medium">Chất liệu vải / Thành phần</label>
                                <input type="text" class="form-control @error('material') is-invalid @enderror" id="material" name="material" value="{{ old('material', $product->material) }}">
                                @error('material')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label font-weight-medium">Mô tả chi tiết sản phẩm</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <!-- SUBMIT BUTTONS -->
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-light border px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Cập Nhật Sản Phẩm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thư viện ảnh Gallery hiện tại -->
        @if($galleryImages->isNotEmpty())
            <div class="card admin-card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-images me-2 text-warning"></i>Bộ Sưu Tập Ảnh Hiện Tại ({{ $galleryImages->count() }} ảnh)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        @foreach($galleryImages as $img)
                            <div class="col-6 col-sm-4 col-md-3 text-center">
                                <div class="position-relative border rounded p-2 bg-light shadow-xs h-100 d-flex flex-column justify-content-between">
                                    <div class="position-relative">
                                        <img src="{{ $img->display_url }}" alt="Gallery Image" class="w-100 rounded" style="height: 130px; object-fit: cover;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80';">
                                        <form action="{{ route('admin.products.images.destroy', $img->id) }}" method="POST" class="position-absolute top-0 end-0 m-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-circle p-1 px-2" title="Xóa ảnh này" onclick="return confirm('Bạn có chắc muốn xóa ảnh này?')">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Dropdown Gán Màu Sắc Tương Ứng -->
                                    <div class="mt-2 text-start pt-2 border-top">
                                        <label class="form-label extra-small text-muted mb-1 fw-bold d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-palette text-primary"></i> <span>Gán cho màu:</span>
                                        </label>
                                        <select class="form-select form-select-sm select-image-color" data-image-id="{{ $img->id }}" style="font-size: 0.78rem;">
                                            <option value="">-- Dùng chung (Tất cả) --</option>
                                            @foreach($colors as $col)
                                                <option value="{{ $col->id }}" {{ $img->color_id == $col->id ? 'selected' : '' }}>
                                                    {{ $col->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="extra-small text-success mt-1 d-none status-saved-msg">
                                            <i class="fa-solid fa-circle-check"></i> Đã lưu
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const priceInput = document.getElementById('price');
        const salePriceInput = document.getElementById('sale_price');
        const calcBanner = document.getElementById('discountCalcBanner');
        const calcText = document.getElementById('discountCalcText');
        const calcBadge = document.getElementById('discountCalcBadge');

        function updateDiscountCalc() {
            const price = parseFloat(priceInput.value) || 0;
            const salePrice = parseFloat(salePriceInput.value) || 0;

            if (price > 0 && salePrice > 0) {
                if (salePrice > price) {
                    calcBanner.classList.remove('d-none', 'alert-info');
                    calcBanner.classList.add('alert-danger');
                    calcText.textContent = "⚠️ Cảnh báo: Giá khuyến mãi không được lớn hơn giá gốc!";
                    calcBadge.textContent = "Lỗi giá";
                } else {
                    const saveAmount = price - salePrice;
                    const percent = Math.round((saveAmount / price) * 100);
                    calcBanner.classList.remove('d-none', 'alert-danger');
                    calcBanner.classList.add('alert-info');
                    calcText.innerHTML = `Khách hàng tiết kiệm: <strong>${saveAmount.toLocaleString('vi-VN')}đ</strong>`;
                    calcBadge.textContent = `-${percent}%`;
                }
            } else {
                calcBanner.classList.add('d-none');
            }
        }

        if (priceInput && salePriceInput) {
            priceInput.addEventListener('input', updateDiscountCalc);
            salePriceInput.addEventListener('input', updateDiscountCalc);
            updateDiscountCalc();
        }

        // ==========================================
        // XEM TRƯỚC ẢNH ĐẠI DIỆN CHÍNH MỚI (THUMBNAIL)
        // ==========================================
        const imageInput = document.getElementById('image');
        const thumbContainer = document.getElementById('thumbnail-preview-container');
        const thumbImg = document.getElementById('thumbnail-preview-img');
        const thumbName = document.getElementById('thumbnail-file-name');
        const thumbSize = document.getElementById('thumbnail-file-size');
        const btnRemoveThumb = document.getElementById('btn-remove-thumbnail');

        if (imageInput && thumbContainer) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (!file.type.startsWith('image/')) {
                        alert('Vui lòng chọn tệp định dạng hình ảnh hợp lệ (jpg, png, webp...)!');
                        this.value = '';
                        thumbContainer.classList.add('d-none');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        thumbImg.src = e.target.result;
                        thumbName.textContent = file.name;
                        thumbSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
                        thumbContainer.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    thumbContainer.classList.add('d-none');
                }
            });

            if (btnRemoveThumb) {
                btnRemoveThumb.addEventListener('click', function() {
                    imageInput.value = '';
                    thumbImg.src = '';
                    thumbContainer.classList.add('d-none');
                });
            }
        }

        // ==========================================
        // XEM TRƯỚC THƯ VIỆN ẢNH CHI TIẾT MỚI (GALLERY)
        // ==========================================
        const galleryInput = document.getElementById('gallery');
        const galleryContainer = document.getElementById('gallery-preview-container');
        const galleryGrid = document.getElementById('gallery-preview-grid');
        const galleryCount = document.getElementById('gallery-preview-count');
        const btnClearGallery = document.getElementById('btn-clear-gallery');
        let galleryDataTransfer = new DataTransfer();

        function renderGalleryPreview() {
            if (!galleryGrid || !galleryContainer) return;
            galleryGrid.innerHTML = '';
            const files = galleryDataTransfer.files;

            if (files.length === 0) {
                galleryContainer.classList.add('d-none');
                return;
            }

            galleryCount.textContent = files.length;
            galleryContainer.classList.remove('d-none');

            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const item = document.createElement('div');
                    item.className = 'position-relative border rounded-2 bg-white shadow-xs p-1';
                    item.style.width = '78px';
                    item.style.height = '78px';
                    item.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}" class="w-100 h-100 rounded-1 object-fit-cover" title="${file.name} (${(file.size / 1024).toFixed(1)} KB)">
                        <button type="button" class="btn btn-danger position-absolute top-0 end-0 translate-middle-y rounded-circle d-flex align-items-center justify-content-center border border-white" style="width: 20px; height: 20px; font-size: 10px; padding: 0; margin-top: 5px; margin-right: 5px;" title="Bỏ ảnh này">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    `;

                    item.querySelector('button').addEventListener('click', function(ev) {
                        ev.preventDefault();
                        ev.stopPropagation();
                        removeGalleryFile(index);
                    });

                    galleryGrid.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeGalleryFile(indexToRemove) {
            const newDT = new DataTransfer();
            Array.from(galleryDataTransfer.files).forEach((file, i) => {
                if (i !== indexToRemove) {
                    newDT.items.add(file);
                }
            });
            galleryDataTransfer = newDT;
            galleryInput.files = galleryDataTransfer.files;
            renderGalleryPreview();
        }

        if (galleryInput && galleryContainer) {
            galleryInput.addEventListener('change', function() {
                galleryDataTransfer = new DataTransfer();
                Array.from(this.files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        galleryDataTransfer.items.add(file);
                    }
                });
                galleryInput.files = galleryDataTransfer.files;
                renderGalleryPreview();
            });

            if (btnClearGallery) {
                btnClearGallery.addEventListener('click', function() {
                    galleryDataTransfer = new DataTransfer();
                    galleryInput.files = galleryDataTransfer.files;
                    renderGalleryPreview();
                });
            }
        }

        // ==========================================
        // TỰ ĐỘNG LƯU MÀU SẮC CHO HÌNH ẢNH GALLERY (AJAX)
        // ==========================================
        document.querySelectorAll('.select-image-color').forEach(function(select) {
            select.addEventListener('change', function() {
                const imgId = this.getAttribute('data-image-id');
                const colorId = this.value;
                const statusMsg = this.closest('.mt-2').querySelector('.status-saved-msg');

                fetch(`/admin/products/images/${imgId}/color`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ color_id: colorId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && statusMsg) {
                        statusMsg.classList.remove('d-none');
                        setTimeout(() => statusMsg.classList.add('d-none'), 2500);
                    }
                })
                .catch(err => {
                    console.error('Lỗi khi gán màu sắc:', err);
                });
            });
        });
    });
</script>
@endpush
