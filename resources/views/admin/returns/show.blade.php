@extends('layouts.admin')

@section('title', 'Chi Tiết Yêu Cầu Đổi / Trả #' . $return->id)
@section('page_title', 'Chi Tiết Yêu Cầu Đổi / Trả')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-rotate-left text-warning me-2"></i>Chi Tiết Yêu Cầu #REQ-{{ $return->id }}</h5>
        <p class="text-muted small mb-0">Tạo lúc: {{ $return->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Danh Sách Yêu Cầu
        </a>
        <a href="{{ route('admin.orders.show', $return->order_id) }}" class="btn btn-sm btn-outline-primary">
            <i class="fa-solid fa-box me-1"></i> Xem Đơn Hàng #{{ $return->order->order_code ?? '' }}
        </a>
    </div>
</div>

@php
    $badge = $return->status_badge;
    $order = $return->order;
@endphp

<div class="row g-4">
    <!-- Cột trái: Thông tin chính -->
    <div class="col-lg-8">
        <div class="card admin-card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">Nội Dung Yêu Cầu</h6>
                <span class="badge {{ $badge['class'] }} px-3 py-1.5 rounded-pill fs-7">
                    <i class="fa-solid {{ $badge['icon'] }} me-1"></i> {{ $badge['text'] }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted extra-small text-uppercase font-weight-bold">Hình Thức Yêu Cầu</label>
                        <div>
                            @if($return->type === 'exchange')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fs-7 fw-bold">
                                    <i class="fa-solid fa-shirt me-1"></i> Đổi size / màu
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 fs-7 fw-bold">
                                    <i class="fa-solid fa-money-bill-transfer me-1"></i> Trả hàng hoàn tiền
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted extra-small text-uppercase font-weight-bold">Lý Do Đổi/Trả</label>
                        <div class="fw-semibold text-dark">{{ $return->reason }}</div>
                    </div>
                    @if($return->note)
                        <div class="col-12">
                            <label class="text-muted extra-small text-uppercase font-weight-bold">Ghi Chú Khách Hàng</label>
                            <div class="p-3 bg-light rounded-3 text-secondary">{{ $return->note }}</div>
                        </div>
                    @endif
                </div>

                @if(!empty($return->images))
                    <div class="mb-4">
                        <label class="text-muted extra-small text-uppercase font-weight-bold d-block mb-2">Hình Ảnh Minh Chứng (Khách Cung Cấp)</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($return->images as $img)
                                @php
                                    $imgUrl = str_starts_with($img, 'http') ? $img : asset('storage/' . $img);
                                @endphp
                                <a href="{{ $imgUrl }}" target="_blank" class="border rounded-3 overflow-hidden d-inline-block shadow-xs">
                                    <img src="{{ $imgUrl }}" alt="Ảnh minh chứng" style="width: 100px; height: 100px; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($return->type === 'return')
                    <div class="border-top pt-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-building-columns text-primary me-2"></i>Thông Tin Nhận Hoàn Tiền</h6>
                        <div class="row g-3 p-3 bg-light rounded-3">
                            <div class="col-md-4">
                                <span class="text-muted extra-small d-block">Ngân Hàng:</span>
                                <strong class="text-dark">{{ $return->bank_name ?? 'Chưa cung cấp' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted extra-small d-block">Số Tài Khoản:</span>
                                <strong class="text-dark font-monospace">{{ $return->bank_account_number ?? 'Chưa cung cấp' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted extra-small d-block">Chủ Tài Khoản:</span>
                                <strong class="text-dark text-uppercase">{{ $return->bank_account_holder ?? 'Chưa cung cấp' }}</strong>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Phản hồi từ quản trị viên -->
                @if($return->admin_note || $return->processed_at)
                    <div class="border-top pt-4 mt-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-user-shield text-info me-2"></i>Kết Quả Xử Lý Của Quản Trị Viên</h6>
                        <div class="p-3 rounded-3 {{ $return->status === 'approved' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                            <div class="d-flex justify-content-between mb-1 extra-small">
                                <span>Xử lý bởi: <strong>{{ $return->processor->name ?? 'Quản trị viên' }}</strong></span>
                                <span>Thời gian: {{ $return->processed_at ? $return->processed_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                            <p class="mb-0 fw-medium">"{{ $return->admin_note ?: 'Không có ghi chú thêm.' }}"</p>
                        </div>
                    </div>
                @endif
            </div>

            @if($return->status === 'pending')
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-danger px-4" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fa-solid fa-xmark me-1"></i> Từ Chối
                    </button>
                    <button type="button" class="btn btn-success px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="fa-solid fa-check me-1"></i> Chấp Thuận
                    </button>
                </div>
            @endif
        </div>

        <!-- Chi tiết sản phẩm trong đơn -->
        @if($order && $order->items)
            <div class="card admin-card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">Sản Phẩm Trong Đơn Hàng #{{ $order->order_code }}</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light extra-small text-uppercase">
                            <tr>
                                <th>Sản Phẩm</th>
                                <th>Phân Loại</th>
                                <th class="text-center">Số Lượng</th>
                                <th class="text-end">Đơn Giá</th>
                                <th class="text-end pe-3">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($item->product && $item->product->primaryImage)
                                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_url) }}" alt="" style="width: 42px; height: 42px; object-fit: cover;" class="rounded border">
                                            @endif
                                            <span class="fw-semibold text-dark">{{ $item->product_name ?? ($item->product->name ?? 'Sản phẩm') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $item->productVariant->color->name ?? '' }} / {{ $item->productVariant->size->name ?? '' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price) }}đ</td>
                                    <td class="text-end pe-3 fw-bold">{{ number_format($item->price * $item->quantity) }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Cột phải: Thông tin đơn hàng & Khách hàng -->
    <div class="col-lg-4">
        <!-- Thông tin đơn hàng -->
        <div class="card admin-card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">Thông Tin Đơn Hàng</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Mã đơn:</span>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold font-monospace text-decoration-none">#{{ $order->order_code }}</a>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Trạng thái đơn:</span>
                        <span class="badge bg-secondary-subtle text-secondary">{{ $order->status }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Tổng thanh toán:</span>
                        <span class="fw-bold text-danger">{{ number_format($order->total_amount) }}đ</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Phương thức TT:</span>
                        <span>{{ strtoupper($order->payment_method ?? 'COD') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Khách hàng -->
        <div class="card admin-card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">Thông Tin Người Nhận</h6>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li>
                        <span class="text-muted d-block extra-small">Họ & Tên:</span>
                        <strong class="text-dark">{{ $order->recipient_name }}</strong>
                    </li>
                    <li>
                        <span class="text-muted d-block extra-small">Số Điện Thoại:</span>
                        <a href="tel:{{ $order->recipient_phone }}" class="text-dark fw-medium text-decoration-none">{{ $order->recipient_phone }}</a>
                    </li>
                    <li>
                        <span class="text-muted d-block extra-small">Địa Chỉ Nhận Hàng:</span>
                        <span class="text-secondary">{{ $order->shipping_address }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Duyệt Yêu Cầu -->
@if($return->status === 'pending')
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.returns.approve', $return->id) }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-success">
                    <i class="fa-solid fa-circle-check me-2"></i> Chấp Thuận Yêu Cầu #REQ-{{ $return->id }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Hệ thống sẽ cập nhật trạng thái yêu cầu sang <strong>Đã Chấp Thuận</strong> và cập nhật trạng thái đơn hàng tương ứng.
                </p>
                <div class="mb-3">
                    <label class="form-label font-weight-medium small text-dark">Ghi chú xử lý / Hướng dẫn gửi hàng:</label>
                    <textarea name="admin_note" rows="3" class="form-control small" placeholder="Ví dụ: Đã liên hệ khách, shipper sẽ tới thu hồi hàng trong 24h..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-sm btn-success fw-bold px-3">Xác Nhận Duyệt</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Từ Chối Yêu Cầu -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.returns.reject', $return->id) }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Từ Chối Yêu Cầu #REQ-{{ $return->id }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label font-weight-medium small text-dark">Lý do từ chối <span class="text-danger">*</span>:</label>
                    <textarea name="admin_note" rows="3" class="form-control small" placeholder="Ví dụ: Sản phẩm đã qua sử dụng / cắt tem mác nên không hỗ trợ..." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-sm btn-danger fw-bold px-3">Xác Nhận Từ Chối</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
