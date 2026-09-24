<!-- 3. FILTER BAR & ADVANCED FILTERS -->
<div class="card admin-card border-0 shadow-sm rounded-3 mb-3">
    <div class="card-body p-2.5">
        <form id="filterForm" action="{{ route('admin.products.index') }}" method="GET">
            <!-- Retain current sorting and per_page parameters -->
            @if(request('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
            @if(request('sort_dir')) <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}"> @endif
            @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif

            <div class="row g-2 align-items-center">
                <!-- Search Keyword (Tự động lấp đầy phần chiều rộng còn lại để không bị khoảng trống) -->
                <div class="col-12 col-md">
                    <div class="input-group">
                        <button type="submit" class="input-group-text bg-white text-muted border-end-0 ps-2.5 pe-2 btn" title="Tìm kiếm">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <input type="text" name="keyword" class="form-control border-start-0 ps-1" placeholder="Tìm sản phẩm / SKU..." value="{{ request('keyword') }}" onkeydown="if(event.key==='Enter') this.form.submit();">
                        @if(request('keyword'))
                            <a href="{{ request()->fullUrlWithQuery(['keyword' => null]) }}" class="input-group-text bg-white text-muted border-start-0 pe-2" title="Xóa từ khóa">
                                <i class="fa-solid fa-xmark small"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Filter Category -->
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="category_id" class="form-select" onchange="this.form.submit();">
                        <option value="">Danh mục ▼</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }} class="{{ !$cat->parent_id ? 'fw-bold bg-light text-dark' : '' }}">
                                {{ $cat->parent_id ? '-- ' : '📁 ' }}{{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit();">
                        <option value="">Trạng thái ▼</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>● Hiển thị</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>● Đang ẩn</option>
                    </select>
                </div>

                <!-- Filter Stock Status -->
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="stock_status" class="form-select" onchange="this.form.submit();">
                        <option value="">Tồn kho ▼</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>● Còn hàng (> 10)</option>
                        <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>● Sắp hết (1 - 10)</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>● Hết hàng (= 0)</option>
                    </select>
                </div>

                <!-- Filter Discount / Sale -->
                <div class="col-12 col-sm-6 col-md-2">
                    <select name="has_sale" class="form-select {{ request('has_sale') === 'yes' ? 'border-danger text-danger fw-bold' : '' }}" onchange="this.form.submit();">
                        <option value="">Giảm giá ▼</option>
                        <option value="yes" {{ request('has_sale') === 'yes' ? 'selected' : '' }}>🔥 Đang giảm giá</option>
                        <option value="no" {{ request('has_sale') === 'no' ? 'selected' : '' }}>Giá gốc (Không giảm)</option>
                    </select>
                </div>

                <!-- Nút Xóa Lọc (Chỉ hiển thị khi đang có bộ lọc hoạt động) -->
                @if(!empty($activeFilterCount) && $activeFilterCount > 0)
                    <div class="col-12 col-sm-auto">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-danger px-2.5 d-flex align-items-center gap-1" title="Xóa tất cả bộ lọc (Đang active {{ $activeFilterCount }} bộ lọc)">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Xóa lọc</span>
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
