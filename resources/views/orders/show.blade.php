@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_code . ' - ATINO Store')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm border small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Lịch sử đơn hàng</a></li>
        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">#{{ $order->order_code }}</li>
    </ol>
</nav>

@php
    $statusBadges = [
        'pending' => ['text' => 'Chờ xử lý', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'icon' => 'fa-clock'],
        'confirmed' => ['text' => 'Đã xác nhận', 'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle', 'icon' => 'fa-clipboard-check'],
        'processing' => ['text' => 'Đang đóng gói', 'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle', 'icon' => 'fa-box-archive'],
        'shipping' => ['text' => 'Đang giao hàng', 'class' => 'bg-primary-subtle text-primary border border-primary-subtle', 'icon' => 'fa-truck-fast'],
        'completed' => ['text' => 'Hoàn thành', 'class' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'fa-circle-check'],
        'cancelled' => ['text' => 'Đã hủy', 'class' => 'bg-danger-subtle text-danger border border-danger-subtle', 'icon' => 'fa-circle-xmark'],
        'returned' => ['text' => 'Trả hàng/Hoàn tiền', 'class' => 'bg-secondary-subtle text-secondary border', 'icon' => 'fa-rotate-left'],
    ];
    $badge = $statusBadges[$order->status] ?? ['text' => $order->status, 'class' => 'bg-secondary text-white', 'icon' => 'fa-tag'];
@endphp

<!-- Header thông tin đơn hàng -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
    <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="fw-extrabold text-dark mb-0 font-monospace">Đơn Hàng #{{ $order->order_code }}</h4>
                <button type="button" class="btn btn-light btn-sm border rounded-pill px-2.5 py-0.5 extra-small" onclick="copyToClipboard('{{ $order->order_code }}', 'Đã copy Mã đơn hàng!')" title="Copy mã đơn hàng">
                    <i class="fa-regular fa-copy"></i>
                </button>
                <span class="badge {{ $badge['class'] }} px-3 py-1.5 rounded-pill small fw-bold">
                    <i class="fa-solid {{ $badge['icon'] }} me-1"></i>{{ $badge['text'] }}
                </span>
            </div>
            <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i>Thời gian đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}</span>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <!-- Nút In Hóa Đơn -->
            <a href="{{ route('orders.invoice', $order->order_code) }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold shadow-sm">
                <i class="fa-solid fa-print me-1"></i> In hóa đơn
            </a>

            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Trở về danh sách
            </a>

            <!-- Nút Mua lại -->
            <form action="{{ route('orders.reorder', $order->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light border btn-sm rounded-pill px-3 fw-bold hover-bg-dark hover-text-white shadow-sm">
                    <i class="fa-solid fa-rotate-right me-1"></i> Mua lại đơn này
                </button>
            </form>

            <!-- Nút Yêu Cầu Đổi / Trả hàng nếu đủ điều kiện -->
            @if($order->canBeReturned())
                <button type="button" class="btn btn-warning text-dark btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#returnRequestModal">
                    <i class="fa-solid fa-rotate-left me-1"></i> Yêu Cầu Đổi / Trả
                </button>
            @endif

            <!-- Nút Hủy Đơn Hàng mở modal nếu ở trạng thái pending -->
            @if($order->canBeCancelled())
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                    <i class="fa-solid fa-ban me-1"></i> Hủy Đơn Hàng
                </button>
            @endif
        </div>
    </div>
</div>

