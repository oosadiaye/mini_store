<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ThemeService;
use App\Services\SecureFileUploader;

class StoreContentController extends Controller
{
    protected $themeService;
    protected $uploader;

    public function __construct(ThemeService $themeService, SecureFileUploader $uploader)
    {
        $this->themeService = $themeService;
        $this->uploader = $uploader;
    }

    /**
     * Show the Store Content settings page.
     */
    public function edit()
    {
        // Load current schema
        $schema = [];
        if (Storage::disk('tenant')->exists('generated_theme_schema.json')) {
            $schema = json_decode(Storage::disk('tenant')->get('generated_theme_schema.json'), true);
        }

        // Extract injected data
        $content = $schema['injected_data'] ?? [];
        $contact = $content['contact_info'] ?? ['email' => '', 'social_links' => []];
        
        // Extract Sections Data
        $sections = collect($schema['sections'] ?? []);
        $splitBanner = $sections->firstWhere('type', 'split_banner')['data'] ?? [];
        $showNewArrivals = $sections->contains(fn($s) => $s['type'] === 'product_grid' && ($s['mode'] ?? '') === 'new_arrivals');
        $showBestSellers = $sections->contains(fn($s) => $s['type'] === 'product_grid' && ($s['mode'] ?? '') === 'best_sellers');
        
        $aboutUs = $schema['pages']['about_us'] ?? [];
        $policies = $schema['policies'] ?? [
            'faq' => '',
            'shipping' => '',
            'returns' => ''
        ];

        // Create config if not exists to act as fallback
        $config = StoreConfig::firstOrCreate(['id' => 1], [
             'store_name' => app('tenant')->name,
             'is_completed' => false
        ]);

        return view('admin.storefront.content', compact('content', 'contact', 'splitBanner', 'showNewArrivals', 'showBestSellers', 'aboutUs', 'policies', 'config'));
    }

    /**
     * Update the store content.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'cta_text' => 'nullable|string|max:50',
            'hero_image' => 'nullable|image|max:4096', // 4MB
            'contact_email' => 'nullable|email',
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required|string',
            'social_links.*.url' => 'required|url',
            // New Fields
            'split_title' => 'nullable|string|max:50',
            'split_image_left' => 'nullable|image|max:4096',
            'split_image_right' => 'nullable|image|max:4096',
            'show_new_arrivals' => 'nullable|boolean',
            'show_new_arrivals' => 'nullable|boolean',
            'show_best_sellers' => 'nullable|boolean',
            // About Us Fields
            'about_title' => 'nullable|string|max:255',
            'about_hero_image' => 'nullable|image|max:4096',
            'about_content' => 'nullable|string',
            'about_mission_title' => 'nullable|string|max:255',
            'about_mission_text' => 'nullable|string',
            'about_stats' => 'nullable|array',
            'about_stats.*.label' => 'required|string',
            'about_stats.*.value' => 'required|string',
            // Policy Fields
            'policy_faq' => 'nullable|string',
            'policy_shipping' => 'nullable|string',
            'policy_returns' => 'nullable|string',
        ]);

        // Load existing schema
        if (!Storage::disk('tenant')->exists('generated_theme_schema.json')) {
             return back()->with('error', 'Theme schema not found. Please run the wizard first.');
        }

        $schema = json_decode(Storage::disk('tenant')->get('generated_theme_schema.json'), true);
        
        // 1. Update Injected Data (Hero, Contact)
        $schema['injected_data']['hero_title'] = $validated['hero_title'];
        $schema['injected_data']['hero_subtitle'] = $validated['hero_subtitle'];
        $schema['injected_data']['cta_text'] = $validated['cta_text'];

        if ($request->hasFile('hero_image')) {
            $path = $this->uploader->upload($request->file('hero_image'), 'branding', 'tenant');
            $schema['injected_data']['banner_image'] = $path;
        }

        $schema['injected_data']['contact_info'] = [
            'email' => $validated['contact_email'],
            'social_links' => $validated['social_links'] ?? []
        ];

        // NEW: Save to DB so Regenerate picks it up
        $config = StoreConfig::firstOrNew();
        $config->store_email = $validated['contact_email'];
        $config->social_links = $validated['social_links'] ?? [];
        $config->save();

        // 2. Rebuild Sections Array
        $existingSections = collect($schema['sections'] ?? []);
        $newSections = [];

        // A. Hero (Always First, lookup existing data or update from injected)
        // Note: The schema structure relies on 'sections' for rendering, but 'injected_data' for content storage in some places.
        // We ensure the section 'hero_banner' uses the updated injected values if we are strictly using the schema for rendering.
        // Updated ThemeService maps injected keys to section data. Here lets just preserve the hero section object but rely on LayoutResolver 
        // using the data. But wait, BrandShowcase uses $section['data']. We must sync them.
        
        $heroSection = [
            'type' => 'hero_banner',
            'data' => [
                'title' => $validated['hero_title'],
                'subtitle' => $validated['hero_subtitle'],
                'cta_text' => $validated['cta_text'],
                'image' => $schema['injected_data']['banner_image'] ?? null
            ]
        ];
        $newSections[] = $heroSection;

        // B. New Arrivals (Toggle)
        if ($request->boolean('show_new_arrivals')) {
            $newSections[] = [
                'type' => 'product_grid',
                'mode' => 'new_arrivals',
                'title' => 'Fresh Drops'
            ];
        }

        // C. Best Sellers (Toggle)
        if ($request->boolean('show_best_sellers')) {
            $newSections[] = [
                'type' => 'product_grid',
                'mode' => 'best_sellers',
                'title' => 'Crowd Favorites'
            ];
        }

        // D. Split Banner (Update Data)
        $oldSplit = $existingSections->firstWhere('type', 'split_banner')['data'] ?? [];
        
        $splitData = [
            'image_left' => $oldSplit['image_left'] ?? null,
            'image_right' => $oldSplit['image_right'] ?? null,
            'center_text' => [
                'title' => $validated['split_title'] ?? ($oldSplit['center_text']['title'] ?? 'Limited'),
                'subtitle' => $oldSplit['center_text']['subtitle'] ?? 'Collection',
                'cta' => $oldSplit['center_text']['cta'] ?? 'Shop Now'
            ]
        ];

        if ($request->hasFile('split_image_left')) {
            $splitData['image_left'] = $this->uploader->upload($request->file('split_image_left'), 'banners', 'tenant');
        }
        if ($request->hasFile('split_image_right')) {
            $splitData['image_right'] = $this->uploader->upload($request->file('split_image_right'), 'banners', 'tenant');
        }

        $newSections[] = [
            'type' => 'split_banner',
            'data' => $splitData
        ];

        $schema['sections'] = $newSections;

        // 3. Update Pages (About Us)
        $existingAbout = $schema['pages']['about_us'] ?? [];
        
        $aboutData = [
            'title' => $validated['about_title'] ?? ($existingAbout['title'] ?? 'Our Story'),
            'hero_image' => $existingAbout['hero_image'] ?? null,
            'content' => $validated['about_content'] ?? ($existingAbout['content'] ?? ''),
            'mission_title' => $validated['about_mission_title'] ?? ($existingAbout['mission_title'] ?? 'Our Mission'),
            'mission_text' => $validated['about_mission_text'] ?? ($existingAbout['mission_text'] ?? ''),
            'stats' => $validated['about_stats'] ?? []
        ];

        if ($request->hasFile('about_hero_image')) {
            $aboutData['hero_image'] = $this->uploader->upload($request->file('about_hero_image'), 'pages', 'tenant');
        }

        $schema['pages']['about_us'] = $aboutData;

        // 4. Update Policies
        $schema['policies'] = [
            'faq' => $validated['policy_faq'] ?? '',
            'shipping' => $validated['policy_shipping'] ?? '',
            'returns' => $validated['policy_returns'] ?? ''
        ];

        // Save back to file
        Storage::disk('tenant')->put('generated_theme_schema.json', json_encode($schema, JSON_PRETTY_PRINT));
        Storage::disk('tenant')->put('theme_settings.json', json_encode($schema, JSON_PRETTY_PRINT));

        return back()->with('success', 'Store content updated successfully.');
    }

    /**
     * Regenerate content from Niche defaults.
     */
    public function regenerate()
    {
        $config = StoreConfig::firstOrFail();
        
        // Use service to generate fresh settings based on config
        $settings = $this->themeService->generateThemeSettings($config);
        
        Storage::disk('tenant')->put('generated_theme_schema.json', json_encode($settings, JSON_PRETTY_PRINT));
        Storage::disk('tenant')->put('theme_settings.json', json_encode($settings, JSON_PRETTY_PRINT));
        
        return back()->with('success', 'Content regenerated from ' . ucfirst($config->industry) . ' defaults.');
    }

