<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\StoreConfig;

class StorefrontService
{
    public function getHomeData($tenant)
    {
        // Get store configuration (first record for now as per previous fix)
        $storeConfig = StoreConfig::first();

        // Load Schema from JSON to get injected data
        $schema = [];
        if (\Illuminate\Support\Facades\Storage::disk('tenant')->exists('generated_theme_schema.json')) {
            $schema = json_decode(\Illuminate\Support\Facades\Storage::disk('tenant')->get('generated_theme_schema.json'), true);
        }

        $injectedData = $schema['injected_data'] ?? [];
        
        // Build hero data (Priority: Injected Data > Store Config > Default)
        $heroData = [
            'title' => $injectedData['hero_title'] ?? $storeConfig?->store_name ?? $tenant->name,
            'subtitle' => $injectedData['hero_subtitle'] ?? 'Welcome to our store',
            'banner_image' => $injectedData['banner_image'] ?? $storeConfig?->hero_banner ?? null,
            'hero_button_text' => $injectedData['cta_text'] ?? 'Shop Now',
            'brand_color' => $storeConfig?->brand_color ?? '#0A2540',
        ];
        
        // Fetch 8 random featured products
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('track_inventory', false)
                      ->orWhere('stock_quantity', '>', 0);
            })
            ->with(['category', 'images'])
            ->inRandomOrder()
            ->limit(8)
            ->get()
            ->map(function ($product) {
                return $this->formatProduct($product);
            });
            
        // Fetch category sections
        $categorySections = [];
        
        if ($storeConfig && $storeConfig->selected_categories) {
            $selectedCategoryIds = is_array($storeConfig->selected_categories) 
                ? $storeConfig->selected_categories 
                : json_decode($storeConfig->selected_categories, true);
            
            if ($selectedCategoryIds && is_array($selectedCategoryIds)) {
                $categories = Category::whereIn('id', $selectedCategoryIds)
                    ->where('is_active', true)
                    // Removed is_visible_online check to satisfy "all selected and activated categories should be visible"
                    ->orderBy('sort_order')
                    ->get();
                
                foreach ($categories as $category) {
                    $products = Product::where('category_id', $category->id)
                        ->where('is_active', true)
                        ->where(function ($query) {
                            $query->where('track_inventory', false)
                                  ->orWhere('stock_quantity', '>', 0);
                        })
                        ->with('images')
                        ->latest()
                        ->limit(8)
                        ->get()
                        ->map(function ($product) {
                            return $this->formatProduct($product);
                        });
                    
                    // Always add the section if it is selected, even if empty (per user requirement)
                    $categorySections[] = [
                        'category_id' => $category->id,
                        'category_name' => $category->public_display_name ?? $category->name,
                        'category_slug' => $category->slug,
                        'products' => $products,
                    ];
                }
            }
        }
        
        return [
            'hero_data' => $heroData,
            'featured_products' => $featuredProducts,
            'category_sections' => $categorySections,
            'layout_mode' => $storeConfig?->layout_preference ?? 'brand_showcase',
        ];
    }
    
    private function formatProduct($product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $product->price,
            'compare_at_price' => $product->compare_at_price ? (float) $product->compare_at_price : null,
            'image_url' => $product->image_url, // Accessor already handles full URL
            'stock_quantity' => $product->stock_quantity,
            'category' => $product->category ? $product->category->name : null,
            'is_flash_sale' => $product->is_flash_sale && $product->isFlashSaleActive(),
            'flash_sale_price' => $product->isFlashSaleActive() ? (float) $product->flash_sale_price : null,
        ];
    }
}
