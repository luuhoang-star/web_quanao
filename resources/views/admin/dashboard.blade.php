@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển - Quản Trị Hệ Thống')
@section('page_title', 'Tổng Quan Hệ Thống & Báo Cáo Doanh Thu')

@section('content')
<!-- Filter Báo Cáo Theo Thời Gian -->
<div class="d-flex justify-content-end align-items-center mb-4">
    <div class="btn-group bg-white p-1 rounded-3 shadow-sm border" role="group" aria-label="Bộ lọc thời gian">
        <a href="{{ route('admin.dashboard', ['period' => 'all']) }}" class="btn btn-sm {{ ($stats['period'] ?? 'all') === 'all' ? 'btn-primary fw-bold' : 'btn-light border-0 text-secondary' }}">Tất cả thời gian</a>
        <a href="{{ route('admin.dashboard', ['period' => 'today']) }}" class="btn btn-sm {{ ($stats['period'] ?? '') === 'today' ? 'btn-primary fw-bold' : 'btn-light border-0 text-secondary' }}">Hôm nay</a>
        <a href="{{ route('admin.dashboard', ['period' => 'this_week']) }}" class="btn btn-sm {{ ($stats['period'] ?? '') === 'this_week' ? 'btn-primary fw-bold' : 'btn-light border-0 text-secondary' }}">Tuần này</a>
        <a href="{{ route('admin.dashboard', ['period' => 'this_month']) }}" class="btn btn-sm {{ ($stats['period'] ?? '') === 'this_month' ? 'btn-primary fw-bold' : 'btn-light border-0 text-secondary' }}">Tháng này</a>
        <a href="{{ route('admin.dashboard', ['period' => 'this_year']) }}" class="btn btn-sm {{ ($stats['period'] ?? '') === 'this_year' ? 'btn-primary fw-bold' : 'btn-light border-0 text-secondary' }}">Năm nay</a>
    </div>
</div>

<!-- Row Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Doanh thu -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Doanh Thu (Hoàn thành)</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">{{ number_format($stats['total_revenue'] ?? 0) }}đ</h3>
                </div>
                <div class="stat-card-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <span class="text-success fw-bold me-1"><i class="fa-solid fa-check"></i> Đã trừ đơn hủy/trả</span>
            </div>
        </div>
    </div>

    <!-- Tổng Đơn Hàng -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tổng Đơn Hàng</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">{{ $stats['total_orders'] ?? 0 }}</h3>
                </div>
                <div class="stat-card-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <span class="badge bg-warning text-dark me-1">{{ $stats['pending_orders'] ?? 0 }} chờ duyệt</span>
                <span class="badge bg-success">{{ $stats['completed_orders'] ?? 0 }} hoàn thành</span>
            </div>
        </div>
    </div>

    <!-- Tổng Sản Phẩm -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Tổng Sản Phẩm</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">{{ $stats['total_products'] ?? 0 }}</h3>
                </div>
                <div class="stat-card-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-shirt"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <span>{{ $stats['total_categories'] ?? 0 }} danh mục hệ thống</span>
            </div>
        </div>
    </div>

    <!-- Tổng Khách Hàng -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold">Khách Hàng</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">{{ $stats['total_users'] ?? 0 }}</h3>
                </div>
                <div class="stat-card-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-3 text-muted small">
                <span class="text-info fw-bold me-1"><i class="fa-solid fa-user-plus"></i> Tài khoản thành viên</span>
            </div>
        </div>
    </div>
</div>

