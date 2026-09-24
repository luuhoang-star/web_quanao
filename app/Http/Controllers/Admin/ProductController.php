<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Danh sách sản phẩm (Search, Filter, Stock Status, Sorting, Pagination & Thống kê).
     */
    public function index(Request $request): View
    {
        // 1. Thống kê sản phẩm tối ưu SQL Count thay vì nạp toàn bộ Models vào RAM
        $stats = $this->productService->getAdminProductStats();

        // 2. Query sản phẩm
        $query = Product::with(['category', 'images'])
            ->withSum('variants', 'stock_quantity')
            ->withCount('variants');

        $activeFilterCount = 0;

        if ($request->filled('keyword')) {
            $activeFilterCount++;
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhereHas('variants', fn ($vq) => $vq->where('sku', 'like', "%{$keyword}%"));
            });
        }

        if ($request->filled('category_id')) {
            $activeFilterCount++;
            $catId = (int) $request->category_id;
            $childIds = Category::where('parent_id', $catId)->pluck('id')->toArray();
            $allCategoryIds = array_merge([$catId], $childIds);
            $query->whereIn('category_id', $allCategoryIds);
        }

        if ($request->filled('status')) {
            $activeFilterCount++;
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('stock_status')) {
            $activeFilterCount++;
            $stockStatus = $request->stock_status;
            if ($stockStatus === 'in_stock') {
                $query->whereIn('id', function ($sub) {
                    $sub->select('product_id')
                        ->from('product_variants')
                        ->groupBy('product_id')
                        ->havingRaw('SUM(stock_quantity) > 10');
                });
            } elseif ($stockStatus === 'low_stock') {
                $query->whereIn('id', function ($sub) {
                    $sub->select('product_id')
                        ->from('product_variants')
                        ->groupBy('product_id')
                        ->havingRaw('SUM(stock_quantity) > 0 AND SUM(stock_quantity) <= 10');
                });
            } elseif ($stockStatus === 'out_of_stock') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('variants')
                        ->orWhereIn('id', function ($sub) {
                            $sub->select('product_id')
                                ->from('product_variants')
                                ->groupBy('product_id')
                                ->havingRaw('SUM(stock_quantity) = 0');
                        });
                });
            }
        }

        if ($request->filled('min_price')) {
            $activeFilterCount++;
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $activeFilterCount++;
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->filled('has_variants')) {
            $activeFilterCount++;
            if ($request->has_variants === 'yes') {
                $query->has('variants');
            } elseif ($request->has_variants === 'no') {
                $query->doesntHave('variants');
            }
        }

        if ($request->filled('has_sale')) {
            $activeFilterCount++;
            if ($request->has_sale === 'yes') {
                $query->whereNotNull('sale_price')
                    ->where('sale_price', '>', 0)
                    ->whereColumn('sale_price', '<', 'price');
            } elseif ($request->has_sale === 'no') {
                $query->where(function ($q) {
                    $q->whereNull('sale_price')
                        ->orWhere('sale_price', '<=', 0)
                        ->orWhereColumn('sale_price', '>=', 'price');
                });
            }
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'name') {
            $query->orderBy('name', $sortDir);
        } elseif ($sortBy === 'price') {
            $query->orderBy('price', $sortDir);
        } elseif ($sortBy === 'stock') {
            $query->orderBy('variants_sum_stock_quantity', $sortDir);
        } else {
            $query->orderBy('created_at', $sortDir);
        }

        $perPage = (int) $request->get('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::getTreeList();

        return view('admin.products.index', compact('products', 'categories', 'stats', 'activeFilterCount', 'sortBy', 'sortDir', 'perPage'));
    }

    /**
     * Form thêm sản phẩm mới.
     */
    public function create(): View
    {
        $categories = Category::getTreeList();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Lưu sản phẩm mới vào CSDL.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'material' => ['nullable', 'string', 'max:100'],
            'care_instructions' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục sản phẩm.',
            'sku.unique' => 'Mã SKU này đã tồn tại.',
            'price.required' => 'Vui lòng nhập giá niêm yết.',
            'image.image' => 'Ảnh đại diện Thumbnail không đúng định dạng.',
            'gallery.*.image' => 'Ảnh thư viện Gallery không đúng định dạng.',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $sku = $request->sku ? strtoupper(trim($request->sku)) : 'SKU-'.strtoupper(Str::random(8));

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $slug,
            'sku' => $sku,
            'price' => $request->price,
            'sale_price' => $request->filled('sale_price') ? (float) $request->sale_price : null,
            'description' => $request->description,
            'material' => $request->material,
            'care_instructions' => $request->care_instructions,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        if ($request->hasFile('image')) {
            $this->productService->storeProductImage($product, $request->file('image'), true, 0);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $file) {
                $this->productService->storeProductImage($product, $file, false, $index + 1);
            }
        }

        return redirect()->to(route('admin.products.edit', $product->id).'#product-images')
            ->with('success', 'Thêm sản phẩm mới thành công! Bạn có thể xem hình ảnh và quản lý biến thể bên dưới.');
    }

    /**
     * Form chỉnh sửa sản phẩm.
     */
    public function edit(Product $product): View
    {
        $categories = Category::getTreeList();

        $product->load(['images.color', 'variants.color']);
        $thumbnail = $product->images->where('is_thumbnail', true)->first();
        $galleryImages = $product->images->where('is_thumbnail', false);
        $colors = Color::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'thumbnail', 'galleryImages', 'colors'));
    }

    /**
     * Cập nhật thông tin sản phẩm & tải bổ sung ảnh.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,'.$product->id],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'material' => ['nullable', 'string', 'max:100'],
            'care_instructions' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'category_id.required' => 'Vui lòng chọn danh mục sản phẩm.',
            'sku.unique' => 'Mã SKU này đã tồn tại.',
            'price.required' => 'Vui lòng nhập giá niêm yết.',
            'image.image' => 'Ảnh Thumbnail không đúng định dạng.',
            'gallery.*.image' => 'Ảnh trong Gallery không đúng định dạng.',
        ]);

        $slug = $product->slug;
        if ($request->name !== $product->name) {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
        }

        $sku = $request->sku ? strtoupper(trim($request->sku)) : $product->sku;

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $slug,
            'sku' => $sku,
            'price' => $request->price,
            'sale_price' => $request->filled('sale_price') ? (float) $request->sale_price : null,
            'description' => $request->description,
            'material' => $request->material,
            'care_instructions' => $request->care_instructions,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : ($product->is_active ?? true),
        ]);

        if ($request->hasFile('image')) {
            $oldThumbnail = $product->images()->where('is_thumbnail', true)->first();
            if ($oldThumbnail) {
                $this->productService->deleteProductImage($oldThumbnail);
            }
            $this->productService->storeProductImage($product, $request->file('image'), true, 0);
        }

        if ($request->hasFile('gallery')) {
            $lastOrder = $product->images()->where('is_thumbnail', false)->max('sort_order') ?: 0;
            foreach ($request->file('gallery') as $index => $file) {
                $this->productService->storeProductImage($product, $file, false, $lastOrder + $index + 1);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm và thư viện ảnh thành công!');
    }

    /**
     * Xóa 1 ảnh cụ thể trong Gallery.
     */
    public function deleteImage(ProductImage $image): RedirectResponse
    {
        if (! $image->product_id || ! Product::where('id', $image->product_id)->exists()) {
            return back()->with('error', 'Hình ảnh không tồn tại hoặc sản phẩm không hợp lệ!');
        }

        $this->productService->deleteProductImage($image);

        return back()->with('success', 'Đã xóa hình ảnh khỏi thư viện sản phẩm!');
    }

    /**
     * Cập nhật màu sắc gắn với ảnh của sản phẩm.
     */
    public function updateImageColor(Request $request, ProductImage $image): JsonResponse|RedirectResponse
    {
        $request->validate([
            'color_id' => ['nullable', 'exists:colors,id'],
        ]);

        $image->update([
            'color_id' => $request->filled('color_id') ? (int) $request->color_id : null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật màu sắc cho hình ảnh thành công!',
            ]);
        }

        return back()->with('success', 'Đã cập nhật màu sắc cho hình ảnh!');
    }

    /**
     * Đổi nhanh trạng thái Ẩn/Hiện của sản phẩm.
     */
    public function toggleStatus(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        $statusLabel = $product->is_active ? 'hiển thị' : 'ẩn';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $product->is_active,
                'message' => "Đã chuyển trạng thái sản phẩm '{$product->name}' sang {$statusLabel}!",
            ]);
        }

        return back()->with('success', "Đã đổi trạng thái sản phẩm '{$product->name}' sang {$statusLabel}!");
    }

    /**
     * Nhân bản sản phẩm.
     */
    public function duplicate(Product $product): RedirectResponse
    {
        $product->load(['images', 'variants']);

        $newProduct = $product->replicate();
        $newProduct->name = 'Bản sao - '.$product->name;

        $slug = Str::slug($newProduct->name);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $newProduct->slug = $slug;
        $newProduct->sku = 'SKU-'.strtoupper(Str::random(6));
        $newProduct->is_active = false;
        $newProduct->save();

        foreach ($product->images as $img) {
            ProductImage::create([
                'product_id' => $newProduct->id,
                'image_url' => $img->image_url,
                'is_thumbnail' => $img->is_thumbnail,
                'sort_order' => $img->sort_order,
            ]);
        }

        foreach ($product->variants as $variant) {
            $newVariant = $variant->replicate();
            $newVariant->product_id = $newProduct->id;
            $newVariant->sku = $newProduct->sku.'-'.strtoupper(Str::random(4));
            $newVariant->save();
        }

        return redirect()->route('admin.products.index')->with('success', "Đã nhân bản sản phẩm '{$product->name}' thành công!");
    }

    /**
     * Xử lý thao tác hàng loạt.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:activate,deactivate,delete,change_category'],
            'product_ids' => ['required', 'array'],
            'product_ids.*' => ['exists:products,id'],
            'target_category_id' => ['nullable', 'exists:categories,id'],
        ], [
            'product_ids.required' => 'Vui lòng chọn ít nhất 1 sản phẩm để thực hiện thao tác!',
        ]);

        $productIds = $request->input('product_ids', []);
        $count = count($productIds);

        switch ($request->action) {
            case 'activate':
                Product::whereIn('id', $productIds)->update(['is_active' => true]);
                $msg = "Đã bật hiển thị cho {$count} sản phẩm được chọn!";
                break;

            case 'deactivate':
                Product::whereIn('id', $productIds)->update(['is_active' => false]);
                $msg = "Đã ẩn {$count} sản phẩm được chọn!";
                break;

            case 'change_category':
                if (! $request->filled('target_category_id')) {
                    return back()->with('error', 'Vui lòng chọn danh mục đích khi chuyển danh mục hàng loạt!');
                }
                Product::whereIn('id', $productIds)->update(['category_id' => $request->target_category_id]);
                $msg = "Đã đổi danh mục thành công cho {$count} sản phẩm được chọn!";
                break;

            case 'delete':
                $products = Product::whereIn('id', $productIds)->get();
                foreach ($products as $p) {
                    $p->delete();
                }
                $msg = "Đã xóa {$count} sản phẩm được chọn thành công!";
                break;

            default:
                return back()->with('error', 'Thao tác không hợp lệ!');
        }

        return back()->with('success', $msg);
    }

    /**
     * Xóa sản phẩm và tất cả các ảnh liên quan.
     */
    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $img) {
            $this->productService->deleteProductImage($img);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    /**
     * Bật/Tắt trạng thái Sản phẩm Nổi bật.
     */
    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update(['is_featured' => ! $product->is_featured]);

        $statusStr = $product->is_featured ? 'BẬT' : 'TẮT';

        return redirect()->back()->with('success', "Đã {$statusStr} trạng thái Nổi bật cho sản phẩm {$product->name}!");
    }
}
