<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Database\Seeders\CollectionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollectionController extends Controller
{
    /**
     * Danh sách Bộ Sưu Tập.
     */
    public function index(Request $request): View
    {
        if (Collection::count() === 0) {
            (new CollectionSeeder)->run();
        }

        $query = Collection::withCount('products');

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('badge_text', 'like', "%{$keyword}%")
                    ->orWhere('subtitle', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $collections = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.collections.index', compact('collections'));
    }

    /**
     * Form tạo mới Bộ Sưu Tập.
     */
    public function create(): View
    {
        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $categories = Category::getTreeList();

        return view('admin.collections.create', compact('products', 'categories'));
    }

    /**
     * Lưu Bộ Sưu Tập mới vào Database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:collections,slug'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['exists:products,id'],
        ], [
            'name.required' => 'Vui lòng nhập tên bộ sưu tập.',
            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn slug khác.',
            'image.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'image.max' => 'Dung lượng ảnh tối đa là 2MB.',
            'image_url.url' => 'Đường dẫn URL ảnh không hợp lệ.',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        $imagePath = $this->resolveImagePath($request);

        $collection = Collection::create([
            'name' => $request->name,
            'slug' => $slug,
            'badge_text' => $request->badge_text,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'image' => $imagePath,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        $this->syncProducts($collection, $request->product_ids ?? []);

        return redirect()->route('admin.collections.index')->with('success', 'Thêm mới Bộ Sưu Tập thành công!');
    }

    /**
     * Form chỉnh sửa Bộ Sưu Tập.
     */
    public function edit(Collection $collection): View
    {
        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $selectedProductIds = $collection->products()->pluck('id')->toArray();
        $categories = Category::getTreeList();

        return view('admin.collections.edit', compact('collection', 'products', 'selectedProductIds', 'categories'));
    }

    /**
     * Cập nhật thông tin Bộ Sưu Tập.
     */
    public function update(Request $request, Collection $collection): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:collections,slug,'.$collection->id],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['exists:products,id'],
        ], [
            'name.required' => 'Vui lòng nhập tên bộ sưu tập.',
            'slug.unique' => 'Slug này đã tồn tại, vui lòng chọn slug khác.',
            'image.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'image.max' => 'Dung lượng ảnh tối đa là 2MB.',
            'image_url.url' => 'Đường dẫn URL ảnh không hợp lệ.',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->name);

        $imagePath = $this->resolveImagePath($request, $collection->image);

        $collection->update([
            'name' => $request->name,
            'slug' => $slug,
            'badge_text' => $request->badge_text,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'image' => $imagePath,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
        ]);

        $this->syncProducts($collection, $request->product_ids ?? []);

        return redirect()->route('admin.collections.index')->with('success', 'Cập nhật Bộ Sưu Tập thành công!');
    }

    /**
     * Đồng bộ danh sách Sản phẩm thuộc Bộ Sưu Tập.
     */
    private function syncProducts(Collection $collection, array $productIds): void
    {
        Product::where('collection_id', $collection->id)
            ->whereNotIn('id', $productIds)
            ->update(['collection_id' => null]);

        if (! empty($productIds)) {
            Product::whereIn('id', $productIds)
                ->update(['collection_id' => $collection->id]);
        }
    }

    /**
     * Bật/Tắt trạng thái hiển thị Bộ Sưu Tập.
     */
    public function toggleStatus(Collection $collection): RedirectResponse
    {
        $collection->update(['is_active' => ! $collection->is_active]);

        $statusStr = $collection->is_active ? 'BẬT' : 'TẮT';

        return redirect()->back()->with('success', "Đã {$statusStr} trạng thái hiển thị cho Bộ sưu tập \"{$collection->name}\"!");
    }

    /**
     * Xóa Bộ Sưu Tập.
     */
    public function destroy(Collection $collection): RedirectResponse
    {
        $collection->delete();

        return redirect()->route('admin.collections.index')->with('success', 'Đã xóa Bộ Sưu Tập thành công!');
    }

    /**
     * Helper xử lý lấy đường dẫn ảnh từ file upload hoặc URL.
     */
    private function resolveImagePath(Request $request, ?string $currentPath = null): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('collections', 'public');
        }

        if ($request->filled('image_url')) {
            return trim($request->image_url);
        }

        return $currentPath;
    }
}
