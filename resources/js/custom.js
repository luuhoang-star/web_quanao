/**
 * Custom JavaScript logic for Uniqlo/H&M style Minimalist Interface
 */

document.addEventListener("DOMContentLoaded", function () {
    // 1. Initialize tooltips and popovers if any
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 2. Add to Cart Ajax handler
    $(document).on('click', '.btn-add-to-cart', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const productId = $btn.data('id');

        if (!productId) return;

        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: "/cart/add",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                product_id: productId,
                quantity: 1
            },
            success: function (response) {
                $btn.prop('disabled', false).html(originalHtml);
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message || 'Đã thêm sản phẩm vào giỏ hàng!',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                    if (response.count !== undefined) {
                        $('.topbar-cart-count').text(response.count);
                    }
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(originalHtml);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Không thể thêm vào giỏ hàng. Vui lòng thử lại!',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            }
        });
    });

    // 3. Wishlist toggle handler
    $(document).on('click', '.btn-toggle-wishlist', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const productId = $btn.data('id');

        if (!productId) return;

        $.ajax({
            url: "/wishlist/toggle",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                product_id: productId
            },
            success: function (response) {
                if (response.attached !== undefined) {
                    const $icon = $btn.find('i');
                    if (response.attached) {
                        $icon.removeClass('fa-regular text-dark').addClass('fa-solid text-danger');
                    } else {
                        $icon.removeClass('fa-solid text-danger').addClass('fa-regular text-dark');
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                }
            }
        });
    });
});
