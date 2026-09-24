@extends('layouts.app')

@section('title', 'Điều Khoản Dịch Vụ - Atino Store')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Điều khoản dịch vụ</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 rounded-pill mb-3 align-self-start fw-bold">Điều Khoản Sử Dụng</span>
                <h1 class="fw-bold text-dark display-6 mb-4">Điều Khoản Dịch Vụ Mua Sắm</h1>
                <p class="text-muted small border-bottom pb-3 mb-4">Cập nhật lần cuối: Tháng 09/2026</p>

                <div class="article-content text-secondary d-flex flex-column gap-4" style="line-height: 1.8; font-size: 0.95rem;">
                    <section>
                        <h5 class="fw-bold text-dark mb-2">1. Chấp Thuận Điều Khoản</h5>
                        <p class="mb-0">
                            Khi truy cập và đặt mua sản phẩm trên website Atino, quý khách đồng ý tuân thủ các điều khoản và điều kiện được nêu tại đây. Nếu không đồng ý với bất kỳ điều khoản nào, quý khách vui lòng dừng sử dụng dịch vụ của website.
                        </p>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">2. Giá Cả Và Thông Tin Sản Phẩm</h5>
                        <p class="mb-0">
                            Tất cả giá bán niêm yết trên website đã bao gồm thuế Giá trị gia tăng (VAT). Atino cam kết cung cấp thông tin sản phẩm trung thực và hình ảnh đúng với sản phẩm thực tế. Trong trường hợp có sự sai sót hiếm hoi về giá hiển thị, chúng tôi sẽ thông báo lại cho khách hàng trước khi xuất hàng.
                        </p>
                    </section>

                    <section>
                        <h5 class="fw-bold text-dark mb-2">3. Quyền Sở Hữu Trí Tuệ</h5>
                        <p class="mb-0">
                            Mọi nhãn hiệu, hình ảnh, văn bản và thiết kế trên website đều thuộc quyền sở hữu của thương hiệu thời trang Atino. Nghiêm cấm mọi hành vi sao chép, tái xuất bản hoặc sử dụng trái phép vì mục đích thương mại mà không có sự đồng ý bằng văn bản.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