    /**
     * Generate content for a policy using "AI" (Smart Templates).
     */
    public function generatePolicy(Request $request)
    {
        $type = $request->input('type');
        $config = StoreConfig::firstOrNew();
        $storeName = app('tenant')->name ?? 'Our Store';
        $industry = $config->industry ?? 'Retail';
        $email = $config->store_email ?? 'support@' . request()->getHost();

        $content = $this->getSmartTemplate($type, $storeName, $industry, $email);

        return response()->json(['content' => $content]);
    }

    private function getSmartTemplate($type, $name, $industry, $email)
    {
        switch ($type) {
            case 'faq':
                return "<h2>Frequently Asked Questions</h2>
<p><strong>Q: How long does shipping take?</strong><br>
A: We typically process orders within 1-2 business days. Standard shipping for {$industry} items usually takes 3-5 business days depending on your location.</p>

<p><strong>Q: Do you offer international shipping?</strong><br>
A: Yes, we ship globally! Shipping rates are calculated at checkout.</p>

<p><strong>Q: Can I return my {$industry} products?</strong><br>
A: Absolutely. We want you to be happy with your purchase. You can return items within 30 days of receipt.</p>

<p><strong>Q: How can I contact support?</strong><br>
A: You can reach us at {$email} or use our Contact page.</p>";

            case 'shipping':
                return "<h2>Shipping Policy</h2>
<p>At <strong>{$name}</strong>, we are committed to delivering your {$industry} products safely and on time.</p>

<h3>Processing Time</h3>
<p>All orders are processed within 1-2 business days. Orders are not shipped or delivered on weekends or holidays.</p>

<h3>Shipping Rates</h3>
<p>Shipping charges for your order will be calculated and displayed at checkout.</p>

<h3>Delivery Estimates</h3>
<ul>
    <li>Standard Shipping: 3-5 business days</li>
    <li>Express Shipping: 1-2 business days</li>
</ul>";

            case 'returns':
                return "<h2>Returns & Refunds</h2>
<p>Thank you for shopping at <strong>{$name}</strong>.</p>

<p>If you are not entirely satisfied with your purchase, we're here to help.</p>

<h3>Returns</h3>
<p>You have 30 calendar days to return an item from the date you received it. To be eligible for a return, your item must be unused and in the same condition that you received it.</p>

<h3>Refunds</h3>
<p>Once we receive your item, we will inspect it and notify you on the status of your refund. If approved, we will initiate a refund to your credit card (or original method of payment).</p>

<h3>Contact Us</h3>
<p>If you have any questions on how to return your item to us, contact us at {$email}.</p>";
            
            default:
                return "";
        }
    }
}
