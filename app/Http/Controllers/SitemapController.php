<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Xuất tệp sitemap.xml tự động chuẩn SEO cho công cụ tìm kiếm (Google, Bing).
     */
    public function index(): Response
    {
        $products = Product::where('is_active', true)->latest()->get();
        $categories = Category::where('is_active', true)->latest()->get();

        return response()->view('seo.sitemap', compact('products', 'categories'))
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Xuất tệp robots.txt chuẩn SEO.
     */
    public function robots(): Response
    {
        $content = "User-agent: *\nDisallow: /admin/\nDisallow: /checkout/\nDisallow: /profile/\nDisallow: /cart/\n\nSitemap: ".url('/sitemap.xml');

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
