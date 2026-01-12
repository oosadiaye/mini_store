<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    /**
     * Generate sitemap.xml for the online store.
     */
    public function sitemap()
    {
        $tenant = app('tenant');
        $baseUrl = route('storefront.home', ['tenant' => $tenant->slug]);

        $urls = [
            ['loc' => $baseUrl, 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('storefront.products.index', ['tenant' => $tenant->slug]), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => route('storefront.about', ['tenant' => $tenant->slug]), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('storefront.contact', ['tenant' => $tenant->slug]), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ];

        // Add Categories
        $categories = Category::storefront()->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('storefront.category', ['tenant' => $tenant->slug, 'slug' => $category->slug]),
                'priority' => '0.7',
                'changefreq' => 'weekly'
            ];
        }

        // Add Products
        $products = Product::active()->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('storefront.product.detail', ['tenant' => $tenant->slug, 'slug' => $product->slug]),
                'priority' => '0.8',
                'changefreq' => 'weekly'
            ];
        }

        $xml = view('admin.seo.sitemap', compact('urls'))->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Generate robots.txt for the online store.
     */
    public function robots()
    {
        $tenant = app('tenant');
        $sitemapUrl = route('storefront.sitemap', ['tenant' => $tenant->slug]);

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /account/\n";
        $content .= "Disallow: /checkout/\n";
        $content .= "Disallow: /cart/\n";
        $content .= "\nSitemap: {$sitemapUrl}\n";

        return Response::make($content, 200, ['Content-Type' => 'text/plain']);
    }

    /**
     * AI-powered Meta Tag Suggestions (Smart Templates).
     */
    public function suggest(Request $request)
    {
        $type = $request->input('type'); // title, description
        $context = $request->input('context', ''); // fallback or extra info
        $tenant = app('tenant');
        $storeName = $tenant->name;
        $industry = $tenant->data['industry'] ?? 'Retail';

        $suggestion = '';

        if ($type === 'meta_title') {
            $templates = [
                "{$storeName} | Best {$industry} Store Online",
                "Shop Premium {$industry} at {$storeName}",
                "{$storeName} - Your One-Stop Shop for {$industry}",
                "Quality {$industry} Products | {$storeName}"
            ];
            $suggestion = $templates[array_rand($templates)];
        } elseif ($type === 'meta_description') {
            $templates = [
                "Welcome to {$storeName}, the best place to find top-quality {$industry} products at unbeatable prices. Shop our latest collection today!",
                "Discover premium {$industry} items at {$storeName}. We offer fast shipping, secure payment, and the best customer service in the industry.",
                "Looking for {$industry}? {$storeName} has everything you need. Quality you can trust, prices you'll love. Explore our catalog now.",
                "Transform your lifestyle with the finest {$industry} from {$storeName}. Join thousands of happy customers and shop our new arrivals."
            ];
            $suggestion = $templates[array_rand($templates)];
        } elseif ($type === 'meta_keywords') {
            $keywords = [
                "{$industry}", "online store", "shop {$storeName}", "buy {$industry}", "premium {$industry}", "best {$industry} in Nigeria"
            ];
            $suggestion = implode(', ', $keywords);
        }

        return response()->json(['suggestion' => $suggestion]);
    }
}
