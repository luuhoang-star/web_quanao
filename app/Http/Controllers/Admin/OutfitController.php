<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOutfitRequest;
use App\Http\Requests\Admin\UpdateOutfitRequest;
use App\Models\Category;
use App\Models\Outfit;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OutfitController extends Controller
{
    /**
     * Danh sách tất cả Outfit phối sẵn.
     */
    public function index(Request $request): View
    {
        $query = Outfit::withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('badge', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $outfits = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.outfits.index', compact('outfits'));
    }

    /**
     * Form tạo mới Outfit.
     */
    public function create(): View
    {
        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $categories = Category::getTreeList();

        return view('admin.outfits.create', compact('products', 'categories'));
    }

    /**
     * Lưu Outfit mới vào Database.
     */
    public function store(StoreOutfitRequest $request): RedirectResponse
    {
        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name);
        $imagePath = $this->resolveHeroImagePath($request);
        $itemsIncluded = $this->parseItemsIncluded($request->items_included);

        $outfit = Outfit::create([
            'name' => $request->name,
            'slug' => $slug,
            'badge' => $request->badge ?: 'OUTFIT',
            'tagline' => $request->tagline,
            'gradient' => $request->gradient ?: 'linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%)',
            'hero_image' => $imagePath,
            'description' => $request->description,
            'items_included' => $itemsIncluded,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        $this->syncProducts($outfit, $request->product_ids ?? []);

        return redirect()->route('admin.outfits.index')->with('success', 'Tạo mới Outfit thành công!');
    }

    /**
     * Form chỉnh sửa Outfit.
     */
    public function edit(Outfit $outfit): View
    {
        $outfit->load('products');
        $products = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $selectedProductIds = $outfit->products->pluck('id')->toArray();
        $itemsIncludedString = is_array($outfit->items_included) ? implode("\n", $outfit->items_included) : '';
        $categories = Category::getTreeList();

        return view('admin.outfits.edit', compact('outfit', 'products', 'selectedProductIds', 'itemsIncludedString', 'categories'));
    }

    /**
     * Cập nhật Outfit.
     */
    public function update(UpdateOutfitRequest $request, Outfit $outfit): RedirectResponse
    {
        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name);
        $imagePath = $this->resolveHeroImagePath($request, $outfit->hero_image);
        $itemsIncluded = $this->parseItemsIncluded($request->items_included);

        $outfit->update([
            'name' => $request->name,
            'slug' => $slug,
            'badge' => $request->badge ?: 'OUTFIT',
            'tagline' => $request->tagline,
            'gradient' => $request->gradient ?: $outfit->gradient,
            'hero_image' => $imagePath,
            'description' => $request->description,
            'items_included' => $itemsIncluded,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : $outfit->sort_order,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
        ]);

        if ($request->has('product_ids')) {
            $this->syncProducts($outfit, $request->product_ids ?? []);
        } else {
            $outfit->products()->detach();
        }

        return redirect()->route('admin.outfits.index')->with('success', 'Cập nhật Outfit thành công!');
    }

    /**
     * Đổi trạng thái Bật/Tắt hiển thị Outfit.
     */
    public function toggleStatus(Outfit $outfit): RedirectResponse
    {
        $outfit->update(['is_active' => ! $outfit->is_active]);

        return back()->with('success', 'Đã đổi trạng thái hiển thị Outfit!');
    }

    /**
     * Xóa Outfit.
     */
    public function destroy(Outfit $outfit): RedirectResponse
    {
        if ($outfit->hero_image && ! str_starts_with($outfit->hero_image, 'http')) {
            Storage::disk('public')->delete($outfit->hero_image);
        }

        $outfit->products()->detach();
        $outfit->delete();

        return redirect()->route('admin.outfits.index')->with('success', 'Đã xóa Outfit!');
    }

    /**
     * Helper xử lý đường dẫn ảnh Hero Banner.
     */
    private function resolveHeroImagePath(Request $request, ?string $currentPath = null): string
    {
        if ($request->hasFile('hero_image')) {
            if ($currentPath && ! str_starts_with($currentPath, 'http')) {
                Storage::disk('public')->delete($currentPath);
            }

            return $request->file('hero_image')->store('outfits', 'public');
        }

        if ($request->filled('hero_image_url')) {
            return $request->hero_image_url;
        }

        return $currentPath ?? 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80';
    }

    /**
     * Helper tách mảng danh sách món bao gồm từ textarea.
     */
    private function parseItemsIncluded(?string $input): array
    {
        if (! $input) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode("\n", str_replace(',', "\n", $input)))));
    }

    /**
     * Helper đồng bộ danh sách sản phẩm với pivot table.
     */
    private function syncProducts(Outfit $outfit, array $productIds): void
    {
        $syncData = [];
        foreach ($productIds as $index => $productId) {
            $syncData[$productId] = ['sort_order' => $index + 1];
        }
        $outfit->products()->sync($syncData);
    }
}
