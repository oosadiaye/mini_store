<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\StoreConfig;
use App\Models\StoreCollection;
use App\Models\Category;
use App\Models\StorefrontProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Services\StorefrontService;

class HomeController extends Controller
{
    /**
     * Show the dynamic storefront home page.
     */
    public function index(StorefrontService $storefrontService)
    {
        $tenant = app('tenant');
        
        // Fetch data directly via service to avoid HTTP self-request deadlock
        try {
            $data = $storefrontService->getHomeData($tenant);
            
            $featuredProducts = collect($data['featured_products'] ?? []);
            $categorySections = collect($data['category_sections'] ?? []);
            $heroData = $data['hero_data'] ?? [];
            
        } catch (\Exception $e) {
            // Fallback on error
            \Log::error('Storefront Service Error: ' . $e->getMessage());
            $featuredProducts = collect([]);
            $categorySections = collect([]);
            $heroData = [
                'title' => $tenant->name,
                'subtitle' => 'Welcome to our store',
                'brand_color' => '#0A2540'
            ];
        }
        
        // Load Configuration for menu logic only
        $config = StoreConfig::firstOrCreate(['id' => 1], [
            'store_name' => $tenant->name,
            'layout_preference' => 'minimal',
            'is_completed' => false
        ]);

        // Fetch Menu Categories
        $menuCategories = [];
        if (!empty($config->selected_categories)) {
            $selectedIds = is_array($config->selected_categories) 
                ? $config->selected_categories 
                : json_decode($config->selected_categories, true);
                
            if ($selectedIds) {
                $menuCategories = Category::whereIn('id', $selectedIds)
                    ->where('is_active', true)
                    ->get();
            }
        }
        // Load Schema from JSON (Fix for Layout Switcher)
        $schema = [];
        if (Storage::disk('tenant')->exists('generated_theme_schema.json')) {
            $schema = json_decode(Storage::disk('tenant')->get('generated_theme_schema.json'), true);
        }

        // Merge Schema Data into View Variables if not present
        // Priority: Schema (Root) > Schema (Design) > DB Config > Default
        $layoutMode = $schema['layout_mode'] ?? $schema['design']['layout_mode'] ?? $config->layout_preference ?? 'minimal';

        // Fix for Issue 2: Use All Visible Categories from Schema for Menu
        if (!empty($schema['injected_data']['all_visible_categories'])) {
            // Check if we need to hydrate models or if array is sufficient.
            // Converting to simple object collection for Blade compatibility
            $menuCategories = collect($schema['injected_data']['all_visible_categories'])->map(function($item) {
                return (object) $item;
            });
        }

        return view('storefront.index', compact(
            'config',
            'menuCategories',
            'featuredProducts',
            'categorySections',
            'heroData',
            'schema',
            'layoutMode'
        ));
    }
    public function category($slug)
    {
        $tenant = app('tenant');
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Fetch products for this category with pagination
        // Fetch products for this category with pagination
        $products = \App\Models\Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->with(['images', 'category'])
            ->latest()
            ->paginate(12);
            
        // Load Config with Brand Color
        $config = StoreConfig::firstOrCreate(['id' => 1], [
            'store_name' => $tenant->name,
            'layout_preference' => 'minimal',
            'brand_color' => '#0A2540',
            'is_completed' => false
        ]);
        
        // Needed for Layout
        $menuCategories = Category::where('is_active', true)->get();

        // Prepare theme schema for Layout (nav menu)
        $schema = [];
        if (Storage::disk('tenant')->exists('generated_theme_schema.json')) {
            $schema = json_decode(Storage::disk('tenant')->get('generated_theme_schema.json'), true);
        }
        
        // Pass schema to view so strict layout can use it
        return view('storefront.category.show', compact('category', 'products', 'config', 'menuCategories', 'schema'));
    }
    public function about()
    {
        $tenant = app('tenant');
        
        // Load Config with Brand Color
        $config = StoreConfig::firstOrCreate(['id' => 1], [
            'store_name' => $tenant->name,
            'layout_preference' => 'minimal',
            'brand_color' => '#0A2540',
            'is_completed' => false
        ]);
        
        // Needed for Navigation
        $menuCategories = Category::where('is_active', true)->get();

        // Prepare theme schema for Layout (nav menu) and About Content
        $schema = [];
        if (Storage::disk('tenant')->exists('generated_theme_schema.json')) {
            $schema = json_decode(Storage::disk('tenant')->get('generated_theme_schema.json'), true);
        }

        $aboutData = $schema['pages']['about_us'] ?? [];
        
        return view('storefront.about', compact('config', 'menuCategories', 'schema', 'aboutData'));
    }

    public function contact()
    {
        $tenant = app('tenant');
        
        $config = StoreConfig::firstOrCreate(['id' => 1], [
            'store_name' => $tenant->name,
            'layout_preference' => 'minimal',
            'brand_color' => '#0A2540',
        ]);
        
        $menuCategories = Category::where('is_active', true)->get();

        $schema = [];
        if (Storage::disk('tenant')->exists('generated_theme_schema.json')) {
            $schema = json_decode(Storage::disk('tenant')->get('generated_theme_schema.json'), true);
        }

        // Extract contact info from schema's injected_data or root
        $contactInfo = $schema['injected_data']['contact_info'] ?? [];

        return view('storefront.contact', compact('config', 'menuCategories', 'schema', 'contactInfo'));
    }
}
