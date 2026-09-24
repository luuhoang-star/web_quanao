@extends('layouts.admin')

@section('title', 'Tạo đợt Flash Sale mới')
@section('page_title', 'Tạo Đợt Flash Sale Mới')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-plus-circle text-danger me-2"></i>Tạo Đợt Flash Sale Mới
            </h4>
            <p class="text-muted small mb-0">Thiết lập chương trình giảm giá Flash Sale cho sản phẩm trong cửa hàng.</p>
        </div>
        <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-outline-secondary px-3 rounded-pill fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i>Quay lại danh sách
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Có lỗi xảy ra, vui lòng kiểm tra lại:</div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="admin-card p-4">
                <form action="{{ route('admin.flash-sales.store') }}" method="POST" id="flash-sale-form">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <label for="product_id" class="form-label fw-bold text-dark">Chọn Sản Phẩm Áp Dụng <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select form-select-lg @error('product_id') is-invalid @enderror" required>
                                <option value="" data-price="0">-- Chọn sản phẩm --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-price="{{ (float) $p->price }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} (Giá gốc: {{ number_format($p->price) }}đ - SKU: {{ $p->sku ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Chỉ các sản phẩm đang hiển thị (Active) mới xuất hiện trong danh sách.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="sale_price" class="form-label fw-bold text-dark">Giá Giảm Flash Sale (VNĐ) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="sale_price" id="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price') }}" placeholder="Nhập giá Flash Sale..." step="any" min="1" required>
                                <span class="input-group-text bg-light fw-bold text-muted">đ</span>
                            </div>
                            <div class="form-text">Phải nhỏ hơn giá gốc sản phẩm.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="quantity" class="form-label fw-bold text-dark">Số Lượng Giới Hạn Flash Sale</label>
                            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 10) }}" placeholder="Mặc định: 10" min="1">
                            <div class="form-text">Số lượng tối đa mở bán Flash Sale. Hết hàng hệ thống sẽ tự dừng.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="start_time" class="form-label fw-bold text-dark">Thời Gian Bắt Đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="end_time" class="form-label fw-bold text-dark">Thời Gian Kết Thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', now()->addHours(24)->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="col-12 text-end pt-2">
                            <button type="submit" class="btn btn-danger px-4 rounded-pill fw-bold shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Lưu đợt Flash Sale
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side: Realtime Discount Calculator & Info -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="admin-card p-4 position-sticky" style="top: 90px;">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-calculator text-primary me-2"></i>Xem Trước Mức Giảm</h5>
                
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Giá gốc sản phẩm:</span>
                        <span class="fw-bold text-dark" id="preview-original-price">0đ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Giá Flash Sale:</span>
                        <span class="fw-bold text-danger" id="preview-sale-price">0đ</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark small">Phần trăm giảm:</span>
                        <span class="badge bg-danger fs-6" id="preview-discount-percent">-0%</span>
                    </div>
                </div>

                <div class="alert alert-info border-0 rounded-3 mb-0 small">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Chương trình Flash Sale sẽ được đếm ngược thời gian thực trên giao diện Cửa hàng & Trang chủ khi đến khung giờ áp dụng.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const productSelect = document.getElementById('product_id');
        const salePriceInput = document.getElementById('sale_price');
        const previewOriginalPrice = document.getElementById('preview-original-price');
        const previewSalePrice = document.getElementById('preview-sale-price');
        const previewDiscountPercent = document.getElementById('preview-discount-percent');

        function updatePreview() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const originalPrice = parseFloat(selectedOption ? selectedOption.getAttribute('data-price') : 0) || 0;
            const salePrice = parseFloat(salePriceInput.value) || 0;

            previewOriginalPrice.textContent = originalPrice > 0 ? new Intl.NumberFormat('vi-VN').format(originalPrice) + 'đ' : '0đ';
            previewSalePrice.textContent = salePrice > 0 ? new Intl.NumberFormat('vi-VN').format(salePrice) + 'đ' : '0đ';

            if (originalPrice > 0 && salePrice > 0 && salePrice < originalPrice) {
                const percent = Math.round(((originalPrice - salePrice) / originalPrice) * 100);
                previewDiscountPercent.textContent = '-' + percent + '%';
                previewDiscountPercent.className = 'badge bg-danger fs-6';
            } else if (salePrice >= originalPrice && originalPrice > 0 && salePrice > 0) {
                previewDiscountPercent.textContent = 'Giá giảm phải < Giá gốc!';
                previewDiscountPercent.className = 'badge bg-warning text-dark small';
            } else {
                previewDiscountPercent.textContent = '-0%';
                previewDiscountPercent.className = 'badge bg-secondary fs-6';
            }
        }

        productSelect.addEventListener('change', updatePreview);
        salePriceInput.addEventListener('input', updatePreview);
        updatePreview();
    });
</script>
@endpush
