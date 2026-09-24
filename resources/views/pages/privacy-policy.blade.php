@extends('layouts.app')

@section('title', 'Chính Sách Bảo Mật Thông Tin - Atino Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Chính sách bảo mật</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <span class="badge bg-primary-subtle text-primary px-3 py-1.5 rounded-pill mb-3 align-self-start fw-bold">Chính Sách & Quy Định</span>
                <h1 class="fw-bold text-dark display-6 mb-4">Chính Sách Bảo Mật Thông Tin</h1>
                <p class="text-muted small border-bottom pb-3 mb-4">Cập nhật lần cuối: Tháng 09/2026</p>

                <div class="article-content text-secondary d-flex flex-column gap-4" style="line-height: 1.8; font-size: 0.95rem;">
                    <section>
                        <h5 class="fw-bold text-dark mb-2">1. Mục Đích Thu Thập Thông Tin</h5>
                        <p class="mb-0">
                            Atino thu thập thông tin khách hàng (họ tên, số điện thoại, địa chỉ nhận hàng, email) nhằm phục vụ công tác xử lý đơn hàng, liên hệ giao nhận, gửi thông báo trạng thái vận chuyển và cung cấp các chính sách bảo hành, đổi trả sản phẩm chu đáo nhất.
                        </p>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">2. Phạm Vi Sử Dụng Thông Tin</h5>
                        <p class="mb-0">
                            Thông tin của quý khách chỉ được sử dụng trong phạm vi nội bộ công ty và cung cấp cho đối tác bưu chính vận chuyển (Giao Hàng Nhanh, Viettel Post, v.v.) để giao hàng tới địa chỉ yêu cầu. Chúng tôi cam kết không bán, chia sẻ hay trao đổi thông tin khách hàng cho bất kỳ bên thứ ba nào vì mục đích thương mại.
                        </p>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">3. Thời Gian Lưu Trữ Thông Tin</h5>
                        <p class="mb-0">
                            Dữ liệu cá nhân của khách hàng sẽ được lưu trữ bảo mật trên hệ thống máy chủ của Atino cho đến khi có yêu cầu hủy bỏ từ phía khách hàng hoặc khách hàng tự đăng nhập để xóa tài khoản cá nhân.
                        </p>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">4. Cam Kết Bảo Mật Tuyệt Đối</h5>
                        <p class="mb-0">
                            Hệ thống website của Atino được tích hợp giao thức mã hóa SSL 256-bit chuẩn quốc tế, bảo vệ an toàn toàn bộ dữ liệu trao đổi giữa trình duyệt của khách hàng và máy chủ. Trong trường hợp máy chủ lưu trữ bị hacker tấn công, chúng tôi có trách nhiệm thông báo vụ việc cho cơ quan chức năng điều tra xử lý và thông báo cho quý khách hàng được biết.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
