@extends('layouts.admin')

@section('title', 'Lịch Sử Biến Động Kho - Admin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- PAGE TITLE & BREADCRUMB -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-primary"></i> Lịch Sử Biến Động Tồn Kho
            </h4>
            <p class="text-muted small mb-0">Truy vết chi tiết người thao tác, thời gian, số lượng biến động và lý do đối soát khi có sai lệch.</p>
        </div>

        <div>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Quay lại Quản Lý Kho
            </a>
        </div>
    </div>

    <!-- CARD BẢNG LỊCH SỬ KHOW -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">

        <!-- FILTER TOOLBAR -->
        <div class="card-header bg-white py-3 border-bottom">
            <form action="{{ route('admin.inventory.movements') }}" method="GET" class="row g-2 align-items-center">
                <!-- Search input -->
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" 
                               placeholder="Tìm theo Mã SKU, tên SP, nhà cung cấp, mã chứng từ..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Lọc loại giao dịch -->
                <div class="col-6 col-md-3">
                    <select name="type" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">-- Tất cả loại giao dịch --</option>
                        <option value="import" {{ request('type') == 'import' ? 'selected' : '' }}>Nhập kho</option>
                        <option value="export" {{ request('type') == 'export' ? 'selected' : '' }}>Xuất kho thủ công</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Điều chỉnh kiểm kê</option>
                        <option value="order_sale" {{ request('type') == 'order_sale' ? 'selected' : '' }}>Bán hàng (Đơn hàng)</option>
                        <option value="order_cancel" {{ request('type') == 'order_cancel' ? 'selected' : '' }}>Hủy đơn (Hoàn kho)</option>
                        <option value="order_return" {{ request('type') == 'order_return' ? 'selected' : '' }}>Trả hàng (Hoàn kho)</option>
                    </select>
                </div>

                <!-- Nút Submit -->
                <div class="col-6 col-md-4 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-filter me-1"></i> Lọc Log
                    </button>
                    @if(request('search') || request('type'))
                        <a href="{{ route('admin.inventory.movements') }}" class="btn btn-sm btn-outline-secondary" title="Đặt lại">
                            <i class="fa-solid fa-rotate"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- BẢNG LỊCH SỬ LOG -->
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light extra-small text-uppercase">
                    <tr>
                        <th style="width: 150px;" class="ps-3">Thời Gian</th>
                        <th style="width: 140px;">Loại Thao Tác</th>
                        <th>Sản Phẩm & Biến Thể</th>
                        <th style="width: 130px;">Mã SKU</th>
                        <th style="width: 110px;" class="text-center">Biến Động</th>
                        <th style="width: 120px;" class="text-center">Tồn Trước ➔ Sau</th>
                        <th style="width: 130px;">Mã Chứng Từ / NCC</th>
                        <th>Người Thực Hiện & Lý Do</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        @php
                            $pv = $m->productVariant;
                        @endphp
                        <tr>
                            <td class="ps-3 text-muted small">
                                <i class="fa-regular fa-clock me-1"></i>{{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <span class="badge {{ $m->type_badge_class }} border rounded-pill px-2.5 py-1 extra-small fw-semibold">
                                    {{ $m->type_label }}
                                </span>
                            </td>
                            <td>
                                @if($pv)
                                    <div class="fw-bold text-dark small mb-0.5">
                                        {{ $pv->product ? $pv->product->name : 'N/A' }}
                                    </div>
                                    <div class="extra-small text-muted">
                                        @if($pv->color) <span class="me-1">Màu: {{ $pv->color->name }}</span> @endif
                                        @if($pv->size) <span>Size: {{ $pv->size->name }}</span> @endif
                                    </div>
                                @else
                                    <span class="text-muted italic small">(SKU đã bị xóa)</span>
                                @endif
                            </td>
                            <td>
                                <code class="fw-bold text-primary">{{ $pv->sku ?? 'N/A' }}</code>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 {{ $m->quantity_change > 0 ? 'text-success' : ($m->quantity_change < 0 ? 'text-danger' : 'text-muted') }}">
                                    {{ $m->quantity_change > 0 ? '+'.$m->quantity_change : $m->quantity_change }}
                                </span>
                            </td>
                            <td class="text-center small">
                                <span class="text-muted">{{ $m->before_quantity }}</span>
                                <i class="fa-solid fa-arrow-right extra-small text-muted mx-1"></i>
                                <span class="fw-bold text-dark">{{ $m->after_quantity }}</span>
                            </td>
                            <td class="small">
                                @if($m->reference_code)
                                    <span class="d-block fw-semibold text-dark"><i class="fa-solid fa-hashtag extra-small me-1"></i>{{ $m->reference_code }}</span>
                                @endif
                                @if($m->supplier)
                                    <span class="d-block text-muted extra-small"><i class="fa-solid fa-truck-field me-1"></i>{{ $m->supplier }}</span>
                                @endif
                                @if(!$m->reference_code && !$m->supplier)
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="small">
                                <div class="fw-semibold text-dark">
                                    <i class="fa-regular fa-user me-1 text-muted"></i>{{ $m->user ? $m->user->name : 'Hệ thống tự động' }}
                                </div>
                                @if($m->note)
                                    <div class="text-muted extra-small text-truncate" style="max-width: 250px;" title="{{ $m->note }}">
                                        {{ $m->note }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fa-solid fa-clock-rotate-left fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                <h6 class="fw-bold text-dark">Chưa có lịch sử biến động kho nào</h6>
                                <p class="small text-muted mb-0">Các thao tác nhập kho, xuất kho hoặc điều chỉnh sẽ ghi lại log tại đây.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($movements->total() > 0)
            <div class="card-footer bg-white py-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $movements->firstItem() }}-{{ $movements->lastItem() }}</strong> trong tổng số <strong>{{ number_format($movements->total()) }}</strong> bản ghi log
                </div>
                <div>
                    {{ $movements->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
