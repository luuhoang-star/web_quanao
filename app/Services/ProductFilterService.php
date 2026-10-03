<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductFilterService
{
    /**
     * Xử lý lọc danh sách sản phẩm theo các tiêu chí (Search, Category, Collection, Outfit, Price, Color, Size, Sort).
     */
    public function getFilteredProducts(Request $request): array
    {
        $query = Product::with(['category', 'collection', 'images', 'activeFlashSale'])
            ->where('is_active', true);

        $baseCountQuery = Product::where('is_active', true);

        // 1. Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
            $baseCountQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Collection
        if ($request->filled('collection')) {
            $collectionSlug = $request->collection;
            $currentCollection = Collection::where('slug', $collectionSlug)->first();
            if ($currentCollection) {
                $query->where('collection_id', $currentCollection->id);
                $baseCountQuery->where('collection_id', $currentCollection->id);
            }
        }

        // 3. Discount / Flash Sale
        if ($request->filled('discount') && ($request->discount == '1' || $request->discount == 'true')) {
            $query->whereNotNull('sale_price')
                ->where('sale_price', '>', 0)
                ->whereColumn('sale_price', '<', 'price');
            $baseCountQuery->whereNotNull('sale_price')
                ->where('sale_price', '>', 0)
                ->whereColumn('sale_price', '<', 'price');
        }

        // 4. Outfit / Tag
        if ($request->filled('outfit') || $request->filled('tag')) {
            $outfitTag = trim($request->get('outfit', $request->get('tag')));
            $query->where(function ($q) use ($outfitTag) {
                $q->where('outfit', $outfitTag)
                    ->orWhere('name', 'like', "%{$outfitTag}%");
            });
            $baseCountQuery->where(function ($q) use ($outfitTag) {
                $q->where('outfit', $outfitTag)
                    ->orWhere('name', 'like', "%{$outfitTag}%");
            });
        }

        // 5. Category
        if ($request->filled('category') || $request->filled('category_id')) {
            $category = null;
            if ($request->filled('category')) {
                $category = Category::where('slug', $request->category)->first();
            } elseif ($request->filled('category_id')) {
                $category = Category::find($request->category_id);
            }

            if ($category) {
                $childIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
                $allCategoryIds = array_merge([$category->id], $childIds);

                $query->whereIn('category_id', $allCategoryIds);
                $baseCountQuery->whereIn('category_id', $allCategoryIds);
            }
        }

        // 6. Price Range & Custom Slider
        $effectivePriceSql = 'CASE WHEN sale_price > 0 AND sale_price < price THEN sale_price ELSE price END';
        $priceRange = $request->get('price_range');
        $minPrice = $request->filled('min_price') ? max(0, (float) $request->min_price) : null;
        $maxPrice = $request->filled('max_price') ? max(0, (float) $request->max_price) : null;

        if ($minPrice !== null || $maxPrice !== null) {
            if ($minPrice !== null && $minPrice > 0) {
                $query->whereRaw("{$effectivePriceSql} >= ?", [$minPrice]);
            }
            if ($maxPrice !== null && $maxPrice > 0) {
                $query->whereRaw("{$effectivePriceSql} <= ?", [$maxPrice]);
            }
            $priceRange = null;
        } elseif (! empty($priceRange)) {
            switch ($priceRange) {
                case 'under_300':
                    $query->whereRaw("{$effectivePriceSql} < 300000");
                    $minPrice = 0;
                    $maxPrice = 300000;
                    break;
                case '300_500':
                    $query->whereRaw("{$effectivePriceSql} BETWEEN 300000 AND 500000");
                    $minPrice = 300000;
                    $maxPrice = 500000;
                    break;
                case '500_1000':
                    $query->whereRaw("{$effectivePriceSql} BETWEEN 500000 AND 1000000");
                    $minPrice = 500000;
                    $maxPrice = 1000000;
                    break;
                case 'over_1000':
                    $query->whereRaw("{$effectivePriceSql} > 1000000");
                    $minPrice = 1000000;
                    $maxPrice = 2000000;
                    break;
            }
        }

        // 7. Colors (Multi-Select)
        $selectedColors = $request->input('colors', $request->input('color_id', []));
        if (! is_array($selectedColors)) {
            $selectedColors = explode(',', (string) $selectedColors);
        }
        $selectedColors = array_filter(array_map('intval', $selectedColors));

        if (! empty($selectedColors)) {
            $query->whereHas('variants', fn ($q) => $q->whereIn('color_id', $selectedColors));
        }

        // 8. Sizes (Multi-Select)
        $selectedSizes = $request->input('sizes', $request->input('size_id', []));
        if (! is_array($selectedSizes)) {
            $selectedSizes = explode(',', (string) $selectedSizes);
        }
        $selectedSizes = array_filter(array_map('intval', $selectedSizes));

        if (! empty($selectedSizes)) {
            $query->whereHas('variants', fn ($q) => $q->whereIn('size_id', $selectedSizes));
        }

        // --- TỐI ƯU HÓA: Đếm số lượng màu và size động theo tập sản phẩm của ngữ cảnh hiện tại ---
        $baseProductIds = (clone $baseCountQuery)->pluck('id')->toArray();

        if (! empty($baseProductIds)) {
            $colorCounts = DB::table('product_variants')
                ->whereIn('product_id', $baseProductIds)
                ->where('stock_quantity', '>', 0)
                ->select('color_id', DB::raw('COUNT(DISTINCT product_id) as total'))
                ->groupBy('color_id')
                ->pluck('total', 'color_id');

            $sizeCounts = DB::table('product_variants')
                ->whereIn('product_id', $baseProductIds)
                ->where('stock_quantity', '>', 0)
                ->select('size_id', DB::raw('COUNT(DISTINCT product_id) as total'))
                ->groupBy('size_id')
                ->pluck('total', 'size_id');
        } else {
            $colorCounts = collect();
            $sizeCounts = collect();
        }

        $priceAggregates = (clone $baseCountQuery)
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN {$effectivePriceSql} < 300000 THEN 1 END) as under_300,
                COUNT(CASE WHEN {$effectivePriceSql} >= 300000 AND {$effectivePriceSql} <= 500000 THEN 1 END) as p300_500,
                COUNT(CASE WHEN {$effectivePriceSql} > 500000 AND {$effectivePriceSql} <= 1000000 THEN 1 END) as p500_1000,
                COUNT(CASE WHEN {$effectivePriceSql} > 1000000 THEN 1 END) as over_1000
            ")
            ->first();

        $priceCounts = [
            'all' => (int) ($priceAggregates->total ?? 0),
            'under_300' => (int) ($priceAggregates->under_300 ?? 0),
            '300_500' => (int) ($priceAggregates->p300_500 ?? 0),
            '500_1000' => (int) ($priceAggregates->p500_1000 ?? 0),
            'over_1000' => (int) ($priceAggregates->over_1000 ?? 0),
        ];

        $colors = Color::where('is_active', true)->get()->map(function ($cl) use ($colorCounts, $selectedColors) {
            $count = (int) ($colorCounts[$cl->id] ?? 0);
            $cl->products_count = $count;
            $cl->is_disabled = ($count === 0 && ! in_array($cl->id, $selectedColors));

            return $cl;
        });

        $sizes = Size::where('is_active', true)->orderBy('sort_order', 'asc')->get()->map(function ($sz) use ($sizeCounts, $selectedSizes) {
            $count = (int) ($sizeCounts[$sz->id] ?? 0);
            $sz->products_count = $count;
            $sz->is_disabled = ($count === 0 && ! in_array($sz->id, $selectedSizes));

            return $sz;
        });

        // Kiểm tra tính khả dụng của từng nhóm filter
        $hasAvailablePriceFilter = ($priceCounts['all'] > 0) || ! empty($priceRange);
        $hasAvailableColorFilter = $colors->contains(fn ($c) => $c->products_count > 0) || ! empty($selectedColors);
        $hasAvailableSizeFilter = $sizes->contains(fn ($s) => $s->products_count > 0) || ! empty($selectedSizes);

        // 9. Active Filter Tags List
        $activeTagList = [];
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minFormatted = $minPrice ? number_format($minPrice).'đ' : '0đ';
            $maxFormatted = $maxPrice ? number_format($maxPrice).'đ' : 'Tối đa';
            $activeTagList[] = [
                'type' => 'price_range',
                'value' => 'slider',
                'label' => "Giá: {$minFormatted} - {$maxFormatted}",
            ];
        } elseif (! empty($priceRange)) {
            $priceTextMap = [
                'under_300' => 'Dưới 300.000đ',
                '300_500' => '300k - 500k',
                '500_1000' => '500k - 1 triệu',
                'over_1000' => 'Trên 1 triệu',
            ];
            $activeTagList[] = [
                'type' => 'price_range',
                'value' => $priceRange,
                'label' => 'Giá: '.($priceTextMap[$priceRange] ?? $priceRange),
            ];
        }

        foreach ($selectedColors as $cId) {
            $cModel = $colors->firstWhere('id', $cId);
            if ($cModel) {
                $activeTagList[] = [
                    'type' => 'color',
                    'value' => $cId,
                    'label' => 'Màu: '.$cModel->name,
                ];
            }
        }

        foreach ($selectedSizes as $sId) {
            $sModel = $sizes->firstWhere('id', $sId);
            if ($sModel) {
                $activeTagList[] = [
                    'type' => 'size',
                    'value' => $sId,
                    'label' => 'Size: '.$sModel->name,
                ];
            }
        }

        // 10. Sorting & Pagination
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw("{$effectivePriceSql} ASC");
                break;
            case 'price_desc':
                $query->orderByRaw("{$effectivePriceSql} DESC");
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return [
            'products' => $products,
            'colors' => $colors,
            'sizes' => $sizes,
            'priceCounts' => $priceCounts,
            'activeTagList' => $activeTagList,
            'selectedColors' => $selectedColors,
            'selectedSizes' => $selectedSizes,
            'priceRange' => $priceRange,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'minPriceLimit' => 0,
            'maxPriceLimit' => 2000000,
            'sort' => $sort,
            'hasAvailablePriceFilter' => $hasAvailablePriceFilter,
            'hasAvailableColorFilter' => $hasAvailableColorFilter,
            'hasAvailableSizeFilter' => $hasAvailableSizeFilter,
        ];
    }
}
