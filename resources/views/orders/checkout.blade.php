@extends('layouts.app')

@section('title', 'Thanh Toán Đơn Hàng - ATINO Store')

@section('content')
<!-- Checkout Progress Stepper -->
<div class="checkout-stepper">
    <a href="{{ route('cart.index') }}" class="checkout-step-item completed">
        <span class="step-num"><i class="fa-solid fa-check"></i></span>
        <span>1. Chọn sản phẩm</span>
    </a>
    <div class="checkout-step-divider active"></div>
    <div class="checkout-step-item active">
        <span class="step-num">2</span>
        <span>2. Thanh toán & Giao hàng</span>
    </div>
    <div class="checkout-step-divider"></div>
    <div class="checkout-step-item">
        <span class="step-num">3</span>
        <span>3. Hoàn tất đơn hàng</span>
    </div>
</div>

<form id="checkoutForm" action="{{ route('orders.processCheckout') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- CỘT TRÁI: THÔNG TIN GIAO HÀNG & PHƯƠNG THỨC THANH TOÁN -->
        <div class="col-lg-7">
            <!-- 1. THÔNG TIN GIAO HÀNG -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2 fs-6">
                        <i class="fa-solid fa-truck-fast text-dark"></i>
                        <span>1. Thông Tin Nhận Hàng</span>
                    </h5>
                    <span class="badge bg-light text-muted border extra-small"><i class="fa-solid fa-shield-halved me-1"></i>Bảo mật SSL</span>
                </div>
                <div class="card-body p-4">
                    @php
                        $defaultAddress = isset($user->addresses) ? ($user->addresses->where('is_default', true)->first() ?? $user->addresses->first()) : null;
                        $defaultName = $defaultAddress ? $defaultAddress->recipient_name : ($user->name ?? '');
                        $defaultPhone = $defaultAddress ? $defaultAddress->recipient_phone : ($user->phone ?? '');
                        $defaultFullAddress = $defaultAddress ? $defaultAddress->full_address : '';
                    @endphp

                    <!-- Địa chỉ đã lưu của tài khoản (nếu có) -->
                    @if(isset($user->addresses) && $user->addresses->isNotEmpty())
                        <div class="mb-4 p-3 rounded-3 bg-light border border-slate-200">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label font-weight-bold text-dark mb-0 extra-small d-flex align-items-center gap-1.5">
                                    <i class="fa-solid fa-bookmark text-primary"></i>
                                    <span>Chọn nhanh từ sổ địa chỉ của bạn:</span>
                                </label>
                                <a href="{{ route('profile.show') }}" class="extra-small text-decoration-none fw-semibold text-primary">
                                    Quản lý địa chỉ <i class="fa-solid fa-arrow-right extra-small"></i>
                                </a>
                            </div>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-map-location-dot input-icon"></i>
                                <select id="saved_address_picker" class="form-select rounded-3">
                                    @foreach($user->addresses as $addr)
                                        <option value="{{ $addr->id }}" 
                                                data-name="{{ $addr->recipient_name }}" 
                                                data-phone="{{ $addr->recipient_phone }}" 
                                                data-address="{{ $addr->full_address }}"
                                                data-province="{{ $addr->province_city }}"
                                                data-district="{{ $addr->district }}"
                                                data-ward="{{ $addr->ward }}"
                                                data-street="{{ $addr->street_address }}"
                                                {{ ($defaultAddress && $defaultAddress->id == $addr->id) ? 'selected' : '' }}>
                                            {{ $addr->recipient_name }} ({{ $addr->recipient_phone }}) - {{ $addr->full_address }} {{ $addr->is_default ? '★ [Mặc định]' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="row g-3">
                        <!-- Họ tên người nhận -->
                        <div class="col-md-6">
                            <label for="recipient_name" class="form-label fw-semibold text-dark small mb-1.5">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-regular fa-user input-icon"></i>
                                <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $defaultName) }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                            </div>
                            @error('recipient_name')
                                <div class="text-danger extra-small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Số điện thoại người nhận -->
                        <div class="col-md-6">
                            <label for="recipient_phone" class="form-label fw-semibold text-dark small mb-1.5">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-phone-volume input-icon"></i>
                                <input type="tel" class="form-control @error('recipient_phone') is-invalid @enderror" id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone', $defaultPhone) }}" placeholder="Ví dụ: 0987654321" required>
                            </div>
                            @error('recipient_phone')
                                <div class="text-danger extra-small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- BỘ CHỌN ĐỊA CHỈ HÀNH CHÍNH 3 CẤP VIỆT NAM -->
                        <div class="col-md-4">
                            <label for="checkout_province" class="form-label fw-semibold text-dark small mb-1.5">
                                Tỉnh / Thành phố <span class="text-danger">*</span>
                            </label>
                            <select id="checkout_province" class="form-select" onchange="onCheckoutProvinceChange()">
                                <option value="">-- Chọn Tỉnh / Thành --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="checkout_district" class="form-label fw-semibold text-dark small mb-1.5">
                                Quận / Huyện <span class="text-danger">*</span>
                            </label>
                            <select id="checkout_district" class="form-select" onchange="onCheckoutDistrictChange()">
                                <option value="">-- Chọn Quận / Huyện --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="checkout_ward" class="form-label fw-semibold text-dark small mb-1.5">
                                Phường / Xã <span class="text-danger">*</span>
                            </label>
                            <select id="checkout_ward" class="form-select" onchange="syncCheckoutAddress()">
                                <option value="">-- Chọn Phường / Xã --</option>
                            </select>
                        </div>

                        <!-- Số nhà & Tên đường chi tiết -->
                        <div class="col-12">
                            <label for="checkout_street" class="form-label fw-semibold text-dark small mb-1.5">
                                Số nhà, tên đường, tòa nhà <span class="text-danger">*</span>
                            </label>
                            <div class="input-icon-wrapper">
                                <i class="fa-solid fa-house-chimney input-icon"></i>
                                <input type="text" class="form-control" id="checkout_street" value="{{ old('checkout_street', $defaultAddress?->street_address ?? '') }}" placeholder="Ví dụ: Số 25, ngõ 102 Trần Phú" oninput="syncCheckoutAddress()">
                            </div>
                        </div>

                        <!-- Địa chỉ giao hàng đầy đủ tổng hợp -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <label for="shipping_address" class="form-label fw-semibold text-dark small mb-0">
                                    Địa chỉ nhận hàng hoàn chỉnh <span class="text-danger">*</span>
                                </label>
                                <span class="text-muted extra-small">
                                    <i class="fa-solid fa-circle-info me-1"></i>Hệ thống tự động đồng bộ theo 3 cấp
                                </span>
                            </div>
                            <div class="textarea-icon-wrapper">
                                <i class="fa-solid fa-location-dot input-icon"></i>
                                <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address" name="shipping_address" rows="2" placeholder="Số nhà, Phường/Xã, Quận/Huyện, Tỉnh/Thành..." required>{{ old('shipping_address', $defaultFullAddress) }}</textarea>
                            </div>
                            @error('shipping_address')
                                <div class="text-danger extra-small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ghi chú đơn hàng -->
                        <div class="col-12">
                            <label for="note" class="form-label fw-semibold text-dark small mb-1.5">Ghi chú giao hàng <span class="text-muted fw-normal extra-small">(Tùy chọn)</span></label>
                            <div class="textarea-icon-wrapper">
                                <i class="fa-regular fa-clipboard input-icon"></i>
                                <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="2" placeholder="Ví dụ: Giao hàng vào giờ hành chính, gọi trước khi giao 15 phút...">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. PHƯƠNG THỨC THANH TOÁN (3 PHƯƠNG THỨC) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3.5 px-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2 fs-6">
                        <i class="fa-solid fa-credit-card text-dark"></i>
                        <span>2. Phương Thức Thanh Toán</span>
                        <span class="text-danger small">*</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <!-- Option 1: COD Tiền Mặt -->
                        <div class="payment-card-option active" id="card_cod" onclick="selectPaymentOption('COD')">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" id="payment_cod" value="COD" checked>
                                    <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center p-2" style="width: 42px; height: 42px;">
                                        <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                                    </div>
                                    <div>
                                        <label class="fw-bold text-dark mb-0 d-block cursor-pointer" for="payment_cod">
                                            Thanh toán tiền mặt khi nhận hàng (COD)
                                        </label>
                                        <span class="extra-small text-muted">Kiểm tra hàng và thanh toán tiền mặt trực tiếp cho Shipper</span>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2.5 py-1 rounded-pill">Tiện lợi</span>
                            </div>
                            <div id="details-cod" class="payment-expand-container mt-3">
                                <div class="d-flex align-items-center gap-2 text-dark small">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Bạn chỉ thanh toán khi đã nhận đúng và đủ sản phẩm theo đơn hàng.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Option 2: VietQR Chuyển Khoản Ngân Hàng -->
                        <div class="payment-card-option" id="card_atm" onclick="selectPaymentOption('bank_transfer')">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" id="payment_atm" value="bank_transfer">
                                    <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center p-2" style="width: 42px; height: 42px;">
                                        <i class="fa-solid fa-building-columns fs-5"></i>
                                    </div>
                                    <div>
                                        <label class="fw-bold text-dark mb-0 d-block cursor-pointer" for="payment_atm">
                                            Chuyển khoản Ngân hàng (VietQR 24/7)
                                        </label>
                                        <span class="extra-small text-muted">Quét mã QR tự động điền số tiền & nội dung qua App mọi ngân hàng</span>
                                    </div>
                                </div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small fw-bold px-2.5 py-1 rounded-pill">Khuyên dùng</span>
                            </div>
                            @php
                                $siteSettings = \Illuminate\Support\Facades\Cache::get('site_settings', \App\Http\Controllers\Admin\AdminSettingsController::getDefaultSettings());
                                $bankCode = $siteSettings['bank_code'] ?? 'vietcombank';
                                $bankName = $siteSettings['bank_name'] ?? 'Vietcombank';
                                $bankAccount = $siteSettings['bank_account'] ?? '9973797151';
                                $bankAccountName = $siteSettings['bank_account_name'] ?? 'LUU VAN HOANG';
                                $bankSyntax = trim($siteSettings['bank_transfer_syntax'] ?? 'TT');
                                $momoPhone = trim($siteSettings['momo_phone'] ?? '0973797151');
                                $momoName = trim($siteSettings['momo_name'] ?? 'LUU VAN HOANG');
                                $bankTransferContent = ($bankSyntax ?: 'TT').' '.($user->phone ?? 'STORE');
                                $vietQrUrl = "https://img.vietqr.io/image/{$bankCode}-{$bankAccount}-compact2.png?amount={$totalAmount}&addInfo=".urlencode($bankTransferContent).'&accountName='.urlencode($bankAccountName);
                                $momoQrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("2|99|{$momoPhone}|{$momoName}|store@atino.vn|0|0|{$totalAmount}");
                            @endphp
                            <div id="details-atm" class="payment-expand-container mt-3 d-none">
                                <div class="row align-items-center g-4">
                                    <div class="col-md-7">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="extra-small text-muted fw-bold text-uppercase">Ngân hàng thụ hưởng</span>
                                                <span class="badge bg-success-subtle text-success extra-small fw-bold">{{ $bankName }}</span>
                                            </div>
                                            
                                            <!-- Số tài khoản -->
                                            <div class="mb-2 pb-2 border-bottom">
                                                <span class="extra-small text-muted d-block">Số tài khoản:</span>
                                                <div class="d-flex align-items-center justify-content-between mt-0.5">
                                                    <span class="bank-info-badge" id="bank_acc_num">{{ $bankAccount }}</span>
                                                    <button type="button" class="copy-btn-action" onclick="copyToClipboard('{{ $bankAccount }}', 'Đã copy Số tài khoản!')">
                                                        <i class="fa-regular fa-copy"></i> Copy STK
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Chủ tài khoản -->
                                            <div class="mb-2 pb-2 border-bottom">
                                                <span class="extra-small text-muted d-block">Chủ tài khoản:</span>
                                                <strong class="text-dark small text-uppercase">{{ $bankAccountName }}</strong>
                                            </div>

                                            <!-- Số tiền -->
                                            <div class="mb-2 pb-2 border-bottom">
                                                <span class="extra-small text-muted d-block">Số tiền thanh toán:</span>
                                                <strong class="text-danger fs-6">{{ number_format($totalAmount) }}đ</strong>
                                            </div>

                                            <!-- Nội dung chuyển khoản -->
                                            <div>
                                                <span class="extra-small text-muted d-block">Nội dung chuyển khoản:</span>
                                                <div class="d-flex align-items-center justify-content-between mt-0.5">
                                                    <span class="text-danger fw-bold small font-monospace" id="bank_content_text">{{ $bankTransferContent }}</span>
                                                    <button type="button" class="copy-btn-action" onclick="copyToClipboard('{{ $bankTransferContent }}', 'Đã copy Nội dung chuyển khoản!')">
                                                        <i class="fa-regular fa-copy"></i> Copy ND
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5 text-center">
                                        <div class="p-2 bg-white rounded-3 border shadow-sm d-inline-block">
                                            <img src="{{ $vietQrUrl }}" 
                                                 alt="VietQR Chuyển khoản" 
                                                 class="img-fluid rounded-2" 
                                                 style="max-height: 210px; width: auto;">
                                        </div>
                                        <span class="d-block extra-small text-muted mt-1.5 fw-semibold">
                                            <i class="fa-solid fa-qrcode me-1 text-primary"></i>Quét mã VietQR tự động điền số tiền & nội dung
                                        </span>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="alert alert-info py-2 px-3 mb-0 rounded-3 border-info-subtle extra-small d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-circle-info text-primary fs-6"></i>
                                            <span><strong>Lưu ý:</strong> Sau khi bấm <strong>"Hoàn Tất Đặt Hàng"</strong>, hệ thống sẽ cấp mã đơn hàng riêng (<code>ORD-...</code>) để bạn tra cứu và đối soát thanh toán.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Option 3: Ví Điện Tử MoMo -->
                        @php
                            $momoMode = $siteSettings['momo_mode'] ?? 'personal';
                        @endphp
                        <div class="payment-card-option" id="card_momo" onclick="selectPaymentOption('momo')">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" id="payment_momo" value="momo">
                                    <div class="rounded-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center p-2" style="width: 42px; height: 42px;">
                                        <i class="fa-solid fa-wallet fs-5"></i>
                                    </div>
                                    <div>
                                        <label class="fw-bold text-dark mb-0 d-block cursor-pointer" for="payment_momo">
                                            {{ $momoMode === 'gateway' ? 'Cổng thanh toán Ví MoMo (MoMo Gateway)' : 'Thanh toán Ví MoMo (Quét mã MoMo QR)' }}
                                        </label>
                                        <span class="extra-small text-muted">
                                            {{ $momoMode === 'gateway' ? 'Thanh toán bảo mật tức thì qua Cổng MoMo (App MoMo, ATM & Thẻ Quốc tế)' : 'Quét mã chuyển tiền nhanh qua ứng dụng MoMo - Tự động duyệt đơn' }}
                                        </span>
                                    </div>
                                </div>
                                <span class="badge {{ $momoMode === 'gateway' ? 'bg-danger text-white' : 'bg-danger-subtle text-danger border border-danger-subtle' }} extra-small fw-bold px-2.5 py-1 rounded-pill">
                                    {{ $momoMode === 'gateway' ? 'MoMo Gateway' : 'Ví MoMo' }}
                                </span>
                            </div>
                            <div id="details-momo" class="payment-expand-container mt-3 d-none">
                                @if($momoMode === 'gateway')
                                    <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle">
                                        <div class="d-flex align-items-center gap-2 mb-2 text-danger fw-bold small">
                                            <i class="fa-solid fa-bolt"></i>
                                            <span>Xử lý tự động & Kích hoạt đơn ngay lập tức:</span>
                                        </div>
                                        <ul class="extra-small text-dark ps-3 mb-2">
                                            <li class="mb-1">Sau khi bấm <strong>"Hoàn Tất Đặt Hàng"</strong>, hệ thống sẽ tự động chuyển hướng bạn đến <strong>Cổng thanh toán bảo mật MoMo</strong>.</li>
                                            <li class="mb-1">Hỗ trợ quét mã QR bằng ứng dụng MoMo trên điện thoại hoặc đăng nhập ví MoMo.</li>
                                            <li>Đơn hàng sẽ được tự động duyệt và xác nhận ngay sau khi giao dịch thành công.</li>
                                        </ul>
                                        <div class="d-flex align-items-center gap-2 text-muted extra-small">
                                            <i class="fa-solid fa-shield-halved text-success"></i>
                                            <span>Giao dịch được mã hóa và bảo mật chuẩn quốc tế PCI DSS của MoMo.</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle">
                                        <div class="row align-items-center g-4">
                                            <div class="col-md-7">
                                                <div class="p-3 bg-white rounded-3 border">
                                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-danger-subtle">
                                                        <span class="extra-small text-danger fw-bold text-uppercase"><i class="fa-solid fa-wallet me-1"></i>Ví MoMo Thụ Hưởng</span>
                                                        <span class="badge extra-small fw-bold text-white px-2 py-0.5" style="background-color: #a50064;">MoMo Pay</span>
                                                    </div>

                                                    <!-- Số điện thoại MoMo -->
                                                    <div class="mb-2 pb-2 border-bottom">
                                                        <span class="extra-small text-muted d-block">Số điện thoại Ví MoMo:</span>
                                                        <div class="d-flex align-items-center justify-content-between mt-0.5">
                                                            <span class="bank-info-badge font-monospace text-danger fs-6">{{ $momoPhone }}</span>
                                                            <button type="button" class="copy-btn-action" onclick="copyToClipboard('{{ $momoPhone }}', 'Đã copy Số điện thoại MoMo!')">
                                                                <i class="fa-regular fa-copy"></i> Copy SĐT
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Chủ tài khoản -->
                                                    <div class="mb-2 pb-2 border-bottom">
                                                        <span class="extra-small text-muted d-block">Chủ ví MoMo / Tài khoản:</span>
                                                        <strong class="text-dark small text-uppercase">{{ $momoName }}</strong>
                                                    </div>

                                                    <!-- Số tiền thanh toán -->
                                                    <div class="mb-2 pb-2 border-bottom">
                                                        <span class="extra-small text-muted d-block">Số tiền thanh toán:</span>
                                                        <strong class="text-danger fs-6">{{ number_format($totalAmount) }}đ</strong>
                                                    </div>

                                                    <!-- Hướng dẫn thao tác -->
                                                    <div class="extra-small text-muted">
                                                        <i class="fa-solid fa-mobile-screen text-danger me-1"></i>Mở <strong>App MoMo</strong> &rarr; Chọn <strong>"Quét mã"</strong> &rarr; Hướng camera vào mã QR bên cạnh để chuyển tiền tự động.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-5 text-center">
                                                <div class="p-2 bg-white rounded-3 border shadow-sm d-inline-block position-relative" style="border-color: #f4c1dc !important;">
                                                    <img src="{{ $vietQrUrl }}" 
                                                         alt="Mã QR Quét Bằng MoMo" 
                                                         class="img-fluid rounded-2" 
                                                         style="max-height: 210px; width: auto;">
                                                </div>
                                                <div class="mt-2">
                                                    <span class="badge text-white extra-small px-2 py-1 rounded-pill" style="background-color: #a50064;">
                                                        <i class="fa-solid fa-qrcode me-1"></i>Quét bằng App MoMo 24/7
                                                    </span>
                                                </div>
                                                <span class="d-block extra-small text-muted mt-1 fw-semibold">
                                                    Tự động điền số tiền & thông tin
                                                </span>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="alert alert-danger py-2 px-3 mb-0 rounded-3 border-danger-subtle extra-small d-flex align-items-center gap-2" style="background-color: #fdf2f7;">
                                                    <i class="fa-solid fa-bolt text-danger fs-6"></i>
                                                    <span><strong>Tự Động Duyệt Đơn:</strong> Sau khi bấm <strong>"Hoàn Tất Đặt Hàng"</strong>, hệ thống sẽ cấp mã đơn <code>ORD-...</code> và <strong>tự động chuyển trạng thái đơn hàng sang "Đã Thanh Toán" ngay khi bạn quét tiền thành công</strong>.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG & NÚT ĐẶT HÀNG (STICKY SIDEBAR) -->
        <div class="col-lg-5">
            <div class="sticky-checkout-summary">
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2 fs-6">
                            <i class="fa-solid fa-bag-shopping text-dark"></i>
                            <span>Đơn Hàng Của Bạn ({{ $cart->items->sum('quantity') }})</span>
                        </h5>
                        <a href="{{ route('cart.index') }}" class="extra-small text-decoration-none fw-semibold text-muted hover-text-dark">
                            <i class="fa-solid fa-pen-to-square me-1"></i>Sửa giỏ
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <!-- Danh sách món hàng cuộn gọn gàng -->
                        <div class="custom-scrollbar-mini pe-1 mb-4" style="max-height: 240px; overflow-y: auto;">
                            @foreach($cart->items as $item)
                                @php
                                    $product = $item->product;
                                    if (! $product) {
                                        continue;
                                    }
                                    $variant = $item->variant;
                                    $thumb = $product->images?->where('is_thumbnail', true)->first() ?: $product->images?->first();
                                    $itemSubtotal = $item->price * $item->quantity;
                                @endphp
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-light">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($thumb)
                                            <img src="{{ $thumb->display_url }}" alt="{{ $product->name }}" class="rounded-3 border object-fit-cover flex-shrink-0" style="width: 52px; height: 52px;">
                                        @else
                                            <div class="bg-light border rounded-3 d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width: 52px; height: 52px;">
                                                <i class="fa-solid fa-shirt"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <h6 class="fw-bold text-dark mb-0.5 small text-truncate" style="max-width: 170px;" title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </h6>
                                            <div class="d-flex align-items-center gap-1.5">
                                                @if($variant && $variant->color)
                                                    <span class="badge bg-light text-secondary border extra-small p-1 px-1.5">
                                                        <span class="d-inline-block rounded-circle me-1 border" style="width: 8px; height: 8px; background-color: {{ $variant->color->hex_code ?? '#000' }};"></span>
                                                        {{ $variant->color->name }}
                                                    </span>
                                                @endif
                                                @if($variant && $variant->size)
                                                    <span class="badge bg-dark text-white extra-small p-1 px-1.5">Size {{ $variant->size->name }}</span>
                                                @endif
                                                <span class="extra-small text-muted fw-bold">x{{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-dark small">{{ number_format($itemSubtotal) }}đ</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Khối nhập Coupon Giảm Giá -->
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-semibold text-dark extra-small d-flex align-items-center gap-1.5 mb-2">
                                <i class="fa-solid fa-ticket text-danger"></i>
                                <span>Mã ưu đãi / Phiếu giảm giá:</span>
                            </label>
                            @if(isset($appliedCoupon) && $appliedCoupon)
                                <div class="d-flex align-items-center justify-content-between bg-white p-2.5 rounded-3 border border-success">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-check me-1"></i>{{ $appliedCoupon['code'] }}</span>
                                        <span class="small text-success fw-bold">-{{ number_format($discountAmount) }}đ</span>
                                    </div>
                                    <form action="{{ route('coupon.remove') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill py-0.5 px-2.5 extra-small fw-semibold">
                                            <i class="fa-solid fa-xmark me-0.5"></i> Hủy mã
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="input-group">
                                    <input type="text" id="coupon_code_input" class="form-control text-uppercase font-monospace small" placeholder="Nhập mã (VD: ATINO10)" style="letter-spacing: 1px;">
                                    <button type="button" id="btn_apply_coupon" class="btn btn-dark fw-bold px-3 btn-sm">
                                        <span id="coupon_btn_text">Áp dụng</span>
                                        <span id="coupon_btn_spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                    </button>
                                </div>
                                <div id="coupon_message" class="extra-small mt-1.5 d-none"></div>
                            @endif
                        </div>

                        <!-- Chi tiết thanh toán -->
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="d-flex justify-content-between align-items-center text-secondary small">
                                <span>Tạm tính tiền hàng:</span>
                                <span class="fw-semibold text-dark">{{ number_format($subtotal) }}đ</span>
                            </div>

                            @if(isset($discountAmount) && $discountAmount > 0)
                                <div class="d-flex justify-content-between align-items-center text-success small">
                                    <span class="d-flex align-items-center gap-1"><i class="fa-solid fa-tag"></i> Giảm giá Voucher:</span>
                                    <span class="fw-bold">-{{ number_format($discountAmount) }}đ</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center text-secondary small">
                                <span>Phí vận chuyển toàn quốc:</span>
                                @if($shippingFee == 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Miễn phí (Freeship)</span>
                                @else
                                    <span class="fw-semibold text-dark">{{ number_format($shippingFee) }}đ</span>
                                @endif
                            </div>
                        </div>

                        <hr class="my-3 text-muted">

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <div>
                                <span class="fw-bold text-dark fs-6 d-block">Tổng cộng thanh toán:</span>
                                <span class="extra-small text-muted">(Đã bao gồm thuế & phí)</span>
                            </div>
                            <div class="text-end">
                                <span class="fs-3 fw-extrabold text-danger">{{ number_format($totalAmount) }}đ</span>
                            </div>
                        </div>

                        <!-- Nút Xác Nhận Đặt Hàng -->
                        <button type="submit" id="btn_submit_order" class="btn btn-dark btn-lg w-100 rounded-pill fw-bold py-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-lock"></i>
                            <span id="submit_btn_text">XÁC NHẬN ĐẶT HÀNG</span>
                            <span id="submit_btn_spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        </button>

                        <!-- Huy hiệu bảo đảm & tin cậy -->
                        <div class="trust-badges-grid">
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-shield-check"></i>
                                <span>Bảo mật chuẩn SSL</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-box-check"></i>
                                <span>Được kiểm tra khi nhận</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>Đổi hàng trong 30 ngày</span>
                            </div>
                            <div class="trust-badge-item">
                                <i class="fa-solid fa-headset"></i>
                                <span>Hỗ trợ tư vấn 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Hàm chọn phương thức thanh toán dạng Card tương tác
    function selectPaymentOption(type) {
        // Đánh dấu radio checked tương ứng
        if (type === 'COD') {
            $('#payment_cod').prop('checked', true);
            $('.payment-card-option').removeClass('active');
            $('#card_cod').addClass('active');
            $('.payment-expand-container').addClass('d-none');
            $('#details-cod').removeClass('d-none');
        } else if (type === 'bank_transfer') {
            $('#payment_atm').prop('checked', true);
            $('.payment-card-option').removeClass('active');
            $('#card_atm').addClass('active');
            $('.payment-expand-container').addClass('d-none');
            $('#details-atm').removeClass('d-none');
        } else if (type === 'momo') {
            $('#payment_momo').prop('checked', true);
            $('.payment-card-option').removeClass('active');
            $('#card_momo').addClass('active');
            $('.payment-expand-container').addClass('d-none');
            $('#details-momo').removeClass('d-none');
        }
    }

    // Hàm sao chép nhanh vào clipboard kèm Toast thông báo
    function copyToClipboard(text, message) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                showToast(message || 'Đã sao chép vào bộ nhớ tạm!');
            });
        } else {
            // Fallback cho trình duyệt cũ
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast(message || 'Đã sao chép vào bộ nhớ tạm!');
            } catch (err) {
                console.error('Không thể sao chép: ', err);
            }
            document.body.removeChild(textArea);
        }
    }

    function showToast(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: msg,
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            alert(msg);
        }
    }

    let vnCheckoutAddressData = [];

    // Danh sách 63 tỉnh thành mặc định dự phòng
    const fallbackVnProvinces = [
        "Hà Nội", "TP. Hồ Chí Minh", "Đà Nẵng", "Hải Phòng", "Cần Thơ", 
        "An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu", 
        "Bắc Ninh", "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước", 
        "Bình Thuận", "Cà Mau", "Cao Bằng", "Đắk Lắk", "Đắk Nông", 
        "Điện Biên", "Đồng Nai", "Đồng Tháp", "Gia Lai", "Hà Giang", 
        "Hà Nam", "Hà Tĩnh", "Hải Dương", "Hậu Giang", "Hòa Bình", 
        "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu", 
        "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", 
        "Nghệ An", "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", 
        "Quảng Bình", "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", 
        "Sóc Trăng", "Sơn La", "Tây Ninh", "Thái Bình", "Thái Nguyên", 
        "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh", "Tuyên Quang", 
        "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"
    ];

    async function loadVnCheckoutAddressData(initialProvince, initialDistrict, initialWard) {
        try {
            const res = await fetch('https://provinces.open-api.vn/api/?depth=3');
            if (res.ok) {
                vnCheckoutAddressData = await res.json();
                populateCheckoutProvinces(initialProvince, initialDistrict, initialWard);
                return;
            }
        } catch (e) {
            console.warn('API Hành chính chưa phản hồi, sử dụng danh sách dự phòng:', e);
        }

        vnCheckoutAddressData = fallbackVnProvinces.map((name, idx) => ({ name: name, code: idx + 1, districts: [] }));
        populateCheckoutProvinces(initialProvince, initialDistrict, initialWard);
    }

    function populateCheckoutProvinces(targetProvince, targetDistrict, targetWard) {
        const provSelect = document.getElementById('checkout_province');
        if (!provSelect) return;
        provSelect.innerHTML = '<option value="">-- Chọn Tỉnh / Thành --</option>';

        vnCheckoutAddressData.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.name;
            opt.textContent = p.name;
            provSelect.appendChild(opt);
        });

        if (targetProvince) {
            provSelect.value = targetProvince;
            if (!provSelect.value) {
                const opt = document.createElement('option');
                opt.value = targetProvince;
                opt.textContent = targetProvince;
                provSelect.appendChild(opt);
                provSelect.value = targetProvince;
            }
            onCheckoutProvinceChange(targetDistrict, targetWard);
        }
    }

    function onCheckoutProvinceChange(targetDistrict, targetWard) {
        const provSelect = document.getElementById('checkout_province');
        const distSelect = document.getElementById('checkout_district');
        const wardSelect = document.getElementById('checkout_ward');

        distSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        const selectedProv = provSelect.value;
        if (!selectedProv) {
            syncCheckoutAddress();
            return;
        }

        const provObj = vnCheckoutAddressData.find(p => p.name === selectedProv);
        if (provObj && provObj.districts && provObj.districts.length > 0) {
            provObj.districts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.name;
                opt.textContent = d.name;
                distSelect.appendChild(opt);
            });
        }

        if (targetDistrict) {
            distSelect.value = targetDistrict;
            if (!distSelect.value) {
                const opt = document.createElement('option');
                opt.value = targetDistrict;
                opt.textContent = targetDistrict;
                distSelect.appendChild(opt);
                distSelect.value = targetDistrict;
            }
            onCheckoutDistrictChange(targetWard);
        }

        syncCheckoutAddress();
    }

    function onCheckoutDistrictChange(targetWard) {
        const provSelect = document.getElementById('checkout_province');
        const distSelect = document.getElementById('checkout_district');
        const wardSelect = document.getElementById('checkout_ward');

        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        const selectedProv = provSelect.value;
        const selectedDist = distSelect.value;
        if (!selectedProv || !selectedDist) {
            syncCheckoutAddress();
            return;
        }

        const provObj = vnCheckoutAddressData.find(p => p.name === selectedProv);
        if (provObj) {
            const distObj = provObj.districts?.find(d => d.name === selectedDist);
            if (distObj && distObj.wards && distObj.wards.length > 0) {
                distObj.wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.name;
                    opt.textContent = w.name;
                    wardSelect.appendChild(opt);
                });
            }
        }

        if (targetWard) {
            wardSelect.value = targetWard;
            if (!wardSelect.value) {
                const opt = document.createElement('option');
                opt.value = targetWard;
                opt.textContent = targetWard;
                wardSelect.appendChild(opt);
                wardSelect.value = targetWard;
            }
        }

        syncCheckoutAddress();
    }

    function syncCheckoutAddress() {
        const street = ($('#checkout_street').val() || '').trim();
        const ward = ($('#checkout_ward').val() || '').trim();
        const district = ($('#checkout_district').val() || '').trim();
        const province = ($('#checkout_province').val() || '').trim();

        const parts = [];
        if (street) parts.push(street);
        if (ward) parts.push(ward);
        if (district) parts.push(district);
        if (province) parts.push(province);

        if (parts.length > 0) {
            $('#shipping_address').val(parts.join(', '));
        }
    }

    $(document).ready(function() {
        // Tải dữ liệu hành chính và thiết lập giá trị ban đầu nếu có
        const initialProv = "{{ $defaultAddress?->province_city ?? '' }}";
        const initialDist = "{{ $defaultAddress?->district ?? '' }}";
        const initialWard = "{{ $defaultAddress?->ward ?? '' }}";
        loadVnCheckoutAddressData(initialProv, initialDist, initialWard);

        // Tự động điền dữ liệu khi chọn địa chỉ đã lưu
        $('#saved_address_picker').on('change', function() {
            var $selected = $(this).find(':selected');
            var name = $selected.data('name');
            var phone = $selected.data('phone');
            var address = $selected.data('address');
            var prov = $selected.data('province');
            var dist = $selected.data('district');
            var ward = $selected.data('ward');
            var street = $selected.data('street');

            if (name) $('#recipient_name').val(name);
            if (phone) $('#recipient_phone').val(phone);

            if (prov || dist || ward || street) {
                if (street) $('#checkout_street').val(street);
                populateCheckoutProvinces(prov, dist, ward);
            } else if (address) {
                $('#shipping_address').val(address);
            }
        });

        // Xử lý áp dụng mã giảm giá Coupon qua AJAX
        $('#btn_apply_coupon').on('click', function() {
            var code = $('#coupon_code_input').val().trim();
            if (!code) {
                $('#coupon_message').removeClass('d-none text-success').addClass('text-danger')
                    .html('<i class="fa-solid fa-circle-exclamation me-1"></i> Vui lòng nhập mã giảm giá.');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true);
            $('#coupon_btn_text').addClass('d-none');
            $('#coupon_btn_spinner').removeClass('d-none');

            $.ajax({
                url: '{{ route("coupon.apply") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon_code: code
                },
                success: function(res) {
                    if (res.success) {
                        window.location.reload();
                    } else {
                        $btn.prop('disabled', false);
                        $('#coupon_btn_text').removeClass('d-none');
                        $('#coupon_btn_spinner').addClass('d-none');
                        $('#coupon_message').removeClass('d-none text-success').addClass('text-danger')
                            .html('<i class="fa-solid fa-circle-exclamation me-1"></i> ' + (res.message || 'Mã không hợp lệ.'));
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false);
                    $('#coupon_btn_text').removeClass('d-none');
                    $('#coupon_btn_spinner').addClass('d-none');
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra khi áp dụng mã.';
                    $('#coupon_message').removeClass('d-none text-success').addClass('text-danger')
                        .html('<i class="fa-solid fa-circle-exclamation me-1"></i> ' + msg);
                }
            });
        });

        // Chống gửi trùng lặp đơn hàng khi bấm Đặt Hàng
        $('#checkoutForm').on('submit', function() {
            var $btn = $('#btn_submit_order');
            $btn.prop('disabled', true);
            $('#submit_btn_text').text('Đang xử lý đơn hàng...');
            $('#submit_btn_spinner').removeClass('d-none');
        });
    });
</script>
@endpush
@endsection
