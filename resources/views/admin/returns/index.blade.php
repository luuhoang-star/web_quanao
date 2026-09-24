@extends('layouts.admin')

@section('title', 'Quản Lý Đổi / Trả Hàng')
@section('page_title', 'Quản Lý Đổi / Trả Hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-rotate-left text-warning me-2"></i>Quản Lý Yêu Cầu Đổi / Trả Hàng</h5>
        <p class="text-muted small mb-0">Theo dõi, xét duyệt và phản hồi các yêu cầu đổi size, đổi màu hoặc trả hàng hoàn tiền từ khách hàng</p>
    </div>
</div>

<!-- Nav tabs bộ lọc trạng thái -->
<div class="card admin-card mb-4 border-0 shadow-sm">
    <div class="card-body p-2">
        <div class="d-flex flex-nowrap gap-2 overflow-x-auto py-1" style="scrollbar-width: thin;">
            <a href="{{ route('admin.returns.index', request()->except('status', 'page')) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ !request()->filled('status') ? 'btn-dark text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>Tất Cả</span>
                <span class="badge {{ !request()->filled('status') ? 'bg-white text-dark' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['all'] }}</span>
            </a>

            <a href="{{ route('admin.returns.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'pending' ? 'btn-warning text-dark fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>⏳ Chờ Duyệt</span>
                <span class="badge {{ request('status') == 'pending' ? 'bg-dark text-white' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['pending'] }}</span>
            </a>

            <a href="{{ route('admin.returns.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'approved' ? 'btn-success text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>✅ Đã Chấp Thuận</span>
                <span class="badge {{ request('status') == 'approved' ? 'bg-white text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['approved'] }}</span>
            </a>

            <a href="{{ route('admin.returns.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" 
               class="btn btn-sm rounded-pill text-nowrap px-3 d-flex align-items-center gap-1.5 {{ request('status') == 'rejected' ? 'btn-danger text-white fw-bold shadow-sm' : 'btn-light text-secondary border' }}">
                <span>❌ Đã Từ Chối</span>
                <span class="badge {{ request('status') == 'rejected' ? 'bg-white text-danger' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">{{ $counts['rejected'] }}</span>
            </a>
        </div>
    </div>
</div>

<!-- Bộ lọc tìm kiếm -->
<div class="card admin-card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ route('admin.returns.index') }}" method="GET" class="row g-2 align-items-center">
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Tìm theo mã đơn, người nhận, SĐT..." value="{{ request('keyword') }}">
                </div>
            </div>

            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">-- Tất cả hình thức --</option>
                    <option value="exchange" {{ request('type') == 'exchange' ? 'selected' : '' }}>👕 Đổi Size / Đổi Màu</option>
                    <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>💸 Trả Hàng Hoàn Tiền</option>
                </select>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-3 shadow-xs">
                    <i class="fa-solid fa-filter me-1"></i> Lọc dữ liệu
                </button>
                <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Đặt lại
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bảng danh sách yêu cầu -->
<div class="card admin-card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list me-2 text-primary"></i>Danh Sách Yêu Cầu ({{ $returns->total() }})</h6>
    </div>

    @if($returns->isEmpty())
        <div class="card-body text-center py-5">
            <i class="fa-solid fa-box-open fa-3x text-muted opacity-50 mb-3"></i>
            <h6 class="fw-bold text-dark">Chưa tìm thấy yêu cầu đổi / trả hàng nào!</h6>
            <p class="text-muted small mb-0">Hiện tại không có yêu cầu nào phù hợp với bộ lọc đã chọn.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light extra-small">
                    <tr>
                        <th class="ps-3">MÃ YÊU CẦU</th>
                        <th>ĐƠN HÀNG</th>
                        <th>KHÁCH HÀNG</th>
                        <th>HÌNH THỨC & LÝ DO</th>
                        <th>TRẠNG THÁI</th>
                        <th class="text-end pe-3">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $return)
                        @php
                            $badge = $return->status_badge;
                            $order = $return->order;
                        @endphp
                        <tr>
                            <td class="ps-3 py-3">
                                <span class="fw-bold text-dark font-monospace">#REQ-{{ $return->id }}</span>
                                <div class="text-muted extra-small mt-0.5">
                                    <i class="fa-regular fa-clock me-1"></i>{{ $return->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>

                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-dark font-monospace text-decoration-none hover-text-primary">
                                    #{{ $order->order_code }}
                                </a>
                                <div class="text-danger extra-small fw-bold mt-0.5">
                                    {{ number_format($order->total_amount) }}đ
                                </div>
                            </td>

                            <td>
                                <div class="fw-medium text-dark">{{ $order->recipient_name }}</div>
                                <div class="text-muted extra-small">{{ $order->recipient_phone }}</div>
                            </td>

                            <td>
                                <div class="mb-1">
                                    @if($return->type === 'exchange')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small fw-bold">
                                            <i class="fa-solid fa-shirt me-1"></i> Đổi size / màu
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small fw-bold">
                                            <i class="fa-solid fa-money-bill-transfer me-1"></i> Trả hàng hoàn tiền
                                        </span>
                                    @endif
                                </div>
                                <div class="small fw-semibold text-dark text-truncate" style="max-width: 280px;" title="{{ $return->reason }}">
                                    {{ $return->reason }}
                                </div>
                                @if($return->note)
                                    <div class="text-muted extra-small text-truncate" style="max-width: 280px;" title="{{ $return->note }}">
                                        {{ $return->note }}
                                    </div>
                                @endif

                                @if(!empty($return->images))
                                    <div class="mt-1 d-flex gap-1">
                                        @foreach($return->images as $img)
                                            @php
                                                $imgUrl = str_starts_with($img, 'http') ? $img : asset('storage/' . $img);
                                            @endphp
                                            <a href="{{ $imgUrl }}" target="_blank" title="Xem ảnh">
                                                <img src="{{ $imgUrl }}" class="rounded border object-fit-cover shadow-xs" style="width: 28px; height: 28px;">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge {{ $badge['class'] }} px-2.5 py-1.5 rounded-pill extra-small fw-bold d-inline-flex align-items-center gap-1">
                                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                                    <span>{{ $badge['text'] }}</span>
                                </span>
                                @if($return->processed_at)
                                    <div class="text-muted extra-small mt-1">
                                        Xử lý: {{ $return->processed_at->format('d/m H:i') }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-end pe-3">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <!-- Xem chi tiết đơn -->
                                    <a href="{{ route('admin.orders.show', $order->id) }}#returnCard" class="btn btn-light btn-sm border rounded-pill px-2.5" title="Xem chi tiết đơn hàng">
                                        <i class="fa-solid fa-eye text-secondary"></i>
                                    </a>

                                    @if($return->status === 'pending')
                                        <!-- Nút Duyệt -->
                                        <button type="button" class="btn btn-success btn-sm rounded-pill px-2.5" 
                                                data-bs-toggle="modal" data-bs-target="#approveModal_{{ $return->id }}" title="Chấp thuận yêu cầu">
                                            <i class="fa-solid fa-check"></i> Duyệt
                                        </button>

                                        <!-- Nút Từ chối -->
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2.5" 
                                                data-bs-toggle="modal" data-bs-target="#rejectModal_{{ $return->id }}" title="Từ chối yêu cầu">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Duyệt Yêu Cầu -->
                        @if($return->status === 'pending')
                            <div class="modal fade text-start" id="approveModal_{{ $return->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <form action="{{ route('admin.returns.approve', $return->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-success fs-6">
                                                    <i class="fa-solid fa-circle-check me-1"></i> Chấp Thuận Yêu Cầu #REQ-{{ $return->id }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <p class="small text-secondary mb-3">
                                                    Bạn có chắc chắn muốn duyệt yêu cầu <strong>{{ $return->type_label }}</strong> cho đơn hàng <strong>#{{ $order->order_code }}</strong>?
                                                    @if($return->type === 'return')
                                                        <span class="d-block text-danger mt-1">Lưu ý: Đơn hàng sẽ được chuyển sang trạng thái <strong>Trả hàng</strong> và tồn kho các sản phẩm sẽ được tự động hoàn lại.</span>
                                                    @endif
                                                </p>

                                                @if($return->type === 'return' && $return->bank_account_number)
                                                    <div class="p-2.5 bg-light rounded-3 border extra-small mb-3">
                                                        <strong class="d-block text-dark mb-1"><i class="fa-solid fa-building-columns me-1"></i>Thông tin hoàn tiền cho khách:</strong>
                                                        <div>Ngân hàng: <strong>{{ $return->bank_name }}</strong></div>
                                                        <div>STK: <strong class="font-monospace text-danger">{{ $return->bank_account_number }}</strong> - Chủ TK: <strong>{{ $return->bank_account_holder }}</strong></div>
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
                            <div class="modal fade text-start" id="rejectModal_{{ $return->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <form action="{{ route('admin.returns.reject', $return->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger fs-6">
                                                    <i class="fa-solid fa-circle-xmark me-1"></i> Từ Chối Yêu Cầu #REQ-{{ $return->id }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <p class="small text-secondary mb-3">
                                                    Vui lòng nhập lý do từ chối yêu cầu đổi/trả cho đơn hàng <strong>#{{ $order->order_code }}</strong> để gửi phản hồi đến khách hàng.
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
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-center">
                {{ $returns->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
