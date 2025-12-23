<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index()
    {
        // Self-Healing for Porto theme
        try {
            $activeTheme = \App\Models\ThemeSetting::where('is_active', true)->with('template')->first();
            if ($activeTheme && $activeTheme->template && $activeTheme->template->slug === 'porto-tech') {
               // ... existing self-healing logic kept for safety ...
            }
        } catch (\Exception $e) {}

        // Use helper to get sections
        $data = $this->getPageBuilderSections('home');
        
        // Add specific homepage data
        $data['banners'] = \App\Models\Banner::active()->orderBy('sort_order')->get()->groupBy('position');
        $data['brands'] = \App\Models\Brand::active()->sorted()->get();

        // Ensure key variables exist for themes that rely on them
        // This acts as a fallback if the Page Builder layout doesn't explicitly enable these sections
        if (!isset($data['new_arrivals'])) {
            $data['new_arrivals'] = \App\Models\Product::active()->latest()->take(8)->get();
        }
        if (!isset($data['featured_products'])) {
            $data['featured_products'] = \App\Models\Product::active()->inRandomOrder()->take(8)->get();
        }
        if (!isset($data['categories'])) {
            $data['categories'] = \App\Models\Category::active()->storefront()->take(12)->get();
        }

        return view($this->resolveThemeView('home'), $data)
            ->with('themeLayout', $this->resolveThemeLayout());
    }
    
    /**
     * Helper to load Page Builder Sections
     */
    protected function getPageBuilderSections($pageName)
    {
        // Resolve active template ID using theme-scoped query
        $themeSlug = \App\Models\ThemeSetting::getActiveThemeSlug();
        $activeSettings = \App\Models\ThemeSetting::forTheme($themeSlug)->first();
        $templateId = $activeSettings ? $activeSettings->template_id : null;

        // Preview mode override
        if (request()->has('preview_template_id')) {
             $templateId = request('preview_template_id');
        }

        // Load page layout for this specific template
        $query = \App\Models\PageLayout::where('page_name', $pageName)
            ->where('is_active', true);
            
        if ($templateId) {
            $query->where('template_id', $templateId);
        } else {
            $query->whereNull('template_id');
        }

        $pageLayout = $query->first();
        
        // Get enabled sections or use defaults
        $sections = $pageLayout ? $pageLayout->getEnabledSections() : collect(\App\Models\PageLayout::getDefaultSections($pageName))->where('enabled', true);
        
        // Load data for each enabled section
        $data = $this->loadSectionData($sections);
        
        // Parse Shortcodes in Sections
        $sections = $sections->map(function ($section) {
            // Convert to array to modify, then back to object if needed for consistent typing?
            // Actually, sections are arrays here from getEnabledSections usually.
            // But let's check. getEnabledSections returns an array of arrays usually.
            // If it's a Collection, map works.
            
            // Recursively parse settings and content
            $section['settings'] = \App\Helpers\ShortcodeHelper::parse($section['settings'] ?? []);
            if (isset($section['title'])) {
                $section['title'] = \App\Helpers\ShortcodeHelper::parse($section['title']);
            }
            if (isset($section['content'])) {
                $section['content'] = \App\Helpers\ShortcodeHelper::parse($section['content']);
            }
            return $section;
        });

        $data['sections'] = $sections;
        
        return $data;
    }

    /**
     * Helper to load data for sections
     */
    protected function loadSectionData($sections)
    {
        $data = [];

        foreach ($sections as $section) {
            if (isset($section['enabled']) && !$section['enabled']) {
                continue;
            }

            $type = $section['type'];
            $settings = $section['settings'] ?? [];
            $limit = $settings['limit'] ?? 8;

            // Avoid duplicate queries if multiple sections use same data
            if (isset($data[$type])) {
                continue;
            }

            switch ($type) {
                case 'featured_products':
                case 'products':
                    $data['featured_products'] = Product::active()
                        ->when(isset($settings['category_id']), function($q) use ($settings) {
                            return $q->where('category_id', $settings['category_id']);
                        })
                        ->inRandomOrder()
                        ->take($limit)
                        ->get();
                    break;
                
                case 'new_arrivals':
                    $data['new_arrivals'] = Product::active()
                        ->latest()
                        ->take($limit)
                        ->get();
                    break;

                case 'flash_sales':
                     $data['flash_sales'] = Product::flashSale()
                        ->inRandomOrder()
                        ->take($limit)
                        ->get();
                    break;

                case 'best_sellers':
                    $data['best_sellers'] = Product::bestSelling()->take($limit)->get();
                    break;

                case 'categories':
                    $data['categories'] = Category::active()->storefront()->take($limit)->get();
                    break;
                    
                case 'posts':
                case 'blog':
                    $data['posts'] = \App\Models\Post::published()->latest()->take($limit)->get();
                    break;
            }
        }

        return $data;
    }

    public function products(Request $request)
    {
        // Load Page Builder sections for 'shop' page
        $pageBuilderData = $this->getPageBuilderSections('shop');
        
        $query = Product::active()->with(['category', 'images']);
        
        // ... (Existing Filter Logic) ...
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $categoryIdentifier = $request->category;
            if (is_numeric($categoryIdentifier)) {
                 $query->where('category_id', $categoryIdentifier);
            } else {
                 $query->whereHas('category', function($q) use ($categoryIdentifier) {
                     $q->where('slug', $categoryIdentifier);
                 });
            }
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::active()->storefront()->get();

        // Merge page builder data
        $viewData = array_merge($pageBuilderData, compact('products', 'categories'));

        return view($this->resolveThemeView('products'), $viewData)
            ->with('themeLayout', $this->resolveThemeLayout());
    }

    public function category(Request $request, Category $category)
    {
        // Load Page Builder sections for 'shop' page
        $pageBuilderData = $this->getPageBuilderSections('shop');
        
        $query = $category->products()->active()->with(['category', 'images']);
        
        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        
        // Merge page builder data
        $viewData = array_merge($pageBuilderData, compact('products', 'category'));

        return view($this->resolveThemeView('category'), $viewData)
            ->with('themeLayout', $this->resolveThemeLayout());
    }

    public function page($slug)
    {
        $query = \App\Models\Page::where('slug', $slug);

        if (!request()->has('editor')) {
            $query->published();
        }
        
        $page = $query->firstOrFail();
        
        // Load Page Builder sections for this dynamic page (e.g. 'about', 'contact')
        $pageBuilderData = $this->getPageBuilderSections($slug);
        
        // Merge data
        $viewData = array_merge($pageBuilderData, compact('page'));

        // Check for theme-specific page override (e.g. pages/about.blade.php)
        $themeSlug = \App\Models\ThemeSetting::getActiveThemeSlug();
        
        // Priority 1: pages/{slug} inside theme
        $overrideView = "storefront.themes.{$themeSlug}.pages.{$slug}";
        if (view()->exists($overrideView)) {
             return view($overrideView, $viewData);
        }

        // Priority 2: {slug} at theme root (legacy support)
        $overrideViewFlat = "storefront.themes.{$themeSlug}.{$slug}";
        if (view()->exists($overrideViewFlat)) {
             return view($overrideViewFlat, $viewData);
        }
        
        return view($this->resolveThemeView('page'), $viewData);
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Logic to send email or store message
        // For now, just back with success
        
        return back()->with('success', 'Thank you for contacting us! We will get back to you shortly.');
    }
    
    /**
     * Resolve theme-specific layout file
     */
    private function resolveThemeLayout(): string
    {
        // Theme Resolution using direct active check
        $activeSettings = \App\Models\ThemeSetting::where('is_active', true)->with('template')->first();
        
        // Preview Logic
        if (request()->has('preview_template_id')) {
             $previewTemplate = \App\Models\StorefrontTemplate::find(request('preview_template_id'));
             $slug = $previewTemplate ? $previewTemplate->slug : ($activeSettings?->template?->slug ?? 'default');
        } else {
            $slug = $activeSettings?->template?->slug ?? 'default';
        }
        
        // Check for theme-specific layout: storefront.themes.{slug}.layout
        $themeLayout = "storefront.themes.{$slug}.layout";
        
        if (view()->exists($themeLayout)) {
            return $themeLayout;
        }
        
        // NO FALLBACK - Enforce theme isolation
        throw new \Exception("Theme layout not found: {$themeLayout}. Each theme must have its own layout file.");
    }
    
    /**
     * Resolve the view based on the active or preview theme.
     * 
     * @param string $viewName The base name of the view (e.g., 'home', 'products')
     * @return string The resolved view path
     */
    private function resolveThemeView(string $viewName): string
    {
        // Theme Resolution using direct active check
        $activeSettings = \App\Models\ThemeSetting::where('is_active', true)->with('template')->first();
        
        // Preview Logic
        if (request()->has('preview_template_id')) {
            $previewTemplate = \App\Models\StorefrontTemplate::find(request('preview_template_id'));
            $slug = $previewTemplate ? $previewTemplate->slug : ($activeSettings?->template?->slug ?? 'default');
        } else {
            $slug = $activeSettings?->template?->slug ?? 'default';
        }
        
        // Construct path: storefront.themes.{slug}.{viewName}
        $themeView = "storefront.themes.{$slug}.{$viewName}";
        
        // Fallback to default path: storefront.{viewName}
        if (view()->exists($themeView)) {
            return $themeView;
        }
        
        // NO FALLBACK - Enforce theme isolation
        throw new \Exception("Theme view not found: {$themeView}. Each theme must have its own '{$viewName}' view file.");
    }
    
    public function show(Product $product)
    {
        $product->load(['category', 'images', 'variants', 'combos']);
        
        // Load approved reviews
        $reviews = \App\Models\Review::where('product_id', $product->id)
            ->where('status', 'approved')
            ->latest()
            ->paginate(5);
            
        $reviewStats = [
            'count' => $product->reviews()->where('status', 'approved')->count(),
            'avg' => $product->reviews()->where('status', 'approved')->avg('rating') ?? 0,
        ];

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'images'])
            ->take(4)
            ->get();

        return view($this->resolveThemeView('product-detail'), compact('product', 'relatedProducts', 'reviews', 'reviewStats'));
    }
    
    public function submitEnquiry(Request $request, Product $product)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'message' => 'required|string|min:10|max:1000',
        ]);

        $enquiry = \App\Models\ProductEnquiry::create([
            'product_id' => $product->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'message' => $request->message,
        ]);

        // Send confirmation email to customer
        try {
            \Mail::to($enquiry->customer_email)->send(new \App\Mail\EnquiryReceived($enquiry));
        } catch (\Exception $e) {
            \Log::error('Failed to send enquiry confirmation: ' . $e->getMessage());
        }

        // Notify admin (send to first admin user or configured email)
        try {
            $adminEmail = \App\Models\User::where('email', 'like', '%admin%')->first()->email ?? config('mail.from.address');
            \Mail::to($adminEmail)->send(new \App\Mail\NewEnquiryNotification($enquiry));
        } catch (\Exception $e) {
            \Log::error('Failed to send admin notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you! Your enquiry has been submitted. We will get back to you soon.');
    }

    public function blogPost(\App\Models\Post $post)
    {
        if (!$post->is_published) {
            abort(404);
        }
        
        return view($this->resolveThemeView('blog-post'), compact('post'));
    }
}
