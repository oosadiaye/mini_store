<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // Homepage
        $urls[] = [
            'loc' => route('storefront.home'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Categories
        $categories = Category::all();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('storefront.category', $category),
                'lastmod' => $category->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Products
        $products = Product::where('is_active', true)->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('storefront.product', $product),
                'lastmod' => $product->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }

        // Pages
        $pages = Page::where('is_published', true)->get();
        foreach ($pages as $page) {
            $urls[] = [
                'loc' => route('storefront.page', $page->slug),
                'lastmod' => $page->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        }

        return response()->view('storefront.sitemap', compact('urls'))
            ->header('Content-Type', 'text/xml');
    }
}
