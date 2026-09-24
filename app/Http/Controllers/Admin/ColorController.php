<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreColorRequest;
use App\Http\Requests\Admin\UpdateColorRequest;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ColorController extends Controller
{
    /**
     * Danh sách màu sắc sản phẩm (Search, Advanced Filters, Stats, Sorting & Pagination).
     */
    public function index(Request $request): View|JsonResponse
    {
        // 1. Thống kê số liệu Realtime cho Summary Cards
        $stats = [
            'total' => Color::count(),
            'active' => Color::where('is_active', true)->count(),
            'used' => Color::has('variants')->count(),
            'unused' => Color::doesntHave('variants')->count(),
        ];

        // 2. Query Builder với eager load count variants & distinct products
        $query = Color::withCount([
            'variants',
            'products' => fn ($q) => $q->select(DB::raw('count(distinct products.id)')),
        ]);
        $activeFilterCount = 0;

        // Tìm kiếm theo Tên màu, Mã code hoặc HEX
        if ($request->filled('search')) {
            $activeFilterCount++;
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('hex_code', 'like', "%{$search}%");
            });
        }

        // Lọc theo Tình trạng sử dụng (used / unused)
        if ($request->filled('usage_status')) {
            $activeFilterCount++;
            if ($request->usage_status === 'used') {
                $query->has('variants');
            } elseif ($request->usage_status === 'unused') {
                $query->doesntHave('variants');
            }
        }

        // Lọc theo Trạng thái hiển thị (1/active hoặc 0/inactive)
        if ($request->filled('is_active')) {
            $activeFilterCount++;
            if ($request->is_active === '1' || $request->is_active === 'active') {
                $query->where('is_active', true);
            } elseif ($request->is_active === '0' || $request->is_active === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'name') {
            $query->orderBy('name', $sortDir);
        } elseif ($sortBy === 'code') {
            $query->orderBy('code', $sortDir);
        } elseif ($sortBy === 'variants') {
            $query->orderBy('variants_count', $sortDir);
        } elseif ($sortBy === 'created_at') {
            $query->orderBy('created_at', $sortDir);
        } else {
            $query->orderBy('id', $sortDir);
        }

        // Phân trang
        $perPage = (int) $request->get('per_page', 15);
        if (! in_array($perPage, [10, 15, 30, 50, 100])) {
            $perPage = 15;
        }

        $colors = $query->paginate($perPage)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $colors,
            ]);
        }

        return view('admin.colors.index', compact('colors', 'stats', 'activeFilterCount', 'sortBy', 'sortDir', 'perPage'));
    }

    /**
     * Form thêm màu sắc mới.
     */
    public function create(): View
    {
        return view('admin.colors.create');
    }

    /**
     * Lưu màu sắc mới vào CSDL.
     */
    public function store(StoreColorRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $color = Color::create([
            'name' => $validated['name'],
            'code' => ! empty($validated['code']) ? $validated['code'] : null,
            'hex_code' => $validated['hex_code'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm màu sắc mới thành công!',
                'data' => $color,
            ]);
        }

        return redirect()->route('admin.colors.index')->with('success', 'Thêm màu sắc mới thành công!');
    }

    /**
     * Form chỉnh sửa màu sắc.
     */
    public function edit(Color $color, Request $request): View|JsonResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $color,
            ]);
        }

        return view('admin.colors.edit', compact('color'));
    }

    /**
     * Cập nhật màu sắc.
     */
    public function update(UpdateColorRequest $request, Color $color): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $color->update([
            'name' => $validated['name'],
            'code' => ! empty($validated['code']) ? $validated['code'] : null,
            'hex_code' => $validated['hex_code'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật màu sắc thành công!',
                'data' => $color,
            ]);
        }

        return redirect()->route('admin.colors.index')->with('success', 'Cập nhật màu sắc thành công!');
    }

    /**
     * Lấy danh sách sản phẩm & biến thể đang sử dụng Màu sắc này (AJAX endpoint cho Modal).
     */
    public function getVariants(Color $color): JsonResponse
    {
        $products = Product::whereHas('variants', fn ($q) => $q->where('color_id', $color->id))
            ->with([
                'category:id,name',
                'primaryImage',
                'images' => fn ($q) => $q->where('color_id', $color->id),
                'variants' => fn ($q) => $q->where('color_id', $color->id)->with('size:id,name'),
            ])
            ->get()
            ->map(function ($product) use ($color) {
                $thumb = $product->images->first()?->display_url
                    ?? $product->primaryImage?->display_url
                    ?? 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80';

                $allActive = $product->variants->every(fn ($v) => (bool) $v->is_active);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku ?? 'SP-'.$product->id,
                    'category_name' => $product->category?->name ?? 'Chưa phân loại',
                    'thumbnail' => $thumb,
                    'price_formatted' => number_format((float) $product->price, 0, ',', '.').' ₫',
                    'edit_url' => route('admin.products.edit', $product->id),
                    'variants_url' => route('admin.products.variants.index', $product->id),
                    'all_active' => $allActive,
                    'detach_url' => route('admin.colors.detachProduct', [$color->id, $product->id]),
                    'toggle_status_url' => route('admin.colors.toggleProductStatus', [$color->id, $product->id]),
                    'variants' => $product->variants->map(fn ($v) => [
                        'id' => $v->id,
                        'size_name' => $v->size?->name ?? 'Freesize',
                        'sku' => $v->sku ?? 'N/A',
                        'stock_quantity' => $v->stock_quantity,
                        'is_active' => (bool) $v->is_active,
                        'stock_status' => $v->stock_status,
                        'stock_status_label' => $v->stock_status_label,
                        'stock_status_badge' => $v->stock_status_badge,
                        'edit_variant_url' => route('admin.products.variants.edit', [$product->id, $v->id]),
                    ]),
                ];
            });

        $availableColors = Color::where('id', '!=', $color->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'hex_code')
            ->get();

        return response()->json([
            'success' => true,
            'color_id' => $color->id,
            'color_name' => $color->name,
            'color_hex' => $color->hex_code ?? '#000000',
            'products_count' => $products->count(),
            'variants_count' => $color->variants()->count(),
            'products' => $products,
            'available_colors' => $availableColors,
            'transfer_url' => route('admin.colors.transfer', $color->id),
        ]);
    }

    /**
     * Gỡ bỏ toàn bộ biến thể màu sắc này khỏi 1 sản phẩm cụ thể.
     */
    public function detachProduct(Color $color, Product $product): JsonResponse
    {
        $deleted = ProductVariant::where('product_id', $product->id)
            ->where('color_id', $color->id)
            ->delete();

        ProductVariantController::syncProductSalePrice($product);

        $remainingProducts = $color->products()->distinct('products.id')->count('products.id');
        $remainingVariants = $color->variants()->count();

        return response()->json([
            'success' => true,
            'message' => "Đã gỡ màu '{$color->name}' ({$deleted} biến thể) khỏi sản phẩm '{$product->name}' thành công!",
            'remaining_products_count' => $remainingProducts,
            'remaining_variants_count' => $remainingVariants,
            'can_delete_color' => $remainingVariants === 0,
        ]);
    }

    /**
     * Bật / Tắt trạng thái bán màu sắc này cho 1 sản phẩm cụ thể.
     */
    public function toggleProductColorStatus(Color $color, Product $product): JsonResponse
    {
        $variants = ProductVariant::where('product_id', $product->id)
            ->where('color_id', $color->id)
            ->get();

        if ($variants->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy biến thể nào của sản phẩm với màu này.',
            ], 404);
        }

        $allActive = $variants->every(fn ($v) => (bool) $v->is_active);
        $newStatus = ! $allActive;

        ProductVariant::where('product_id', $product->id)
            ->where('color_id', $color->id)
            ->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'BẬT bán' : 'TẠM NGƯNG bán';

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
            'message' => "Đã {$statusText} màu '{$color->name}' cho sản phẩm '{$product->name}'!",
        ]);
    }

    /**
     * Chuyển đổi toàn bộ biến thể từ Màu này sang Màu khác hàng loạt (1-click merge).
     */
    public function transferColor(Color $color, Request $request): JsonResponse
    {
        $request->validate([
            'target_color_id' => ['required', 'exists:colors,id', 'different:color_id'],
        ], [
            'target_color_id.required' => 'Vui lòng chọn màu sắc đích muốn chuyển sang.',
            'target_color_id.exists' => 'Màu sắc đích được chọn không hợp lệ.',
            'target_color_id.different' => 'Màu đích phải khác với màu hiện tại.',
        ]);

        $targetColor = Color::findOrFail($request->target_color_id);

        $variants = ProductVariant::where('color_id', $color->id)->get();
        $updatedCount = 0;
        $mergedCount = 0;

        DB::transaction(function () use ($variants, $targetColor, &$updatedCount, &$mergedCount) {
            foreach ($variants as $variant) {
                $existing = ProductVariant::where('product_id', $variant->product_id)
                    ->where('color_id', $targetColor->id)
                    ->where('size_id', $variant->size_id)
                    ->where('id', '!=', $variant->id)
                    ->first();

                if ($existing) {
                    $existing->increment('stock_quantity', $variant->stock_quantity);
                    $variant->delete();
                    $mergedCount++;
                } else {
                    $variant->update(['color_id' => $targetColor->id]);
                    $updatedCount++;
                }
            }

            $productCategoryIds = Product::whereIn('id', $variants->pluck('product_id')->unique())
                ->pluck('category_id')
                ->filter()
                ->unique();

            if ($productCategoryIds->isNotEmpty()) {
                $targetColor->categories()->syncWithoutDetaching($productCategoryIds);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Đã chuyển toàn bộ biến thể từ '{$color->name}' sang '{$targetColor->name}' thành công! (Cập nhật {$updatedCount}, gộp kho {$mergedCount} biến thể).",
            'remaining_products_count' => 0,
            'remaining_variants_count' => 0,
        ]);
    }

    /**
     * Đổi nhanh trạng thái Ẩn/Hiện màu sắc.
     */
    public function toggleStatus(Color $color, Request $request): RedirectResponse|JsonResponse
    {
        $color->update([
            'is_active' => ! $color->is_active,
        ]);

        $statusText = $color->is_active ? 'hiển thị' : 'tạm ẩn';
        $message = "Đã đổi trạng thái màu sắc '{$color->name}' sang {$statusText}!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $color->is_active,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Thao tác hàng loạt trên nhiều Màu sắc.
     */
    public function bulkAction(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:enable,disable,delete'],
            'color_ids' => ['required', 'array'],
            'color_ids.*' => ['exists:colors,id'],
        ], [
            'color_ids.required' => 'Vui lòng chọn ít nhất 1 màu sắc để thực hiện thao tác!',
        ]);

        $colorIds = $request->input('color_ids', []);
        $colors = Color::whereIn('id', $colorIds)->get();

        if ($request->action === 'enable') {
            Color::whereIn('id', $colorIds)->update(['is_active' => true]);
            $msg = 'Đã bật hiển thị cho '.count($colorIds).' màu sắc!';
        } elseif ($request->action === 'disable') {
            Color::whereIn('id', $colorIds)->update(['is_active' => false]);
            $msg = 'Đã ẩn '.count($colorIds).' màu sắc!';
        } elseif ($request->action === 'delete') {
            DB::transaction(function () use ($colorIds) {
                ProductVariant::whereIn('color_id', $colorIds)->update(['color_id' => null]);
                ProductImage::whereIn('color_id', $colorIds)->update(['color_id' => null]);
                DB::table('category_color')->whereIn('color_id', $colorIds)->delete();
                Color::whereIn('id', $colorIds)->delete();
            });
            $msg = 'Đã xóa thành công '.count($colorIds).' màu sắc! Toàn bộ sản phẩm và biến thể liên quan vẫn được bảo toàn nguyên vẹn.';
        }

        return redirect()->route('admin.colors.index')->with('success', $msg);
    }

    /**
     * Xóa màu sắc (Bảo toàn 100% sản phẩm và biến thể, chỉ gỡ gán màu).
     */
    public function destroy(Color $color, Request $request): RedirectResponse|JsonResponse
    {
        $colorName = $color->name;

        DB::transaction(function () use ($color) {
            // Cập nhật các biến thể mang màu này về null để không làm mất biến thể hay sản phẩm
            ProductVariant::where('color_id', $color->id)->update(['color_id' => null]);

            // Cập nhật ảnh sản phẩm liên quan về null
            ProductImage::where('color_id', $color->id)->update(['color_id' => null]);

            // Xóa quan hệ danh mục
            $color->categories()->detach();

            // Xóa màu
            $color->delete();
        });

        $msg = "Đã xóa màu sắc '{$colorName}' thành công! Các sản phẩm và biến thể vẫn được giữ nguyên an toàn.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->route('admin.colors.index')->with('success', $msg);
    }
}
