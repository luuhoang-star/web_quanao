@extends('layouts.app')

@section('title', '404 - Không Tìm Thấy Trang | Atino')

@section('content')
<div class="container py-5 text-center my-md-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="mb-4">
                <span class="display-1 fw-black text-primary" style="font-weight: 900; letter-spacing: -2px;">404</span>
            </div>
            <h2 class="fw-bold mb-3">Rất tiếc! Trang không tồn tại</h2>
            <p class="text-muted mb-4 lead fs-6">
                Địa chỉ bạn đang tìm kiếm có thể đã bị xóa, đổi tên hoặc tạm thời không khả dụng. Hãy thử tìm kiếm sản phẩm hoặc quay lại trang chủ.
            </p>

            <form action="{{ route('shop.index') }}" method="GET" class="mb-4 d-flex justify-content-center">
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" name="search" class="form-control rounded-start-pill ps-4" placeholder="Tìm kiếm sản phẩm quần áo...">
                    <button class="btn btn-dark rounded-end-pill px-4" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a href="{{ route('home') }}" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">
                    <i class="fa-solid fa-house me-1.5"></i> Về Trang Chủ
                </a>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
                    <i class="fa-solid fa-bag-shopping me-1.5"></i> Xem Cửa Hàng
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
