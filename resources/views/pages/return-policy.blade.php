@extends('layouts.app')

@section('title', 'Chính Sách Đổi Trả 30 Ngày - Atino Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Chính sách đổi trả 30 ngày</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill mb-3 align-self-start fw-bold">Đổi Trả Dễ Dàng</span>
                <h1 class="fw-bold text-dark display-6 mb-4">Chính Sách Đổi Trả 30 Ngày Tại Atino</h1>
                <p class="text-muted small border-bottom pb-3 mb-4">Cập nhật lần cuối: Tháng 09/2026</p>

                <div class="article-content text-secondary d-flex flex-column gap-4" style="line-height: 1.8; font-size: 0.95rem;">
                    <section>
                        <h5 class="fw-bold text-dark mb-2">1. Điều Kiện Áp Dụng Đổi Trả</h5>
                        <ul class="ps-3 mb-0">
                            <li>Sản phẩm còn nguyên tem mác, chưa qua sử dụng, giặt ủi hoặc chỉnh sửa.</li>
                            <li>Thời gian yêu cầu đổi trả không quá <strong>30 ngày</strong> kể từ thời điểm nhận hàng thành công.</li>
                            <li>Đầy đủ hóa đơn mua hàng hoặc thông tin số điện thoại đặt hàng trên hệ thống.</li>
                        </ul>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">2. Các Trường Hợp Hỗ Trợ Đổi Hàng</h5>
                        <ul class="ps-3 mb-0">
                            <li>Đổi kích cỡ (size) hoặc đổi màu sắc khi mặc không vừa hoặc không hợp ý.</li>
                            <li>Đổi sang sản phẩm khác có giá trị tương đương hoặc cao hơn (bù chênh lệch).</li>
                            <li>Sản phẩm gặp lỗi kỹ thuật từ nhà sản xuất (rách chỉ, lỗi vải, khóa hỏng...).</li>
                        </ul>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">3. Quy Trình Đổi Hàng Tại Nhà Nhanh Gọn</h5>
                        <p class="mb-2">
                            Bạn không cần phải mang ra bưu cục gửi hàng, Atino hỗ trợ đổi hàng tận nơi:
                        </p>
                        <ol class="ps-3 mb-0">
                            <li>Liên hệ hotline <strong>0973.797.151</strong> hoặc nhắn tin yêu cầu đổi hàng.</li>
                            <li>Nhân viên bưu tá sẽ mang sản phẩm mới tới tận nhà bạn.</li>
                            <li>Bạn nhận sản phẩm mới và giao lại sản phẩm cần đổi cho bưu tá tại cùng thời điểm.</li>
                        </ol>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
