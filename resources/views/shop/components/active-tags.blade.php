@if(!empty($activeTagList) && count($activeTagList) > 0)
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3 bg-light p-2.5 rounded-3 border">
        <span class="text-muted extra-small fw-semibold me-1">
            <i class="fa-solid fa-filter text-primary me-1"></i>Đang lọc theo:
        </span>
        @foreach($activeTagList as $tag)
            <span class="badge bg-white text-dark border shadow-2xs d-inline-flex align-items-center gap-1.5 px-2.5 py-1.5 rounded-pill fs-7 font-weight-medium">
                {{ $tag['label'] }}
                <button type="button" 
                        class="btn-close ms-1 btn-remove-filter-tag" 
                        style="font-size: 0.65rem;" 
                        data-type="{{ $tag['type'] }}" 
                        data-value="{{ $tag['value'] }}"
                        aria-label="Xóa bộ lọc {{ $tag['label'] }}"
                        title="Xóa bộ lọc {{ $tag['label'] }}">
                </button>
            </span>
        @endforeach

        <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none extra-small ms-auto p-0 fw-semibold" id="btn-clear-all-filters-tag">
            <i class="fa-solid fa-rotate-left me-1"></i>Xóa tất cả
        </button>
    </div>
@endif
