<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Tính toán số liệu thống kê tổng quan tồn kho (Dashboard).
     */
    public function getInventoryStats(): array
    {
        $totalSkus = ProductVariant::count();
        $totalStock = (int) ProductVariant::sum('stock_quantity');

        $lowStockCount = ProductVariant::where('stock_quantity', '>', 0)
            ->whereRaw('stock_quantity <= low_stock_threshold')
            ->count();

        $outOfStockCount = ProductVariant::where('stock_quantity', '<=', 0)->count();

        $estimatedStockValue = (float) ProductVariant::selectRaw('SUM(stock_quantity * (CASE WHEN cost_price > 0 THEN cost_price ELSE price END)) as total_val')
            ->value('total_val');

        return [
            'total_skus' => $totalSkus,
            'total_stock' => $totalStock,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'estimated_stock_value' => $estimatedStockValue,
        ];
    }

    /**
     * Lấy Map số lượng hàng đặt trước (Reserved Stock) từ đơn hàng đang chờ giao.
     */
    public function getReservedStockMap(): array
    {
        return OrderItem::whereHas('order', function ($q) {
            $q->whereIn('status', ['pending', 'processing', 'confirmed']);
        })
            ->select('product_variant_id', DB::raw('SUM(quantity) as total_reserved'))
            ->groupBy('product_variant_id')
            ->pluck('total_reserved', 'product_variant_id')
            ->toArray();
    }

    /**
     * Thực hiện Nhập kho (Import Stock).
     */
    public function importStock(array $data): ProductVariant
    {
        return DB::transaction(function () use ($data) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($data['product_variant_id']);
            $beforeQty = $variant->stock_quantity;
            $qty = (int) $data['quantity'];
            $costPrice = isset($data['cost_price']) && $data['cost_price'] !== '' ? (float) $data['cost_price'] : $variant->cost_price;

            $variant->stock_quantity += $qty;
            if ($costPrice > 0) {
                $variant->cost_price = $costPrice;
            }
            $variant->save();

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'user_id' => Auth::id(),
                'type' => 'import',
                'quantity_change' => $qty,
                'before_quantity' => $beforeQty,
                'after_quantity' => $variant->stock_quantity,
                'cost_price' => $costPrice,
                'supplier' => $data['supplier'] ?? null,
                'reference_code' => $data['reference_code'] ?? null,
                'note' => $data['note'] ?? 'Nhập hàng mới vào kho',
            ]);

            return $variant;
        });
    }

    /**
     * Thực hiện Xuất kho thủ công (Export Stock).
     */
    public function exportStock(array $data): ProductVariant
    {
        return DB::transaction(function () use ($data) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($data['product_variant_id']);
            $beforeQty = $variant->stock_quantity;
            $qty = (int) $data['quantity'];

            if ($qty > $variant->stock_quantity) {
                throw new \InvalidArgumentException("Số lượng xuất kho ({$qty}) vượt quá số lượng tồn kho hiện tại ({$variant->stock_quantity}) của sản phẩm!");
            }

            $variant->stock_quantity = $variant->stock_quantity - $qty;
            $variant->save();

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'user_id' => Auth::id(),
                'type' => 'export',
                'quantity_change' => -$qty,
                'before_quantity' => $beforeQty,
                'after_quantity' => $variant->stock_quantity,
                'reference_code' => $data['reference_code'] ?? null,
                'note' => $data['note'],
            ]);

            return $variant;
        });
    }

    /**
     * Điều chỉnh tồn kho kiểm kê thực tế (Adjust Stock).
     */
    public function adjustStock(array $data): ProductVariant
    {
        return DB::transaction(function () use ($data) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($data['product_variant_id']);
            $beforeQty = $variant->stock_quantity;
            $actualQty = (int) $data['actual_quantity'];
            $change = $actualQty - $beforeQty;

            $variant->stock_quantity = $actualQty;
            $variant->save();

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity_change' => $change,
                'before_quantity' => $beforeQty,
                'after_quantity' => $actualQty,
                'note' => $data['note'],
            ]);

            return $variant;
        });
    }
}