<!-- BANNER THÔNG TIN YÊU CẦU ĐỔI / TRẢ HÀNG (NẾU CÓ) -->
@if($order->returns->isNotEmpty())
    @php
        $latestReturn = $order->returns->first();
        $retBadge = $latestReturn->status_badge;
        $retBorderColor = match($latestReturn->status) {
            'approved' => 'border-success bg-success-subtle bg-opacity-25',
            'rejected' => 'border-danger bg-danger-subtle bg-opacity-25',
            'completed' => 'border-primary bg-primary-subtle bg-opacity-25',
            default => 'border-warning bg-warning-subtle bg-opacity-25',
        };
    @endphp
    <div class="card border {{ $retBorderColor }} rounded-4 mb-4 shadow-sm" id="returnSection">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 border-bottom pb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center bg-white border shadow-xs" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-rotate-left fs-5 text-dark"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            Yêu Cầu {{ $latestReturn->type_label }} (#REQ-{{ $latestReturn->id }})
                        </h6>
                        <span class="text-muted extra-small">Gửi lúc: {{ $latestReturn->created_at->format('H:i - d/m/Y') }}</span>
                    </div>
                </div>

                <div>
                    <span class="badge {{ $retBadge['class'] }} px-3 py-2 rounded-pill small fw-bold">
                        <i class="fa-solid {{ $retBadge['icon'] }} me-1"></i> {{ $retBadge['text'] }}
                    </span>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-7">
                    <div class="small mb-2">
                        <span class="text-muted">Lý do từ khách hàng:</span> 
                        <strong class="text-dark">{{ $latestReturn->reason }}</strong>
                    </div>
                    @if($latestReturn->note)
                        <div class="small mb-2">
                            <span class="text-muted">Ghi chú cụ thể:</span> 
                            <span class="text-dark">{{ $latestReturn->note }}</span>
                        </div>
                    @endif

                    @if($latestReturn->type === 'return' && $latestReturn->bank_account_number)
                        <div class="p-2.5 bg-white rounded-3 border extra-small mb-2">
                            <strong class="text-dark d-block mb-1"><i class="fa-solid fa-building-columns me-1 text-primary"></i>Thông tin hoàn tiền:</strong>
                            <div>Ngân hàng: <strong>{{ $latestReturn->bank_name }}</strong></div>
                            <div>Số tài khoản: <strong class="font-monospace text-danger">{{ $latestReturn->bank_account_number }}</strong> - Chủ TK: <strong>{{ $latestReturn->bank_account_holder }}</strong></div>
                        </div>
                    @endif

                    @if(!empty($latestReturn->images))
                        <div class="mt-2">
                            <div class="extra-small text-muted mb-1.5 fw-bold"><i class="fa-regular fa-image me-1"></i>Hình ảnh bằng chứng đính kèm:</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($latestReturn->images as $imgPath)
                                    @php
                                        $fullImg = str_starts_with($imgPath, 'http') ? $imgPath : asset('storage/' . $imgPath);
                                    @endphp
                                    <a href="{{ $fullImg }}" target="_blank" class="d-inline-block">
                                        <img src="{{ $fullImg }}" alt="Ảnh bằng chứng" class="rounded-3 border object-fit-cover shadow-xs" style="width: 70px; height: 70px;">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-5">
                    @if($latestReturn->status === 'approved')
                        <div class="p-3 bg-success-subtle text-success-emphasis rounded-3 border border-success-subtle h-100">
                            <h6 class="fw-bold extra-small text-success mb-1.5 d-flex align-items-center gap-1.5">
                                <i class="fa-solid fa-circle-check fs-6"></i>
                                <span>Phản Hồi & Hướng Dẫn Từ Shop:</span>
                            </h6>
                            <div class="small">{{ $latestReturn->admin_note ?: 'Yêu cầu của bạn đã được chấp thuận. Vui lòng đóng gói sản phẩm còn nguyên tem mác và gửi về địa chỉ shop để được hỗ trợ đổi/hoàn tiền.' }}</div>
                            <div class="extra-small text-muted mt-2">Duyệt lúc: {{ $latestReturn->processed_at?->format('H:i d/m/Y') }}</div>
                        </div>
                    @elseif($latestReturn->status === 'rejected')
                        <div class="p-3 bg-danger-subtle text-danger-emphasis rounded-3 border border-danger-subtle h-100">
                            <h6 class="fw-bold extra-small text-danger mb-1.5 d-flex align-items-center gap-1.5">
                                <i class="fa-solid fa-circle-xmark fs-6"></i>
                                <span>Lý Do Từ Chối Yêu Cầu:</span>
                            </h6>
                            <div class="small">{{ $latestReturn->admin_note }}</div>
                            <div class="extra-small text-muted mt-2">Xử lý lúc: {{ $latestReturn->processed_at?->format('H:i d/m/Y') }}</div>
                        </div>
                    @else
                        <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3 border border-warning-subtle h-100">
                            <h6 class="fw-bold extra-small text-warning-emphasis mb-1.5 d-flex align-items-center gap-1.5">
                                <i class="fa-solid fa-clock-rotate-left fs-6"></i>
                                <span>Đang Trong Quá Trình Xem Xét:</span>
                            </h6>
                            <div class="small">Chuyên viên hỗ trợ của ATINO Store đang kiểm tra hình ảnh và thông tin của bạn. Chúng tôi sẽ phản hồi trong vòng 24 giờ làm việc.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<!-- TIMELINE TIẾN TRÌNH ĐƠN HÀNG (STEPPER NGANG) -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
    <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-route text-dark"></i>
            <span>Tiến Trình Đơn Hàng</span>
        </h6>

        <!-- Toggle nút mở nhật ký chi tiết -->
        <button class="btn btn-sm btn-outline-secondary rounded-pill extra-small fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#statusHistoryCollapse" aria-expanded="false">
            <i class="fa-solid fa-clock-rotate-left me-1"></i> Lịch sử cập nhật ({{ $order->statusHistories->count() }})
        </button>
    </div>
    
    <div class="card-body p-4">
        @if($order->status === 'cancelled')
            <div class="alert alert-danger border-0 rounded-4 p-3.5 d-flex align-items-center gap-3 mb-0 shadow-sm">
                <i class="fa-solid fa-circle-xmark fs-2 text-danger"></i>
                <div>
                    <h6 class="fw-bold text-danger mb-1">Đơn hàng này đã bị hủy.</h6>
                    <div class="small text-danger-emphasis">
                        {{ $order->statusHistories->where('status', 'cancelled')->last()?->note ?? 'Khách hàng hoặc quản trị viên đã xử lý hủy đơn hàng này.' }}
                    </div>
                </div>
            </div>
        @else
            @php
                $stepMap = ['pending' => 1, 'confirmed' => 2, 'processing' => 2, 'shipping' => 3, 'completed' => 4];
                $currentStep = $stepMap[$order->status] ?? 1;

                $pendingHistory = $order->statusHistories->where('status', 'pending')->last();
                $confirmedHistory = $order->statusHistories->whereIn('status', ['confirmed', 'processing'])->last();
                $shippingHistory = $order->statusHistories->where('status', 'shipping')->last();
                $completedHistory = $order->statusHistories->where('status', 'completed')->last();
            @endphp

            <!-- Thanh tiến trình Stepper ngang -->
            <div class="position-relative my-3 px-3">
                <div class="progress position-absolute top-50 start-0 translate-middle-y w-100" style="height: 4px; z-index: 1;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ (($currentStep - 1) / 3) * 100 }}%;"></div>
                </div>

                <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                    <!-- Step 1: Đặt hàng -->
                    <div class="text-center bg-white px-2">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm {{ $currentStep >= 1 ? 'bg-success text-white' : 'bg-light text-muted border' }}" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <div class="small fw-bold mt-2 {{ $currentStep >= 1 ? 'text-dark' : 'text-muted' }}">Đặt hàng</div>
                        <div class="extra-small text-muted">{{ $pendingHistory ? $pendingHistory->created_at->format('H:i d/m') : '' }}</div>
                    </div>

                    <!-- Step 2: Xác nhận -->
                    <div class="text-center bg-white px-2">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm {{ $currentStep >= 2 ? 'bg-success text-white' : 'bg-light text-muted border' }}" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                        <div class="small fw-bold mt-2 {{ $currentStep >= 2 ? 'text-dark' : 'text-muted' }}">Đã xác nhận</div>
                        <div class="extra-small text-muted">{{ $confirmedHistory ? $confirmedHistory->created_at->format('H:i d/m') : '' }}</div>
                    </div>

                    <!-- Step 3: Đang giao -->
                    <div class="text-center bg-white px-2">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm {{ $currentStep >= 3 ? 'bg-success text-white' : 'bg-light text-muted border' }}" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <div class="small fw-bold mt-2 {{ $currentStep >= 3 ? 'text-dark' : 'text-muted' }}">Đang giao hàng</div>
                        <div class="extra-small text-muted">{{ $shippingHistory ? $shippingHistory->created_at->format('H:i d/m') : '' }}</div>
                    </div>

                    <!-- Step 4: Hoàn thành -->
                    <div class="text-center bg-white px-2">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm {{ $currentStep >= 4 ? 'bg-success text-white' : 'bg-light text-muted border' }}" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="small fw-bold mt-2 {{ $currentStep >= 4 ? 'text-success' : 'text-muted' }}">Hoàn thành</div>
                        <div class="extra-small text-muted">{{ $completedHistory ? $completedHistory->created_at->format('H:i d/m') : '' }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Thu gọn Lịch sử nhật ký chi tiết dạng Accordion Collapse -->
        <div class="collapse mt-4 pt-3 border-top" id="statusHistoryCollapse">
            <h6 class="fw-bold text-dark small mb-3"><i class="fa-solid fa-history me-1 text-primary"></i>Nhật Ký Chi Tiết:</h6>
            <div class="ps-3 border-start ms-2">
                @foreach($order->statusHistories as $history)
                    @php
                        $statusText = [
                            'pending' => 'Khởi tạo đơn hàng thành công',
                            'confirmed' => 'Đơn hàng đã được xác nhận',
                            'processing' => 'Đang chuẩn bị đóng gói hàng',
                            'shipping' => 'Đơn hàng đang trên đường giao đến bạn',
                            'completed' => 'Giao hàng thành công',
                            'cancelled' => 'Đơn hàng đã hủy',
                            'returned' => 'Yêu cầu trả hàng/hoàn tiền',
                        ][$history->status] ?? $history->status;
                    @endphp
                    <div class="position-relative ps-4 pb-3">
                        <div class="position-absolute top-0 start-0 translate-middle-x rounded-circle bg-dark" style="width: 10px; height: 10px; margin-left: -1px; margin-top: 5px;"></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <strong class="text-dark small">{{ $statusText }}</strong>
                            <span class="text-muted extra-small">{{ $history->created_at->format('H:i - d/m/Y') }}</span>
                        </div>
                        @if($history->note)
                            <div class="text-muted extra-small mt-1 bg-light p-2 rounded border">{{ $history->note }}</div>
                        @endif
                        <div class="extra-small text-muted opacity-75 mt-0.5">
                            Cập nhật bởi: {{ $history->updated_by_name ?? 'Hệ thống' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- CỘT TRÁI: DANH SÁCH SẢN PHẨM TRONG ĐƠN -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
            <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-shirt text-dark"></i>
                    <span>Chi Tiết Sản Phẩm Trong Đơn</span>
                </h6>
                <span class="badge bg-light text-dark border extra-small">{{ $order->items->count() }} món hàng</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light extra-small">
                        <tr>
                            <th class="ps-4 py-3">SẢN PHẨM</th>
                            <th class="text-center">ĐƠN GIÁ</th>
                            <th class="text-center">SỐ LƯỢNG</th>
                            <th class="text-end pe-4">THÀNH TIỀN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            @php
                                $product = $item->product;
                                $imgObj = $product?->primaryImage;
                                $imageUrl = 'https://placehold.co/100x100?text=ATINO';
                                if ($imgObj && $imgObj->image_url) {
                                    $imageUrl = str_starts_with($imgObj->image_url, 'http') ? $imgObj->image_url : asset('storage/' . $imgObj->image_url);
                                }
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" 
                                             class="rounded-3 border object-fit-cover shadow-sm flex-shrink-0" style="width: 62px; height: 62px;">
                                        <div>
                                            <div class="fw-bold text-dark mb-0.5">
                                                @if($product)
                                                    <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none text-dark hover-text-primary">
                                                        {{ $item->product_name }}
                                                    </a>
                                                @else
                                                    {{ $item->product_name }}
                                                @endif
                                            </div>
                                            <span class="badge bg-light text-secondary border extra-small">{{ $item->variant_info }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-semibold text-dark small">{{ number_format($item->price) }}đ</td>
                                <td class="text-center"><span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold">x{{ $item->quantity }}</span></td>
                                <td class="text-end pe-4 fw-extrabold text-danger">{{ number_format($item->total_price) }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: THÔNG TIN GIAO HÀNG & TÓM TẮT THANH TOÁN -->
    <div class="col-lg-4">
        <!-- Thông tin nhận hàng -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white py-3.5 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-location-dot text-danger"></i>
                    <span>Thông Tin Nhận Hàng</span>
                </h6>
            </div>
            <div class="card-body p-4 small text-secondary">
                <p class="mb-2"><strong class="text-dark">Người nhận:</strong> {{ $order->recipient_name }}</p>
                <p class="mb-2"><strong class="text-dark">Số điện thoại:</strong> {{ $order->recipient_phone }}</p>
                <p class="mb-2"><strong class="text-dark">Địa chỉ giao:</strong> {{ $order->shipping_address }}</p>
                @if($order->note)
                    <div class="bg-light p-2.5 rounded-3 border mt-3">
                        <strong class="text-dark extra-small d-block mb-0.5"><i class="fa-regular fa-note-sticky me-1"></i>Ghi chú từ bạn:</strong>
                        <span class="extra-small text-muted">{{ $order->note }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tóm tắt chi phí & Thanh Toán -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white py-3.5 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-credit-card text-dark"></i>
                    <span>Thanh Toán & Hóa Đơn</span>
                </h6>
            </div>
            <div class="card-body p-4 small">
                @php
                    $payment = $order->payment ?? $order->payments->first();
                    $paymentStatus = $payment->payment_status ?? 'pending';
                    $isPaid = in_array($paymentStatus, ['paid', 'completed']) || $order->status === 'completed';
                    $paymentMethodName = match($payment->payment_method ?? 'COD') {
                        'bank_transfer' => 'Chuyển khoản Ngân hàng (VietQR)',
                        'momo' => 'Ví điện tử MoMo (MoMo QR)',
                        default => 'Tiền mặt khi nhận hàng (COD)',
                    };
                @endphp
                
                <div class="mb-3 p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                        <span class="text-muted extra-small fw-bold text-uppercase">Hình thức:</span>
                        @if($isPaid)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill extra-small fw-bold">
                                <i class="fa-solid fa-circle-check me-1"></i>Đã thanh toán
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill extra-small fw-bold">
                                <i class="fa-solid fa-clock me-1"></i>Chờ thanh toán
                            </span>
                        @endif
                    </div>
                    <strong class="text-dark d-block">{{ $paymentMethodName }}</strong>
                </div>

                @if($isPaid)
                    <div class="p-3 bg-success-subtle rounded-3 border border-success-subtle mb-3">
                        <div class="d-flex align-items-center gap-2 text-success fw-bold mb-1">
                            <i class="fa-solid fa-circle-check fs-5"></i>
                            <span>Đơn hàng ĐÃ THANH TOÁN</span>
                        </div>
                        <div class="small text-dark mt-1">
                            Số tiền đã thanh toán: <strong class="text-danger fs-6">{{ number_format($order->total_amount) }}đ</strong>
                        </div>
                    </div>
                @else
                    @php
                        $siteSettings = \Illuminate\Support\Facades\Cache::get('site_settings', \App\Http\Controllers\Admin\AdminSettingsController::getDefaultSettings());
                        $bankCode = $siteSettings['bank_code'] ?? 'vietcombank';
                        $bankName = $siteSettings['bank_name'] ?? 'Vietcombank';
                        $bankAccount = $siteSettings['bank_account'] ?? '9973797151';
                        $bankAccountName = $siteSettings['bank_account_name'] ?? 'LUU VAN HOANG';
                        $bankSyntax = trim($siteSettings['bank_transfer_syntax'] ?? 'TT');
                        $momoPhone = trim($siteSettings['momo_phone'] ?? '0973797151');
                        $momoName = trim($siteSettings['momo_name'] ?? 'LUU VAN HOANG');
                        $orderTransferContent = ($bankSyntax ?: 'TT').' '.$order->order_code;
                        $vietQrUrl = "https://img.vietqr.io/image/{$bankCode}-{$bankAccount}-compact2.png?amount={$order->total_amount}&addInfo=".urlencode($orderTransferContent).'&accountName='.urlencode($bankAccountName);
                        $momoQrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("2|99|{$momoPhone}|{$momoName}|info@store.vn|0|0|{$order->total_amount}");
                    @endphp

                    @if($payment && $payment->payment_method === 'bank_transfer' && $payment->payment_status === 'pending')
                        <div class="mb-3 pt-1">
                            <!-- Live status indicator tinh gọn -->
                            <div class="text-center mb-2.5">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill py-1.5 px-3 extra-small fw-semibold d-inline-flex align-items-center gap-1.5">
                                    <span class="spinner-grow spinner-grow-sm text-primary" role="status" style="width: 7px; height: 7px;"></span>
                                    <span>Chờ chuyển khoản (Tự động duyệt 24/7)</span>
                                </span>
                            </div>

                            <!-- Ảnh QR VietQR vừa vặn, thanh lịch -->
                            <div class="text-center mb-2.5">
                                <div class="p-1.5 bg-white rounded-3 border shadow-xs d-inline-block">
                                    <img src="{{ $vietQrUrl }}" alt="VietQR {{ $bankName }}" class="img-fluid rounded-2" style="max-height: 185px; width: auto;">
                                </div>
                                <div class="extra-small text-muted mt-1">
                                    <i class="fa-solid fa-qrcode me-1 text-primary"></i>Quét mã qua App Ngân hàng hoặc MoMo
                                </div>
                            </div>

                            <!-- 2 dòng thông tin cốt lõi (Không box-in-box) -->
                            <div class="p-2.5 bg-light rounded-3 border extra-small mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1.5 pb-1.5 border-bottom border-light-subtle">
                                    <span class="text-muted">STK ({{ $bankName }}):</span>
                                    <div class="d-flex align-items-center">
                                        <strong class="font-monospace text-dark me-1.5 fs-6">{{ $bankAccount }}</strong>
                                        <button type="button" class="copy-btn-action" onclick="copyToClipboard('{{ $bankAccount }}', 'Đã copy STK!')">Copy</button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Nội dung CK:</span>
                                    <div class="d-flex align-items-center">
                                        <strong class="text-danger font-monospace fw-bold me-1.5">{{ $orderTransferContent }}</strong>
                                        <button type="button" class="copy-btn-action" onclick="copyToClipboard('{{ $orderTransferContent }}', 'Đã copy Nội dung CK!')">Copy</button>
                                    </div>
                                </div>
                            </div>

                            <div class="extra-small text-muted text-center mb-1">
                                <i class="fa-solid fa-bolt text-warning me-1"></i>Hệ thống tự động duyệt sang <strong>Đã xác nhận</strong> ngay khi nhận tiền.
                            </div>
                        </div>
                    @elseif($payment && $payment->payment_method === 'momo' && $payment->payment_status === 'pending')
                        @php
                            $momoMode = $siteSettings['momo_mode'] ?? 'personal';
                        @endphp
                        @if($momoMode === 'gateway')
                            <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle mb-3">
                                <div class="d-flex align-items-center gap-2 text-danger fw-bold small mb-2">
                                    <i class="fa-solid fa-wallet fs-5"></i>
                                    <span>Thanh toán qua Cổng MoMo</span>
                                </div>
                                <p class="extra-small text-dark mb-3">
                                    Đơn hàng đang chờ thanh toán số tiền <strong class="text-danger">{{ number_format($order->total_amount) }}đ</strong> qua Cổng MoMo bảo mật. Bạn có thể nhấn nút bên dưới để chuyển sang giao diện thanh toán MoMo.
                                </p>
                                <form action="{{ route('orders.momoPay', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100 fw-bold rounded-pill py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        <span>Tiến Hành Thanh Toán MoMo</span>
                                    </button>
                                </form>
                                <div class="extra-small text-muted text-center mt-2">
                                    <i class="fa-solid fa-shield-halved text-success me-1"></i>Bảo mật giao dịch theo chuẩn MoMo
                                </div>
                            </div>
                        @else
                            <div class="mb-3 pt-1">
                                <!-- Live status indicator MoMo tinh gọn -->
                                <div class="text-center mb-2.5">
                                    <span class="badge rounded-pill py-1.5 px-3 extra-small fw-semibold d-inline-flex align-items-center gap-1.5" style="background-color: #fdf2f7; color: #a50064; border: 1px solid #f4c1dc;">
                                        <span class="spinner-grow spinner-grow-sm" role="status" style="width: 7px; height: 7px; background-color: #a50064;"></span>
                                        <span>Chờ quét MoMo (Tự động duyệt 24/7)</span>
                                    </span>
                                </div>

                                <!-- Mã QR MoMo vừa vặn, thanh lịch -->
                                <div class="text-center mb-2.5">
                                    <div class="p-1.5 bg-white rounded-3 border shadow-xs d-inline-block position-relative" style="border-color: #f4c1dc !important;">
                                        <img src="{{ $vietQrUrl }}" alt="QR Thanh toán MoMo" class="img-fluid rounded-2" style="max-height: 185px; width: auto;">
                                    </div>
                                    <div class="extra-small text-muted mt-1">
                                        <i class="fa-solid fa-mobile-screen me-1" style="color: #a50064;"></i>Mở <strong>App MoMo</strong> &rarr; Chọn <strong>"Quét mã"</strong>
                                    </div>
                                </div>

                                <!-- 2 dòng thông tin cốt lõi MoMo -->
                                <div class="p-2.5 rounded-3 border extra-small mb-2" style="background-color: #fff9fc; border-color: #f4c1dc !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-1.5 pb-1.5 border-bottom" style="border-color: #fae2ee !important;">
                                        <span class="text-muted">SĐT Ví MoMo:</span>
                                        <div class="d-flex align-items-center">
                                            <strong class="font-monospace me-1.5 fs-6" style="color: #a50064;">{{ $momoPhone }}</strong>
                                            <button type="button" class="copy-btn-action" onclick="copyToClipboard('{{ $momoPhone }}', 'Đã copy SĐT MoMo!')">Copy</button>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Nội dung CK:</span>
                                        <div class="d-flex align-items-center">
                                            <strong class="text-danger font-monospace fw-bold me-1.5">{{ $orderTransferContent }}</strong>
                                            <button type="button" class="copy-btn-action" onclick="copyToClipboard('{{ $orderTransferContent }}', 'Đã copy Nội dung CK!')">Copy</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="extra-small text-muted text-center mb-1">
                                    <i class="fa-solid fa-bolt text-warning me-1"></i>Hệ thống tự động duyệt đơn hàng ngay khi nhận được tiền.
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="p-3 bg-warning-subtle rounded-3 border border-warning-subtle mb-3 extra-small text-dark">
                            <i class="fa-solid fa-circle-info text-warning me-1"></i> Thanh toán tiền mặt khi nhận hàng (COD): <strong class="text-danger fs-6 d-block mt-1">{{ number_format($order->total_amount) }}đ</strong>
                        </div>
                    @endif
                @endif

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính tiền hàng:</span>
                    <span class="fw-semibold text-dark">{{ number_format($order->subtotal) }}đ</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phí vận chuyển:</span>
                    <span class="fw-semibold text-dark">{{ number_format($order->shipping_fee) }}đ</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span><i class="fa-solid fa-tag me-1"></i>Mã giảm giá:</span>
                        <span class="fw-bold">-{{ number_format($order->discount_amount) }}đ</span>
                    </div>
                @endif
                <hr class="my-2.5">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark fs-6">Tổng cộng:</span>
                    <span class="fw-extrabold text-danger fs-5">{{ number_format($order->total_amount) }}đ</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HỦY ĐƠN HÀNG THÂN THIỆN (CLIENT) -->
@if($order->canBeCancelled())
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-danger-subtle p-2 text-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark fs-6" id="cancelOrderModalLabel">Hủy Đơn Hàng #{{ $order->order_code }}</h5>
                                <p class="text-muted extra-small mb-0">Vui lòng cho shop biết lý do bạn muốn hủy đơn hàng</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-2">Chọn lý do hủy đơn: <span class="text-danger">*</span></label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="show_reason1" value="Đổi ý, không muốn mua nữa" checked>
                                    <label class="form-check-label small fw-medium text-dark cursor-pointer" for="show_reason1">
                                        Đổi ý, không muốn mua nữa
                                    </label>
                                </div>
                                <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="show_reason2" value="Muốn thay đổi địa chỉ nhận hàng / SĐT">
                                    <label class="form-check-label small fw-medium text-dark cursor-pointer" for="show_reason2">
                                        Muốn thay đổi địa chỉ nhận hàng / SĐT
                                    </label>
                                </div>
                                <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="show_reason3" value="Muốn đổi size / màu hoặc sản phẩm khác">
                                    <label class="form-check-label small fw-medium text-dark cursor-pointer" for="show_reason3">
                                        Muốn đổi size / màu hoặc sản phẩm khác
                                    </label>
                                </div>
                                <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="show_reason4" value="Quên áp dụng mã giảm giá">
                                    <label class="form-check-label small fw-medium text-dark cursor-pointer" for="show_reason4">
                                        Quên áp dụng mã giảm giá (voucher)
                                    </label>
                                </div>
                                <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="show_reason5" value="Đặt trùng đơn / tạo nhầm đơn">
                                    <label class="form-check-label small fw-medium text-dark cursor-pointer" for="show_reason5">
                                        Đặt trùng đơn / tạo nhầm đơn hàng
                                    </label>
                                </div>
                                <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="show_reason6" value="Lý do khác">
                                    <label class="form-check-label small fw-medium text-dark cursor-pointer" for="show_reason6">
                                        Lý do khác...
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="show_cancel_note" class="form-label fw-bold text-dark small mb-1">Ghi chú thêm (tùy chọn):</label>
                            <textarea class="form-control rounded-3 text-secondary" id="show_cancel_note" name="cancel_note" rows="2" placeholder="Nhập thêm chi tiết hoặc lý do nếu có..."></textarea>
                        </div>

                        <div class="alert alert-info py-2 px-3 rounded-3 extra-small mb-0 border-0 bg-info-subtle text-info-emphasis d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-info fs-6"></i>
                            <span>Mã giảm giá và số lượng sản phẩm trong đơn sẽ được tự động hoàn lại cho bạn sau khi hủy đơn thành công.</span>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-3 py-2 btn-sm fw-bold" data-bs-dismiss="modal">Đóng, Giữ đơn</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 btn-sm fw-bold shadow-sm">
                            <i class="fa-solid fa-ban me-1"></i> Xác Nhận Hủy Đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- MODAL GỬI YÊU CẦU ĐỔI / TRẢ HÀNG (CLIENT) -->
@if($order->canBeReturned())
    <div class="modal fade" id="returnRequestModal" tabindex="-1" aria-labelledby="returnRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('orders.requestReturn', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning-subtle p-2 text-warning d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-rotate-left fs-5 text-dark"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark fs-6" id="returnRequestModalLabel">Yêu Cầu Đổi / Trả Hàng - Đơn #{{ $order->order_code }}</h5>
                                <p class="text-muted extra-small mb-0">Hỗ trợ đổi size hoặc trả hàng hoàn tiền nhanh chóng trong vòng 7 ngày</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-3">
                        <!-- Chọn Hình thức yêu cầu -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-2">Hình thức bạn mong muốn: <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="form-check p-3 border rounded-3 bg-light bg-opacity-50 h-100 return-type-box cursor-pointer">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="type" id="type_exchange" value="exchange" checked onchange="toggleRefundFields(false)">
                                        <label class="form-check-label fw-bold text-dark small cursor-pointer" for="type_exchange">
                                            <i class="fa-solid fa-shirt text-primary me-1"></i> Đổi Size / Đổi Màu Khác
                                        </label>
                                        <div class="text-muted extra-small mt-1 ps-4">Phù hợp khi bạn mặc không vừa chật/rộng hoặc muốn đổi sang phân loại khác.</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check p-3 border rounded-3 bg-light bg-opacity-50 h-100 return-type-box cursor-pointer">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="type" id="type_return" value="return" onchange="toggleRefundFields(true)">
                                        <label class="form-check-label fw-bold text-dark small cursor-pointer" for="type_return">
                                            <i class="fa-solid fa-money-bill-transfer text-danger me-1"></i> Trả Hàng & Hoàn Tiền
                                        </label>
                                        <div class="text-muted extra-small mt-1 ps-4">Phù hợp khi hàng bị lỗi, rách, sai mẫu hoặc bạn không có nhu cầu sử dụng.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chọn Lý do -->
                        <div class="mb-3">
                            <label for="return_reason" class="form-label fw-bold text-dark small mb-1">Lý do yêu cầu: <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3 small" id="return_reason" name="reason" required>
                                <option value="">-- Chọn lý do phù hợp nhất --</option>
                                <option value="Mặc không vừa (bị chật / bị rộng)">Mặc không vừa (bị chật / bị rộng)</option>
                                <option value="Sản phẩm bị lỗi (sứt chỉ, rách, bẩn, lỗi khóa kéo...)">Sản phẩm bị lỗi (sứt chỉ, rách, bẩn, lỗi phụ kiện...)</option>
                                <option value="Giao sai mẫu / sai màu sắc / sai kích thước">Giao sai mẫu / sai màu sắc / sai kích thước so với đơn đặt</option>
                                <option value="Sản phẩm khác nhiều so với hình ảnh mô tả">Sản phẩm khác nhiều so với hình ảnh mô tả</option>
                                <option value="Không ưng ý sau khi nhận hàng">Không ưng ý sau khi nhận hàng</option>
                                <option value="Lý do khác">Lý do khác...</option>
                            </select>
                        </div>

                        <!-- Ghi chú chi tiết -->
                        <div class="mb-3">
                            <label for="return_note" class="form-label fw-bold text-dark small mb-1">Mô tả cụ thể yêu cầu của bạn: <span class="text-muted fw-normal">(Ví dụ: muốn đổi từ size L sang size M, hoặc vị trí đường may bị lỗi...)</span></label>
                            <textarea class="form-control rounded-3 text-secondary" id="return_note" name="note" rows="3" placeholder="Nhập chi tiết nhu cầu đổi size/màu hoặc mô tả tình trạng sản phẩm..."></textarea>
                        </div>

                        <!-- Tải ảnh minh chứng -->
                        <div class="mb-3">
                            <label for="return_images" class="form-label fw-bold text-dark small mb-1">
                                Tải ảnh chụp sản phẩm <span class="text-muted fw-normal">(Tối đa 3 ảnh, rõ tem mác hoặc chỗ lỗi):</span>
                            </label>
                            <input class="form-control rounded-3" type="file" id="return_images" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text extra-small text-muted">Định dạng hỗ trợ: JPG, PNG, WEBP. Dung lượng tối đa: 3MB / ảnh.</div>
                        </div>

                        <!-- Khối thông tin Hoàn Tiền qua Ngân Hàng (ẩn/hiện) -->
                        <div id="refundBankFields" class="p-3 bg-light rounded-3 border mb-3" style="display: none;">
                            <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1.5">
                                <i class="fa-solid fa-building-columns text-primary"></i>
                                <span>Thông Tin Nhận Tiền Hoàn Trả:</span>
                            </h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-bold text-dark mb-1">Tên Ngân Hàng: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="bank_name" placeholder="VD: Vietcombank, MB Bank...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-bold text-dark mb-1">Số Tài Khoản: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm rounded-2 font-monospace" name="bank_account_number" placeholder="VD: 0987654321">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-bold text-dark mb-1">Tên Chủ Tài Khoản: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm rounded-2" name="bank_account_holder" placeholder="VD: NGUYEN VAN A">
                                </div>
                            </div>
                            <div class="extra-small text-muted mt-1.5">* Tiền hoàn sẽ được chuyển khoản sau khi Shop nhận lại và kiểm tra sản phẩm đúng quy định.</div>
                        </div>

                        <div class="alert alert-warning py-2.5 px-3 rounded-3 extra-small mb-0 border-0 bg-warning-subtle text-warning-emphasis">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fa-solid fa-circle-exclamation fs-6 mt-0.5"></i>
                                <div>
                                    <strong class="d-block">Quy định đổi trả hàng tại ATINO:</strong>
                                    <span>Sản phẩm phải còn nguyên vẹn tem mác, chưa qua sử dụng hay giặt tẩy. Thời gian áp dụng trong vòng 7 ngày kể từ khi đơn hàng giao thành công.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-3 py-2 btn-sm fw-bold" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 btn-sm fw-bold shadow-sm">
                            <i class="fa-solid fa-paper-plane me-1"></i> Gửi Yêu Cầu Cho Shop
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    function toggleRefundFields(isReturn) {
        const bankFields = document.getElementById('refundBankFields');
        if (bankFields) {
            bankFields.style.display = isReturn ? 'block' : 'none';
        }
    }

    function copyToClipboard(text, message) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: message || 'Đã sao chép!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    alert(message);
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#returnSection') {
            const el = document.getElementById('returnSection');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
        }

        @if($payment && in_array($payment->payment_method, ['bank_transfer', 'momo']) && $payment->payment_status === 'pending')
        // Tự động kiểm tra trạng thái thanh toán theo thời gian thực (Polling mỗi 2.5s)
        let isCheckingPayment = false;
        const checkStatusUrl = "{{ route('orders.checkPaymentStatus', $order->order_code) }}";
        
        const paymentPollInterval = setInterval(async function() {
            if (isCheckingPayment) return;
            isCheckingPayment = true;

            try {
                const response = await fetch(checkStatusUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.is_paid) {
                        clearInterval(paymentPollInterval);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '🎉 Thanh toán thành công!',
                                text: 'Hệ thống đã tự động xác nhận đơn hàng #{{ $order->order_code }}. Đang cập nhật giao diện...',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            alert('Thanh toán thành công! Đơn hàng đã được xác nhận.');
                            window.location.reload();
                        }
                    }
                }
            } catch (err) {
                console.error('Lỗi kiểm tra trạng thái thanh toán:', err);
            } finally {
                isCheckingPayment = false;
            }
        }, 2500);
        @endif
    });
</script>
@endpush
@endsection
