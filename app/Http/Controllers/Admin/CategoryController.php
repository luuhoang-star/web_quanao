<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Danh sách danh mục sản phẩm (Search, Advanced Filters, Tree Hierarchy, Stats & Pagination).
     */
    public function index(Request $request): View|JsonResponse
    {
        // 1. Thống kê số liệu Realtime cho Summary Cards
        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'with_products' => Category::has('products')->count(),
            'roots' => Category::whereNull('parent_id')->count(),
        ];

        $isFiltering = $request->anyFilled(['search', 'level', 'parent_id', 'is_active', 'has_products', 'sort_by']);
        $activeFilterCount = 0;

        if ($isFiltering) {
            $query = Category::with(['parent', 'children'])
                ->withCount(['products', 'children']);

            if ($request->filled('search')) {
                $activeFilterCount++;
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            if ($request->filled('level')) {
                $activeFilterCount++;
                if ($request->level === 'root') {
                    $query->whereNull('parent_id');
                } elseif ($request->level === 'child') {
                    $query->whereNotNull('parent_id');
                }
            }

            if ($request->filled('parent_id')) {
                $activeFilterCount++;
                $query->where('parent_id', $request->parent_id);
            }

            if ($request->filled('is_active')) {
                $activeFilterCount++;
                if ($request->is_active === '1' || $request->is_active === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->is_active === '0' || $request->is_active === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            if ($request->filled('has_products')) {
                $activeFilterCount++;
                if ($request->has_products === 'yes') {
                    $query->has('products');
                } elseif ($request->has_products === 'no') {
                    $query->doesntHave('products');
                }
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

            if ($sortBy === 'name') {
                $query->orderBy('name', $sortDir);
            } elseif ($sortBy === 'products') {
                $query->orderBy('products_count', $sortDir);
            } elseif ($sortBy === 'created_at') {
                $query->orderBy('created_at', $sortDir);
            } else {
                $query->orderBy('sort_order', $sortDir)->orderBy('name', 'asc');
            }

            $perPage = (int) $request->get('per_page', 50);
            if (! in_array($perPage, [10, 15, 25, 50, 100])) {
                $perPage = 50;
            }

            $allCategories = $query->paginate($perPage)->withQueryString();
        } else {
            // Sắp xếp phân cấp: Danh mục Gốc (Cha) đứng đầu nhóm, theo sau là các Danh mục Con
            $roots = Category::with([
                'parent',
                'children' => fn ($q) => $q->withCount(['products', 'children'])->orderBy('sort_order', 'asc')->orderBy('name', 'asc'),
            ])
                ->withCount(['products', 'children'])
                ->whereNull('parent_id')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            $orderedList = collect();
            foreach ($roots as $root) {
                // Đưa danh mục gốc (Cha) vào danh sách
                $orderedList->push($root);

                // Đưa các danh mục con thuộc danh mục gốc vào ngay bên dưới
                foreach ($root->children as $child) {
                    $orderedList->push($child);
                }
            }

            // Lấy thêm danh mục độc lập nếu có (không có cha và không có con)
            $standalones = Category::withCount(['products', 'children'])
                ->whereNull('parent_id')
                ->doesntHave('children')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            foreach ($standalones as $standalone) {
                if (! $orderedList->contains('id', $standalone->id)) {
                    $orderedList->push($standalone);
                }
            }

            $page = Paginator::resolveCurrentPage() ?: 1;
            $perPage = (int) $request->get('per_page', 50);
            if (! in_array($perPage, [10, 15, 25, 50, 100])) {
                $perPage = 50;
            }

            $itemsForCurrentPage = $orderedList->slice(($page - 1) * $perPage, $perPage)->values();

            $allCategories = new LengthAwarePaginator(
                $itemsForCurrentPage,
                $orderedList->count(),
                $perPage,
                $page,
                ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }

        $rootCategories = Category::whereNull('parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $allCategories,
            ]);
        }

        return view('admin.categories.index', compact('allCategories', 'rootCategories', 'stats', 'activeFilterCount'));
    }

    /**
     * Form thêm mới danh mục.
     */
    public function create(Request $request): View
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->get();
        $selectedParentId = $request->query('parent_id');

        return view('admin.categories.create', compact('parentCategories', 'selectedParentId'));
    }

    /**
     * Lưu danh mục mới vào CSDL.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // 1. Tạo Slug chuẩn SEO
        $slug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        // 2. Upload Ảnh Danh Mục (nếu có)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $parentId = ! empty($validated['parent_id']) ? (int) $validated['parent_id'] : null;
        $maxOrder = Category::where('parent_id', $parentId)->max('sort_order') ?? 0;
        $sortOrder = isset($validated['sort_order']) && $validated['sort_order'] !== '' ? (int) $validated['sort_order'] : ($maxOrder + 1);

        Category::create([
            'parent_id' => $parentId,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'sort_order' => $sortOrder,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->reindexCategoryGroup($parentId);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục sản phẩm mới thành công!');
    }

    /**
     * Form chỉnh sửa danh mục.
     */
    public function edit(Category $category): View
    {
        // Chống chọn chính nó và chống chọn danh mục con/cháu của chính nó làm danh mục cha (Chống circular parent)
        $excludedIds = array_merge([$category->id], $category->getDescendantIds());

        $parentCategories = Category::whereNull('parent_id')
            ->whereNotIn('id', $excludedIds)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Cập nhật danh mục.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();
        $oldParentId = $category->parent_id;

        // 1. Cập nhật Slug SEO
        $slug = $category->slug;
        if (! empty($validated['slug'])) {
            $slug = Str::slug($validated['slug']);
        } elseif ($validated['name'] !== $category->name) {
            $slug = Str::slug($validated['name']);
        }

        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        // 2. Quản lý Ảnh Danh Mục (Upload ảnh mới -> Xóa ảnh cũ)
        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $newParentId = ! empty($validated['parent_id']) ? (int) $validated['parent_id'] : null;

        $category->update([
            'parent_id' => $newParentId,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'sort_order' => isset($validated['sort_order']) && $validated['sort_order'] !== '' ? (int) $validated['sort_order'] : $category->sort_order,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($category->is_active ?? true),
        ]);

        $this->reindexCategoryGroup($newParentId);
        if ($oldParentId !== $newParentId) {
            $this->reindexCategoryGroup($oldParentId);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục sản phẩm thành công!');
    }

    /**
     * Đổi nhanh trạng thái Ẩn/Hiện danh mục.
     */
    public function toggleStatus(Category $category, Request $request): RedirectResponse|JsonResponse
    {
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        $statusText = $category->is_active ? 'hiển thị' : 'tạm ẩn';
        $message = "Đã đổi trạng thái danh mục '{$category->name}' sang {$statusText}!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $category->is_active,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Thao tác hàng loạt trên nhiều Danh mục.
     */
    public function bulkAction(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:enable,disable,delete'],
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ], [
            'category_ids.required' => 'Vui lòng chọn ít nhất 1 danh mục để thực hiện thao tác!',
        ]);

        $categoryIds = $request->input('category_ids', []);
        $categories = Category::whereIn('id', $categoryIds)->get();

        if ($request->action === 'enable') {
            Category::whereIn('id', $categoryIds)->update(['is_active' => true]);
            $msg = 'Đã bật hiển thị cho '.count($categoryIds).' danh mục!';
        } elseif ($request->action === 'disable') {
            Category::whereIn('id', $categoryIds)->update(['is_active' => false]);
            $msg = 'Đã ẩn '.count($categoryIds).' danh mục!';
        } elseif ($request->action === 'delete') {
            $deletedCount = 0;
            $skippedCount = 0;
            $affectedParentIds = [];

            foreach ($categories as $cat) {
                // Kiểm tra xem danh mục có sản phẩm hoặc danh mục con không
                if ($cat->products()->exists() || $cat->children()->exists()) {
                    $skippedCount++;

                    continue;
                }

                if ($cat->image && Storage::disk('public')->exists($cat->image)) {
                    Storage::disk('public')->delete($cat->image);
                }

                $affectedParentIds[] = $cat->parent_id;
                $cat->sizes()->detach();
                $cat->colors()->detach();
                $cat->delete();
                $deletedCount++;
            }

            foreach (array_unique($affectedParentIds) as $pId) {
                $this->reindexCategoryGroup($pId);
            }

            if ($skippedCount > 0 && $deletedCount > 0) {
                $msg = "Đã xóa {$deletedCount} danh mục rỗng. Bỏ qua {$skippedCount} danh mục đang chứa sản phẩm hoặc danh mục con (chặn xóa để bảo vệ dữ liệu).";
            } elseif ($skippedCount > 0 && $deletedCount === 0) {
                return back()->with('error', "Không thể xóa {$skippedCount} danh mục đã chọn vì tất cả đều đang có sản phẩm hoặc danh mục con!");
            } else {
                $msg = "Đã xóa thành công {$deletedCount} danh mục!";
            }
        }

        return redirect()->route('admin.categories.index')->with('success', $msg);
    }

    /**
     * Xóa danh mục (CHẶN XÓA khi danh mục đang chứa sản phẩm hoặc danh mục con).
     */
    public function destroy(Category $category, Request $request): RedirectResponse|JsonResponse
    {
        // 1. Chặn xóa nếu danh mục đang có sản phẩm
        if ($category->products()->exists()) {
            $productCount = $category->products()->count();
            $errorMessage = "Không thể xóa danh mục '{$category->name}' vì đang có {$productCount} sản phẩm sử dụng. Vui lòng chuyển hướng sản phẩm trước hoặc chuyển danh mục sang Tạm Ẩn.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return back()->with('error', $errorMessage);
        }

        // 2. Chặn xóa nếu danh mục đang có danh mục con
        if ($category->children()->exists()) {
            $childCount = $category->children()->count();
            $errorMessage = "Không thể xóa danh mục '{$category->name}' vì đang có {$childCount} danh mục con. Vui lòng xóa hoặc gỡ danh mục con trước.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return back()->with('error', $errorMessage);
        }

        // 3. Xóa tệp ảnh đại diện nếu có
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        // Gỡ liên kết pivot size/color và thực hiện soft delete
        $parentId = $category->parent_id;
        $category->sizes()->detach();
        $category->colors()->detach();
        $categoryName = $category->name;
        $category->delete();

        $this->reindexCategoryGroup($parentId);

        $parentRemainingChildren = 0;
        $parentRemainingProducts = 0;
        $parentCanDelete = false;

        if ($parentId) {
            $parent = Category::find($parentId);
            if ($parent) {
                $parentRemainingChildren = $parent->children()->count();
                $parentRemainingProducts = $parent->products()->count();
                $parentCanDelete = ($parentRemainingChildren === 0 && $parentRemainingProducts === 0);
            }
        }

        $successMessage = "Xóa danh mục '{$categoryName}' thành công!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'deleted_id' => $category->id,
                'parent_id' => $parentId,
                'parent_remaining_children' => $parentRemainingChildren,
                'parent_remaining_products' => $parentRemainingProducts,
                'parent_can_delete' => $parentCanDelete,
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', $successMessage);
    }

    /**
     * Lấy danh sách sản phẩm thuộc danh mục (AJAX Modal).
     */
    public function getProducts(Category $category): JsonResponse
    {
        $products = $category->products()
            ->with(['primaryImage'])
            ->select('id', 'category_id', 'name', 'slug', 'sku', 'price', 'sale_price', 'is_active')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'price' => number_format((float) $p->price, 0, ',', '.').'đ',
                    'sale_price' => $p->sale_price ? number_format((float) $p->sale_price, 0, ',', '.').'đ' : null,
                    'is_active' => $p->is_active,
                    'image_url' => $p->primaryImage?->display_url ?? 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=150&q=80',
                    'edit_url' => route('admin.products.edit', $p->id),
                ];
            });

        return response()->json([
            'success' => true,
            'category_id' => $category->id,
            'category_name' => $category->name,
            'count' => $products->count(),
            'products' => $products,
        ]);
    }

    /**
     * Xóa 1 sản phẩm cụ thể khỏi danh mục (AJAX).
     */
    public function deleteProduct(Category $category, Product $product): JsonResponse
    {
        if ($product->category_id !== $category->id) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không thuộc danh mục này!',
            ], 400);
        }

        $productName = $product->name;
        $product->delete();

        $remainingProducts = $category->products()->count();
        $childrenCount = $category->children()->count();
        $canDeleteCategory = ($remainingProducts === 0 && $childrenCount === 0);

        return response()->json([
            'success' => true,
            'message' => "Đã xóa sản phẩm '{$productName}' thành công!",
            'category_id' => $category->id,
            'remaining_products' => $remainingProducts,
            'can_delete_category' => $canDeleteCategory,
        ]);
    }

    /**
     * Xóa các sản phẩm được tích chọn trong danh mục (AJAX Bulk Delete).
     */
    public function deleteSelectedProducts(Category $category, Request $request): JsonResponse
    {
        $productIds = $request->input('product_ids', []);
        if (empty($productIds) || ! is_array($productIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng tích chọn ít nhất 1 sản phẩm để xóa!',
            ], 422);
        }

        $products = $category->products()->whereIn('id', $productIds)->get();
        $deletedCount = $products->count();

        if ($deletedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm hợp lệ thuộc danh mục này để xóa!',
            ], 404);
        }

        foreach ($products as $product) {
            $product->delete();
        }

        $remainingProducts = $category->products()->count();
        $childrenCount = $category->children()->count();
        $canDeleteCategory = ($remainingProducts === 0 && $childrenCount === 0);

        return response()->json([
            'success' => true,
            'message' => "Đã xóa {$deletedCount} sản phẩm đã chọn thành công!",
            'category_id' => $category->id,
            'deleted_ids' => $productIds,
            'remaining_products' => $remainingProducts,
            'can_delete_category' => $canDeleteCategory,
        ]);
    }

    /**
     * Xóa toàn bộ sản phẩm trong danh mục (AJAX).
     */
    public function deleteAllProducts(Category $category): JsonResponse
    {
        $products = $category->products;
        $count = $products->count();

        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Danh mục này không còn sản phẩm nào!',
            ], 400);
        }

        foreach ($products as $p) {
            $p->delete();
        }

        $childrenCount = $category->children()->count();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa toàn bộ {$count} sản phẩm trong danh mục '{$category->name}'!",
            'category_id' => $category->id,
            'remaining_products' => 0,
            'can_delete_category' => ($childrenCount === 0),
        ]);
    }

    /**
     * Cập nhật thứ tự sắp xếp danh mục bằng Kéo - Thả (Drag & Drop AJAX).
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:categories,id',
            'items.*.sort_order' => 'required|integer',
            'items.*.parent_id' => 'nullable',
        ]);

        $affectedParentIds = [];

        foreach ($request->input('items', []) as $item) {
            $cat = Category::find($item['id']);
            if ($cat) {
                $oldParentId = $cat->parent_id;
                $newParentId = isset($item['parent_id']) && $item['parent_id'] !== '' && $item['parent_id'] !== 'null' ? (int) $item['parent_id'] : null;

                $cat->update([
                    'parent_id' => $newParentId,
                    'sort_order' => (int) $item['sort_order'],
                ]);

                $affectedParentIds[] = $oldParentId;
                $affectedParentIds[] = $newParentId;
            }
        }

        foreach (array_unique($affectedParentIds) as $pId) {
            $this->reindexCategoryGroup($pId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thứ tự và nhóm danh mục thành công!',
        ]);
    }

    /**
     * Đánh lại số thứ tự tự tăng (1, 2, 3...) cho danh mục thuộc parent_id để đảm bảo không ngắt quãng.
     */
    protected function reindexCategoryGroup(?int $parentId = null): void
    {
        $categories = Category::where('parent_id', $parentId)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        foreach ($categories as $index => $cat) {
            $cat->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Đánh lại số thứ tự tự tăng (1, 2, 3...) cho toàn bộ danh mục Cha và Con theo từng nhóm parent_id.
     */
    protected function reindexAllCategories(): void
    {
        $roots = Category::whereNull('parent_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($roots as $pIndex => $root) {
            $root->update(['sort_order' => $pIndex + 1]);

            $children = Category::where('parent_id', $root->id)
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($children as $cIndex => $child) {
                $child->update(['sort_order' => $cIndex + 1]);
            }
        }
    }
}
