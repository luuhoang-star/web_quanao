<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Dashboard & Danh sách tồn kho theo biến thể sản phẩm.
     */
    public function index(Request $request): View|JsonResponse
    {
        $stats = $this->inventoryService->getInventoryStats();
        $reservedStockMap = $this->inventoryService->getReservedStockMap();

        $query = ProductVariant::with(['product.category', 'product.images', 'color', 'size']);
        $activeFilterCount = 0;

        // Tìm kiếm theo Mã SKU hoặc Tên sản phẩm
        if ($request->filled('search')) {
            $activeFilterCount++;
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
        }

        // Lọc theo Danh mục sản phẩm
        if ($request->filled('category_id')) {
            $activeFilterCount++;
            $catId = $request->category_id;
            $query->whereHas('product', fn ($pq) => $pq->where('category_id', $catId));
        }

        // Lọc theo Trạng thái tồn kho
        if ($request->filled('stock_status')) {
            $activeFilterCount++;
            $status = $request->stock_status;
            if ($status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($status === 'low_stock') {
                $query->where('stock_quantity', '>', 0)
                    ->whereRaw('stock_quantity <= low_stock_threshold');
            } elseif ($status === 'in_stock') {
                $query->whereRaw('stock_quantity > low_stock_threshold');
            }
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'stock_quantity');
        $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'sku') {
            $query->orderBy('sku', $sortDir);
        } elseif ($sortBy === 'cost_price') {
            $query->orderBy('cost_price', $sortDir);
        } elseif ($sortBy === 'id') {
            $query->orderBy('id', $sortDir);
        } else {
            $query->orderBy('stock_quantity', $sortDir)->orderBy('id', 'asc');
        }

        $perPage = (int) $request->get('per_page', 15);
        if (! in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $variants = $query->paginate($perPage)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $variants,
            ]);
        }

        $categories = Category::orderBy('name', 'asc')->get();
        $allVariants = ProductVariant::with(['product', 'color', 'size'])->orderBy('sku', 'asc')->get();

        return view('admin.inventory.index', compact(
            'stats',
            'variants',
            'categories',
            'allVariants',
            'reservedStockMap',
            'activeFilterCount',
            'sortBy',
            'sortDir',
            'perPage'
        ));
    }

    /**
     * Nhập kho (Tăng số lượng tồn, cập nhật giá vốn và ghi log StockMovement).
     */
    public function import(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'reference_code' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string'],
        ], [
            'product_variant_id.required' => 'Vui lòng chọn biến thể sản phẩm cần nhập kho.',
            'quantity.required' => 'Vui lòng nhập số lượng hàng cần nhập.',
            'quantity.min' => 'Số lượng nhập phải từ 1 trở lên.',
        ]);

        $this->inventoryService->importStock($validated);
        $msg = 'Nhập kho thành công!';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.inventory.index')->with('success', $msg);
    }

    /**
     * Xuất kho thủ công.
     */
    public function export(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_code' => ['nullable', 'string', 'max:100'],
            'note' => ['required', 'string'],
        ], [
            'product_variant_id.required' => 'Vui lòng chọn biến thể sản phẩm cần xuất kho.',
            'quantity.required' => 'Vui lòng nhập số lượng cần xuất.',
            'note.required' => 'Vui lòng nhập lý do xuất kho.',
        ]);

        $this->inventoryService->exportStock($validated);
        $msg = 'Xuất kho thành công!';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.inventory.index')->with('success', $msg);
    }

    /**
     * Điều chỉnh tồn kho thực tế.
     */
    public function adjust(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'actual_quantity' => ['required', 'integer', 'min:0'],
            'note' => ['required', 'string'],
        ], [
            'product_variant_id.required' => 'Vui lòng chọn biến thể sản phẩm.',
            'actual_quantity.required' => 'Vui lòng nhập số lượng kiểm kê thực tế.',
            'note.required' => 'Vui lòng nhập lý do điều chỉnh kiểm kê.',
        ]);

        $this->inventoryService->adjustStock($validated);
        $msg = 'Điều chỉnh tồn kho thành công!';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.inventory.index')->with('success', $msg);
    }

    /**
     * Cập nhật nhanh ngưỡng cảnh báo tồn kho tối thiểu.
     */
    public function updateThreshold(Request $request, ProductVariant $variant): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ]);

        $variant->update(['low_stock_threshold' => $validated['low_stock_threshold']]);
        $msg = "Đã cập nhật ngưỡng cảnh báo cho SKU {$variant->sku} thành {$variant->low_stock_threshold}!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Danh sách Lịch sử & Truy vết biến động tồn kho (Log).
     */
    public function movements(Request $request): View|JsonResponse
    {
        $query = StockMovement::with(['productVariant.product', 'productVariant.color', 'productVariant.size', 'user'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhereHas('productVariant', fn ($pv) => $pv->where('sku', 'like', "%{$search}%")->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%")));
            });
        }

        $movements = $query->paginate(20)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $movements]);
        }

        return view('admin.inventory.movements', compact('movements'));
    }

    /**
     * Báo cáo & Xuất file Excel (CSV) danh sách tồn kho.
     */
    public function exportExcel(): StreamedResponse
    {
        $fileName = 'bao-cao-ton-kho-'.date('Y-m-d_H-i-s').'.csv';
        $variants = ProductVariant::with(['product.category', 'color', 'size'])->get();
        $reservedStockMap = $this->inventoryService->getReservedStockMap();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($variants, $reservedStockMap) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'STT',
                'Mã SKU',
                'Tên Sản Phẩm',
                'Màu Sắc',
                'Kích Cỡ',
                'Tồn Kho Hiện Tại',
                'Đã Đặt Trước',
                'Có Thể Bán',
                'Giá Vốn (VND)',
                'Giá Bán (VND)',
                'Giá Trị Tồn Kho (VND)',
                'Ngưỡng Cảnh Báo',
                'Trạng Thái Tồn Kho',
            ]);

            foreach ($variants as $idx => $v) {
                $reserved = $reservedStockMap[$v->id] ?? 0;
                $available = max(0, $v->stock_quantity - $reserved);
                $unitCost = $v->cost_price > 0 ? $v->cost_price : $v->price;
                $totalVal = $v->stock_quantity * $unitCost;

                fputcsv($file, [
                    $idx + 1,
                    $v->sku ?? 'N/A',
                    $v->product ? $v->product->name : 'N/A',
                    $v->color ? $v->color->name : 'N/A',
                    $v->size ? $v->size->name : 'N/A',
                    $v->stock_quantity,
                    $reserved,
                    $available,
                    number_format($v->cost_price, 0, ',', '.'),
                    number_format($v->price, 0, ',', '.'),
                    number_format($totalVal, 0, ',', '.'),
                    $v->low_stock_threshold ?? 5,
                    $v->stock_status_label,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
