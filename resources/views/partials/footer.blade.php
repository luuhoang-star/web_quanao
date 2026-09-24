<!-- Footer Partial (Minimalist Atino Style 4 Columns) -->
<footer class="bg-white border-top pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Cột 1: HỖ TRỢ KHÁCH HÀNG -->
            <div class="col-12 col-sm-6 col-lg-3">
                <h6 class="fw-bold text-uppercase text-dark mb-3 small tracking-wider" style="letter-spacing: 0.05em;">HỖ TRỢ KHÁCH HÀNG</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 0.85rem;">
                    <li><a href="{{ url('/huong-dan-mua-hang.html') }}" class="text-muted text-decoration-none hover-dark">Hướng dẫn mua hàng</a></li>
                    <li><a href="{{ url('/phuong-thuc-thanh-toan.html') }}" class="text-muted text-decoration-none hover-dark">Phương thức thanh toán</a></li>
                    <li><a href="{{ url('/tra-cuu-don-hang.html') }}" class="text-muted text-decoration-none hover-dark">Tra cứu đơn hàng</a></li>
                    <li><a href="{{ url('/cham-soc-khach-hang.html') }}" class="text-muted text-decoration-none hover-dark">Chăm sóc khách hàng</a></li>
                </ul>
            </div>

            <!-- Cột 2: CHÍNH SÁCH -->
            <div class="col-12 col-sm-6 col-lg-3">
                <h6 class="fw-bold text-uppercase text-dark mb-3 small tracking-wider" style="letter-spacing: 0.05em;">CHÍNH SÁCH</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size: 0.85rem;">
                    <li><a href="{{ url('/chinh-sach-bao-mat.html') }}" class="text-muted text-decoration-none hover-dark">Chính sách bảo mật</a></li>
                    <li><a href="{{ url('/chinh-sach-van-chuyen.html') }}" class="text-muted text-decoration-none hover-dark">Chính sách vận chuyển</a></li>
                    <li><a href="{{ url('/chinh-sach-doi-tra.html') }}" class="text-muted text-decoration-none hover-dark">Chính sách đổi trả 30 ngày</a></li>
                    <li><a href="{{ url('/chinh-sach-kiem-hang.html') }}" class="text-muted text-decoration-none hover-dark">Chính sách kiểm hàng</a></li>
                    <li><a href="{{ url('/dieu-khoan-dich-vu.html') }}" class="text-muted text-decoration-none hover-dark">Điều khoản dịch vụ</a></li>
                </ul>
            </div>

            <!-- Cột 3: ĐĂNG KÝ NHẬN TIN -->
            <div class="col-12 col-sm-6 col-lg-3">
                <h6 class="fw-bold text-uppercase text-dark mb-3 small tracking-wider" style="letter-spacing: 0.05em;">ĐĂNG KÝ NHẬN TIN</h6>
                <p class="text-muted mb-3" style="font-size: 0.85rem; line-height: 1.5;">
                    Đăng ký để nhận thông tin bộ sưu tập mới nhất và ưu đãi độc quyền.
                </p>
                <form id="footer-newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <div class="d-flex rounded overflow-hidden border">
                        <input type="email" name="email" required class="form-control border-0 shadow-none px-3 py-2 bg-light" placeholder="Nhập email của bạn..." style="font-size: 0.825rem; background-color: #f8fafc !important;" id="footer-newsletter-email">
                        <button class="btn btn-dark fw-bold text-uppercase px-3 rounded-0 d-flex align-items-center justify-content-center" type="submit" id="footer-newsletter-btn" style="font-size: 0.75rem; letter-spacing: 0.05em; min-width: 90px; background-color: #000;">
                            <span class="btn-text">ĐĂNG KÝ</span>
                            <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Cột 4: THÔNG TIN LIÊN HỆ -->
            <div class="col-12 col-sm-6 col-lg-3">
                <h6 class="fw-bold text-uppercase text-dark mb-3 small tracking-wider" style="letter-spacing: 0.05em;">THÔNG TIN LIÊN HỆ</h6>
                <ul class="list-unstyled mb-3 d-flex flex-column gap-2" style="font-size: 0.85rem;">
                    <li class="d-flex align-items-start gap-2 text-muted">
                        <i class="fa-solid fa-location-dot mt-1 text-dark"></i>
                        <span>Tầng 5, Tòa nhà Thời Trang, Hà Nội</span>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-muted">
                        <i class="fa-solid fa-phone text-dark"></i>
                        <a href="tel:0973797151" class="text-muted text-decoration-none hover-dark">0973.797.151 (8:30 - 22:00)</a>
                    </li>
                    <li class="d-flex align-items-center gap-2 text-muted">
                        <i class="fa-solid fa-envelope text-dark"></i>
                        <a href="mailto:luuvanhoang2k4@gmail.com" class="text-muted text-decoration-none hover-dark">luuvanhoang2k4@gmail.com</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3 fs-5 text-dark mt-2">
                    <a href="#" class="text-dark hover-opacity" title="QR Code"><i class="fa-solid fa-qrcode"></i></a>
                    <a href="#" class="text-dark hover-opacity" title="Instagram"><i class="fa-solid fa-camera"></i></a>
                </div>
            </div>
        </div>

        <hr class="my-4 text-muted opacity-25">

        <!-- Bottom Copyright Bar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2" style="font-size: 0.775rem;">
            <div class="text-muted">
                © 2026 Atino Store. Designed in Minimalist Uniqlo & H&M Style. All rights reserved.
            </div>
            <div class="d-flex align-items-center gap-4 text-muted">
                <span class="d-flex align-items-center gap-1">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Bảo mật 100%</span>
                </span>
                <span class="d-flex align-items-center gap-1">
                    <i class="fa-solid fa-truck-fast text-primary"></i>
                    <span>Đổi trả dễ dàng</span>
                </span>
            </div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('footer-newsletter-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('footer-newsletter-email');
        const btn = document.getElementById('footer-newsletter-btn');
        const btnText = btn ? btn.querySelector('.btn-text') : null;
        const spinner = btn ? btn.querySelector('.spinner-border') : null;

        const email = input ? input.value.trim() : '';
        if (!email) return;

        if (btn) btn.disabled = true;
        if (btnText) btnText.textContent = 'XỬ LÝ...';
        if (spinner) spinner.classList.remove('d-none');

        fetch("{{ route('newsletter.subscribe') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (body.success) {
                if (body.already_subscribed) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Thông báo',
                        text: body.message,
                        confirmButtonColor: '#000'
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đăng Ký Thành Công!',
                        text: body.message,
                        confirmButtonColor: '#000'
                    });
                    if (input) input.value = '';
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: body.message || 'Không thể đăng ký nhận tin lúc này.',
                    confirmButtonColor: '#000'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi kết nối',
                text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau!',
                confirmButtonColor: '#000'
            });
        })
        .finally(() => {
            if (btn) btn.disabled = false;
            if (btnText) btnText.textContent = 'ĐĂNG KÝ';
            if (spinner) spinner.classList.add('d-none');
        });
    });
});
</script>
