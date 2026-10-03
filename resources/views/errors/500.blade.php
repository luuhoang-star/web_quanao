@extends('layouts.app')

@section('title', '500 - Sự Cố Máy Chủ | Atino')

@section('content')
<div class="container py-5 text-center my-md-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="mb-4">
                <span class="display-1 fw-black text-danger" style="font-weight: 900; letter-spacing: -2px;">500</span>
            </div>
            <h2 class="fw-bold mb-3">Đã xảy ra sự cố hệ thống</h2>
            <p class="text-muted mb-4 lead fs-6">
                Máy chủ đang gặp sự cố tạm thời hoặc đang được bảo trì. Đội ngũ kỹ thuật của Atino đã ghi nhận lỗi và đang khắc phục. Xin quý khách vui lòng thử lại sau ít phút.
            </p>

            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ route('home') }}" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">
                    <i class="fa-solid fa-house me-1.5"></i> Về Trang Chủ
                </a>
                <a href="javascript:location.reload()" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
                    <i class="fa-solid fa-rotate-right me-1.5"></i> Tải Lại Trang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
