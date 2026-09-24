@extends('layouts.admin')

@section('title', 'Quản Lý Biến Thể - ' . $product->name)
@section('page_title', 'Biến Thể Sản Phẩm')

@section('content')
<!-- Header thông tin sản phẩm & Điều hướng -->
<div class="card admin-card mb-4 shadow-sm">
    <div class="card-body p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    {{ $product->category->name ?? 'N/A' }}
                </span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                    {{ $variants->total() }} biến thể hiện có
                </span>
            </div>
            <h4 class="fw-bold mb-1 text-dark">{{ $product->name }}</h4>
            <div class="small text-muted">
                <span>Mã SKU gốc: <code>{{ $product->sku }}</code></span> |
                <span>Giá gốc: <strong>{{ number_format($product->price) }}đ</strong></span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Danh sách sản phẩm
            </a>
            <a href="{{ route('admin.products.variants.create', $product->id) }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Thêm Biến Thể Mới
            </a>
        </div>
    </div>
</div>

<!-- SECTION: DANH SÁCH BIẾN THỂ & BULK ACTIONS -->
<div class="card admin-card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-list-ul me-2 text-primary"></i>Bảng Quản Lý Biến Thể
        </h6>
    </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light extra-small">
                    <tr>
                        <th class="ps-3">Màu Sắc (Color)</th>
                        <th>Kích Cỡ (Size)</th>
                        <th>Mã SKU</th>
                        <th>Giá Gốc</th>
                        <th>Giá Khuyến Mãi</th>
                        <th>Tồn Kho</th>
                        <th>Trạng Thái</th>
                        <th class="text-end pe-3">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $variant)
                        <tr>
                            <td class="ps-3">
                                @if($variant->color)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-inline-block rounded-circle border shadow-sm" style="width: 22px; height: 22px; background-color: {{ $variant->color->hex_code ?? '#000000' }};"></span>
                                        <span class="fw-semibold text-dark">{{ $variant->color->name }}</span>
                                    </div>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Không gán màu</span>
                                @endif
                            </td>
                            <td>
                                @if($variant->size)
                                    <span class="badge bg-dark px-3 py-1.5 fs-6 font-weight-bold">
                                        {{ $variant->size->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Freesize / Không gán</span>
                                @endif
                            </td>
                            <td><code>{{ $variant->sku }}</code></td>
                            <td class="fw-semibold text-dark">{{ number_format($variant->price) }}đ</td>
                            <td>
                                @php
                                    $vSale = $variant->sale_price && $variant->sale_price > 0 && $variant->sale_price < $variant->price ? $variant->sale_price : ($product->sale_price && $product->sale_price > 0 && $product->sale_price < $variant->price ? $product->sale_price : null);
                                @endphp
                                @if($vSale)
                                    <div class="fw-bold text-danger">{{ number_format($vSale) }}đ</div>
                                    @php
                                        $vDiscount = round((($variant->price - $vSale) / $variant->price) * 100);
                                    @endphp
                                    @if($vDiscount > 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small">
                                            🔥 Giảm -{{ $vDiscount }}%
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted small">Không áp dụng</span>
                                @endif
                            </td>
                            <td>
                                @if($variant->stock_quantity > 10)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        Còn {{ $variant->stock_quantity }}
                                    </span>
                                @elseif($variant->stock_quantity > 0)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        Sắp hết ({{ $variant->stock_quantity }})
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        Hết hàng (0)
                                    </span>
                                @endif
                            </td>
                            <td>
                                <button type="button" 
                                    class="btn btn-sm border-0 p-0 btn-toggle-variant-status" 
                                    data-id="{{ $variant->id }}" 
                                    data-url="{{ route('admin.products.variants.toggleStatus', [$product->id, $variant->id]) }}" 
                                    title="Click để đổi trạng thái Hiển thị / Ẩn"
                                    style="cursor: pointer;">
                                    @if($variant->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Hiển thị</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Đang ẩn</span>
                                    @endif
                                </button>
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.products.variants.edit', [$product->id, $variant->id]) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-delete-var" data-id="{{ $variant->id }}" data-sku="{{ $variant->sku }}" title="Xóa">
                                        <i class="fa-solid fa-trash-can me-1"></i>Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-layer-group fa-2x mb-2 text-secondary d-block"></i>
                                Sản phẩm này chưa có biến thể nào. Bạn có thể sử dụng công cụ sinh tự động phía trên để khởi tạo biến thể.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @if($variants->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $variants->links() }}
        </div>
    @endif
</div>

<!-- Forms Xóa đơn lẻ -->
@foreach($variants as $variant)
    <form id="delete-form-{{ $variant->id }}" action="{{ route('admin.products.variants.destroy', [$product->id, $variant->id]) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Delete single
        $('.btn-delete-var').on('click', function() {
            const varId = $(this).data('id');
            const varSku = $(this).data('sku');

            Swal.fire({
                title: 'Xác nhận xóa biến thể?',
                text: `Bạn có chắc chắn muốn xóa biến thể "${varSku}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#delete-form-${varId}`).submit();
                }
            });
        });

        // Click đổi nhanh trạng thái biến thể bằng AJAX
        $(document).on('click', '.btn-toggle-variant-status', function(e) {
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
                            btn.html('<span class="badge bg-success-subtle text-success border border-success-subtle">Hiển thị</span>');
                        } else {
                            btn.html('<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Đang ẩn</span>');
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
                        Swal.fire('Lỗi!', response.message || 'Không thể đổi trạng thái biến thể.', 'error');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Đã xảy ra lỗi khi đổi trạng thái biến thể!';
                    Swal.fire('Lỗi!', msg, 'error');
                }
            });
        });
    });
</script>
@endpush
