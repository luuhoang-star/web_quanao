<?php

namespace App\Http\Controllers;

use App\Models\Outfit;
use App\Models\Product;
use Illuminate\View\View;

class OutfitController extends Controller
{
    /**
     * Hiển thị chi tiết một Outfit theo slug (/outfits/{slug}) từ Database.
     */
    public function show(string $slug): View
    {
        $outfitModel = Outfit::with(['products.category', 'products.images', 'products.variants'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($outfitModel) {
            $outfitInfo = [
                'id' => $outfitModel->id,
                'slug' => $outfitModel->slug,
                'name' => $outfitModel->name,
                'badge' => $outfitModel->badge,
                'tagline' => $outfitModel->tagline,
                'gradient' => $outfitModel->gradient ?: 'linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%)',
                'hero_image' => $outfitModel->hero_image_url,
                'description' => $outfitModel->description,
                'items_included' => $outfitModel->items_included ?: ['Trang phục chính', 'Phụ kiện đi kèm'],
            ];

            $products = $outfitModel->products;
        } else {
            // Fallback nếu không tìm thấy trong DB
            $outfitInfo = [
                'slug' => $slug,
                'name' => 'Outfit '.ucfirst($slug),
                'badge' => strtoupper($slug).' OUTFIT',
                'tagline' => 'Phong cách phối đồ ấn tượng dành cho bạn',
                'gradient' => 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)',
                'hero_image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Bộ phối trang phục được đề xuất đặc biệt giúp bạn hoàn thiện phong cách thời trang nhanh chóng.',
                'items_included' => ['Trang phục chính', 'Phụ kiện đi kèm'],
            ];
            $products = collect();
        }

        // Nếu sản phẩm thuộc outfit chưa đủ 3, lấy thêm sản phẩm gợi ý cho đủ 3 món
        if ($products->count() < 3) {
            $existingIds = $products->pluck('id')->toArray();
            $moreProducts = Product::with(['category', 'images', 'variants.color', 'variants.size'])
                ->where('is_active', true)
                ->whereNotIn('id', $existingIds)
                ->take(3 - $products->count())
                ->get();

            $products = $products->merge($moreProducts);
        }

        // Tính tổng giá set đồ
        $totalOriginalPrice = $products->sum('price');
        $totalSalePrice = $products->sum(function ($p) {
            return $p->effective_sale_price ?: $p->price;
        });
        $savingsAmount = $totalOriginalPrice - $totalSalePrice;

        // Outfit liên quan lấy từ DB
        $relatedOutfits = Outfit::where('slug', '!=', $slug)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->take(3)
            ->get();

        return view('outfits.show', compact(
            'outfitInfo',
            'products',
            'totalOriginalPrice',
            'totalSalePrice',
            'savingsAmount',
            'relatedOutfits'
        ));
    }
}
