@extends('layouts.app')

@section('title', '403 - Quyền Truy Cập Bị Từ Chối | Atino')

@section('content')
<div class="container py-5 text-center my-md-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="mb-4">
                <span class="display-1 fw-black text-warning" style="font-weight: 900; letter-spacing: -2px;">403</span>
            </div>
            <h2 class="fw-bold mb-3">Truy Cập Bị Từ Chối</h2>
            <p class="text-muted mb-4 lead fs-6">
                Bạn không có quyền truy cập vào tài nguyên này. Nếu bạn cho rằng đây là một sự nhầm lẫn, vui lòng liên hệ bộ phận hỗ trợ khách hàng.
            </p>

            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ route('home') }}" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">
                    <i class="fa-solid fa-house me-1.5"></i> Về Trang Chủ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
