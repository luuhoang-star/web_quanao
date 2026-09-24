@extends('layouts.app')

@section('title', 'Hướng Dẫn Mua Hàng - Atino Store')
@section('meta_description', 'Hướng dẫn chi tiết từng bước mua sắm trực tuyến nhanh chóng, tiện lợi và an toàn tại hệ thống thời trang Atino.')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Hướng dẫn mua hàng</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Banner Card -->
            <div class="card border-0 rounded-4 bg-light shadow-sm p-4 p-md-5 mb-5 text-center position-relative overflow-hidden">
                <div class="position-relative z-1">
                    <span class="badge bg-dark text-white px-3 py-2 rounded-pill text-uppercase mb-3" style="letter-spacing: 0.05em; font-size: 0.75rem;">Trợ Giúp Mua Sắm</span>
                    <h1 class="fw-bold text-dark display-6 mb-3">Hướng Dẫn Mua Hàng Trực Tuyến</h1>
                    <p class="text-muted mx-auto mb-0" style="max-width: 600px; line-height: 1.6;">
                        Chỉ với 4 bước đơn giản, bạn có thể dễ dàng lựa chọn và sở hữu những trang phục thời thượng nhất từ Atino một cách nhanh chóng và an toàn.
                    </p>
                </div>
            </div>

            <!-- 4 Bước Mua Hàng Trực Quan -->
            <div class="row g-4 mb-5">
                <!-- Bước 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center hover-lift transition-all bg-white">
                        <div class="rounded-circle bg-primary-subtle text-primary mx-auto d-flex align-items-center justify-content-center mb-3 fw-bold fs-4" style="width: 60px; height: 60px;">
                            1
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Tìm Kiếm Sản Phẩm</h6>
                        <p class="text-muted small mb-0">
                            Tìm trang phục ưng ý qua thanh tìm kiếm, danh mục Thời trang Nam/Nữ hoặc lọc theo màu sắc, kích cỡ và mức giá.
                        </p>
                    </div>
                </div>

                <!-- Bước 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center hover-lift transition-all bg-white">
                        <div class="rounded-circle bg-success-subtle text-success mx-auto d-flex align-items-center justify-content-center mb-3 fw-bold fs-4" style="width: 60px; height: 60px;">
                            2
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Chọn Size & Thêm Vào Giỏ</h6>
                        <p class="text-muted small mb-0">
                            Tham khảo bảng kích cỡ chuẩn xác, chọn màu sắc yêu thích và nhấn <strong>"Thêm Vào Giỏ Hàng"</strong> hoặc <strong>"Mua Ngay"</strong>.
                        </p>
                    </div>
                </div>

                <!-- Bước 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center hover-lift transition-all bg-white">
                        <div class="rounded-circle bg-warning-subtle text-warning mx-auto d-flex align-items-center justify-content-center mb-3 fw-bold fs-4" style="width: 60px; height: 60px;">
                            3
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Nhập Thông Tin Giao Hàng</h6>
                        <p class="text-muted small mb-0">
                            Kiểm tra lại giỏ hàng, áp dụng mã giảm giá (nếu có), điền thông tin người nhận và địa chỉ nhận hàng chính xác.
                        </p>
                    </div>
                </div>

                <!-- Bước 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center hover-lift transition-all bg-white">
                        <div class="rounded-circle bg-info-subtle text-info mx-auto d-flex align-items-center justify-content-center mb-3 fw-bold fs-4" style="width: 60px; height: 60px;">
                            4
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Thanh Toán & Nhận Hàng</h6>
                        <p class="text-muted small mb-0">
                            Lựa chọn hình thức thanh toán COD hoặc Chuyển khoản QR. Đơn hàng sẽ được giao tận tay bạn sau 1 - 3 ngày.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Chi tiết các lưu ý -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-5">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-circle-info text-primary me-2"></i>Một Số Lưu Ý Quan Trọng</h4>
                
                <div class="d-flex flex-column gap-3 text-secondary" style="font-size: 0.95rem; line-height: 1.7;">
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-check-circle text-success fs-5 mt-1"></i>
                        <div>
                            <strong class="text-dark">Xác nhận đơn hàng:</strong> Sau khi hoàn tất đặt hàng thành công, hệ thống sẽ gửi tin nhắn SMS/Email và nhân viên CSKH có thể liên hệ để xác nhận đơn hàng đối với các sản phẩm giá trị lớn.
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-check-circle text-success fs-5 mt-1"></i>
                        <div>
                            <strong class="text-dark">Kiểm tra mã vận đơn:</strong> Bạn có thể theo dõi hành trình đơn hàng bất cứ lúc nào qua tính năng <a href="{{ route('pages.order-lookup') }}" class="text-primary fw-semibold text-decoration-underline">Tra Cứu Đơn Hàng</a> trên hệ thống.
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-check-circle text-success fs-5 mt-1"></i>
                        <div>
                            <strong class="text-dark">Chính sách đồng kiểm:</strong> Khách hàng được phép mở gói hàng kiểm tra số lượng và mẫu mã trước khi thanh toán cho nhân viên giao hàng.
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-check-circle text-success fs-5 mt-1"></i>
                        <div>
                            <strong class="text-dark">Hỗ trợ đổi size trong 30 ngày:</strong> Nếu sản phẩm không vừa hoặc có lỗi từ nhà sản xuất, bạn được hỗ trợ đổi size/mẫu hoàn toàn miễn phí tại nhà.
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <span class="text-muted small">Cần hỗ trợ trực tiếp từ nhân viên tư vấn?</span>
                    <a href="tel:0973797151" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">
                        <i class="fa-solid fa-phone me-2"></i>Hotline: 0973.797.151
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
