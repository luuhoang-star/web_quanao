@extends('layouts.admin')

@section('title', 'Thêm Sản Phẩm Mới')
@section('page_title', 'Thêm Sản Phẩm')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card admin-card shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-plus me-2 text-primary"></i>Tạo Sản Phẩm Mới
                </h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="createProductForm">
                    @csrf

                    <!-- SECTION A: THÔNG TIN CƠ BẢN -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="fa-solid fa-circle-info text-primary me-2"></i>A. THÔNG TIN CƠ BẢN
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label font-weight-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Áo sơ mi nam Silk Premium, Đồ ngủ Satin lụa..." required autofocus>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="sku" class="form-label font-weight-medium">Mã SKU sản phẩm</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku') }}" placeholder="Tự động sinh nếu để trống">
                                @error('sku')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="category_id" class="form-label font-weight-medium">Danh mục sản phẩm <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                                    <input type="number" step="1000" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" placeholder="350000" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sale_price" class="form-label font-weight-medium">Giá khuyến mãi (VNĐ - Bỏ trống nếu không giảm)</label>
                                <div class="input-group">
                                    <input type="number" step="1000" min="0" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" placeholder="290000">
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('sale_price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION C: HÌNH ÁNH SẢN PHẨM -->
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
                                    </div>

                                    <div>
                                        <label class="form-label extra-small text-muted mb-1 font-weight-medium">
                                            Chọn tệp ảnh đại diện chính:
                                        </label>
                                        <input type="file" class="form-control form-control-sm @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                        @error('image')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                        <!-- Khung Xem Trước Ảnh Đại Diện (Preview) -->
                                        <div id="thumbnail-preview-container" class="mt-3 p-2.5 bg-light rounded-3 border d-none">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="extra-small fw-bold text-primary">
                                                    <i class="fa-solid fa-eye me-1"></i>Xem trước ảnh đại diện:
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
                                                        <i class="fa-solid fa-circle-check me-1"></i>Sẵn sàng tải lên
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
                                    </div>

                                    <div>
                                        <label class="form-label extra-small text-muted mb-1 font-weight-medium">
                                            Tải lên các ảnh chi tiết (tùy chọn):
                                        </label>
                                        <input type="file" class="form-control form-control-sm @error('gallery.*') is-invalid @enderror" id="gallery" name="gallery[]" accept="image/*" multiple>
                                        <div class="form-text text-muted extra-small mt-1">
                                            <i class="fa-solid fa-lightbulb text-warning me-1"></i>Mẹo: Giữ phím <strong>Ctrl</strong> (Windows) hoặc <strong>Cmd</strong> (Mac) để bấm chọn nhiều ảnh cùng lúc.
                                        </div>
                                        @error('gallery.*')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                        <!-- Khung Xem Trước Thư Viện Ảnh (Preview) -->
                                        <div id="gallery-preview-container" class="mt-3 p-2.5 bg-light rounded-3 border d-none">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="extra-small fw-bold text-success">
                                                    <i class="fa-solid fa-images me-1"></i>Xem trước ảnh chi tiết (<span id="gallery-preview-count">0</span> ảnh):
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
                                <input type="text" class="form-control @error('material') is-invalid @enderror" id="material" name="material" value="{{ old('material') }}" placeholder="Ví dụ: 100% Cotton Premium, Lụa Satin, Jeans Co giãn...">
                                @error('material')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label font-weight-medium">Mô tả chi tiết sản phẩm</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Mô tả về phong cách, form dáng, tính năng sản phẩm...">{{ old('description') }}</textarea>
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
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Sản Phẩm
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
        }

        // ==========================================
        // XEM TRƯỚC ẢNH ĐẠI DIỆN CHÍNH (THUMBNAIL)
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
        // XEM TRƯỚC THƯ VIỆN ẢNH CHI TIẾT (GALLERY)
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
    });
</script>
@endpush
