@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm')
@section('page_title', 'Sản Phẩm Thời Trang')

@section('content')
    <!-- 1. Header & Summary Cards -->
    @include('admin.products.components.product-stats')

    <!-- 2. Search & Filter Bar -->
    @include('admin.products.components.product-filter')

    <!-- 3. Product Table -->
    @include('admin.products.components.product-table')
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Xóa đơn lẻ bằng SweetAlert2
        $('.btn-delete-single').on('click', function() {
            const prodId = $(this).data('id');
            const prodName = $(this).data('name');

            Swal.fire({
                title: 'Xác nhận xóa sản phẩm?',
                text: `Bạn có chắc chắn muốn xóa sản phẩm "${prodName}" không?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#single-delete-form-${prodId}`).submit();
                }
            });
        });

        // Bật/tắt trạng thái Hiển thị / Ẩn sản phẩm trực tiếp khi click ở mục Trạng thái
        $(document).on('click', '.btn-toggle-product-status', function(e) {
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
                            btn.html('<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">● Đã ẩn</span>');
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
                        Swal.fire('Lỗi!', response.message || 'Không thể đổi trạng thái sản phẩm.', 'error');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Đã xảy ra lỗi khi đổi trạng thái sản phẩm!';
                    Swal.fire('Lỗi!', msg, 'error');
                }
            });
        });
    });
</script>
@endpush
