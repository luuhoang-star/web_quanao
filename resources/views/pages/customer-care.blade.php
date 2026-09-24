@extends('layouts.app')

@section('title', 'Trung Tâm Chăm Sóc Khách Hàng - Atino Store')
@section('meta_description', 'Trung tâm chăm sóc khách hàng Atino Store: Hỗ trợ 24/7, hotline, giải đáp thắc mắc, câu hỏi thường gặp về đơn hàng và sản phẩm.')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Chăm sóc khách hàng</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Card -->
            <div class="card border-0 rounded-4 bg-light shadow-sm p-4 p-md-5 mb-5 text-center position-relative overflow-hidden">
                <span class="badge bg-dark text-white px-3 py-2 rounded-pill text-uppercase mb-3 mx-auto" style="letter-spacing: 0.05em; font-size: 0.75rem;">Hỗ Trợ Tận Tâm</span>
                <h1 class="fw-bold text-dark display-6 mb-3">Trung Tâm Chăm Sóc Khách Hàng Atino</h1>
                <p class="text-muted mx-auto mb-0" style="max-width: 600px; line-height: 1.6;">
                    Chúng tôi luôn lắng nghe và sẵn sàng đồng hành cùng bạn trên mọi trải nghiệm mua sắm thời trang.
                </p>
            </div>

            <!-- Các Kênh Liên Hệ Nhanh -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                        <div class="rounded-circle bg-primary-subtle text-primary mx-auto d-flex align-items-center justify-content-center mb-3 fs-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Hotline Hỗ Trợ</h6>
                        <p class="text-muted extra-small mb-2">Tất cả các ngày trong tuần (8:30 - 22:00)</p>
                        <a href="tel:0973797151" class="text-primary fw-bold fs-5 text-decoration-none">0973.797.151</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                        <div class="rounded-circle bg-success-subtle text-success mx-auto d-flex align-items-center justify-content-center mb-3 fs-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Hòm Thư Điện Tử</h6>
                        <p class="text-muted extra-small mb-2">Phản hồi giải đáp trong vòng 24 giờ</p>
                        <a href="mailto:luuvanhoang2k4@gmail.com" class="text-success fw-bold text-decoration-none">luuvanhoang2k4@gmail.com</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                        <div class="rounded-circle bg-warning-subtle text-warning mx-auto d-flex align-items-center justify-content-center mb-3 fs-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Trụ Sở Văn Phòng</h6>
                        <p class="text-muted extra-small mb-2">Tiếp nhận khiếu nại & văn phòng điều hành</p>
                        <span class="text-dark small fw-semibold">Tầng 5, Tòa Nhà Thời Trang, Hà Nội</span>
                    </div>
                </div>
            </div>

            <!-- Câu Hỏi Thường Gặp (Accordion FAQs) -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-5">
                <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-circle-question text-primary me-2"></i>Câu Hỏi Thường Gặp (FAQs)</h4>

                <div class="accordion accordion-flush" id="faqAccordion">
                    <!-- Q1 -->
                    <div class="accordion-item border-bottom py-2">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                1. Tôi muốn đổi kích cỡ (size) hoặc đổi màu sản phẩm thì làm thế nào?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                                Atino hỗ trợ đổi size/màu trong vòng 30 ngày kể từ ngày nhận hàng. Bạn chỉ cần liên hệ hotline 0973.797.151 hoặc nhắn tin cho chúng tôi. Nhân viên bưu tá sẽ mang sản phẩm mới đến tận nhà đổi cho bạn và thu hồi sản phẩm cũ cùng lúc.
                            </div>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="accordion-item border-bottom py-2">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                2. Thời gian giao hàng dự kiến mất bao lâu?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                                - Khu vực nội thành Hà Nội & TP.HCM: Nhận hàng sau 1 - 2 ngày làm việc.<br>
                                - Các tỉnh thành khác: Giao trong vòng 2 - 4 ngày làm việc. Bạn hoàn toàn có thể theo dõi vị trí kiện hàng trên trang tra cứu đơn hàng.
                            </div>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="accordion-item border-bottom py-2">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                3. Đơn hàng từ bao nhiêu thì được miễn phí vận chuyển?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                                Atino áp dụng chính sách <strong>Freeship toàn quốc</strong> cho tất cả đơn hàng có giá trị từ <strong>499.000đ</strong> trở lên. Đối với đơn dưới 499.000đ, phí vận chuyển đồng giá ưu đãi là 30.000đ trên toàn quốc.
                            </div>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="accordion-item border-bottom py-2">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                4. Tôi có được kiểm tra thử đồ trước khi thanh toán tiền không?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                                Bạn được quyền mở hộp kiểm tra đúng mẫu mã, màu sắc, kích cỡ và số lượng sản phẩm. Nếu có bất kỳ sai lệch nào so với đơn đặt hàng, bạn có quyền từ chối nhận hàng mà không tốn bất kỳ chi phí nào.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
