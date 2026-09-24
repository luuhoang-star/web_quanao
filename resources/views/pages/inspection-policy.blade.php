@extends('layouts.app')

@section('title', 'Chính Sách Kiểm Hàng - Atino Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Chính sách kiểm hàng</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <span class="badge bg-warning-subtle text-warning px-3 py-1.5 rounded-pill mb-3 align-self-start fw-bold">Minh Bạch & An Tâm</span>
                <h1 class="fw-bold text-dark display-6 mb-4">Chính Sách Đồng Kiểm Hàng Hóa</h1>
                <p class="text-muted small border-bottom pb-3 mb-4">Cập nhật lần cuối: Tháng 09/2026</p>

                <div class="article-content text-secondary d-flex flex-column gap-4" style="line-height: 1.8; font-size: 0.95rem;">
                    <section>
                        <h5 class="fw-bold text-dark mb-2">1. Quyền Lợi Kiểm Hàng Trước Khi Thanh Toán</h5>
                        <p class="mb-0">
                            Để mang lại sự an tâm tuyệt đối khi mua sắm online, Atino luôn cho phép khách hàng <strong>đồng kiểm kiện hàng</strong> cùng nhân viên giao nhận tại thời điểm nhận hàng trước khi ký nhận hoặc thanh toán tiền mặt.
                        </p>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">2. Nội Dung Đồng Kiểm Được Phép</h5>
                        <ul class="ps-3 mb-0">
                            <li>Mở hộp kiểm tra đúng mẫu mã, màu sắc, kích cỡ và số lượng sản phẩm theo đơn đặt hàng.</li>
                            <li>Kiểm tra tình trạng ngoại quan sản phẩm (không bị rách bẩn, còn nguyên tem nhãn bao bì).</li>
                            <li>Lưu ý: Không hỗ trợ mặc thử đồ hoặc làm biến dạng bao bì niêm phong trước khi thanh toán.</li>
                        </ul>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">3. Xử Lý Khi Có Sai Sót</h5>
                        <p class="mb-0">
                            Nếu kiện hàng bị thiếu, sai sản phẩm hoặc sản phẩm có dấu hiệu hư hỏng trong quá trình vận chuyển, quý khách có quyền từ chối nhận hàng và gọi ngay hotline <strong>0973.797.151</strong> để được gửi kiện hàng mới thay thế ngay lập tức.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
