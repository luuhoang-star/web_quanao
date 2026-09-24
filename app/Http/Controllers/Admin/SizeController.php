<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSizeRequest;
use App\Http\Requests\Admin\UpdateSizeRequest;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SizeController extends Controller
{
    /**
     * Danh sách kích cỡ sản phẩm (Search, Advanced Filters, Size Type, Stats, Sorting & Pagination).
     */
    public function index(Request $request): View|JsonResponse
    {
        // 1. Thống kê số liệu Realtime cho KPI Cards
        $stats = [
            'total' => Size::count(),
            'active' => Size::where('is_active', true)->count(),
            'used' => Size::has('variants')->count(),
            'unused' => Size::doesntHave('variants')->count(),
        ];

        // 2. Query Builder
        $query = Size::with(['categories'])->withCount(['variants', 'categories']);

        $activeFilterCount = 0;

        // Tìm kiếm theo Tên kích cỡ hoặc Mã code
        if ($request->filled('search')) {
            $activeFilterCount++;
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Lọc theo Loại Kích Cỡ (size_type: LETTER, NUMBER, FREE_SIZE, OTHER)
        if ($request->filled('size_type')) {
            $activeFilterCount++;
            $sizeType = $request->size_type;
            if ($sizeType === 'LETTER') {
                $query->whereIn('size_type', ['LETTER', 'TOPS']);
            } elseif ($sizeType === 'NUMBER') {
                $query->whereIn('size_type', ['NUMBER', 'BOTTOMS']);
            } elseif ($sizeType === 'FREE_SIZE') {
                $query->whereIn('size_type', ['FREE_SIZE', 'FREE']);
            } else {
                $query->where('size_type', $sizeType);
            }
        }

        // Lọc theo Danh mục sản phẩm áp dụng
        if ($request->filled('category_id')) {
            $activeFilterCount++;
            $query->whereHas('categories', fn ($catQuery) => $catQuery->where('categories.id', $request->category_id));
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

        // 3. Sắp xếp (Sorting)
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'name') {
            $query->orderBy('name', $sortDir);
        } elseif ($sortBy === 'code') {
            $query->orderBy('code', $sortDir);
        } elseif ($sortBy === 'size_type') {
            $query->orderBy('size_type', $sortDir);
        } elseif ($sortBy === 'id') {
            $query->orderBy('id', $sortDir);
        } elseif ($sortBy === 'variants') {
            $query->orderBy('variants_count', $sortDir);
        } elseif ($sortBy === 'created_at') {
            $query->orderBy('created_at', $sortDir);
        } else {
            $query->orderBy('sort_order', $sortDir)->orderBy('name', 'asc');
        }

        // 4. Phân trang
        $perPage = (int) $request->get('per_page', 15);
        if (! in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $sizes = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name', 'asc')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $sizes,
            ]);
        }

        return view('admin.sizes.index', compact('sizes', 'categories', 'stats', 'activeFilterCount', 'sortBy', 'sortDir', 'perPage'));
    }

    /**
     * Form thêm kích cỡ mới.
     */
    public function create(): View
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.sizes.create', compact('categories'));
    }

    /**
     * Lưu kích cỡ vào CSDL.
     */
    public function store(StoreSizeRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $size = Size::create([
            'name' => $validated['name'],
            'code' => ! empty($validated['code']) ? $validated['code'] : null,
            'size_type' => $validated['size_type'] ?? 'OTHER',
            'specifications' => $validated['specifications'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (! empty($validated['categories'])) {
            $size->categories()->sync($validated['categories']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm kích cỡ mới thành công!',
                'data' => $size,
            ]);
        }

        return redirect()->route('admin.sizes.index')->with('success', 'Thêm kích cỡ mới thành công!');
    }

    /**
     * Form chỉnh sửa kích cỡ.
     */
    public function edit(Size $size, Request $request): View|JsonResponse
    {
        $size->load('categories');
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name', 'asc')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $size,
            ]);
        }

        return view('admin.sizes.edit', compact('size', 'categories'));
    }

    /**
     * Cập nhật kích cỡ.
     */
    public function update(UpdateSizeRequest $request, Size $size): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $size->update([
            'name' => $validated['name'],
            'code' => ! empty($validated['code']) ? $validated['code'] : null,
            'size_type' => $validated['size_type'] ?? 'OTHER',
            'specifications' => $validated['specifications'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $size->categories()->sync($validated['categories'] ?? []);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật kích cỡ thành công!',
                'data' => $size,
            ]);
        }

        return redirect()->route('admin.sizes.index')->with('success', 'Cập nhật kích cỡ thành công!');
    }

    /**
     * Lấy danh sách sản phẩm & biến thể đang sử dụng Size này (AJAX endpoint).
     */
    public function getVariants(Size $size): JsonResponse
    {
        $variants = $size->variants()
            ->with([
                'product' => fn ($q) => $q->select('id', 'name', 'sku', 'price'),
                'color' => fn ($q) => $q->select('id', 'name', 'hex_code'),
            ])
            ->get();

        return response()->json([
            'success' => true,
            'size_name' => $size->name,
            'data' => $variants,
        ]);
    }

    /**
     * Đổi nhanh trạng thái Ẩn/Hiện kích cỡ.
     */
    public function toggleStatus(Size $size, Request $request): RedirectResponse|JsonResponse
    {
        $size->update([
            'is_active' => ! $size->is_active,
        ]);

        $statusText = $size->is_active ? 'hiển thị' : 'tạm ẩn';
        $message = "Đã đổi trạng thái kích cỡ '{$size->name}' sang {$statusText}!";

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => (bool) $size->is_active,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Thao tác hàng loạt trên nhiều Kích cỡ.
     */
    public function bulkAction(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:enable,disable,delete'],
            'size_ids' => ['required', 'array'],
            'size_ids.*' => ['exists:sizes,id'],
        ], [
            'size_ids.required' => 'Vui lòng chọn ít nhất 1 kích cỡ để thực hiện thao tác!',
        ]);

        $sizeIds = $request->input('size_ids', []);
        $sizes = Size::whereIn('id', $sizeIds)->get();

        if ($request->action === 'enable') {
            Size::whereIn('id', $sizeIds)->update(['is_active' => true]);
            $msg = 'Đã bật hiển thị cho '.count($sizeIds).' kích cỡ!';
        } elseif ($request->action === 'disable') {
            Size::whereIn('id', $sizeIds)->update(['is_active' => false]);
            $msg = 'Đã ẩn '.count($sizeIds).' kích cỡ!';
        } elseif ($request->action === 'delete') {
            DB::transaction(function () use ($sizeIds) {
                ProductVariant::whereIn('size_id', $sizeIds)->update(['size_id' => null]);
                DB::table('category_size')->whereIn('size_id', $sizeIds)->delete();
                Size::whereIn('id', $sizeIds)->delete();
            });
            $msg = 'Đã xóa thành công '.count($sizeIds).' kích cỡ! Toàn bộ sản phẩm và biến thể liên quan vẫn được bảo toàn nguyên vẹn.';
        }

        return redirect()->route('admin.sizes.index')->with('success', $msg);
    }

    /**
     * Xóa kích cỡ (Bảo toàn 100% sản phẩm và biến thể, chỉ gỡ gán kích cỡ).
     */
    public function destroy(Size $size, Request $request): RedirectResponse|JsonResponse
    {
        $sizeName = $size->name;

        DB::transaction(function () use ($size) {
            ProductVariant::where('size_id', $size->id)->update(['size_id' => null]);
            $size->categories()->detach();
            $size->delete();
        });

        $successMessage = "Đã xóa kích cỡ '{$sizeName}' thành công! Các sản phẩm và biến thể vẫn được bảo toàn nguyên vẹn.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
        }

        return redirect()->route('admin.sizes.index')->with('success', $successMessage);
    }
}
