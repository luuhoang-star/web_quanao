@extends('layouts.app')

@section('title', 'Phương Thức Thanh Toán - Atino Store')
@section('meta_description', 'Các hình thức thanh toán tiện lợi và an toàn được chấp nhận tại Atino Store: COD, Chuyển khoản QR, Ví điện tử và Thẻ ngân hàng.')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Phương thức thanh toán</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Card -->
            <div class="card border-0 rounded-4 bg-light shadow-sm p-4 p-md-5 mb-5 text-center position-relative overflow-hidden">
                <span class="badge bg-dark text-white px-3 py-2 rounded-pill text-uppercase mb-3 mx-auto" style="letter-spacing: 0.05em; font-size: 0.75rem;">Thanh Toán Linh Hoạt</span>
                <h1 class="fw-bold text-dark display-6 mb-3">Phương Thức Thanh Toán Tại Atino</h1>
                <p class="text-muted mx-auto mb-0" style="max-width: 620px; line-height: 1.6;">
                    Chúng tôi hỗ trợ đa dạng phương thức thanh toán linh hoạt, hiện đại và bảo mật tuyệt đối 100% để mang lại trải nghiệm mua sắm an tâm nhất cho bạn.
                </p>
            </div>

            <!-- Các Phương Thức Thanh Toán Chi Tiết -->
            <div class="row g-4 mb-5">
                <!-- 1. COD -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 bg-success-subtle text-success p-3 fs-3">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Thanh Toán Khi Nhận Hàng (COD)</h5>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Phổ biến nhất</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">
                            Bạn thanh toán tiền mặt trực tiếp cho nhân viên bưu tá giao hàng sau khi đã nhận và kiểm tra kiện hàng.
                        </p>
                        <ul class="text-secondary small mb-0 ps-3">
                            <li>Áp dụng cho mọi đơn hàng trên toàn quốc.</li>
                            <li>Được kiểm tra hàng trước khi gửi tiền.</li>
                            <li>Không phát sinh thêm bất kỳ chi phí thu hộ nào.</li>
                        </ul>
                    </div>
                </div>

                <!-- 2. Chuyển Khoản Ngân Hàng (VietQR) -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 bg-primary-subtle text-primary p-3 fs-3">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Chuyển Khoản Nhanh VietQR</h5>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Tiện lợi & Nhanh chóng</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">
                            Quét mã QR qua ứng dụng ngân hàng bất kỳ (Mobile Banking), hệ thống tự động điền số tiền và nội dung chuyển khoản.
                        </p>
                        <div class="bg-light p-3 rounded-3 small text-muted">
                            <div><strong>Ngân hàng:</strong> Vietcombank (Chi nhánh Hà Nội)</div>
                            <div><strong>Số tài khoản:</strong> 9988.7766.5544</div>
                            <div><strong>Chủ tài khoản:</strong> CONG TY TNHH THOI TRANG ATINO</div>
                            <div><strong>Nội dung CK:</strong> [Mã đơn hàng] - [Số điện thoại]</div>
                        </div>
                    </div>
                </div>

                <!-- 3. Ví Điện Tử (MoMo / VNPay / ZaloPay) -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 bg-danger-subtle text-danger p-3 fs-3">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Ví Điện Tử (MoMo / VNPay)</h5>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nhiều ưu đãi</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">
                            Thanh toán siêu tốc 1 chạm qua ứng dụng Ví điện tử MoMo, VNPay-QR hoặc ZaloPay với nhiều voucher giảm giá hấp dẫn.
                        </p>
                        <ul class="text-secondary small mb-0 ps-3">
                            <li>Xử lý giao dịch tức thì trong 3 giây.</li>
                            <li>Tích điểm thưởng thành viên khi thanh toán qua ví.</li>
                            <li>Tự động nhận thông báo biến động số dư.</li>
                        </ul>
                    </div>
                </div>

                <!-- 4. Thẻ ATM / Visa / Mastercard -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 bg-info-subtle text-info p-3 fs-3">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Thẻ Quốc Tế & ATM Nội Địa</h5>
                                <span class="badge bg-info-subtle text-info border border-info-subtle">Chuẩn Quốc Tế</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">
                            Chấp nhận mọi loại thẻ ATM nội địa phát hành bởi các ngân hàng Việt Nam và thẻ tín dụng/ghi nợ quốc tế Visa, Mastercard, JCB.
                        </p>
                        <ul class="text-secondary small mb-0 ps-3">
                            <li>Xác thực bảo mật 2 lớp 3D-Secure qua mã OTP ngân hàng.</li>
                            <li>Mã hóa SSL 256-bit chuẩn PCI DSS cấp 1.</li>
                            <li>Atino tuyệt đối không lưu thông tin thẻ của quý khách.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Cam Kết An Toàn -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-5">
                <div class="row align-items-center g-4">
                    <div class="col-md-2 text-center">
                        <i class="fa-solid fa-shield-halved text-success" style="font-size: 4rem;"></i>
                    </div>
                    <div class="col-md-10">
                        <h5 class="fw-bold text-dark mb-2">Cam Kết Bảo Mật Thông Tin Thanh Toán Tuyệt Đối</h5>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            Toàn bộ giao dịch trực tuyến tại Atino được kết nối thông qua các cổng thanh toán uy tín hàng đầu, đảm bảo tiêu chuẩn an ninh mạng quốc tế khắt khe nhất. Mọi dữ liệu tài chính của khách hàng đều được mã hóa và bảo vệ an toàn.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
