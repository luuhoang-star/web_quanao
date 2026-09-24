@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_code)
@section('page_title', 'Chi Tiết Đơn Hàng')

@section('content')
<!-- Header thông tin đơn hàng & Cập nhật trạng thái -->
<div class="card admin-card mb-4">
    <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h5 class="fw-bold mb-0 text-dark">Đơn Hàng #{{ $order->order_code }}</h5>
                @php
                    $statusBadges = [
                        'pending' => ['text' => 'Chờ xử lý', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
                        'confirmed' => ['text' => 'Đã xác nhận', 'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
                        'processing' => ['text' => 'Đang chuẩn bị hàng', 'class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
                        'shipping' => ['text' => 'Đang giao hàng', 'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
                        'completed' => ['text' => 'Giao thành công', 'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
                        'cancelled' => ['text' => 'Đã hủy đơn', 'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
                        'returned' => ['text' => 'Trả hàng / Hoàn tiền', 'class' => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle'],
                    ];
                    $badge = $statusBadges[$order->status] ?? ['text' => $order->status_label, 'class' => 'bg-secondary'];
                @endphp
                <span class="badge {{ $badge['class'] }} px-3 py-2 fs-6">{{ $badge['text'] }}</span>
            </div>
            <span class="text-muted small">Thời gian khởi tạo: {{ $order->created_at->format('d/m/Y H:i:s') }}</span>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn btn-dark shadow-sm">
                <i class="fa-solid fa-print me-1.5"></i> In Hóa Đơn / Phiếu Gửi Hàng
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Trở về danh sách
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- CỘT TRÁI: DANH SÁCH MÓN HÀNG VÀ LỊCH SỬ TRẠNG THÁI -->
    <div class="col-lg-8">
        <!-- KHỐI YÊU CẦU ĐỔI / TRẢ HÀNG (NẾU CÓ) -->
        @if($order->returns->isNotEmpty())
            @foreach($order->returns as $ret)
                @php
                    $retBadge = $ret->status_badge;
                @endphp
                <div class="card admin-card mb-4 border-warning shadow-sm" id="returnCard">
                    <div class="card-header bg-warning-subtle py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-rotate-left me-2 text-warning-emphasis"></i>
                            Yêu Cầu {{ $ret->type_label }} (#REQ-{{ $ret->id }})
                        </h6>
                        <span class="badge {{ $retBadge['class'] }} px-3 py-1.5 fs-6">{{ $retBadge['text'] }}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <div class="small mb-2">
                                    <span class="text-muted">Thời gian gửi:</span>
                                    <strong class="text-dark">{{ $ret->created_at->format('d/m/Y H:i:s') }}</strong>
                                </div>
                                <div class="small mb-2">
                                    <span class="text-muted">Lý do khách đưa ra:</span>
                                    <strong class="text-dark">{{ $ret->reason }}</strong>
                                </div>
                                @if($ret->note)
                                    <div class="small mb-2">
                                        <span class="text-muted">Ghi chú cụ thể:</span>
                                        <span class="text-dark">{{ $ret->note }}</span>
                                    </div>
                                @endif

                                @if($ret->type === 'return' && $ret->bank_account_number)
                                    <div class="p-2.5 bg-light rounded-3 border extra-small mb-2">
                                        <strong class="text-dark d-block mb-1"><i class="fa-solid fa-building-columns me-1 text-primary"></i>Thông tin hoàn tiền cho khách:</strong>
                                        <div>Ngân hàng: <strong>{{ $ret->bank_name }}</strong></div>
                                        <div>STK: <strong class="font-monospace text-danger">{{ $ret->bank_account_number }}</strong> - Chủ TK: <strong>{{ $ret->bank_account_holder }}</strong></div>
                                    </div>
                                @endif

                                @if(!empty($ret->images))
                                    <div class="mt-2">
                                        <div class="extra-small text-muted mb-1 fw-bold"><i class="fa-regular fa-image me-1"></i>Hình ảnh bằng chứng:</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($ret->images as $img)
                                                @php
                                                    $url = str_starts_with($img, 'http') ? $img : asset('storage/' . $img);
                                                @endphp
                                                <a href="{{ $url }}" target="_blank" title="Bấm để xem ảnh phóng to">
                                                    <img src="{{ $url }}" class="rounded border object-fit-cover shadow-xs" style="width: 70px; height: 70px;">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-5">
                                @if($ret->status === 'pending')
                                    <div class="p-3 bg-light rounded border text-center">
                                        <div class="extra-small text-muted mb-2">Yêu cầu này đang chờ bạn xử lý:</div>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-success btn-sm px-3 fw-bold shadow-xs" data-bs-toggle="modal" data-bs-target="#adminApproveModal_{{ $ret->id }}">
                                                <i class="fa-solid fa-check me-1"></i> Chấp Thuận
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm px-3 fw-bold shadow-xs" data-bs-toggle="modal" data-bs-target="#adminRejectModal_{{ $ret->id }}">
                                                <i class="fa-solid fa-xmark me-1"></i> Từ Chối
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 bg-light rounded border">
                                        <div class="extra-small text-muted mb-1">
                                            Đã xử lý bởi: <strong class="text-dark">{{ $ret->processor?->name ?? 'Quản trị viên' }}</strong>
                                        </div>
                                        <div class="extra-small text-muted mb-2">
                                            Thời gian: {{ $ret->processed_at?->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="small">
                                            <strong>Phản hồi:</strong>
                                            <span class="text-dark">{{ $ret->admin_note ?: 'Không có ghi chú' }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($ret->status === 'pending')
                    <!-- Modal Duyệt Yêu Cầu -->
                    <div class="modal fade text-start" id="adminApproveModal_{{ $ret->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form action="{{ route('admin.returns.approve', $ret->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-success fs-6">
                                            <i class="fa-solid fa-circle-check me-1"></i> Chấp Thuận Yêu Cầu #REQ-{{ $ret->id }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        <p class="small text-secondary mb-3">
                                            Bạn có chắc chắn muốn duyệt yêu cầu <strong>{{ $ret->type_label }}</strong> cho đơn hàng này?
                                            @if($ret->type === 'return')
                                                <span class="d-block text-danger mt-1">Lưu ý: Đơn hàng sẽ được chuyển sang trạng thái <strong>Trả hàng</strong> và tồn kho các sản phẩm sẽ được tự động hoàn lại.</span>
                                            @endif
                                        </p>

                                        @if($ret->type === 'return' && $ret->bank_account_number)
                                            <div class="p-2.5 bg-light rounded-3 border extra-small mb-3">
                                                <strong class="d-block text-dark mb-1"><i class="fa-solid fa-building-columns me-1"></i>Thông tin hoàn tiền:</strong>
                                                <div>Ngân hàng: <strong>{{ $ret->bank_name }}</strong></div>
                                                <div>STK: <strong class="font-monospace text-danger">{{ $ret->bank_account_number }}</strong> - Chủ TK: <strong>{{ $ret->bank_account_holder }}</strong></div>
                                                <div>Số tiền: <strong class="text-danger">{{ number_format($order->total_amount) }}đ</strong></div>
                                            </div>
                                        @endif

                                        <div class="mb-2">
                                            <label class="form-label fw-bold text-dark small mb-1">Ghi chú & Hướng dẫn gửi hàng lại cho khách (tùy chọn):</label>
                                            <textarea class="form-control rounded-3" name="admin_note" rows="3" placeholder="Nhập địa chỉ nhận hàng của shop, số hotline hỗ trợ hoặc hướng dẫn đóng gói..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-3 py-2 btn-sm fw-bold" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 btn-sm fw-bold shadow-sm">
                                            <i class="fa-solid fa-check me-1"></i> Xác Nhận Duyệt
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Từ Chối Yêu Cầu -->
                    <div class="modal fade text-start" id="adminRejectModal_{{ $ret->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <form action="{{ route('admin.returns.reject', $ret->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-danger fs-6">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Từ Chối Yêu Cầu #REQ-{{ $ret->id }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-3">
                                        <p class="small text-secondary mb-3">
                                            Vui lòng nhập lý do từ chối yêu cầu đổi/trả để gửi phản hồi đến khách hàng.
                                        </p>

                                        <div class="mb-2">
                                            <label class="form-label fw-bold text-dark small mb-1">Lý do từ chối: <span class="text-danger">*</span></label>
                                            <textarea class="form-control rounded-3" name="admin_note" rows="3" required placeholder="VD: Sản phẩm đã qua sử dụng, tem mác không còn nguyên vẹn, hoặc đã quá thời hạn 7 ngày quy định..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-3 py-2 btn-sm fw-bold" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 btn-sm fw-bold shadow-sm">
                                            <i class="fa-solid fa-xmark me-1"></i> Xác Nhận Từ Chối
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        <!-- 1. Danh sách sản phẩm đặt mua -->
        <div class="card admin-card mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-box-open me-2 text-primary"></i>Danh Sách Sản Phẩm Đặt Mua</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light extra-small">
                        <tr>
                            <th class="ps-3">Sản Phẩm</th>
                            <th>Đơn Giá</th>
                            <th>Số Lượng</th>
                            <th class="text-end pe-3">Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                    <span class="badge bg-light text-dark border extra-small mt-1">{{ $item->variant_info }}</span>
                                </td>
                                <td class="fw-semibold text-dark">{{ number_format($item->price) }}đ</td>
                                <td><span class="badge bg-dark">x {{ $item->quantity }}</span></td>
                                <td class="text-end pe-3 fw-bold text-danger">{{ number_format($item->total_price) }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Card Lịch sử trạng thái (Timeline) -->
        <div class="card admin-card mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-timeline me-2 text-primary"></i>Lịch Sử Trạng Thái (Timeline)</h6>
                <span class="badge bg-light text-dark border">{{ $order->statusHistories->count() }} mốc thời gian</span>
            </div>
            <div class="card-body p-4">
                <div class="order-timeline ms-2 border-start ps-3">
                    @forelse($order->statusHistories as $history)
                        @php
                            $badgeClasses = [
                                'pending' => 'bg-warning text-dark',
                                'confirmed' => 'bg-info text-white',
                                'processing' => 'bg-secondary text-white',
                                'shipping' => 'bg-primary text-white',
                                'completed' => 'bg-success text-white',
                                'cancelled' => 'bg-danger text-white',
                                'returned' => 'bg-dark text-white',
                            ];
                            $badgeClass = $badgeClasses[$history->status] ?? 'bg-secondary text-white';
                            
                            $statusText = [
                                'pending' => 'Chờ xử lý',
                                'confirmed' => 'Đã xác nhận',
                                'processing' => 'Đang chuẩn bị',
                                'shipping' => 'Đang giao hàng',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                                'returned' => 'Trả hàng',
                            ][$history->status] ?? $history->status;
                        @endphp
                        <div class="timeline-item position-relative ps-3 pb-3">
                            <div class="timeline-badge position-absolute top-0 start-0 translate-middle-x rounded-circle bg-white border border-2 border-primary" style="width: 14px; height: 14px; margin-left: -1px; margin-top: 3px;"></div>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                <small class="text-muted fw-semibold">{{ $history->created_at->format('d/m/Y H:i:s') }}</small>
                            </div>
                            <div class="small text-dark fw-medium mb-1">{{ $history->note ?? 'Cập nhật trạng thái' }}</div>
                            <div class="extra-small text-muted">
                                <i class="fa-solid fa-user-check me-1 text-primary"></i> Người thực hiện: 
                                <strong class="text-dark">{{ $history->updated_by_name ?? 'Hệ thống' }}</strong>
                                <span class="badge bg-light text-secondary border ms-1">{{ strtoupper($history->updated_by_type) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0 text-center">Chưa có mốc lịch sử trạng thái nào.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: FORM CẬP NHẬT TRẠNG THÁI & THÔNG TIN GIAO HÀNG -->
    <div class="col-lg-4">
        <!-- Form cập nhật trạng thái đơn -->
        <div class="card admin-card mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check me-2 text-warning"></i>Cập Nhật Trạng Thái Đơn Hàng</h6>
            </div>
            <div class="card-body p-4">
                @php
                    $payment = $order->payment ?? $order->payments->first();
                    $paymentStatus = $payment->payment_status ?? 'pending';
                    $isPaid = in_array($paymentStatus, ['paid', 'completed']) || $order->status === 'completed';
                    $needsPaymentBeforeShipping = in_array($payment->payment_method ?? '', ['bank_transfer', 'momo']) && !$isPaid;
                @endphp

                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="status" class="form-label font-weight-medium">Trạng thái mới <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select form-select-lg">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>✅ Đã xác nhận</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>📦 Đang chuẩn bị hàng</option>
                            <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }} {{ $needsPaymentBeforeShipping ? 'disabled' : '' }}>
                                🚚 Đang giao hàng {{ $needsPaymentBeforeShipping ? '(Cần thanh toán trước)' : '' }}
                            </option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }} {{ $needsPaymentBeforeShipping ? 'disabled' : '' }}>
                                🎉 Giao thành công {{ $needsPaymentBeforeShipping ? '(Cần thanh toán trước)' : '' }}
                            </option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Đã hủy đơn</option>
                            <option value="returned" {{ $order->status == 'returned' ? 'selected' : '' }}>↩️ Trả hàng / Hoàn tiền</option>
                        </select>
                        @if($needsPaymentBeforeShipping)
                            <div class="text-danger extra-small mt-2 d-flex align-items-center gap-1.5">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>Đơn chuyển khoản/online cần xác nhận thanh toán trước khi cho đơn hàng đi giao!</span>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 font-weight-medium">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Cập Nhật Trạng Thái
                    </button>
                </form>
            </div>
        </div>

        <!-- Thông tin người nhận -->
        <div class="card admin-card mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user me-2 text-info"></i>Thông Tin Người Nhận</h6>
            </div>
            <div class="card-body p-3 small text-secondary">
                <p class="mb-1"><strong>Họ tên:</strong> {{ $order->recipient_name }}</p>
                <p class="mb-1"><strong>Số điện thoại:</strong> {{ $order->recipient_phone }}</p>
                <p class="mb-1"><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                @if($order->note)
                    <p class="mb-0 text-muted border-top pt-2 mt-2"><strong>Ghi chú:</strong> {{ $order->note }}</p>
                @endif
            </div>
        </div>

        <!-- Tóm tắt thanh toán -->
        <div class="card admin-card">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt me-2 text-success"></i>Tóm Tắt Thanh Toán</h6>
            </div>
            <div class="card-body p-3 small">
                @php
                    $paymentMethodName = match($payment->payment_method ?? 'COD') {
                        'bank_transfer' => 'Chuyển khoản Ngân hàng (VietQR)',
                        'momo' => 'Ví điện tử MoMo (MoMo QR)',
                        default => 'Tiền mặt khi nhận hàng (COD)',
                    };
                @endphp

                <div class="mb-3 p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted extra-small">Phương thức:</span>
                        @if($isPaid)
                            <span class="badge bg-success px-2.5 py-1 rounded-pill extra-small fw-bold">
                                <i class="fa-solid fa-circle-check me-1"></i>Đã thanh toán
                            </span>
                        @else
                            <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill extra-small fw-bold">
                                <i class="fa-solid fa-clock me-1"></i>Chưa thanh toán
                            </span>
                        @endif
                    </div>
                    <strong class="text-dark d-block">{{ $paymentMethodName }}</strong>
                </div>

                @if($needsPaymentBeforeShipping)
                    <div class="alert alert-warning p-2.5 rounded-3 border-warning extra-small mb-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-warning fs-5 mt-0.5"></i>
                            <div>
                                <strong class="d-block text-dark">Chưa nhận được chuyển khoản</strong>
                                <span>Khách chọn thanh toán chuyển khoản. Vui lòng kiểm tra tài khoản ngân hàng và xác nhận đã nhận tiền để mở khóa giao hàng.</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="payment_status" value="paid">
                        <button type="submit" class="btn btn-success btn-sm w-100 py-2 fw-bold shadow-xs">
                            <i class="fa-solid fa-circle-check me-1"></i> Xác Nhận Đã Nhận Tiền Chuyển Khoản
                        </button>
                    </form>
                @endif

                @if($isPaid)
                    <div class="p-3 bg-success-subtle rounded border border-success-subtle mb-3">
                        <div class="d-flex align-items-center gap-2 text-success fw-bold mb-1">
                            <i class="fa-solid fa-circle-check fs-5"></i>
                            <span>Đã thanh toán thành công</span>
                        </div>
                        <div class="small text-dark mt-1">
                            Số tiền đã thanh toán: <strong class="text-danger fs-6">{{ number_format($order->total_amount) }}đ</strong>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính:</span>
                    <span class="fw-semibold text-dark">{{ number_format($order->subtotal) }}đ</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phí vận chuyển:</span>
                    <span class="fw-semibold text-dark">{{ number_format($order->shipping_fee) }}đ</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Giảm giá:</span>
                        <span class="fw-semibold text-success">-{{ number_format($order->discount_amount) }}đ</span>
                    </div>
                @endif
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="fw-bold text-dark fs-6">Tổng tiền:</span>
                    <span class="fw-bold text-danger fs-5">{{ number_format($order->total_amount) }}đ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
