<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Tính toán thống kê sản phẩm trong trang Admin (Tối ưu SQL Count thay vì nạp toàn bộ Models vào RAM).
     */
    public function getAdminProductStats(): array
    {
        return [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'low_stock' => Product::whereIn('id', function ($sub) {
                $sub->select('product_id')
                    ->from('product_variants')
                    ->groupBy('product_id')
                    ->havingRaw('SUM(stock_quantity) > 0 AND SUM(stock_quantity) <= 10');
            })->count(),
            'out_of_stock' => Product::where(function ($q) {
                $q->whereDoesntHave('variants')
                    ->orWhereIn('id', function ($sub) {
                        $sub->select('product_id')
                            ->from('product_variants')
                            ->groupBy('product_id')
                            ->havingRaw('SUM(stock_quantity) = 0');
                    });
            })->count(),
        ];
    }

    /**
     * Xử lý lưu hình ảnh Thumbnail & Gallery cho sản phẩm.
     */
    public function storeProductImage(Product $product, $file, bool $isThumbnail = false, int $sortOrder = 0): ProductImage
    {
        $imagePath = $file->store('products', 'public');

        if ($isThumbnail) {
            ProductImage::where('product_id', $product->id)->update(['is_thumbnail' => false]);
        }

        return ProductImage::create([
            'product_id' => $product->id,
            'image_url' => $imagePath,
            'is_thumbnail' => $isThumbnail,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Xóa ảnh sản phẩm khỏi Ổ đĩa & CSDL.
     */
    public function deleteProductImage(ProductImage $image): bool
    {
        if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }

        return (bool) $image->delete();
    }
}
