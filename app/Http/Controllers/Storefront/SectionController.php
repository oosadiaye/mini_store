<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

use App\Models\Category; // Added import

class SectionController extends Controller
{
    public function index()
    {
        // 1. Fetch Categories Direct from DB (Live Source of Truth)
        $categories = Category::where('is_visible_online', true)
            ->orderBy('sort_order', 'asc')
            ->get();
            
        // 2. Fetch CMS Content (Banners) from Schema
        $schema = json_decode(\Illuminate\Support\Facades\Storage::disk('tenant')->get('generated_theme_schema.json') ?? '{}', true);
        $cmsSections = collect($schema['sections'] ?? [])->filter(fn($s) => $s['type'] !== 'category_section')->values();

        $sections = [];

        // 3. Add CMS Sections (e.g. Split Banner) - positioned at top or mixed? 
        // For now, let's put Split Banner after the first category or at top.
        // Let's iterate and merge.
        

        // 3. Add CMS Sections (e.g. Split Banner)
        foreach ($cmsSections as $cms) {
             if ($cms['type'] === 'split_banner') {
                 // Convert Image Paths to URLs
                 if (!empty($cms['data']['image_left'])) {
                     $img = $cms['data']['image_left'];
                     $cms['data']['image_left'] = \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : route('tenant.media', ['tenant' => app('tenant')->slug, 'path' => $img]);
                 }
                 if (!empty($cms['data']['image_right'])) {
                     $img = $cms['data']['image_right'];
                     $cms['data']['image_right'] = \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : route('tenant.media', ['tenant' => app('tenant')->slug, 'path' => $img]);
                 }
                 $sections[] = $cms;
             }
        }
        
        // 3a. Inject "Featured Collection" Dynamically (Top 8 Featured Products)
        // If no featured products, fallback to latest.
        $featured = Product::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();
            
        if ($featured->isEmpty()) {
             $featured = Product::where('is_active', true)
                ->latest()
                ->take(8)
                ->get();
        }
            
        if ($featured->isNotEmpty()) {
            // Prepend or insert at specific position? Let's put it at the very top or after banner.
            // Let's optimize: Put it FIRST.
            array_unshift($sections, [
                'type' => 'product_slider', // Use dedicated slider layout
                'title' => 'Featured Collection',
                'link_slug' => route('storefront.products.index', ['tenant' => app('tenant')->slug]),
                'products' => $featured->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => $p->price,
                        'compare_at_price' => $p->compare_at_price,
                        'slug' => $p->slug, // Ensure slug is passed for links
                        'image_url' => $p->image_url ?? $p->image_path,
                        'category' => $p->category->name ?? 'Uncategorized',
                        'stock_quantity' => $p->stock_quantity ?? 10,
                    ];
                })
            ]);
        }

        
        // 4. Loop & Query Categories
        foreach ($categories as $cat) {
            $products = Product::where('category_id', $cat->id)
                ->where('is_active', true)
                // ->where(function($q) {
                //     $q->where('track_inventory', false)
                //       ->orWhere('stock_quantity', '>', 0);
                // })
                ->take(8) // Top 8 active products
                ->latest()
                ->get();
                
            // 5. Format Response
            $sections[] = [
                'type' => 'category_section',
                'title' => $cat['name'],
                'link_slug' => route('storefront.category', ['tenant' => app('tenant')->slug, 'slug' => $cat['slug'] ?? $cat['id']]),
                'products' => $products->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => $p->price,
                        'compare_at_price' => $p->compare_at_price,
                        'image_url' => $p->image_url ?? $p->image_path,
                        'category' => $p->category->name ?? 'Uncategorized',
                        'stock_quantity' => $p->stock_quantity ?? 10,
                    ];
                })
            ];
        }
        
        return response()->json($sections);
    }
}
