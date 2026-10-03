<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\Product;
use App\Services\ProductFilterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(
        protected ProductFilterService $filterService
    ) {}

    /**
     * Giao diện trang danh sách sản phẩm dành cho khách hàng.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filterData = $this->filterService->getFilteredProducts($request);

        $products = $filterData['products'];
        $colors = $filterData['colors'];
        $sizes = $filterData['sizes'];
        $priceCounts = $filterData['priceCounts'];
        $activeTagList = $filterData['activeTagList'];
        $selectedColors = $filterData['selectedColors'];
        $selectedSizes = $filterData['selectedSizes'];
        $priceRange = $filterData['priceRange'];

        $hasAvailablePriceFilter = $filterData['hasAvailablePriceFilter'] ?? true;
        $hasAvailableColorFilter = $filterData['hasAvailableColorFilter'] ?? true;
        $hasAvailableSizeFilter = $filterData['hasAvailableSizeFilter'] ?? true;

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->get();

        $currentCollection = $request->filled('collection')
            ? Collection::where('slug', $request->collection)->first()
            : null;

        $isDiscount = $request->filled('discount') && ($request->discount == '1' || $request->discount == 'true');
        $currentOutfit = $request->get('outfit', $request->get('tag'));

        $suggestedProducts = collect();
        if ($products->total() < 6) {
            $existingIds = $products->pluck('id')->toArray();
            $suggestedProducts = Product::with(['category', 'images', 'activeFlashSale'])
                ->where('is_active', true)
                ->whereNotIn('id', $existingIds)
                ->latest()
                ->take(4)
                ->get();
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $productsHtml = view('shop.components.product-list', compact('products', 'suggestedProducts'))->render();
            $tagsHtml = view('shop.components.active-tags', ['activeTagList' => $activeTagList])->render();
            $paginationHtml = $products->hasPages() ? $products->links()->render() : '';

            $filterDesktopHtml = view('shop.components.filter-form', array_merge($filterData, [
                'context' => 'desktop',
                'categories' => $categories,
            ]))->render();

            $filterMobileHtml = view('shop.components.filter-form', array_merge($filterData, [
                'context' => 'mobile',
                'categories' => $categories,
            ]))->render();

            return response()->json([
                'success' => true,
                'products_html' => $productsHtml,
                'tags_html' => $tagsHtml,
                'pagination_html' => $paginationHtml,
                'filter_desktop_html' => $filterDesktopHtml,
                'filter_mobile_html' => $filterMobileHtml,
                'total' => $products->total(),
                'first_item' => $products->firstItem() ?? 0,
                'last_item' => $products->lastItem() ?? 0,
                'active_count' => count($activeTagList),
            ]);
        }

        return view('shop.index', array_merge($filterData, [
            'categories' => $categories,
            'currentCollection' => $currentCollection,
            'isDiscount' => $isDiscount,
            'currentOutfit' => $currentOutfit,
            'suggestedProducts' => $suggestedProducts,
        ]));
    }

    /**
     * Giao diện trang Flash Sale giảm giá (/flash-sale).
     */
    public function flashSale(Request $request): View|RedirectResponse|JsonResponse
    {

        $flashSales = FlashSale::active()
            ->with(['product.category', 'product.images', 'product.variants'])
            ->orderBy('end_time', 'asc')
            ->paginate(12)
            ->withQueryString();

        $flashSaleEndTime = $flashSales->isNotEmpty()
            ? $flashSales->min('end_time')?->format('Y-m-d H:i:s')
            : null;

        return view('shop.flash-sale', compact('flashSales', 'flashSaleEndTime'));
    }

    /**
     * Hiển thị danh sách sản phẩm theo Outfit (/outfits/{slug}).
     */
    public function outfit(string $slug, Request $request): View|JsonResponse
    {
        $request->merge(['outfit' => $slug]);

        return $this->index($request);
    }

    /**
     * Chi tiết sản phẩm (Gallery, Review, Related Products, Active Flash Sale).
     */
    public function show(string $slug): View
    {
        $product = Product::with([
            'category',
            'images',
            'variants.color',
            'variants.size',
            'reviews.user',
            'reviews.images',
            'activeFlashSale',
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $avgRating = $product->reviews->avg('rating') ?: 5.0;
        $reviewsCount = $product->reviews->count();

        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->latest()
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'avgRating', 'reviewsCount', 'relatedProducts', 'coupons'));
    }

    /**
     * API Gợi ý tìm kiếm trực tiếp cho Header Live Search.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->get('q', ''));
        if (mb_strlen($keyword) < 1) {
            return response()->json(['success' => true, 'products' => [], 'total' => 0]);
        }

        $products = Product::with(['images', 'category', 'activeFlashSale'])
            ->where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%");
            })
            ->latest()
            ->take(6)
            ->get();

        $totalMatches = Product::where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%");
            })
            ->count();

        $mapped = $products->map(function ($p) {
            $img = $p->images->where('is_thumbnail', true)->first() ?: $p->images->first();
            $imageUrl = $img && $img->image_url ? asset('storage/'.$img->image_url) : null;
            $flashSale = $p->activeFlashSale;

            $effectivePrice = $flashSale ? (float) $flashSale->sale_price : $p->effective_sale_price;
            $unitPrice = $effectivePrice ?? (float) $p->price;
            $origPrice = $effectivePrice ? (float) $p->price : null;

            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'sku' => $p->sku,
                'image' => $imageUrl,
                'price' => number_format($unitPrice).'đ',
                'original_price' => $origPrice ? number_format($origPrice).'đ' : null,
                'has_discount' => ! is_null($origPrice) && $origPrice > $unitPrice,
                'url' => route('shop.show', $p->slug),
                'category' => $p->category->name ?? 'Thời trang',
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $mapped,
            'total' => $totalMatches,
            'all_url' => route('shop.index', ['search' => $keyword]),
        ]);
    }

    /**
     * API Lấy thông tin tóm tắt cho Quick View Modal.
     */
    public function quickView(Product $product): JsonResponse
    {
        if (! $product->is_active) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không khả dụng'], 404);
        }

        $product->load(['images', 'category', 'variants.color', 'variants.size', 'activeFlashSale']);

        $images = $product->images->map(fn ($img) => asset('storage/'.$img->image_url));
        if ($images->isEmpty()) {
            $images = collect(['https://placehold.co/400x400?text=ATINO']);
        }

        $colors = $product->variants->pluck('color')->filter()->unique('id')->values()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'hex_code' => $c->hex_code ?? '#000',
        ]);

        $sizes = $product->variants->pluck('size')->filter()->unique('id')->values()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
        ]);

        $variants = $product->variants->map(fn ($v) => [
            'id' => $v->id,
            'color_id' => $v->color_id,
            'size_id' => $v->size_id,
            'price' => $v->price,
            'sale_price' => $v->sale_price,
            'stock_quantity' => $v->stock_quantity,
        ]);

        $flashSale = $product->activeFlashSale;
        $effectivePrice = $flashSale ? (float) $flashSale->sale_price : $product->effective_sale_price;
        $unitPrice = $effectivePrice ?? (float) $product->price;
        $origPrice = $effectivePrice ? (float) $product->price : null;

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'description' => Str::limit(strip_tags($product->description ?? ''), 180),
                'price' => number_format($unitPrice).'đ',
                'raw_price' => $unitPrice,
                'original_price' => $origPrice ? number_format($origPrice).'đ' : null,
                'images' => $images,
                'colors' => $colors,
                'sizes' => $sizes,
                'variants' => $variants,
                'url' => route('shop.show', $product->slug),
            ],
        ]);
    }
}
