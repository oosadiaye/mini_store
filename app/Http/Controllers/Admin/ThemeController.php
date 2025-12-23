<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use App\Models\StorefrontTemplate;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Display the unified theme customizer with page builder
     */
    public function customizer(Request $request)
    {
        $pageName = $request->get('page', 'home');
        
        // Get active theme settings
        $activeSettings = ThemeSetting::active()->first();
        $templateId = $activeSettings ? $activeSettings->template_id : null;
        
        // Get or create page layout
        $query = \App\Models\PageLayout::where('page_name', $pageName)
            ->where('is_active', true);
        
        if ($templateId) {
            $query->where('template_id', $templateId);
        } else {
            $query->whereNull('template_id');
        }
        
        $layout = $query->first();
        
        // If no layout exists, create one with empty sections
        if (!$layout) {
            $layout = \App\Models\PageLayout::create([
                'page_name' => $pageName,
                'template_id' => $templateId,
                'sections' => [],
                'is_active' => true,
            ]);
        }
        
        // Ensure sections is an array
        if (!isset($layout->sections) || !is_array($layout->sections)) {
            $layout->sections = [];
        }
        
        // Get available section types
        $availableSections = $this->getAvailableSections();
        
        return view('admin.theme.customizer', compact('layout', 'availableSections', 'pageName'));
    }
    
    /**
     * Get available section types
     */
    private function getAvailableSections()
    {
        return [
            [
                'type' => 'hero',
                'name' => 'Hero Banner',
                'icon' => '🎨',
                'description' => 'Large banner with image and text',
            ],
            [
                'type' => 'products',
                'name' => 'Product Grid',
                'icon' => '🛍️',
                'description' => 'Display products in a grid',
            ],
            [
                'type' => 'featured_products',
                'name' => 'Featured Products',
                'icon' => '⭐',
                'description' => 'Showcase featured products',
            ],
            [
                'type' => 'categories',
                'name' => 'Categories',
                'icon' => '📁',
                'description' => 'Product categories grid',
            ],
            [
                'type' => 'text',
                'name' => 'Text Block',
                'icon' => '📝',
                'description' => 'Rich text content block',
            ],
            [
                'type' => 'content_block',
                'name' => 'Content Block',
                'icon' => '📄',
                'description' => 'Custom content area',
            ],
        ];
    }

    /**
     * Save customizer changes
     */
    public function saveCustomizer(Request $request)
    {
        $pageName = $request->input('page_name', 'home');
        $sections = $request->input('sections', []);
        
        // Get active theme
        $activeSettings = ThemeSetting::active()->first();
        $templateId = $activeSettings ? $activeSettings->template_id : null;
        
        // Find or create layout
        $layout = \App\Models\PageLayout::where('page_name', $pageName)
            ->where('template_id', $templateId)
            ->where('is_active', true)
            ->first();
        
        if ($layout) {
            $layout->update(['sections' => $sections]);
        } else {
            \App\Models\PageLayout::create([
                'page_name' => $pageName,
                'template_id' => $templateId,
                'sections' => $sections,
                'is_active' => true,
            ]);
        }
        
        return response()->json(['success' => true, 'message' => 'Layout saved successfully']);
    }


    /**
     * Display the theme settings page.
     */
    public function index(Request $request)
    {
        // Seed Premium Themes
        $themes = [
            [
                'name' => 'Modern Minimal',
                'slug' => 'modern-minimal',
                'description' => 'Clean, minimalist design.',
                'is_premium' => false,
                'default_settings' => [
                    'colors' => ['primary' => '#4f46e5', 'secondary' => '#1f2937', 'accent' => '#fbbf24'],
                    'fonts' => ['heading' => 'Inter', 'body' => 'Inter'],
                    'layout_settings' => [
                        'header_style' => 'minimal',
                        'sections' => ['hero' => true, 'category_slider' => true, 'featured_products' => true, 'dual_banners' => true, 'best_sellers' => true, 'testimonials' => true, 'newsletter' => true],
                        'section_order' => ['hero', 'category_slider', 'featured_products', 'dual_banners', 'best_sellers', 'testimonials', 'newsletter'],
                        'visuals' => ['radius' => 8, 'shadow' => 'sm'],
                        'footer' => ['about' => '', 'copyright' => '', 'social' => []]
                    ],
                ]
            ],
            [
                'name' => 'Retail Shop',
                'slug' => 'retail-shop',
                'description' => 'A refined, premium retail theme with shop filters and extensive page layouts.',
                'is_premium' => true,
                'default_settings' => [
                    'colors' => ['primary' => '#0d9488', 'secondary' => '#581c87', 'accent' => '#f59e0b'],
                    'fonts' => ['heading' => 'Playfair Display', 'body' => 'Inter'],
                    'layout_settings' => [
                        'header_style' => 'sticky',
                        'sections' => ['hero' => true, 'categories' => true, 'featured_products' => true],
                        'section_order' => ['hero', 'categories', 'featured_products'],
                        'visuals' => ['radius' => 4, 'shadow' => 'md'],
                        'footer' => ['about' => '', 'copyright' => '', 'social' => []]
                    ],
                ]
            ],
            [
                'name' => 'Electro Retail',
                'slug' => 'electro-retail',
                'description' => 'A tech-focused theme with high information density, countdown deals, and spec-oriented layouts.',
                'is_premium' => true,
                'default_settings' => [
                    'colors' => ['primary' => '#2563eb', 'secondary' => '#0f172a', 'accent' => '#facc15'],
                    'fonts' => ['heading' => 'Oswald', 'body' => 'Roboto'],
                    'layout_settings' => [
                        'header_style' => 'tech-mega',
                        'sections' => ['hero' => true, 'deal_strip' => true, 'categories' => true, 'featured_products' => true, 'trust_badges' => true],
                        'section_order' => ['hero', 'deal_strip', 'categories', 'featured_products', 'trust_badges'],
                        'header_menu' => [
                            ['label' => 'Home', 'url' => '/'],
                            ['label' => 'Shop', 'url' => '/products'],
                            ['label' => 'Categories', 'url' => '/products'],
                            ['label' => 'Deals', 'url' => '#deals'],
                            ['label' => 'Contact', 'url' => '/contact'],
                        ],
                        'visuals' => ['radius' => 0, 'shadow' => 'none'],
                        'footer' => ['about' => '', 'copyright' => '', 'social' => []]
                    ],
                ]
            ]
        ];

        // Cleanup: Delete any templates NOT in this list
        $slugsToKeep = collect($themes)->pluck('slug');

        // Delete theme settings for themes being removed
        ThemeSetting::whereHas('template', function($query) use ($slugsToKeep) {
            $query->whereNotIn('slug', $slugsToKeep);
        })->delete();

        // Now safe to delete
        StorefrontTemplate::whereNotIn('slug', $slugsToKeep)->delete();

        foreach ($themes as $theme) {
            StorefrontTemplate::updateOrCreate(
                ['slug' => $theme['slug']],
                $theme
            );
        }

        // Fetch all templates (including the newly seeded ones)
        // We use all() because we want to list available themes, not just active/premium filtered ones if that logic existed
        $templates = StorefrontTemplate::all();
        
        // Determine Active Settings (Global State)
        $activeSettings = ThemeSetting::where('is_active', true)->with('template')->first();

        // Determine which theme to edit (Context)
        $editSlug = $request->query('edit_theme');
        
        if ($editSlug) {
            $currentTemplate = StorefrontTemplate::where('slug', $editSlug)->firstOrFail();
        } else {
            // Default to active theme, or fall back to Modern Minimal if nothing active
            $currentTemplate = $activeSettings ? $activeSettings->template : StorefrontTemplate::where('slug', 'modern-minimal')->first();
        }

        // Retrieve or Create Settings for the Edited Theme (Isolation)
        // We ensure a record exists for this specific theme slug
        $currentSettings = ThemeSetting::firstOrCreate(
            ['theme_slug' => $currentTemplate->slug, 'template_id' => $currentTemplate->id],
            [
                'colors' => $currentTemplate->default_settings['colors'],
                'fonts' => $currentTemplate->default_settings['fonts'],
                'layout_settings' => $currentTemplate->default_settings['layout_settings'] ?? [],
                'is_active' => $activeSettings && $activeSettings->template_id === $currentTemplate->id, // Maintain active status inheritance on creation
            ]
        );
        
        // Safety: If somehow no active settings exist at all, make the default one active
        if (!$activeSettings && !$editSlug) {
             $currentSettings->update(['is_active' => true]);
             $activeSettings = $currentSettings;
        }

        return view('admin.theme.index', compact('currentSettings', 'activeSettings', 'templates'));
    }

    public function activate(Request $request)
    {
        $request->validate(['template_id' => 'required|exists:storefront_templates,id']);
        
        $template = StorefrontTemplate::findOrFail($request->template_id);
        
        // Deactivate all others
        ThemeSetting::query()->update(['is_active' => false]);
        
        // Find or Create settings for this theme (DO NOT RESET DEFAULTS if exists)
        $settings = ThemeSetting::firstOrCreate(
            ['theme_slug' => $template->slug, 'template_id' => $template->id],
            [
                'colors' => $template->default_settings['colors'],
                'fonts' => $template->default_settings['fonts'],
                'layout_settings' => $template->default_settings['layout_settings'] ?? [],
            ]
        );
        
        $settings->is_active = true;
        $settings->save();

        // Template specific logic (e.g. Porto Tech homepage)
        if ($template->slug === 'porto-tech') {
            \App\Models\PageLayout::updateOrCreate(
                ['page_name' => 'home'],
                ['sections' => \App\Models\PageLayout::getDefaultSections('home')]
            );
        }

        return redirect()->route('admin.theme.index')->with('success', "Activated {$template->name} theme successfully!");
    }

    /**
     * Update the theme settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:storefront_templates,id',
            'colors' => 'array',
            'fonts' => 'array',
            'layout_settings' => 'array',
            'custom_css' => 'nullable|string',
        ]);

        $template = StorefrontTemplate::findOrFail($validated['template_id']);

        // Update strictly the record for this template
        $settings = ThemeSetting::updateOrCreate(
            ['theme_slug' => $template->slug, 'template_id' => $template->id],
            [
                'colors' => $validated['colors'] ?? [],
                'fonts' => $validated['fonts'] ?? [],
                'layout_settings' => $validated['layout_settings'] ?? [],
                'custom_css' => $validated['custom_css'] ?? '',
            ]
        );

        // Keep active status if it was active
        if ($settings->is_active) {
            // No action needed, just ensuring we didn't flip it
        }

        return back()->with('success', "Settings for {$template->name} saved successfully.");
    }
    /**
     * Delete the specified theme.
     */
    public function destroy($id)
    {
        $template = StorefrontTemplate::findOrFail($id);
        
        // Prevent deleting active theme
        $activeSettings = ThemeSetting::where('is_active', true)->first();
        if ($activeSettings && $activeSettings->template_id == $template->id) {
            return back()->with('error', 'Cannot delete the active theme. Please activate another theme first.');
        }

        $template->delete();

        return back()->with('success', 'Theme deleted successfully.');
    }
}
