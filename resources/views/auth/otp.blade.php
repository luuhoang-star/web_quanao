@extends('layouts.app')

@section('title', 'Xác Thực OTP Gmail - ATINO')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-md-7 col-lg-5">
        <div class="card auth-card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white text-center py-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle p-3 mb-2" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-envelope-circle-check fs-3 text-warning"></i>
                </div>
                <h4 class="fw-bold mb-1">Xác Thực Mã OTP Qua Gmail</h4>
                <p class="small text-white-50 mb-0">Nhập mã xác nhận vừa được gửi tới Gmail <strong>{{ $user->email }}</strong></p>
            </div>
            
            <div class="card-body p-4 bg-white">
                <!-- Form xác thực OTP 6 ô nhập số -->
                <form action="{{ route('otp.verify') }}" method="POST" id="otp-form">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label font-weight-bold text-dark small mb-3 text-center d-block">
                            Nhập 6 Chữ Số OTP:
                        </label>
                        <div class="d-flex justify-content-between gap-2" id="otp-inputs">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" 
                                       name="otp_digits[]" 
                                       class="form-control text-center fw-extrabold fs-4 rounded-3 border border-2 otp-digit-input shadow-none" 
                                       style="width: 48px; height: 56px;" 
                                       maxlength="1" 
                                       pattern="[0-9]*" 
                                       inputmode="numeric" 
                                       required 
                                       autocomplete="off"
                                       {{ $i === 0 ? 'autofocus' : '' }}>
                            @endfor
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill fw-bold py-3 text-uppercase shadow-sm" style="font-size: 0.9rem; letter-spacing: 0.03em;">
                            <i class="fa-solid fa-check-circle me-2"></i> Xác Nhận Kích Hoạt
                        </button>
                    </div>
                </form>

                <!-- Form gửi lại mã OTP (Resend Cooldown 60s) -->
                <div class="text-center pt-3 border-top">
                    <p class="small text-muted mb-2">Chưa nhận được mã OTP hoặc mã đã hết hạn?</p>
                    <form action="{{ route('otp.resend') }}" method="POST" id="resend-form">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none fw-bold small p-0 text-primary" id="btn-resend-otp">
                            <i class="fa-solid fa-rotate-right me-1"></i> Gửi lại mã OTP
                        </button>
                        <span id="resend-timer-text" class="small text-muted d-none ms-1">
                            (Thử lại sau <span id="resend-timer-count" class="fw-bold text-dark">60</span>s)
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-digit-input');
        const form = document.getElementById('otp-form');

        // Tự động chuyển focus giữa 6 ô nhập số OTP
        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const val = this.value;
                if (!/^[0-9]$/.test(val)) {
                    this.value = '';
                    return;
                }
                if (val.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                // Tự động Submit khi đã nhập đủ 6 số
                let fullCode = Array.from(inputs).map(i => i.value).join('');
                if (fullCode.length === 6 && /^\d{6}$/.test(fullCode)) {
                    form.submit();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Hỗ trợ Paste mã 6 số
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                if (/^\d{6}$/.test(pasteData)) {
                    pasteData.split('').forEach((char, i) => {
                        if (inputs[i]) inputs[i].value = char;
                    });
                    inputs[5].focus();
                    form.submit();
                }
            });
        });

        // Xử lý bộ đếm ngược 60s Cooldown gửi lại OTP
        const resendBtn = document.getElementById('btn-resend-otp');
        const timerText = document.getElementById('resend-timer-text');
        const timerCount = document.getElementById('resend-timer-count');
        
        let cooldownSeconds = 60;
        let lastSentTime = "{{ Auth::user()->otp_sent_at ? Auth::user()->otp_sent_at->timestamp : 0 }}";
        let nowTime = Math.floor(Date.now() / 1000);
        let elapsed = nowTime - parseInt(lastSentTime || '0');

        if (elapsed < 60 && lastSentTime > 0) {
            cooldownSeconds = 60 - elapsed;
            startCooldown();
        }

        function startCooldown() {
            resendBtn.classList.add('pe-none', 'opacity-50');
            timerText.classList.remove('d-none');
            timerCount.textContent = cooldownSeconds;

            const interval = setInterval(() => {
                cooldownSeconds--;
                timerCount.textContent = cooldownSeconds;
                if (cooldownSeconds <= 0) {
                    clearInterval(interval);
                    resendBtn.classList.remove('pe-none', 'opacity-50');
                    timerText.classList.add('d-none');
                }
            }, 1000);
        }
    });
</script>
@endpush
