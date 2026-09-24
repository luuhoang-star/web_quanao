<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLookbookRequest;
use App\Http\Requests\Admin\UpdateLookbookRequest;
use App\Models\Category;
use App\Models\Lookbook;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LookbookController extends Controller
{
    /**
     * Danh sách tất cả Lookbooks.
     */
    public function index(Request $request): View
    {
        $query = Lookbook::withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $lookbooks = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.lookbooks.index', compact('lookbooks'));
    }

    /**
     * Form tạo mới Lookbook.
     */
    public function create(): View
    {
        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $categories = Category::getTreeList();

        return view('admin.lookbooks.create', compact('products', 'categories'));
    }

    /**
     * Lưu Lookbook mới vào Database.
     */
    public function store(StoreLookbookRequest $request): RedirectResponse
    {
        $imagePath = $this->resolveImagePath($request);

        $lookbook = Lookbook::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'instagram_url' => $request->instagram_url,
            'button_text' => $request->button_text ?: null,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        $this->syncProducts($lookbook, $request->product_ids ?? []);

        return redirect()->route('admin.lookbooks.index')->with('success', 'Thêm mới Lookbook thành công!');
    }

    /**
     * Form chỉnh sửa Lookbook.
     */
    public function edit(Lookbook $lookbook): View
    {
        $lookbook->load('products');
        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $selectedProductIds = $lookbook->products->pluck('id')->toArray();
        $categories = Category::getTreeList();

        return view('admin.lookbooks.edit', compact('lookbook', 'products', 'selectedProductIds', 'categories'));
    }

    /**
     * Cập nhật Lookbook trong Database.
     */
    public function update(UpdateLookbookRequest $request, Lookbook $lookbook): RedirectResponse
    {
        $imagePath = $this->resolveImagePath($request, $lookbook->image_path);

        $lookbook->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'instagram_url' => $request->instagram_url,
            'button_text' => $request->button_text ?: null,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : $lookbook->sort_order,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
        ]);

        if ($request->has('product_ids')) {
            $this->syncProducts($lookbook, $request->product_ids ?? []);
        } else {
            $lookbook->products()->detach();
        }

        return redirect()->route('admin.lookbooks.index')->with('success', 'Cập nhật Lookbook thành công!');
    }

    /**
     * Đổi trạng thái Bật/Tắt hiển thị Lookbook.
     */
    public function toggleStatus(Lookbook $lookbook): RedirectResponse
    {
        $lookbook->update(['is_active' => ! $lookbook->is_active]);

        return back()->with('success', 'Đã đổi trạng thái hiển thị Lookbook!');
    }

    /**
     * Xóa Lookbook.
     */
    public function destroy(Lookbook $lookbook): RedirectResponse
    {
        if ($lookbook->image_path && ! str_starts_with($lookbook->image_path, 'http')) {
            Storage::disk('public')->delete($lookbook->image_path);
        }

        $lookbook->products()->detach();
        $lookbook->delete();

        return redirect()->route('admin.lookbooks.index')->with('success', 'Đã xóa Lookbook!');
    }

    /**
     * Helper xử lý đường dẫn hình ảnh tải lên hoặc từ URL.
     */
    private function resolveImagePath(Request $request, ?string $currentPath = null): string
    {
        if ($request->hasFile('image')) {
            if ($currentPath && ! str_starts_with($currentPath, 'http')) {
                Storage::disk('public')->delete($currentPath);
            }

            return $request->file('image')->store('lookbooks', 'public');
        }

        if ($request->filled('image_url')) {
            return $request->image_url;
        }

        return $currentPath ?? '';
    }

    /**
     * Helper đồng bộ danh sách sản phẩm với pivot table.
     */
    private function syncProducts(Lookbook $lookbook, array $productIds): void
    {
        $syncData = [];
        foreach ($productIds as $index => $productId) {
            $syncData[$productId] = ['sort_order' => $index + 1];
        }
        $lookbook->products()->sync($syncData);
    }
}
