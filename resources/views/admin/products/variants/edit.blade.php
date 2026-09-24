@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Biến Thể - ' . $product->name)
@section('page_title', 'Chỉnh Sửa Biến Thể')

@section('content')
<!-- BREADCRUMB NAVIGATION -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb extra-small">
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none">Sản phẩm</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.edit', $product->id) }}" class="text-decoration-none">{{ Str::limit($product->name, 25) }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.variants.index', $product->id) }}" class="text-decoration-none">Biến thể</a></li>
        <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa #{{ $variant->id }}</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card admin-card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Chỉnh Sửa Biến Thể</h6>
                    <span class="text-muted small">Sản phẩm: <strong>{{ $product->name }}</strong></span>
                </div>
                <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.products.variants.update', [$product->id, $variant->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- BƯỚC 1: CHỌN MÀU SẮC (COLOR) -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="color_id" class="form-label font-weight-bold text-dark mb-0">
                                <span class="badge bg-primary me-2">1</span>Chọn Màu Sắc (Color) <span class="text-muted fw-normal">(Tùy chọn)</span>
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickAddColorModal">
                                <i class="fa-solid fa-plus me-1"></i>Thêm màu mới
                            </button>
                        </div>
                        <select class="form-select form-select-lg @error('color_id') is-invalid @enderror" id="color_id" name="color_id">
                            <option value="">-- Không chọn (Không gán màu sắc) --</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}" {{ old('color_id', $variant->color_id) == $color->id ? 'selected' : '' }}>
                                    🎨 {{ $color->name }} ({{ $color->hex_code ?? '#000000' }})
                                </option>
                            @endforeach
                        </select>
                        @error('color_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- BƯỚC 2: CHỌN KÍCH CỠ (SIZE) -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <label for="size_id" class="form-label font-weight-bold text-dark mb-2">
                            <span class="badge bg-primary me-2">2</span>Chọn Kích Cỡ (Size) <span class="text-muted fw-normal">(Tùy chọn)</span>
                        </label>
                        <select class="form-select form-select-lg @error('size_id') is-invalid @enderror" id="size_id" name="size_id">
                            <option value="">-- Không chọn (Không gán kích cỡ / Freesize) --</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}" {{ old('size_id', $variant->size_id) == $size->id ? 'selected' : '' }}>
                                    📏 Size: {{ $size->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('size_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- BƯỚC 3: NHẬP SỐ LƯỢNG KHO (STOCK) -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <label for="stock_quantity" class="form-label font-weight-bold text-dark mb-2">
                            <span class="badge bg-primary me-2">3</span>Số Lượng Tồn Kho (Stock) <span class="text-danger">*</span>
                        </label>
                        <input type="number" min="0" class="form-control form-control-lg @error('stock_quantity') is-invalid @enderror" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $variant->stock_quantity) }}" required>
                        @error('stock_quantity')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- BƯỚC 4: NHẬP MÃ SKU (SKU) -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <label for="sku" class="form-label font-weight-bold text-dark mb-2">
                            <span class="badge bg-primary me-2">4</span>Mã SKU Biến Thể (SKU)
                        </label>
                        <input type="text" class="form-control form-control-lg @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $variant->sku) }}" required>
                        @error('sku')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- BƯỚC 5: NHẬP GIÁ BÁN (PRICE) -->
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <label class="form-label font-weight-bold text-dark mb-2">
                            <span class="badge bg-primary me-2">5</span>Giá Bán (Price & Sale Price) <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label small text-muted">Giá gốc (VNĐ) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="1000" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $variant->price) }}" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="sale_price" class="form-label small text-muted">Giá khuyến mãi (VNĐ - Bỏ trống nếu không giảm)</label>
                                <div class="input-group">
                                    <input type="number" step="1000" min="0" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price', $variant->sale_price) }}" placeholder="290000">
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('sale_price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn btn-light border px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Cập Nhật Biến Thể
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Nhanh Màu Sắc -->
<div class="modal fade" id="quickAddColorModal" tabindex="-1" aria-labelledby="quickAddColorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light py-3">
                <h6 class="modal-title fw-bold text-dark" id="quickAddColorModalLabel">
                    <i class="fa-solid fa-palette me-2 text-primary"></i>Thêm Nhanh Màu Sắc Mới
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddColorForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="quick_color_name" class="form-label font-weight-medium">Tên Màu Sắc <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_color_name" name="name" placeholder="Ví dụ: Xanh Rêu, Be Nhạt, Đen Nhám..." required>
                        <div class="invalid-feedback" id="quick_color_name_err"></div>
                    </div>
                    <div class="mb-3">
                        <label for="quick_hex_code" class="form-label font-weight-medium">Mã Màu (HEX) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color border-end-0" id="quick_color_picker" value="#2563EB" title="Chọn màu trực quan">
                            <input type="text" class="form-control font-monospace text-uppercase" id="quick_hex_code" name="hex_code" value="#2563EB" placeholder="#2563EB" required>
                            <span class="input-group-text bg-light">
                                <span id="quick_preview_swatch" class="d-inline-block rounded-circle" style="width: 22px; height: 22px; background-color: #2563EB; border: 1px solid #cbd5e1;"></span>
                            </span>
                        </div>
                        <div class="invalid-feedback d-block" id="quick_hex_code_err"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-3" id="btnSaveQuickColor">
                        <i class="fa-solid fa-plus me-1"></i> Lưu & Chọn ngay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const quickPicker = document.getElementById('quick_color_picker');
        const quickHex = document.getElementById('quick_hex_code');
        const quickSwatch = document.getElementById('quick_preview_swatch');
        const quickForm = document.getElementById('quickAddColorForm');
        const colorSelect = document.getElementById('color_id');
        const quickModalEl = document.getElementById('quickAddColorModal');
        const quickModal = new bootstrap.Modal(quickModalEl);

        function syncQuickSwatch(hexVal) {
            let clean = hexVal.trim();
            if (!clean.startsWith('#')) clean = '#' + clean;
            if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i.test(clean)) {
                quickPicker.value = clean;
                quickSwatch.style.backgroundColor = clean;
            }
        }

        if (quickPicker && quickHex) {
            quickPicker.addEventListener('input', function() {
                const val = this.value.toUpperCase();
                quickHex.value = val;
                quickSwatch.style.backgroundColor = val;
            });
            quickHex.addEventListener('input', function() {
                syncQuickSwatch(this.value);
            });
        }

        if (quickForm) {
            quickForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = document.getElementById('quick_color_name').value.trim();
                const hex = quickHex.value.trim();
                const saveBtn = document.getElementById('btnSaveQuickColor');

                if (!name || !hex) return;

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang lưu...';

                fetch("{{ route('admin.colors.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: name,
                        hex_code: hex,
                        is_active: 1
                    })
                })
                .then(res => res.json())
                .then(data => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fa-solid fa-plus me-1"></i> Lưu & Chọn ngay';

                    if (data.success && data.data) {
                        const newColor = data.data;
                        const opt = new Option(`🎨 ${newColor.name} (${newColor.hex_code})`, newColor.id, true, true);
                        colorSelect.add(opt);
                        colorSelect.value = newColor.id;

                        quickForm.reset();
                        quickModal.hide();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: `Đã thêm màu "${newColor.name}" thành công!`,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    } else {
                        const msg = data.message || 'Không thể tạo màu mới';
                        alert(msg);
                    }
                })
                .catch(err => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fa-solid fa-plus me-1"></i> Lưu & Chọn ngay';
                    alert('Có lỗi xảy ra khi tạo màu mới!');
                });
            });
        }
    });
</script>
@endpush
