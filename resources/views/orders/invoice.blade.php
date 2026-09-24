<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn & Phiếu Giao Hàng #{{ $order->order_code }} - ATINO Store</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0f172a;
            --accent-color: #f59e0b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 20px 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .invoice-wrapper {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            padding: 40px;
            position: relative;
        }

        .action-bar {
            max-width: 850px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 14px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .table-invoice th {
            background-color: #f8fafc !important;
            color: #334155;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #cbd5e1;
            padding: 12px 14px;
        }

        .table-invoice td {
            padding: 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .shipping-badge {
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            padding: 14px;
            background: #fafafa;
        }

        .cod-highlight-box {
            background: #fff1f2;
            border: 2px solid #f43f5e;
            border-radius: 10px;
            padding: 14px 18px;
            color: #be123c;
        }

        .paid-highlight-box {
            background: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 10px;
            padding: 14px 18px;
            color: #15803d;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 7rem;
            font-weight: 900;
            color: rgba(15, 23, 42, 0.03);
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            letter-spacing: 10px;
        }

        /* Chế độ Phiếu Gửi Hàng Nhãn Bưu Kiện (Compact Shipping Label) */
        body.label-mode .invoice-standard-section {
            display: none !important;
        }

        body:not(.label-mode) .invoice-label-section {
            display: none !important;
        }

        /* MEDIA PRINT CSS CHUẨN IN ẤN A4 */
        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .action-bar {
                display: none !important;
            }

            .invoice-wrapper {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 15mm !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .table-invoice th {
                background-color: #f1f5f9 !important;
            }

            .page-break-avoid {
                page-break-inside: avoid;
            }

            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    @php
        $payment = $order->payment ?? $order->payments?->first();
        $paymentStatus = $payment->payment_status ?? 'pending';
        $isPaid = in_array($paymentStatus, ['paid', 'completed']) || $order->status === 'completed';
        
        $paymentMethodText = match($payment->payment_method ?? 'COD') {
            'bank_transfer' => 'Chuyển khoản Ngân hàng (VietQR)',
            'momo' => 'Ví điện tử MoMo',
            default => 'Thanh toán tiền mặt khi nhận hàng (COD)',
        };

        $qrData = route('orders.show', $order->order_code);
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($qrData);
    @endphp

    <!-- THANH ĐIỀU KHIỂN THAO TÁC (ẨN KHI IN) -->
    <div class="action-bar">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold" onclick="window.history.back()">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
            </button>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-dark active fw-bold" id="btn-mode-invoice" onclick="switchPrintMode('standard')">
                    <i class="fa-solid fa-file-invoice me-1"></i> Mẫu Hóa Đơn A4
                </button>
                <button type="button" class="btn btn-outline-dark fw-bold" id="btn-mode-label" onclick="switchPrintMode('label')">
                    <i class="fa-solid fa-box-archive me-1"></i> Mẫu Phiếu Bưu Kiện
                </button>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="window.print()">
                <i class="fa-solid fa-print me-1.5"></i> IN NGAY (Ctrl + P)
            </button>
        </div>
    </div>

    <!-- KHUNG NỘI DUNG IN CHÍNH -->
    <div class="invoice-wrapper">
        <div class="watermark">ATINO STORE</div>

        <!-- ================= 1. MẪU HÓA ĐƠN BÁN LẺ A4 TIÊU CHUẨN ================= -->
        <div class="invoice-standard-section">
            <!-- Header Header: Logo Shop & Mã Đơn -->
            <div class="row align-items-center pb-4 mb-4 border-bottom">
                <div class="col-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="fs-2 fw-extrabold text-dark tracking-tight">ATINO</span>
                        <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 extra-small fw-bold">OFFICIAL STORE</span>
                    </div>
                    <div class="small text-secondary">
                        <div><strong>Thương hiệu Thời trang Cao cấp ATINO Việt Nam</strong></div>
                        <div><i class="fa-solid fa-location-dot me-1 text-muted"></i> 123 Đường Cầu Giấy, Q. Cầu Giấy, Hà Nội</div>
                        <div><i class="fa-solid fa-phone me-1 text-muted"></i> Hotline CSKH: 0973.797.151</div>
                        <div><i class="fa-solid fa-globe me-1 text-muted"></i> Website: quanao.test | Email: luuvanhoang2k4@gmail.com</div>
                    </div>
                </div>
                <div class="col-5 text-end">
                    <h3 class="fw-extrabold text-dark mb-1 text-uppercase tracking-wider">HÓA ĐƠN BÁN HÀNG</h3>
                    <div class="font-mono fs-5 fw-bold text-danger mb-2">#{{ $order->order_code }}</div>
                    
                    <div class="extra-small text-muted">
                        <div>Ngày đặt: <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong></div>
                        <div>Ngày in: <strong>{{ now()->format('d/m/Y H:i') }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- Khối 2 Cột: Bên Gửi & Bên Nhận -->
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="shipping-badge h-100">
                        <span class="badge bg-secondary text-white text-uppercase extra-small mb-2 fw-bold">Người gửi hàng:</span>
                        <h6 class="fw-bold text-dark mb-1">ATINO FASHION STORE (Kho Tổng)</h6>
                        <div class="small text-secondary">
                            <div>SĐT: <strong>0973.797.151</strong></div>
                            <div>Địa chỉ: Kho Vận ATINO, Cầu Giấy, Hà Nội</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="shipping-badge h-100 bg-light border-primary border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary text-white text-uppercase extra-small fw-bold">Người nhận hàng:</span>
                            <img src="{{ $qrImageUrl }}" alt="QR Tra cứu" class="rounded border bg-white p-0.5" style="width: 48px; height: 48px;">
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $order->recipient_name }}</h6>
                        <div class="small text-secondary">
                            <div>Số điện thoại: <strong class="text-dark font-mono fs-6">{{ $order->recipient_phone }}</strong></div>
                            <div>Địa chỉ giao: <strong class="text-dark">{{ $order->shipping_address }}</strong></div>
                            @if($order->note)
                                <div class="mt-1 text-dark"><em>Ghi chú: {{ $order->note }}</em></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng Danh Sách Sản Phẩm -->
            <div class="table-responsive mb-4">
                <table class="table table-invoice mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">STT</th>
                            <th>Tên Sản Phẩm / Mã SKU</th>
                            <th class="text-center" style="width: 140px;">Phân Loại</th>
                            <th class="text-center" style="width: 70px;">SL</th>
                            <th class="text-end" style="width: 120px;">Đơn Giá</th>
                            <th class="text-end" style="width: 130px;">Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $idx => $item)
                            <tr>
                                <td class="text-center text-muted font-mono">{{ $idx + 1 }}</td>
                                <td>
                                    <strong class="text-dark d-block">{{ $item->product_name }}</strong>
                                    <span class="text-muted extra-small font-mono">SKU: {{ $item->productVariant?->sku ?? ($item->product?->sku ?? 'ATN-' . $item->id) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1 small">
                                        {{ $item->variant_info ?: 'Chuẩn' }}
                                    </span>
                                </td>
                                <td class="text-center font-mono fw-bold">x{{ $item->quantity }}</td>
                                <td class="text-end text-secondary font-mono">{{ number_format($item->price) }}đ</td>
                                <td class="text-end fw-bold text-dark font-mono">{{ number_format($item->total_price) }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tóm Tắt Thanh Toán & Khối Thu Hộ COD -->
            <div class="row g-4 mb-4 page-break-avoid">
                <div class="col-6">
                    <!-- Hộp cảnh báo thu hộ COD hoặc Đã thanh toán -->
                    @if($isPaid)
                        <div class="paid-highlight-box mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fa-solid fa-circle-check fs-4"></i>
                                <h6 class="fw-bold mb-0">ĐÃ THANH TOÁN ONLINE</h6>
                            </div>
                            <div class="small">
                                Hình thức: <strong>{{ $paymentMethodText }}</strong>
                            </div>
                            <div class="fs-6 fw-extrabold mt-1 text-success font-mono">
                                TIỀN THU HỘ SHIPPER: 0 VNĐ
                            </div>
                        </div>
                    @else
                        <div class="cod-highlight-box mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fa-solid fa-hand-holding-dollar fs-4"></i>
                                <h6 class="fw-bold mb-0 text-uppercase">TIỀN THU HỘ SHIPPER (COD)</h6>
                            </div>
                            <div class="display-6 fw-extrabold font-mono text-danger my-1">
                                {{ number_format($order->total_amount) }} VNĐ
                            </div>
                            <div class="extra-small text-danger fw-semibold">
                                * Shipper vui lòng thu đúng số tiền trên trước khi giao hàng cho khách!
                            </div>
                        </div>
                    @endif

                    <!-- Lời dặn dò & Điều khoản giao hàng -->
                    <div class="p-3 bg-light rounded-3 border extra-small text-secondary">
                        <strong class="text-dark d-block mb-1"><i class="fa-solid fa-shield-halved text-primary me-1"></i>Chính sách đồng kiểm & Đổi trả:</strong>
                        <div>- Khách hàng được quyền kiểm tra hàng trước khi nhận (Đồng kiểm).</div>
                        <div>- Hỗ trợ đổi size / đổi mẫu trong 30 ngày nếu mặc không vừa.</div>
                        <div>- Giữ nguyên tem mác sản phẩm và phiếu mua hàng này khi có nhu cầu đổi trả.</div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between py-1.5 border-bottom small">
                            <span class="text-secondary">Tổng tiền hàng:</span>
                            <strong class="font-mono text-dark">{{ number_format($order->subtotal) }}đ</strong>
                        </div>
                        
                        @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between py-1.5 border-bottom small text-success">
                                <span>Giảm giá Voucher ({{ $order->coupon?->code ?? 'VOUCHER' }}):</span>
                                <strong class="font-mono">-{{ number_format($order->discount_amount) }}đ</strong>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between py-1.5 border-bottom small">
                            <span class="text-secondary">Phí vận chuyển:</span>
                            <strong class="font-mono text-dark">
                                {{ $order->shipping_fee > 0 ? number_format($order->shipping_fee) . 'đ' : 'Miễn phí' }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between pt-2.5 pb-1 align-items-center">
                            <span class="fw-bold text-dark fs-6">TỔNG CỘNG:</span>
                            <span class="fs-4 fw-extrabold text-danger font-mono">{{ number_format($order->total_amount) }}đ</span>
                        </div>

                        <div class="extra-small text-muted text-end border-top pt-2 mt-1">
                            Phương thức: <strong>{{ $paymentMethodText }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chữ Ký Xác Nhận 3 Bên -->
            <div class="row text-center pt-3 mt-4 border-top page-break-avoid">
                <div class="col-4">
                    <div class="fw-bold small text-dark mb-1">NGƯỜI LẬP PHIẾU</div>
                    <div class="extra-small text-muted">(Ký, ghi rõ họ tên)</div>
                    <div style="height: 60px;"></div>
                    <div class="small fw-semibold text-secondary">ATINO Store</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold small text-dark mb-1">ĐƠN VỊ VẬN CHUYỂN</div>
                    <div class="extra-small text-muted">(Ký xác nhận nhận hàng)</div>
                    <div style="height: 60px;"></div>
                    <div class="small fw-semibold text-secondary">GHTK / GHN / Shipper</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold small text-dark mb-1">NGƯỜI NHẬN HÀNG</div>
                    <div class="extra-small text-muted">(Ký xác nhận đã nhận đủ hàng)</div>
                    <div style="height: 60px;"></div>
                    <div class="small fw-semibold text-secondary">{{ $order->recipient_name }}</div>
                </div>
            </div>
        </div>

        <!-- ================= 2. MẪU PHIẾU GỬI HÀNG DÁN BƯU KIỆN (SHIPPING LABEL) ================= -->
        <div class="invoice-label-section">
            <div class="border border-3 border-dark rounded-3 p-4">
                <!-- Top Header nhãn vận chuyển -->
                <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-2 border-dark">
                    <div>
                        <h2 class="fw-extrabold mb-0 text-dark">ATINO STORE</h2>
                        <span class="small fw-bold text-muted">PHIẾU GIAO HÀNG THỜI TRANG</span>
                    </div>
                    <div class="text-end">
                        <img src="{{ $qrImageUrl }}" alt="QR Code" style="width: 70px; height: 70px;" class="border p-1 bg-white">
                        <div class="font-mono extra-small fw-bold mt-1 text-dark">{{ $order->order_code }}</div>
                    </div>
                </div>

                <!-- 2 Cột Gửi / Nhận To Rõ Cho Shipper -->
                <div class="row g-3 mb-3 pb-3 border-bottom border-2 border-dark">
                    <div class="col-6 border-end border-2 border-dark pe-3">
                        <div class="extra-small fw-bold text-uppercase text-muted">TỪ (SENDER):</div>
                        <h6 class="fw-bold text-dark mb-1">ATINO SHOP</h6>
                        <div class="small">Hotline: <strong>0973.797.151</strong></div>
                        <div class="small">Kho: Cầu Giấy, Hà Nội</div>
                    </div>
                    <div class="col-6 ps-3">
                        <div class="extra-small fw-bold text-uppercase text-muted">ĐẾN (RECIPIENT):</div>
                        <h5 class="fw-bold text-dark mb-1">{{ $order->recipient_name }}</h5>
                        <div class="fs-5 fw-extrabold text-danger font-mono">{{ $order->recipient_phone }}</div>
                        <div class="small fw-semibold text-dark">{{ $order->shipping_address }}</div>
                    </div>
                </div>

                <!-- Danh sách sản phẩm rút gọn để đóng gói -->
                <div class="mb-3 pb-2 border-bottom border-2 border-dark">
                    <div class="extra-small fw-bold text-uppercase text-muted mb-2">NỘI DUNG ĐƠN HÀNG ({{ $order->items->sum('quantity') }} sản phẩm):</div>
                    <table class="table table-sm table-bordered extra-small mb-1 align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Tên sản phẩm</th>
                                <th>Phân loại</th>
                                <th>SL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="text-start fw-semibold">{{ $item->product_name }}</td>
                                    <td>{{ $item->variant_info ?: 'Tiêu chuẩn' }}</td>
                                    <td class="fw-bold font-mono">x{{ $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tiền COD to khổng lồ để Shipper không nhìn nhầm -->
                <div class="row align-items-center g-2 mb-3">
                    <div class="col-7">
                        <div class="p-2 border border-2 border-dark rounded text-center">
                            <span class="extra-small fw-bold text-uppercase d-block">TIỀN THU HỘ (COD):</span>
                            @if($isPaid)
                                <span class="fs-3 fw-extrabold text-success font-mono">0 VNĐ</span>
                                <span class="extra-small d-block text-success fw-bold">(ĐÃ THANH TOÁN)</span>
                            @else
                                <span class="fs-2 fw-extrabold text-danger font-mono">{{ number_format($order->total_amount) }} VNĐ</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="p-2 bg-light border rounded extra-small">
                            <strong>Chỉ dẫn shipper:</strong>
                            <div>- Cho xem hàng</div>
                            <div>- Không cho thử</div>
                            <div>- Chuyển hoàn sau 3 lần phát</div>
                        </div>
                    </div>
                </div>

                <div class="text-center extra-small text-muted pt-2 border-top">
                    Cảm ơn quý khách đã mua sắm tại <strong>ATINO</strong>! Hotline hỗ trợ: <strong>0973.797.151</strong>
                </div>
            </div>
        </div>

    </div>

    <!-- Script chuyển đổi mẫu in -->
    <script>
        function switchPrintMode(mode) {
            if (mode === 'label') {
                document.body.classList.add('label-mode');
                document.getElementById('btn-mode-label').classList.add('btn-dark', 'active');
                document.getElementById('btn-mode-label').classList.remove('btn-outline-dark');
                document.getElementById('btn-mode-invoice').classList.remove('btn-dark', 'active');
                document.getElementById('btn-mode-invoice').classList.add('btn-outline-dark');
            } else {
                document.body.classList.remove('label-mode');
                document.getElementById('btn-mode-invoice').classList.add('btn-dark', 'active');
                document.getElementById('btn-mode-invoice').classList.remove('btn-outline-dark');
                document.getElementById('btn-mode-label').classList.remove('btn-dark', 'active');
                document.getElementById('btn-mode-label').classList.add('btn-outline-dark');
            }
        }
    </script>
</body>
</html>
