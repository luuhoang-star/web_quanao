<!-- Offcanvas Cart Drawer (Giỏ Hàng Trượt Từ Lề Phải) -->
<div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel" style="width: 420px; max-width: 90vw;">
    <div class="offcanvas-header border-bottom py-3 px-4 bg-white">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-bag-shopping fs-5 text-dark"></i>
            <h5 class="offcanvas-title fw-bold text-dark mb-0" id="cartDrawerLabel" style="font-size: 1.1rem; letter-spacing: -0.02em;">
                Giỏ Hàng Của Bạn (<span id="drawer-cart-count">0</span>)
            </h5>
        </div>
        <button type="button" class="btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4 d-flex flex-column justify-content-between bg-white">
        <!-- Danh Sách Sản Phẩm Trong Giỏ Hàng -->
        <div id="drawer-cart-items-container" class="overflow-auto pe-1 flex-grow-1" style="max-height: calc(100vh - 260px);">
            <!-- Dynamic Cart Items Rendered via JavaScript / AJAX -->
            <div id="drawer-cart-empty" class="text-center py-5">
                <i class="fa-solid fa-bag-shopping display-3 text-secondary opacity-25 mb-3 d-block"></i>
                <h6 class="fw-bold text-dark mb-1">Giỏ hàng đang trống</h6>
                <p class="small text-muted mb-4">Hãy chọn thêm sản phẩm thời trang bạn yêu thích vào giỏ hàng.</p>
                <button type="button" class="btn btn-outline-dark rounded-pill btn-sm px-4 fw-semibold" data-bs-dismiss="offcanvas">
                    Tiếp tục mua sắm
                </button>
            </div>
            
            <div id="drawer-cart-list" class="d-flex flex-column gap-3 d-none">
                <!-- Single Item Dynamic Template -->
            </div>
        </div>

        <!-- Footer Giỏ Hàng (Tổng Tiền & Nút Thanh Toán) -->
        <div id="drawer-cart-footer" class="border-top pt-3 mt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">Tạm tính:</span>
                <span id="drawer-subtotal-price" class="fw-extrabold text-danger fs-5">0đ</span>
            </div>
            <p class="extra-small text-muted mb-3"><i class="fa-solid fa-truck-fast me-1 text-success"></i> Miễn phí vận chuyển cho đơn hàng từ 499.000đ</p>
            
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('orders.checkout') }}" class="btn btn-dark btn-lg w-100 rounded-pill fw-bold py-3 text-uppercase shadow-sm" style="font-size: 0.9rem; letter-spacing: 0.03em;">
                    Thanh Toán Ngay <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
                <a href="{{ route('cart.index') }}" class="btn btn-light btn-sm w-100 rounded-pill text-secondary fw-semibold py-2">
                    Xem chi tiết giỏ hàng
                </a>
            </div>
        </div>
    </div>
</div>
