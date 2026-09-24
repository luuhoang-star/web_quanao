@extends('layouts.app')

@section('title', 'Lịch Sử Đơn Hàng - ATINO Store')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm border small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('profile.show') }}" class="text-decoration-none text-muted">Tài khoản</a></li>
        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Lịch sử đơn hàng</li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fa-solid fa-bag-shopping text-dark"></i>
            <span>Lịch Sử Đơn Hàng</span>
        </h3>
        <p class="text-muted small mb-0">Quản lý và theo dõi tiến trình tất cả các đơn hàng bạn đã đặt tại ATINO</p>
    </div>
</div>

<!-- THANH TAB PHÂN LOẠI TRẠNG THÁI -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
    <div class="card-body p-2">
        <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto py-1 custom-scrollbar-mini" id="order-status-tabs">
            @php
                $tabs = [
                    'all' => ['label' => 'Tất cả', 'icon' => 'fa-list-ul', 'count' => $counts['all']],
                    'pending' => ['label' => 'Chờ xử lý', 'icon' => 'fa-clock', 'count' => $counts['pending']],
                    'confirmed' => ['label' => 'Đã xác nhận', 'icon' => 'fa-clipboard-check', 'count' => $counts['confirmed']],
                    'shipping' => ['label' => 'Đang giao', 'icon' => 'fa-truck-fast', 'count' => $counts['shipping']],
                    'completed' => ['label' => 'Hoàn thành', 'icon' => 'fa-circle-check', 'count' => $counts['completed']],
                    'cancelled' => ['label' => 'Đã hủy', 'icon' => 'fa-ban', 'count' => $counts['cancelled']],
                    'returned' => ['label' => 'Đổi/Trả hàng', 'icon' => 'fa-rotate-left', 'count' => $counts['returned'] ?? 0],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <li class="nav-item">
                    <a class="nav-link text-nowrap rounded-3 fw-bold px-3 py-2.5 d-flex align-items-center justify-content-center gap-2 {{ $currentStatus === $key ? 'active bg-dark text-white shadow-sm' : 'text-secondary hover-bg-light' }}" 
                       href="{{ route('orders.index', ['status' => $key]) }}" style="font-size: 0.88rem;">
                        <i class="fa-solid {{ $tab['icon'] }}"></i>
                        <span>{{ $tab['label'] }}</span>
                        @if($tab['count'] > 0)
                            <span class="badge rounded-pill {{ $currentStatus === $key ? 'bg-white text-dark' : 'bg-secondary-subtle text-dark border' }} extra-small">
                                {{ $tab['count'] }}
                            </span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- DANH SÁCH THẺ ĐƠN HÀNG (ORDER CARDS) -->
@if($orders->isEmpty())
    <div class="card border-0 shadow-sm rounded-4 bg-white text-center py-5">
        <div class="card-body p-5">
            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-4 mb-3" style="width: 90px; height: 90px;">
                <i class="fa-solid fa-box-open fa-3x text-muted opacity-50"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Chưa tìm thấy đơn hàng nào!</h5>
            <p class="text-muted small mb-4">
                @if($currentStatus !== 'all')
                    Bạn chưa có đơn hàng nào ở trạng thái này.
                @else
                    Bạn chưa từng đặt đơn hàng nào. Hãy khám phá các bộ sưu tập thời trang mới nhất ngay hôm nay!
                @endif
            </p>
            <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                <i class="fa-solid fa-store me-2"></i> Khám phá mua sắm ngay
            </a>
        </div>
    </div>
@else
    <div class="d-flex flex-column gap-4">
        @foreach($orders as $order)
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
                $badge = $statusBadges[$order->status] ?? ['text' => $order->status, 'class' => 'bg-light text-dark border', 'icon' => 'fa-tag'];
            @endphp

            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden hover-shadow transition-all">
                <!-- Header của Card Đơn Hàng -->
                <div class="card-header bg-light bg-opacity-50 py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-extrabold text-dark font-monospace fs-6">#{{ $order->order_code }}</span>
                        <span class="text-muted small">|</span>
                        <span class="text-muted small"><i class="fa-regular fa-calendar me-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($order->latestReturn)
                            @php
                                $latestRet = $order->latestReturn;
                                $retBadge = $latestRet->status_badge;
                            @endphp
                            <span class="badge {{ $retBadge['class'] }} px-2.5 py-1.5 rounded-pill extra-small fw-bold d-inline-flex align-items-center gap-1">
                                <i class="fa-solid {{ $retBadge['icon'] }}"></i>
                                <span>{{ $latestRet->type_label }}: {{ $retBadge['text'] }}</span>
                            </span>
                        @endif

                        <span class="badge {{ $badge['class'] }} px-3 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5">
                            <i class="fa-solid {{ $badge['icon'] }}"></i>
                            {{ $badge['text'] }}
                        </span>
                    </div>
                </div>

                <!-- Body chứa danh sách sản phẩm trong đơn -->
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($order->items as $item)
                            @php
                                $product = $item->product;
                                $imgObj = $product?->primaryImage;
                                $imageUrl = 'https://placehold.co/100x100?text=ATINO';
                                if ($imgObj && $imgObj->image_url) {
                                    $imageUrl = str_starts_with($imgObj->image_url, 'http') ? $imgObj->image_url : asset('storage/' . $imgObj->image_url);
                                }
                            @endphp
                            <div class="list-group-item p-3 p-md-4 border-bottom-0 border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <a href="{{ $product ? route('shop.show', $product->slug) : '#' }}" class="flex-shrink-0">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->product_name }}" 
                                             class="rounded-3 border object-fit-cover shadow-sm" style="width: 72px; height: 72px;">
                                    </a>

                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="fw-bold text-dark mb-1 text-truncate">
                                            @if($product)
                                                <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none text-dark hover-text-primary">
                                                    {{ $item->product_name }}
                                                </a>
                                            @else
                                                {{ $item->product_name }}
                                            @endif
                                        </h6>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <span class="badge bg-light text-secondary border extra-small fw-normal">
                                                {{ $item->variant_info }}
                                            </span>
                                            <span class="extra-small text-muted fw-bold">x{{ $item->quantity }}</span>
                                        </div>
                                    </div>

                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-semibold text-dark small">{{ number_format($item->price) }}đ</div>
                                        <div class="fw-extrabold text-danger small mt-0.5">{{ number_format($item->total_price) }}đ</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer chứa thông tin tổng tiền và các NÚT THAO TÁC NHANH -->
                <div class="card-footer bg-white p-3 p-md-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="small">
                        <span class="text-muted">Người nhận:</span> <strong class="text-dark">{{ $order->recipient_name }}</strong> 
                        <span class="text-muted ms-2">({{ $order->recipient_phone }})</span>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 ms-auto">
                        <div class="me-md-3 text-end">
                            <span class="extra-small text-muted d-block">Tổng số tiền:</span>
                            <span class="fs-5 fw-extrabold text-danger">{{ number_format($order->total_amount) }}đ</span>
                        </div>

                        <!-- Nút Xem Chi Tiết Tiến Trình -->
                        <a href="{{ route('orders.show', $order->order_code) }}" class="btn btn-outline-dark rounded-pill px-3 py-2 btn-sm fw-bold shadow-sm">
                            <i class="fa-solid fa-circle-info me-1"></i> Chi tiết
                        </a>

                        <!-- Nút Mua Lại (Re-order) cho tất cả các đơn -->
                        <form action="{{ route('orders.reorder', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light border rounded-pill px-3 py-2 btn-sm fw-bold hover-bg-dark hover-text-white shadow-sm">
                                <i class="fa-solid fa-rotate-right me-1"></i> Mua lại
                            </button>
                        </form>

                        <!-- Nút Yêu Cầu Đổi / Trả hàng nếu đủ điều kiện -->
                        @if($order->canBeReturned())
                            <a href="{{ route('orders.show', $order->order_code) }}#returnSection" class="btn btn-outline-warning text-dark border-warning rounded-pill px-3 py-2 btn-sm fw-bold shadow-sm">
                                <i class="fa-solid fa-rotate-left text-warning-emphasis me-1"></i> Đổi / Trả hàng
                            </a>
                        @endif

                        <!-- Nút Hủy Đơn Hàng mở Modal thân thiện nếu ở trạng thái pending -->
                        @if($order->canBeCancelled())
                            <button type="button" class="btn btn-outline-danger rounded-pill px-3 py-2 btn-sm fw-bold shadow-sm" 
                                    data-bs-toggle="modal" data-bs-target="#cancelModal_{{ $order->id }}">
                                <i class="fa-solid fa-ban me-1"></i> Hủy đơn
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- MODAL HỦY ĐƠN HÀNG THÂN THIỆN CHO KHÁCH HÀNG -->
            @if($order->canBeCancelled())
                <div class="modal fade" id="cancelModal_{{ $order->id }}" tabindex="-1" aria-labelledby="cancelModalLabel_{{ $order->id }}" aria-hidden="true">
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
                                            <h5 class="modal-title fw-bold text-dark fs-6" id="cancelModalLabel_{{ $order->id }}">Hủy Đơn Hàng #{{ $order->order_code }}</h5>
                                            <p class="text-muted extra-small mb-0">Vui lòng cho shop biết lý do bạn muốn hủy đơn hàng này</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark small mb-2">Chọn lý do hủy đơn: <span class="text-danger">*</span></label>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                                <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="reason1_{{ $order->id }}" value="Đổi ý, không muốn mua nữa" checked>
                                                <label class="form-check-label small fw-medium text-dark cursor-pointer" for="reason1_{{ $order->id }}">
                                                    Đổi ý, không muốn mua nữa
                                                </label>
                                            </div>
                                            <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                                <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="reason2_{{ $order->id }}" value="Muốn thay đổi địa chỉ nhận hàng / SĐT">
                                                <label class="form-check-label small fw-medium text-dark cursor-pointer" for="reason2_{{ $order->id }}">
                                                    Muốn thay đổi địa chỉ nhận hàng / SĐT
                                                </label>
                                            </div>
                                            <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                                <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="reason3_{{ $order->id }}" value="Muốn đổi size / màu hoặc sản phẩm khác">
                                                <label class="form-check-label small fw-medium text-dark cursor-pointer" for="reason3_{{ $order->id }}">
                                                    Muốn đổi size / màu hoặc sản phẩm khác
                                                </label>
                                            </div>
                                            <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                                <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="reason4_{{ $order->id }}" value="Quên áp dụng mã giảm giá">
                                                <label class="form-check-label small fw-medium text-dark cursor-pointer" for="reason4_{{ $order->id }}">
                                                    Quên áp dụng mã giảm giá (voucher)
                                                </label>
                                            </div>
                                            <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                                <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="reason5_{{ $order->id }}" value="Đặt trùng đơn / tạo nhầm đơn">
                                                <label class="form-check-label small fw-medium text-dark cursor-pointer" for="reason5_{{ $order->id }}">
                                                    Đặt trùng đơn / tạo nhầm đơn hàng
                                                </label>
                                            </div>
                                            <div class="form-check p-2.5 border rounded-3 bg-light bg-opacity-50">
                                                <input class="form-check-input ms-0 me-2" type="radio" name="reason" id="reason6_{{ $order->id }}" value="Lý do khác">
                                                <label class="form-check-label small fw-medium text-dark cursor-pointer" for="reason6_{{ $order->id }}">
                                                    Lý do khác...
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="cancel_note_{{ $order->id }}" class="form-label fw-bold text-dark small mb-1">Ghi chú thêm (tùy chọn):</label>
                                        <textarea class="form-control rounded-3 text-secondary" id="cancel_note_{{ $order->id }}" name="cancel_note" rows="2" placeholder="Nhập thêm chi tiết hoặc lý do nếu có..."></textarea>
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
        @endforeach
    </div>

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
@endif
@endsection
