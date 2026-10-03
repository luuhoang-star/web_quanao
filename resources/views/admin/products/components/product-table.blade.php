<!-- 4. PRODUCT TABLE -->
<div class="card admin-card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead class="table-light extra-small">
                <tr>
                    <th style="width: 70px;" class="ps-3">Ảnh</th>
                        <th>
                            @php
                                $nextDir = ($sortBy === 'name' && $sortDir === 'asc') ? 'desc' : 'asc';
                            @endphp
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_dir' => $nextDir]) }}" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1">
                                Sản phẩm
                                @if($sortBy === 'name')
                                    <i class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @else
                                    <i class="fa-solid fa-sort text-muted opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th>Danh mục</th>
                        <th>
                            @php
                                $nextPriceDir = ($sortBy === 'price' && $sortDir === 'asc') ? 'desc' : 'asc';
                            @endphp
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'price', 'sort_dir' => $nextPriceDir]) }}" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1">
                                Giá
                                @if($sortBy === 'price')
                                    <i class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @else
                                    <i class="fa-solid fa-sort text-muted opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th>Giá Khuyến Mãi</th>
                        <th>
                            @php
                                $nextStockDir = ($sortBy === 'stock' && $sortDir === 'asc') ? 'desc' : 'asc';
                            @endphp
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'stock', 'sort_dir' => $nextStockDir]) }}" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1">
                                Tồn kho
                                @if($sortBy === 'stock')
                                    <i class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                @else
                                    <i class="fa-solid fa-sort text-muted opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $thumb = $product->images->where('is_thumbnail', true)->first() ?? $product->images->first();
                            $totalStock = $product->variants_sum_stock_quantity ?? $product->variants->sum('stock_quantity');
                            $variantCount = $product->variants_count ?? $product->variants->count();
                        @endphp
                        <tr>
                            <td class="ps-3">
                                @if($thumb)
                                    <img src="{{ $thumb->display_url }}" alt="{{ $product->name }}" class="rounded-3 border object-fit-cover" style="width: 48px; height: 48px;">
                                @else
                                    <div class="bg-light border rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;">
                                        <i class="fa-solid fa-image text-secondary"></i>
                                    </div>
                                @endif
                            </td>
                            <td style="max-width: 260px;">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="fw-bold text-dark text-decoration-none hover-primary mb-0.5 d-block text-truncate" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </a>
                                <code class="extra-small text-muted bg-light px-1.5 py-0.5 rounded border">
                                    <i class="fa-solid fa-barcode me-1"></i>{{ $product->sku }}
                                </code>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    {{ $product->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ number_format($product->price) }}đ</span>
                            </td>
                            <td>
                                @if($product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->price)
                                    <div class="fw-bold text-danger leading-none">{{ number_format($product->sale_price) }}đ</div>
                                    @php
                                        $discountPct = round((($product->price - $product->sale_price) / $product->price) * 100);
                                    @endphp
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small mt-1">
                                        🔥 Giảm -{{ $discountPct }}%
                                    </span>
                                @else
                                    <span class="text-muted small">Không áp dụng</span>
                                @endif
                            </td>
                            <td>
                                @if($totalStock > 10)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">
                                        Còn hàng
                                    </span>
                                @elseif($totalStock > 0)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle mb-1">
                                        Sắp hết
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1">
                                        Hết hàng
                                    </span>
                                @endif
                                <div class="extra-small text-muted">
                                    {{ $totalStock }} sản phẩm / {{ $variantCount }} biến thể
                                </div>
                            </td>
                            <td>
                                <button type="button" 
                                    class="btn btn-sm border-0 p-0 btn-toggle-product-status" 
                                    data-id="{{ $product->id }}" 
                                    data-url="{{ route('admin.products.toggleStatus', $product->id) }}" 
                                    title="Click để đổi trạng thái Hiển thị / Ẩn"
                                    style="cursor: pointer;">
                                    @if($product->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            ● Hiển thị
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            ● Đã ẩn
                                        </span>
                                    @endif
                                </button>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    <!-- Nút Biến thể nổi bật -->
                                    <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1 rounded-2 font-weight-medium" title="Quản lý Biến thể sản phẩm">
                                        <i class="fa-solid fa-layer-group me-1"></i>Biến thể
                                    </a>

                                    <!-- Nút Sửa trực tiếp -->
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1 rounded-2 font-weight-medium" title="Chỉnh sửa sản phẩm">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Sửa
                                    </a>

                                    <!-- Nút Xóa trực tiếp màu đỏ -->
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1 rounded-2 font-weight-medium btn-delete-single" data-id="{{ $product->id }}" data-name="{{ $product->name }}" title="Xóa sản phẩm">
                                        <i class="fa-solid fa-trash-can me-1"></i>Xóa
                                    </button>

                                    <!-- Dropdown Menu Thao Tác Phụ [⋮] -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border rounded-2 px-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Thao tác khác">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1">
                                            <li>
                                                <a class="dropdown-item py-1.5" href="{{ route('shop.show', $product->slug) }}" target="_blank">
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-info me-2"></i>Xem sản phẩm trên shop
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- Single Delete Form -->
                                <form id="single-delete-form-{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                @if(!empty($activeFilterCount) && $activeFilterCount > 0)
                                    <div class="text-muted">
                                        <i class="fa-solid fa-magnifying-glass fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-bold text-dark">Không tìm thấy sản phẩm</h5>
                                        <p class="small text-muted mb-3">Thử thay đổi bộ lọc hoặc tạo sản phẩm mới.</p>
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-xmark me-1"></i> Xóa bộ lọc
                                        </a>
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <i class="fa-solid fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-bold text-dark">Không tìm thấy sản phẩm</h5>
                                        <p class="small text-muted mb-3">Thử thay đổi bộ lọc hoặc tạo sản phẩm mới.</p>
                                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-plus me-1"></i> Thêm sản phẩm
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- 5. PAGINATION & ITEMS PER PAGE -->
    @if($products->total() > 0)
        <div class="card-footer bg-white border-top py-3 px-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small">
                    Hiển thị <strong>{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong> trong tổng số <strong>{{ $products->total() }}</strong> sản phẩm
                </div>

                <!-- Dropdown số mục trên mỗi trang -->
                <div class="d-flex align-items-center gap-1.5 small text-muted">
                    <span>Xem:</span>
                    <select class="form-select form-select-sm" style="width: 70px;" onchange="window.location.href=this.value;">
                        @foreach([10, 25, 50, 100] as $size)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}" {{ $perPage == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
