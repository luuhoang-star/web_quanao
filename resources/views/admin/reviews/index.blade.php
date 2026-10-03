@extends('layouts.admin')

@section('title', 'Quản Lý Đánh Giá Sản Phẩm')
@section('page_title', 'Quản Lý Đánh Giá')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-star text-warning me-2"></i>Quản Lý Đánh Giá Sản Phẩm</h5>
        <p class="text-muted small mb-0">Quản lý, duyệt hoặc ẩn các bình luận đánh giá từ phía khách hàng</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Tổng Đánh Giá</span>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalReviews }}</h3>
            </div>
            <div class="stat-card-icon bg-primary-subtle text-primary">
                <i class="fa-solid fa-comments"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Điểm Trung Bình</span>
                <h3 class="fw-bold mb-0 text-warning">{{ $averageRating }} <i class="fa-solid fa-star fs-6"></i></h3>
            </div>
            <div class="stat-card-icon bg-warning-subtle text-warning">
                <i class="fa-solid fa-star-half-stroke"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Đã Duyệt</span>
                <h3 class="fw-bold mb-0 text-success" id="stat-approved-count">{{ $approvedCount }}</h3>
            </div>
            <div class="stat-card-icon bg-success-subtle text-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card admin-card p-3 d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted extra-small text-uppercase font-weight-bold">Đang Ẩn</span>
                <h3 class="fw-bold mb-0 text-secondary" id="stat-hidden-count">{{ $hiddenCount }}</h3>
            </div>
            <div class="stat-card-icon bg-secondary-subtle text-secondary">
                <i class="fa-solid fa-eye-slash"></i>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc tìm kiếm -->
