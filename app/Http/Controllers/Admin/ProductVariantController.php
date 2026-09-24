<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    /**
     * Danh sách biến thể của 1 sản phẩm.
     */
    public function index(Product $product): View
    {
        $product->load(['category.sizes', 'category.colors']);
        $variants = ProductVariant::with(['color', 'size'])
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(15);

        return view('admin.products.variants.index', compact('product', 'variants'));
    }

    /**
     * Form thêm biến thể mới cho sản phẩm.
     */
    public function create(Product $product): View
    {
        $product->load(['category.sizes', 'category.colors']);

        $colors = $this->getAllowedColorsForProduct($product);
        $sizes = $this->getAllowedSizesForProduct($product);

        return view('admin.products.variants.create', compact('product', 'colors', 'sizes'));
    }

    /**
     * Lưu biến thể mới vào CSDL.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'color_id' => ['nullable', 'exists:colors,id'],
            'size_id' => ['nullable', 'exists:sizes,id'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'color_id.exists' => 'Màu sắc được chọn không hợp lệ.',
            'size_id.exists' => 'Kích cỡ được chọn không hợp lệ.',
            'stock_quantity.required' => 'Vui lòng nhập số lượng tồn kho.',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'sku.unique' => 'Mã SKU biến thể này đã tồn tại.',
            'price.required' => 'Vui lòng nhập giá bán biến thể.',
            'sale_price.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá niêm yết.',
        ]);

        $colorId = $request->filled('color_id') ? (int) $request->color_id : null;
        $sizeId = $request->filled('size_id') ? (int) $request->size_id : null;

        // Validation kiểm tra Color phải thuộc Category của Sản phẩm (nếu có chọn Color)
        if ($colorId && ! $this->isColorAllowedForProduct($product, $colorId)) {
            return back()->withInput()->with('error', 'Màu sắc được chọn không thuộc cấu hình Danh mục của sản phẩm này!');
        }

        // Validation kiểm tra Size phải thuộc Category của Sản phẩm (nếu có chọn Size)
        if ($sizeId && ! $this->isSizeAllowedForProduct($product, $sizeId)) {
            return back()->withInput()->with('error', 'Kích cỡ được chọn không thuộc cấu hình Danh mục của sản phẩm này!');
        }

        // Kiểm tra cặp trùng lặp (Product + Color + Size)
        $exists = ProductVariant::where('product_id', $product->id)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Biến thể với kết hợp Màu sắc và Kích cỡ này đã tồn tại cho sản phẩm!');
        }

        $sku = $request->sku ? strtoupper(trim($request->sku)) : $product->sku.'-'.strtoupper(Str::random(5));

        ProductVariant::create([
            'product_id' => $product->id,
            'color_id' => $colorId,
            'size_id' => $sizeId,
            'stock_quantity' => $request->stock_quantity,
            'sku' => $sku,
            'price' => $request->price,
            'sale_price' => $request->sale_price ?: null,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        self::syncProductSalePrice($product);

        return redirect()->route('admin.products.variants.index', $product->id)->with('success', 'Thêm biến thể sản phẩm mới thành công!');
    }

    /**
     * Form chỉnh sửa biến thể.
     */
    public function edit(Product $product, ProductVariant $variant): View
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $product->load(['category.sizes', 'category.colors']);

        $colors = $this->getAllowedColorsForProduct($product);
        $sizes = $this->getAllowedSizesForProduct($product);

        return view('admin.products.variants.edit', compact('product', 'variant', 'colors', 'sizes'));
    }

    /**
     * Cập nhật thông tin biến thể.
     */
    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $request->validate([
            'color_id' => ['nullable', 'exists:colors,id'],
            'size_id' => ['nullable', 'exists:sizes,id'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku,'.$variant->id],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'color_id.exists' => 'Màu sắc được chọn không hợp lệ.',
            'size_id.exists' => 'Kích cỡ được chọn không hợp lệ.',
            'stock_quantity.required' => 'Vui lòng nhập số lượng kho.',
            'sku.unique' => 'Mã SKU biến thể đã tồn tại.',
            'price.required' => 'Vui lòng nhập giá bán biến thể.',
            'sale_price.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá niêm yết.',
        ]);

        $colorId = $request->filled('color_id') ? (int) $request->color_id : null;
        $sizeId = $request->filled('size_id') ? (int) $request->size_id : null;

        // Validation kiểm tra Color phải thuộc Category của Sản phẩm
        if ($colorId && ! $this->isColorAllowedForProduct($product, $colorId)) {
            return back()->withInput()->with('error', 'Màu sắc được chọn không thuộc cấu hình Danh mục của sản phẩm này!');
        }

        // Validation kiểm tra Size phải thuộc Category của Sản phẩm
        if ($sizeId && ! $this->isSizeAllowedForProduct($product, $sizeId)) {
            return back()->withInput()->with('error', 'Kích cỡ được chọn không thuộc cấu hình Danh mục của sản phẩm này!');
        }

        // Kiểm tra cặp trùng lặp loại trừ chính biến thể đang sửa
        $exists = ProductVariant::where('product_id', $product->id)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->where('id', '!=', $variant->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Biến thể với kết hợp Màu sắc và Kích cỡ này đã tồn tại!');
        }

        $sku = $request->sku ? strtoupper(trim($request->sku)) : $variant->sku;

        $updateData = [
            'color_id' => $colorId,
            'size_id' => $sizeId,
            'stock_quantity' => $request->stock_quantity,
            'sku' => $sku,
            'price' => $request->price,
            'sale_price' => $request->sale_price ?: null,
        ];

        if ($request->has('is_active')) {
            $updateData['is_active'] = (bool) $request->is_active;
        }

        $variant->update($updateData);

        self::syncProductSalePrice($product);

        return redirect()->route('admin.products.variants.index', $product->id)->with('success', 'Cập nhật biến thể sản phẩm thành công!');
    }

    /**
     * Đổi nhanh trạng thái Ẩn/Hiện của biến thể sản phẩm.
     */
    public function toggleStatus(Request $request, Product $product, ProductVariant $variant): RedirectResponse|JsonResponse
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $variant->update([
            'is_active' => ! $variant->is_active,
        ]);

        self::syncProductSalePrice($product);

        $statusLabel = $variant->is_active ? 'hiển thị' : 'ẩn';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $variant->is_active,
                'message' => "Đã chuyển trạng thái biến thể '{$variant->sku}' sang {$statusLabel}!",
            ]);
        }

        return back()->with('success', "Đã đổi trạng thái biến thể '{$variant->sku}' sang {$statusLabel}!");
    }

    /**
     * Đồng bộ giá sale_price của sản phẩm mẹ từ các biến thể.
     */
    public static function syncProductSalePrice(Product $product): void
    {
        $minVariantSale = ProductVariant::where('product_id', $product->id)
            ->where('is_active', true)
            ->whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->whereColumn('sale_price', '<', 'price')
            ->min('sale_price');

        if ($minVariantSale) {
            $product->update(['sale_price' => $minVariantSale]);
        } elseif (ProductVariant::where('product_id', $product->id)->where('is_active', true)->exists()) {
            $product->update(['sale_price' => null]);
        }
    }

    /**
     * Xóa biến thể sản phẩm.
     */
    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $variant->delete();

        self::syncProductSalePrice($product);

        return redirect()->route('admin.products.variants.index', $product->id)->with('success', 'Xóa biến thể sản phẩm thành công!');
    }

    /**
     * Helper lấy danh sách Size cho sản phẩm quản lý biến thể.
     */
    private function getAllowedSizesForProduct(Product $product)
    {
        return Size::orderBy('name', 'asc')->get();
    }

    /**
     * Helper kiểm tra size_id có hợp lệ không (tồn tại trong hệ thống).
     */
    private function isSizeAllowedForProduct(Product $product, ?int $sizeId): bool
    {
        if (is_null($sizeId)) {
            return true;
        }

        return Size::where('id', $sizeId)->exists();
    }

    /**
     * Helper lấy danh sách Màu Sắc cho sản phẩm quản lý biến thể.
     */
    private function getAllowedColorsForProduct(Product $product)
    {
        return Color::orderBy('name', 'asc')->get();
    }

    /**
     * Helper kiểm tra color_id có hợp lệ không (tồn tại trong hệ thống).
     */
    private function isColorAllowedForProduct(Product $product, ?int $colorId): bool
    {
        if (is_null($colorId)) {
            return true;
        }

        return Color::where('id', $colorId)->exists();
    }

    /**
     * Sinh biến thể tự động dạng Cartesian Product (Màu Sắc x Kích Cỡ).
     */
    public function generateBatch(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'colors' => ['nullable', 'array'],
            'colors.*' => ['exists:colors,id'],
            'sizes' => ['nullable', 'array'],
            'sizes.*' => ['exists:sizes,id'],
            'default_price' => ['nullable', 'numeric', 'min:0'],
            'default_stock' => ['nullable', 'integer', 'min:0'],
        ]);

        $colorIds = $request->input('colors', []);
        $sizeIds = $request->input('sizes', []);

        if (empty($colorIds) && empty($sizeIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất 1 Màu sắc hoặc 1 Kích cỡ để sinh biến thể tự động!');
        }

        $colorsCount = max(1, count($colorIds));
        $sizesCount = max(1, count($sizeIds));
        if (($colorsCount * $sizesCount) > 50) {
            return back()->with('error', 'Số lượng tổ hợp biến thể sinh ra trong một lần không được vượt quá 50 biến thể!');
        }

        $colors = ! empty($colorIds) ? Color::whereIn('id', $colorIds)->get() : collect([null]);
        $sizes = ! empty($sizeIds) ? Size::whereIn('id', $sizeIds)->get() : collect([null]);

        $defaultPrice = $request->filled('default_price') ? (float) $request->default_price : $product->price;
        $defaultSalePrice = $product->sale_price;
        $defaultStock = $request->filled('default_stock') ? (int) $request->default_stock : 10;

        $createdCount = 0;

        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                $colorId = $color ? $color->id : null;
                $sizeId = $size ? $size->id : null;

                // Kiểm tra xem tổ hợp này đã tồn tại chưa
                $exists = ProductVariant::where('product_id', $product->id)
                    ->where('color_id', $colorId)
                    ->where('size_id', $sizeId)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Tạo SKU theo cú pháp: PRODUCT_SKU-COLOR-SIZE
                $colorSlug = $color ? strtoupper(Str::slug($color->name)) : 'NOCOLOR';
                $sizeSlug = $size ? strtoupper(Str::slug($size->name)) : 'NOSIZE';
                $sku = $product->sku.'-'.$colorSlug.'-'.$sizeSlug;

                $originalSku = $sku;
                $count = 1;
                while (ProductVariant::where('sku', $sku)->exists()) {
                    $sku = $originalSku.'-'.$count;
                    $count++;
                }

                ProductVariant::create([
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                    'size_id' => $sizeId,
                    'sku' => $sku,
                    'price' => $defaultPrice,
                    'sale_price' => $defaultSalePrice,
                    'stock_quantity' => $defaultStock,
                    'is_active' => true,
                ]);

                $createdCount++;
            }
        }

        if ($createdCount === 0) {
            return back()->with('info', 'Tất cả các tổ hợp Màu sắc & Kích cỡ được chọn đã tồn tại trước đó!');
        }

        return redirect()->route('admin.products.variants.index', $product->id)
            ->with('success', "Đã tự động khởi tạo thành công {$createdCount} biến thể sản phẩm mới!");
    }

    /**
     * Thao tác hàng loạt trên các biến thể sản phẩm.
     */
    public function bulkAction(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'action' => ['required', 'string', 'in:enable,disable,update_stock,update_price,delete'],
            'variant_ids' => ['required', 'array'],
            'variant_ids.*' => ['exists:product_variants,id'],
            'bulk_stock' => ['nullable', 'integer', 'min:0'],
            'bulk_price' => ['nullable', 'numeric', 'min:0'],
            'bulk_sale_price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'variant_ids.required' => 'Vui lòng chọn ít nhất 1 biến thể để thực hiện thao tác!',
        ]);

        $variantIds = $request->input('variant_ids', []);
        $variantsQuery = ProductVariant::where('product_id', $product->id)->whereIn('id', $variantIds);
        $count = $variantsQuery->count();

        if ($count === 0) {
            return back()->with('error', 'Không tìm thấy biến thể hợp lệ thuộc sản phẩm này!');
        }

        switch ($request->action) {
            case 'enable':
                $variantsQuery->update(['is_active' => true]);
                $msg = "Đã bật hiển thị cho {$count} biến thể!";
                break;

            case 'disable':
                $variantsQuery->update(['is_active' => false]);
                $msg = "Đã ẩn {$count} biến thể!";
                break;

            case 'update_stock':
                if (! $request->filled('bulk_stock')) {
                    return back()->with('error', 'Vui lòng nhập số lượng tồn kho mới!');
                }
                $variantsQuery->update(['stock_quantity' => (int) $request->bulk_stock]);
                $msg = "Đã cập nhật tồn kho = {$request->bulk_stock} cho {$count} biến thể!";
                break;

            case 'update_price':
                if (! $request->filled('bulk_price')) {
                    return back()->with('error', 'Vui lòng nhập giá niêm yết mới!');
                }
                $updateData = ['price' => (float) $request->bulk_price];
                if ($request->filled('bulk_sale_price')) {
                    $updateData['sale_price'] = (float) $request->bulk_sale_price;
                }
                $variantsQuery->update($updateData);
                $msg = "Đã cập nhật giá bán mới cho {$count} biến thể!";
                break;

            case 'delete':
                $variantsQuery->delete();
                $msg = "Đã xóa {$count} biến thể được chọn!";
                break;

            default:
                return back()->with('error', 'Thao tác không hợp lệ!');
        }

        self::syncProductSalePrice($product);

        return redirect()->route('admin.products.variants.index', $product->id)->with('success', $msg);
    }
}
