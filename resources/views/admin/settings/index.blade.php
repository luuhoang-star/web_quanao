@extends('layouts.admin')

@section('title', 'Cấu Hình Hệ Thống & Nội Dung Trang Chủ')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-sliders me-2 text-primary"></i>Cấu Hình Website & Nội Dung Trang Chủ</h4>
        <p class="text-muted small mb-0">Tùy chỉnh thông tin liên hệ, phí giao hàng, ẩn/hiện và đổi tiêu đề Flash Sale, văn bản chính sách cam kết</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i><strong>Vui lòng kiểm tra lại thông tin:</strong>
        <ul class="mb-0 mt-1 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf

    <!-- NAV TABS -->
    <ul class="nav nav-pills mb-4 gap-2" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold rounded-pill px-4" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-content" type="button" role="tab">
                <i class="fa-solid fa-store me-2"></i>1. Cửa Hàng & Vận Chuyển
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold rounded-pill px-4" id="home-content-tab" data-bs-toggle="tab" data-bs-target="#home-content" type="button" role="tab">
                <i class="fa-solid fa-newspaper me-2"></i>2. Nội Dung Trang Chủ (Flash Sale & Cam Kết)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold rounded-pill px-4" id="banking-tab" data-bs-toggle="tab" data-bs-target="#banking-content" type="button" role="tab">
                <i class="fa-solid fa-qrcode me-2"></i>3. Tài Khoản Ngân Hàng & VietQR
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold rounded-pill px-4" id="integration-tab" data-bs-toggle="tab" data-bs-target="#integration-content" type="button" role="tab">
                <i class="fa-brands fa-telegram me-2 text-info"></i>4. Thông Báo Telegram & Webhook
            </button>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabContent">
        <!-- ================= TAB 1: CỬA HÀNG & VẬN CHUYỂN ================= -->
        <div class="tab-pane fade show active" id="general-content" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-gears me-2 text-primary"></i>Thông Số Cơ Bản & Hotline</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="store_name" class="form-label font-weight-medium text-dark">Tên Thương Hiệu / Cửa Hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? '') }}" required>
                            @error('store_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="hotline" class="form-label font-weight-medium text-dark">Số Hotline Liên Hệ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('hotline') is-invalid @enderror" id="hotline" name="hotline" value="{{ old('hotline', $settings['hotline'] ?? '') }}" required>
                            @error('hotline')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label font-weight-medium text-dark">Email Hỗ Trợ Khách Hàng <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label font-weight-medium text-dark">Địa Chỉ Trụ Sở Cửa Hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $settings['address'] ?? '') }}" required>
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="free_shipping_threshold" class="form-label font-weight-medium text-dark">Ngưỡng Miễn Phí Vận Chuyển (VNĐ)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="free_shipping_threshold" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? 500000) }}" min="0" step="10000">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            <div class="form-text extra-small text-muted">Đơn hàng đạt giá trị này sẽ được miễn phí giao hàng toàn quốc.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="default_shipping_fee" class="form-label font-weight-medium text-dark">Phí Giao Hàng Tiêu Chuẩn (VNĐ)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="default_shipping_fee" name="default_shipping_fee" value="{{ old('default_shipping_fee', $settings['default_shipping_fee'] ?? 30000) }}" min="0" step="1000">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="maintenance_mode" name="maintenance_mode" value="1" {{ !empty($settings['maintenance_mode']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="maintenance_mode">
                                    <i class="fa-solid fa-wrench me-1 text-warning"></i>Bật Chế Độ Bảo Trì Website (Maintenance Mode)
                                </label>
                                <p class="text-muted extra-small mb-0 ms-5">Khi bật, khách hàng thông thường sẽ tạm thời không truy cập được trang mua hàng.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 2: NỘI DUNG TRANG CHỦ ================= -->
        <div class="tab-pane fade" id="home-content" role="tabpanel">
            <!-- 1. QUẢN LÝ FLASH SALE TRÊN TRANG CHỦ -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-bolt text-danger me-2"></i>Chương Trình Flash Sale Trang Chủ
                    </h6>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Trang Chủ / Banner</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="home_show_flash_sale" name="home_show_flash_sale" value="1" {{ !empty($settings['home_show_flash_sale']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="home_show_flash_sale">
                                    <i class="fa-solid fa-eye me-1 text-primary"></i> Hiển thị Khối Flash Sale Trên Trang Chủ
                                </label>
                                <p class="text-muted extra-small mb-0 ms-5">Tắt tùy chọn này nếu bạn muốn tạm thời ẩn toàn bộ khối Flash Sale trên trang chủ.</p>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="home_flash_sale_badge" class="form-label font-weight-medium text-dark">Chữ trên Badge Flash Sale</label>
                            <input type="text" class="form-control" id="home_flash_sale_badge" name="home_flash_sale_badge" value="{{ old('home_flash_sale_badge', $settings['home_flash_sale_badge'] ?? 'FLASH SALE') }}" placeholder="FLASH SALE">
                        </div>

                        <div class="col-md-8 mb-3">
                            <label for="home_flash_sale_title" class="form-label font-weight-medium text-dark">Tiêu Đề Chính Của Flash Sale <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('home_flash_sale_title') is-invalid @enderror" id="home_flash_sale_title" name="home_flash_sale_title" value="{{ old('home_flash_sale_title', $settings['home_flash_sale_title'] ?? 'Ưu Đãi Đặc Biệt - Săn Sale Giá Sốc') }}" required>
                            @error('home_flash_sale_title')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-2">
                            <label for="home_flash_sale_subtitle" class="form-label font-weight-medium text-dark">Phụ Đề / Mô Tả Ngắn Flash Sale</label>
                            <input type="text" class="form-control" id="home_flash_sale_subtitle" name="home_flash_sale_subtitle" value="{{ old('home_flash_sale_subtitle', $settings['home_flash_sale_subtitle'] ?? 'Số lượng có hạn, nhanh tay sở hữu!') }}" placeholder="Số lượng có hạn, nhanh tay sở hữu!">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. QUẢN LÝ 4 KHỐI CAM KẾT / DỊCH VỤ -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-award text-success me-2"></i>4 Khối Cam Kết Dịch Vụ (Service Highlights)
                    </h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Miễn phí vận chuyển & Đổi trả</span>
                </div>
                <div class="card-body p-4">
                    <div class="col-12 mb-4">
                        <div class="form-check form-switch p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="home_show_features" name="home_show_features" value="1" {{ !empty($settings['home_show_features']) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="home_show_features">
                                <i class="fa-solid fa-eye me-1 text-success"></i> Hiển thị Khối 4 Cam Kết Trên Trang Chủ
                            </label>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Khối 1: Vận chuyển -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-truck-fast text-primary me-2"></i>Cam Kết 1: Vận Chuyển</h6>
                                <div class="mb-2">
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Tiêu đề</label>
                                    <input type="text" class="form-control form-control-sm" name="feature_1_title" value="{{ old('feature_1_title', $settings['feature_1_title'] ?? 'Miễn Phí Vận Chuyển') }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Mô tả ngắn</label>
                                    <input type="text" class="form-control form-control-sm" name="feature_1_desc" value="{{ old('feature_1_desc', $settings['feature_1_desc'] ?? 'Cho tất cả đơn hàng từ 499.000đ toàn quốc') }}" required>
                                </div>
                                <div>
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Icon FontAwesome</label>
                                    <input type="text" class="form-control form-control-sm font-monospace" name="feature_1_icon" value="{{ old('feature_1_icon', $settings['feature_1_icon'] ?? 'fa-solid fa-truck-fast') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Khối 2: Đổi trả -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-arrows-rotate text-success me-2"></i>Cam Kết 2: Đổi Trả</h6>
                                <div class="mb-2">
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Tiêu đề (VD: Đổi Trả 30 Ngày)</label>
                                    <input type="text" class="form-control form-control-sm" name="feature_2_title" value="{{ old('feature_2_title', $settings['feature_2_title'] ?? 'Đổi Trả 30 Ngày') }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Mô tả ngắn</label>
                                    <input type="text" class="form-control form-control-sm" name="feature_2_desc" value="{{ old('feature_2_desc', $settings['feature_2_desc'] ?? 'Miễn phí đổi size & mẫu trong 30 ngày') }}" required>
                                </div>
                                <div>
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Icon FontAwesome</label>
                                    <input type="text" class="form-control form-control-sm font-monospace" name="feature_2_icon" value="{{ old('feature_2_icon', $settings['feature_2_icon'] ?? 'fa-solid fa-arrows-rotate') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Khối 3: Thanh toán -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shield-halved text-purple me-2" style="color: #9333ea;"></i>Cam Kết 3: Thanh Toán</h6>
                                <div class="mb-2">
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Tiêu đề</label>
                                    <input type="text" class="form-control form-control-sm" name="feature_3_title" value="{{ old('feature_3_title', $settings['feature_3_title'] ?? 'Thanh Toán An Toàn') }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Mô tả ngắn</label>
                                    <input type="text" class="form-control form-control-sm" name="feature_3_desc" value="{{ old('feature_3_desc', $settings['feature_3_desc'] ?? 'Bảo mật 100% giao dịch VNPay, MoMo, COD') }}" required>
                                </div>
                                <div>
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Icon FontAwesome</label>
                                    <input type="text" class="form-control form-control-sm font-monospace" name="feature_3_icon" value="{{ old('feature_3_icon', $settings['feature_3_icon'] ?? 'fa-solid fa-shield-halved') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Khối 4: Hỗ trợ -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-headset text-warning me-2"></i>Cam Kết 4: Hỗ Trợ</h6>
                                <div class="mb-2">
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Tiêu đề</label>
                                    <input type="text" class="form-control form-control-sm" name="feature_4_title" value="{{ old('feature_4_title', $settings['feature_4_title'] ?? 'Hỗ Trợ 24/7') }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Mô tả ngắn</label>
                                    <input type="text" class="form-control form-control-sm" name="feature_4_desc" value="{{ old('feature_4_desc', $settings['feature_4_desc'] ?? 'Tư vấn chọn size & giải đáp mọi thắc mắc') }}" required>
                                </div>
                                <div>
                                    <label class="form-label extra-small font-weight-medium text-dark mb-1">Icon FontAwesome</label>
                                    <input type="text" class="form-control form-control-sm font-monospace" name="feature_4_icon" value="{{ old('feature_4_icon', $settings['feature_4_icon'] ?? 'fa-solid fa-headset') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 3: TÀI KHOẢN NGÂN HÀNG & VIETQR ================= -->
        <div class="tab-pane fade" id="banking-content" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-building-columns me-2 text-primary"></i>Tài Khoản Ngân Hàng & Thanh Toán VietQR Tự Động</h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-3 py-1.5 rounded-pill">VietQR 24/7 Napas</span>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info py-2.5 px-3 rounded-3 border-info-subtle small mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info fs-5 text-primary"></i>
                        <div>
                            Hệ thống sử dụng cổng <strong>VietQR Chuẩn Napas247</strong> hoàn toàn tự động. Khách hàng khi thanh toán chỉ cần quét mã QR bằng App mọi ngân hàng, hệ thống sẽ tự động điền đúng <strong>Số tài khoản, Số tiền và Nội dung chuyển khoản theo Mã Đơn Hàng</strong>.
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="row g-3">
                                <!-- Chọn ngân hàng -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_code" class="form-label font-weight-medium text-dark">Ngân Hàng Thụ Hưởng <span class="text-danger">*</span></label>
                                    @php
                                        $banks = [
                                            'vietcombank' => 'Vietcombank (VCB)',
                                            'mbbank' => 'MB Bank (Quân Đội)',
                                            'techcombank' => 'Techcombank (TCB)',
                                            'acb' => 'ACB (Á Châu)',
                                            'bidv' => 'BIDV (Đầu Tư & Phát Triển)',
                                            'vpb' => 'VPBank (Việt Nam Thịnh Vượng)',
                                            'tpbank' => 'TPBank (Tiên Phong)',
                                            'vietinbank' => 'VietinBank (Công Thương)',
                                            'agribank' => 'Agribank (Nông Nghiệp)',
                                            'sacombank' => 'Sacombank (Sài Gòn Thương Tín)',
                                            'ocb' => 'OCB (Phương Đông)',
                                            'hdbank' => 'HDBank (Phát Triển TP.HCM)',
                                            'msb' => 'MSB (Hàng Hải)',
                                            'shb' => 'SHB (Sài Gòn - Hà Nội)',
                                            'vib' => 'VIB (Quốc Tế)',
                                        ];
                                        $selectedBank = old('bank_code', $settings['bank_code'] ?? 'vietcombank');
                                    @endphp
                                    <select class="form-select" id="bank_code" name="bank_code">
                                        @foreach($banks as $bCode => $bName)
                                            <option value="{{ $bCode }}" {{ $selectedBank === $bCode ? 'selected' : '' }}>{{ $bName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Tên ngân hàng hiển thị -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_name" class="form-label font-weight-medium text-dark">Tên Hiển Thị Ngân Hàng</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $settings['bank_name'] ?? 'Vietcombank') }}" placeholder="VD: Vietcombank">
                                </div>

                                <!-- Số tài khoản -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_account" class="form-label font-weight-medium text-dark">Số Tài Khoản Nhận Tiền <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control font-monospace fw-bold" id="bank_account" name="bank_account" value="{{ old('bank_account', $settings['bank_account'] ?? '9973797151') }}" required placeholder="VD: 9973797151">
                                </div>

                                <!-- Chủ tài khoản -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_account_name" class="form-label font-weight-medium text-dark">Tên Chủ Tài Khoản (In hoa không dấu) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fw-bold" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $settings['bank_account_name'] ?? 'LUU VAN HOANG') }}" required placeholder="VD: LUU VAN HOANG" style="text-transform: uppercase;">
                                </div>

                                <!-- Cú pháp chuyển khoản -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_transfer_syntax" class="form-label font-weight-medium text-dark">Cú Pháp Nội Dung CK <span class="text-muted extra-small">(Mặc định: TT)</span></label>
                                    <input type="text" class="form-control font-monospace" id="bank_transfer_syntax" name="bank_transfer_syntax" value="{{ old('bank_transfer_syntax', $settings['bank_transfer_syntax'] ?? 'TT') }}" placeholder="VD: TT">
                                    <div class="form-text extra-small text-muted">Ví dụ mã đơn ORD-12345 thì nội dung là: <code>TT ORD-12345</code></div>
                                </div>

                                <div class="col-12"><hr class="my-2 text-muted"></div>

                                <!-- Cấu hình Ví MoMo -->
                                <div class="col-md-6 mb-3">
                                    <label for="momo_phone" class="form-label font-weight-medium text-dark"><i class="fa-solid fa-mobile-screen me-1 text-danger"></i>Số Điện Thoại Ví MoMo</label>
                                    <input type="text" class="form-control font-monospace" id="momo_phone" name="momo_phone" value="{{ old('momo_phone', $settings['momo_phone'] ?? '0973797151') }}" placeholder="VD: 0973797151">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="momo_name" class="form-label font-weight-medium text-dark">Tên Tài Khoản MoMo</label>
                                    <input type="text" class="form-control fw-bold" id="momo_name" name="momo_name" value="{{ old('momo_name', $settings['momo_name'] ?? 'LUU VAN HOANG') }}" placeholder="VD: LUU VAN HOANG" style="text-transform: uppercase;">
                                </div>
                            </div>
                        </div>

                        <!-- Cột phải: Xem trước VietQR Mẫu -->
                        <div class="col-lg-5 text-center">
                            <div class="p-3 bg-light rounded-4 border">
                                <h6 class="fw-bold text-dark mb-2 small"><i class="fa-solid fa-qrcode me-1 text-primary"></i>Xem Trước Mã VietQR Tự Động</h6>
                                <p class="text-muted extra-small mb-3">Mã QR này sẽ hiển thị tại trang Thanh toán & Chi tiết đơn hàng của khách hàng</p>
                                
                                <div class="p-2 bg-white rounded-3 border shadow-sm d-inline-block">
                                    <img src="https://img.vietqr.io/image/{{ $settings['bank_code'] ?? 'vietcombank' }}-{{ $settings['bank_account'] ?? '9973797151' }}-compact2.png?amount=500000&addInfo=TT%20ORD-DEMO&accountName={{ urlencode($settings['bank_account_name'] ?? 'LUU VAN HOANG') }}" 
                                         alt="VietQR Demo Preview" 
                                         class="img-fluid rounded-2" 
                                         style="max-height: 220px; width: auto;"
                                         id="vietqr_preview_img">
                                </div>
                                <div class="mt-2 text-muted extra-small">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Tự động điền số tiền & nội dung đơn</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= TAB 4: THÔNG BÁO TELEGRAM & WEBHOOK ================= -->
        <div class="tab-pane fade" id="integration-content" role="tabpanel">
            <!-- 1. CẤU HÌNH TELEGRAM BOT -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-brands fa-telegram me-2 text-info"></i>Thông Báo Tức Thì Qua Telegram Bot</h6>
                    <span class="badge bg-info-subtle text-info border border-info-subtle extra-small fw-bold px-3 py-1.5 rounded-pill">Realtime Alert</span>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info py-2.5 px-3 rounded-3 border-info-subtle small mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info fs-5 text-info"></i>
                        <div>
                            Tự động gửi tin nhắn báo động về điện thoại/nhóm Telegram của bạn ngay khi có <strong>đơn hàng mới</strong> hoặc khách hàng gửi <strong>yêu cầu đổi/trả hàng</strong>.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="telegram_bot_token" class="form-label font-weight-medium text-dark">Telegram Bot Token</label>
                            <input type="text" class="form-control font-monospace" id="telegram_bot_token" name="telegram_bot_token" value="{{ old('telegram_bot_token', $settings['telegram_bot_token'] ?? '') }}" placeholder="VD: 7123456789:AAFxz_xxxx...">
                            <div class="form-text extra-small text-muted">Lấy mã token từ <code>@BotFather</code> trên Telegram.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telegram_chat_id" class="form-label font-weight-medium text-dark">Telegram Chat ID / Group ID</label>
                            <input type="text" class="form-control font-monospace" id="telegram_chat_id" name="telegram_chat_id" value="{{ old('telegram_chat_id', $settings['telegram_chat_id'] ?? '') }}" placeholder="VD: 987654321 hoặc -100xxxxxxx">
                            <div class="form-text extra-small text-muted">Lấy ID từ <code>@userinfobot</code> hoặc ID của group có gắn bot.</div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="telegram_notify_order" name="telegram_notify_order" value="1" {{ !empty($settings['telegram_notify_order']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark small" for="telegram_notify_order">Bật thông báo khi có Đơn Hàng Mới</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="telegram_notify_return" name="telegram_notify_return" value="1" {{ !empty($settings['telegram_notify_return']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark small" for="telegram_notify_return">Bật thông báo khi có Yêu Cầu Đổi / Trả Hàng</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. CẤU HÌNH WEBHOOK THANH TOÁN TỰ ĐỘNG -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-bolt me-2 text-warning"></i>Cấu Hình Webhook Tự Động Xác Nhận Chuyển Khoản (SePay / Casso)</h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-3 py-1.5 rounded-pill">Auto Payment</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7 mb-3">
                            <label class="form-label font-weight-medium text-dark">Địa Chỉ Webhook Nhận Dữ Liệu (Webhook URL):</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace bg-light" readonly value="{{ url('/api/webhook/payment') }}" id="webhook_url_input">
                                <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('{{ url('/api/webhook/payment') }}', 'Đã copy Webhook URL!')">
                                    <i class="fa-regular fa-copy"></i> Copy
                                </button>
                            </div>
                            <div class="form-text extra-small text-muted">Dán link này vào mục Webhook trên tài khoản SePay, Casso hoặc phần mềm đọc sao kê ngân hàng.</div>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="webhook_api_key" class="form-label font-weight-medium text-dark">Khóa Bí Mật Xác Thực (API Secret Key):</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="webhook_api_key" name="webhook_api_key" value="{{ old('webhook_api_key', $settings['webhook_api_key'] ?? 'ATINO_SECURE_KEY_2026') }}">
                                <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard(document.getElementById('webhook_api_key').value, 'Đã copy API Key!')">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="auto_confirm_payment" name="auto_confirm_payment" value="1" {{ !empty($settings['auto_confirm_payment']) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark small" for="auto_confirm_payment">Tự động chuyển trạng thái đơn sang "Đã Xác Nhận" & "Đã Thanh Toán" khi khớp số tiền & mã đơn</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NÚT LƯU CẤU HÌNH -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-5">
        <div class="d-flex align-items-center justify-content-between">
            <span class="text-muted small"><i class="fa-solid fa-circle-info text-primary me-1"></i>Các thay đổi sẽ được cập nhật ngay lập tức lên website.</span>
            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">
                <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Tất Cả Cấu Hình
            </button>
        </div>
    </div>
</form>
@endsection