<div class="card admin-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên sản phẩm, tên khách hàng hoặc nội dung... (ấn Enter để tìm)" value="{{ request('keyword') }}">
                    @if(request('keyword'))
                        <a href="{{ route('admin.reviews.index', array_merge(request()->except('keyword'), ['page' => 1])) }}" class="input-group-text bg-white text-muted" title="Xóa từ khóa"><i class="fa-solid fa-xmark"></i></a>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <select name="rating" class="form-select bg-white" onchange="this.form.submit()">
                    <option value="">-- Tất cả số sao --</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 sao)</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4 sao)</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3 sao)</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ (2 sao)</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ (1 sao)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <select name="status" class="form-select bg-white" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Đã duyệt</option>
                    <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>🚫 Đang ẩn</option>
                </select>
                @if(request()->hasAny(['keyword', 'rating', 'status']))
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary px-3 text-nowrap d-flex align-items-center gap-1" title="Đặt lại bộ lọc">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Đặt lại</span>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Bảng danh sách đánh giá -->
<div class="card admin-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light extra-small">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Sản Phẩm</th>
                    <th>Khách Hàng</th>
                    <th>Đánh Giá</th>
                    <th>Nội Dung Nhận Xét</th>
                    <th>Trạng Thái (Click để đổi)</th>
                    <th class="text-end pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td class="ps-3 fw-bold text-secondary">#{{ $review->id }}</td>
                        <td>
                            @if($review->product)
                                <a href="{{ route('shop.show', $review->product->slug) }}" target="_blank" class="text-decoration-none fw-bold text-dark hover-primary">
                                    {{ $review->product->name }}
                                </a>
                            @else
                                <span class="text-muted italic">Sản phẩm đã bị xóa</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-0">{{ $review->user->name ?? 'Khách vô danh' }}</div>
                            <span class="extra-small text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td>
                            <div class="text-warning small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                                <span class="fw-bold text-dark ms-1">({{ $review->rating }}/5)</span>
                            </div>
                        </td>
                        <td>
                            <p class="small text-dark mb-1">{{ $review->comment }}</p>
                            @if($review->images && $review->images->isNotEmpty())
                                <div class="d-flex gap-1 mt-1 mb-2">
                                    @foreach($review->images as $img)
                                        <img src="{{ $img->display_url }}" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endforeach
                                </div>
                            @endif

                            @if($review->admin_reply)
                                <div class="p-2 bg-light rounded-2 border-start border-3 border-primary extra-small mt-2">
                                    <div class="fw-bold text-primary mb-0.5 d-flex align-items-center justify-content-between">
                                        <span><i class="fa-solid fa-reply me-1"></i>Shop phản hồi:</span>
                                        <span class="text-muted fw-normal">{{ $review->replied_at ? $review->replied_at->format('d/m/Y H:i') : '' }}</span>
                                    </div>
                                    <div class="text-secondary">{{ $review->admin_reply }}</div>
                                </div>
                            @endif
                        </td>
                        <td>
                            <button type="button" 
                                    class="btn btn-sm btn-toggle-approval p-0 border-0 bg-transparent text-decoration-none" 
                                    data-id="{{ $review->id }}"
                                    data-url="{{ route('admin.reviews.toggleApproval', $review->id) }}"
                                    title="Click để thay đổi trạng thái">
                                @if($review->is_approved)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill shadow-xs" style="cursor: pointer; font-size: 0.82rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i>Đã duyệt
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill shadow-xs" style="cursor: pointer; font-size: 0.82rem;">
                                        <i class="fa-solid fa-eye-slash me-1"></i>Đang ẩn
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1.5">
                                <button type="button" class="btn btn-sm btn-outline-primary px-2" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}" title="Phản hồi đánh giá">
                                    <i class="fa-solid fa-reply me-1"></i>{{ $review->admin_reply ? 'Sửa trả lời' : 'Trả lời' }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-review px-2" data-id="{{ $review->id }}" title="Xóa đánh giá">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>

                            <!-- Modal Phản hồi đánh giá -->
                            <div class="modal fade text-start" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-bottom py-3">
                                                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-reply text-primary"></i>
                                                    <span>Phản Hồi Đánh Giá (#{{ $review->id }})</span>
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <!-- Thông tin nhận xét của khách -->
                                                <div class="p-3 bg-light rounded-3 border mb-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <strong class="text-dark small">{{ $review->user->name ?? 'Khách' }}</strong>
                                                        <div class="text-warning small">
                                                            @for($s = 1; $s <= 5; $s++)
                                                                <i class="fa-{{ $s <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <p class="mb-0 small text-secondary">"{{ $review->comment }}"</p>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="reply_content_{{ $review->id }}" class="form-label fw-bold text-dark small mb-1">Nội dung phản hồi từ Cửa hàng:</label>
                                                    <textarea class="form-control" id="reply_content_{{ $review->id }}" name="admin_reply" rows="4" placeholder="Nhập câu trả lời lịch sự, cảm ơn hoặc giải đáp thắc mắc của khách hàng..." required>{{ old('admin_reply', $review->admin_reply) }}</textarea>
                                                    <div class="form-text extra-small text-muted">Phản hồi sẽ được hiển thị công khai ngay bên dưới nhận xét của khách hàng.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top py-2.5 px-4 bg-light rounded-bottom-4">
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                                                    <i class="fa-solid fa-paper-plane me-1"></i>Lưu & Gửi Phản Hồi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <form id="delete-review-form-{{ $review->id }}" action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-comments fa-2x mb-2 text-secondary d-block"></i>
                            Không có đánh giá nào khớp với bộ lọc.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle approval status via AJAX
        $('.btn-toggle-approval').on('click', function(e) {
            e.preventDefault();
            const btn = $(this);
            const url = btn.data('url');

            if (btn.hasClass('disabled')) return;
            btn.addClass('disabled').css('opacity', '0.6');

            $.ajax({
                url: url,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function(res) {
                    btn.removeClass('disabled').css('opacity', '1');
                    if (res.success) {
                        if (res.is_approved) {
                            btn.html('<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill shadow-xs" style="cursor: pointer; font-size: 0.82rem;"><i class="fa-solid fa-circle-check me-1"></i>Đã duyệt</span>');
                        } else {
                            btn.html('<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill shadow-xs" style="cursor: pointer; font-size: 0.82rem;"><i class="fa-solid fa-eye-slash me-1"></i>Đang ẩn</span>');
                        }

                        if (res.approvedCount !== undefined) {
                            $('#stat-approved-count').text(res.approvedCount);
                        }
                        if (res.hiddenCount !== undefined) {
                            $('#stat-hidden-count').text(res.hiddenCount);
                        }

                        if (typeof Swal !== 'undefined') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: res.message
                            });
                        }
                    }
                },
                error: function() {
                    btn.removeClass('disabled').css('opacity', '1');
                    alert('Đã xảy ra lỗi khi cập nhật trạng thái. Vui lòng thử lại!');
                }
            });
        });

        // Delete review with confirmation
        $('.btn-delete-review').on('click', function() {
            const reviewId = $(this).data('id');

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: 'Bạn có chắc chắn muốn xóa đánh giá này khỏi hệ thống không?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#delete-review-form-${reviewId}`).submit();
                }
            });
        });
    });
</script>
@endpush