<!-- ================= HÀNG BIỂU ĐỒ THỐNG KÊ (CHART.JS) ================= -->
<div class="row g-4 mb-4">
    <!-- Cột trái: Biểu đồ biến động doanh thu 7 ngày gần nhất -->
    <div class="col-lg-8">
        <div class="card admin-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div>
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-line text-primary"></i>
                        <span>Biểu Đồ Doanh Thu 7 Ngày Gần Nhất</span>
                    </h6>
                    <span class="text-muted extra-small">Cập nhật theo doanh thu thực tế các đơn hàng hoàn tất</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small fw-bold px-2.5 py-1">
                        <i class="fa-solid fa-circle-dot me-1"></i>Doanh thu (VNĐ)
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary border extra-small fw-bold px-2.5 py-1">
                        <i class="fa-solid fa-box me-1"></i>Số đơn hàng
                    </span>
                </div>
            </div>
            <div style="position: relative; height: 310px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Cột phải: Biểu đồ tròn Phân bổ Trạng thái Đơn hàng & Thanh toán -->
    <div class="col-lg-4">
        <div class="card admin-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div>
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-success"></i>
                        <span>Tỷ Lệ Trạng Thái Đơn Hàng</span>
                    </h6>
                    <span class="text-muted extra-small">Phân bổ tỷ lệ các đơn hàng trong hệ thống</span>
                </div>
            </div>
            <div style="position: relative; height: 230px; width: 100%;" class="d-flex justify-content-center">
                <canvas id="statusDoughnutChart"></canvas>
            </div>

            <!-- Mini stats Phương thức thanh toán -->
            <div class="mt-3 pt-3 border-top">
                <span class="extra-small text-muted fw-bold text-uppercase d-block mb-2">Phương thức thanh toán:</span>
                <div class="row g-2 text-center extra-small">
                    @foreach($paymentDistribution as $pName => $pCount)
                        <div class="col-4">
                            <div class="p-2 bg-light rounded-2 border">
                                <span class="d-block text-truncate text-muted" title="{{ $pName }}">{{ $pName }}</span>
                                <strong class="text-dark fs-6">{{ $pCount }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: Bảng Đơn Hàng Mới & Top Sản Phẩm Bán Chạy -->
<div class="row g-4 mb-4">
    <!-- Đơn hàng gần đây -->
    <div class="col-lg-7">
        <div class="card admin-card h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Đơn Hàng Mới Nhất</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light border">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light extra-small">
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-primary">{{ $order->order_code }}</td>
                                <td>{{ $order->recipient_name }}</td>
                                <td class="fw-semibold">{{ number_format($order->total_amount) }}đ</td>
                                <td>
                                    @if($order->status === 'confirmed')
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Đã xác nhận</span>
                                    @elseif($order->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Chờ xử lý</span>
                                    @elseif($order->status === 'processing')
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">Đang chuẩn bị</span>
                                    @elseif($order->status === 'shipping')
                                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">Đang giao</span>
                                    @elseif($order->status === 'completed')
                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Giao thành công</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">Đã hủy</span>
                                    @elseif($order->status === 'returned')
                                        <span class="badge bg-dark-subtle text-dark-emphasis border border-dark-subtle">Trả hàng</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $order->status_label }}</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-inbox fa-2x mb-2 text-secondary d-block"></i>
                                    Chưa có đơn hàng nào trong hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top 5 Sản Phẩm Bán Chạy -->
    <div class="col-lg-5">
        <div class="card admin-card h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-fire me-2 text-danger"></i>Top Sản Phẩm Bán Chạy</h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small font-weight-medium">
                    @if($stats['period'] === 'today') Hôm nay
                    @elseif($stats['period'] === 'this_week') Tuần này
                    @elseif($stats['period'] === 'this_month') Tháng {{ now()->format('m/Y') }}
                    @elseif($stats['period'] === 'this_year') Năm {{ now()->year }}
                    @else Tất cả thời gian
                    @endif
                </span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light extra-small">
                        <tr>
                            <th>Sản Phẩm</th>
                            <th class="text-center">Đã Bán</th>
                            <th class="text-end">Doanh Thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $item)
                            <tr>
                                <td class="fw-semibold text-dark text-truncate" style="max-width: 160px;">{{ $item->product_name }}</td>
                                <td class="text-center"><span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">{{ $item->total_sold }}</span></td>
                                <td class="text-end fw-bold text-success">{{ number_format($item->total_revenue) }}đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Chưa có dữ liệu bán hàng trong khoảng thời gian này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Định dạng tiền tệ VNĐ
    const formatVND = (val) => new Intl.NumberFormat('vi-VN').format(val) + 'đ';

    // 1. BIỂU ĐỒ DOANH THU & ĐƠN HÀNG 7 NGÀY GẦN NHẤT
    const ctxRevenue = document.getElementById('revenueChart');
    if (ctxRevenue) {
        const labels = @json($chartDates);
        const revenueData = @json($chartRevenues);
        const orderCountData = @json($chartOrderCounts);

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Doanh Thu (VNĐ)',
                        data: revenueData,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4.5,
                        pointHoverRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Số Đơn Hàng',
                        data: orderCountData,
                        borderColor: '#06b6d4',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        tension: 0.35,
                        pointBackgroundColor: '#06b6d4',
                        pointRadius: 4,
                        pointHoverRadius: 5.5,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 12, weight: 'bold' },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'y') {
                                    return ' ' + context.dataset.label + ': ' + formatVND(context.parsed.y);
                                }
                                return ' ' + context.dataset.label + ': ' + context.parsed.y + ' đơn';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            font: { size: 11 },
                            callback: function(val) {
                                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                                return val;
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: {
                            stepSize: 1,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    // 2. BIỂU ĐỒ TRÒN TRẠNG THÁI ĐƠN HÀNG (DOUGHNUT)
    const ctxStatus = document.getElementById('statusDoughnutChart');
    if (ctxStatus) {
        const statusMap = @json($statusDistribution);
        const statusLabels = Object.keys(statusMap);
        const statusValues = Object.values(statusMap);
        const totalStatusOrders = statusValues.reduce((a, b) => a + b, 0);

        const colors = [
            '#f59e0b', // Chờ xử lý: Amber
            '#06b6d4', // Đã xác nhận: Cyan
            '#64748b', // Đang đóng gói: Slate
            '#3b82f6', // Đang giao: Blue
            '#10b981', // Hoàn thành: Emerald
            '#ef4444', // Đã hủy: Red
            '#475569'  // Trả hàng: Dark
        ];

        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: totalStatusOrders > 0 ? statusValues : [1],
                    backgroundColor: totalStatusOrders > 0 ? colors : ['#e2e8f0'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 10.5 },
                            boxWidth: 8,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (totalStatusOrders === 0) return ' Chưa có đơn hàng';
                                const val = context.raw || 0;
                                const pct = ((val / totalStatusOrders) * 100).toFixed(1);
                                return ' ' + context.label + ': ' + val + ' đơn (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
