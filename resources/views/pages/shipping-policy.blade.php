@extends('layouts.app')

@section('title', 'Chính Sách Vận Chuyển & Giao Nhận - Atino Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Chính sách vận chuyển</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <span class="badge bg-primary-subtle text-primary px-3 py-1.5 rounded-pill mb-3 align-self-start fw-bold">Vận Chuyển Toàn Quốc</span>
                <h1 class="fw-bold text-dark display-6 mb-4">Chính Sách Vận Chuyển & Giao Nhận</h1>
                <p class="text-muted small border-bottom pb-3 mb-4">Cập nhật lần cuối: Tháng 09/2026</p>

                <div class="article-content text-secondary d-flex flex-column gap-4" style="line-height: 1.8; font-size: 0.95rem;">
                    <section>
                        <h5 class="fw-bold text-dark mb-2">1. Phạm Vi Áp Dụng</h5>
                        <p class="mb-0">
                            Atino cung cấp dịch vụ giao hàng tận nơi trên toàn bộ 63 tỉnh thành cả nước thông qua các đối tác vận chuyển chuyên nghiệp và uy tín hàng đầu.
                        </p>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">2. Biểu Phí Vận Chuyển</h5>
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Giá trị đơn hàng</th>
                                        <th>Khu vực giao hàng</th>
                                        <th>Cước phí vận chuyển</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Từ 499.000đ trở lên</strong></td>
                                        <td>Toàn quốc</td>
                                        <td><span class="badge bg-success">MIỄN PHÍ (FREESHIP)</span></td>
                                    </tr>
                                    <tr>
                                        <td>Dưới 499.000đ</td>
                                        <td>Toàn quốc</td>
                                        <td>Đồng giá 30.000đ / đơn</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">3. Thời Gian Giao Hàng Dự Kiến</h5>
                        <ul class="ps-3 mb-0">
                            <li><strong>Nội thành Hà Nội & TP.HCM:</strong> Giao trong 24h - 48h làm việc.</li>
                            <li><strong>Các tỉnh/thành phố khác:</strong> Từ 2 - 4 ngày làm việc tùy khu vực địa lý.</li>
                            <li>Lưu ý: Thời gian giao hàng không tính các ngày Chủ Nhật và ngày Lễ, Tết theo quy định nhà nước.</li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
