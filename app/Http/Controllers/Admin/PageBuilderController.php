<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageLayout;
use Illuminate\Http\Request;

use App\Models\StorefrontTemplate;

class PageBuilderController extends Controller
{
    /**
     * Display the page builder interface
     */
    public function index(Request $request)
    {
        $pageName = $request->get('page', 'home');
        
        $activeSettings = \App\Models\ThemeSetting::active()->first();
        $templateId = $activeSettings ? $activeSettings->template_id : null;

        $query = PageLayout::where('page_name', $pageName)
            ->where('is_active', true);

        if ($templateId) {
            $query->where('template_id', $templateId);
        } else {
            $query->whereNull('template_id');
        }

        $layout = $query->first(); // Force fetch fresh
        
        // If no layout exists, create one with defaults
        if (!$layout) {
            $layout = PageLayout::create([
                'page_name' => $pageName,
                'template_id' => $templateId,
                'sections' => PageLayout::getDefaultSections($pageName),
                'is_active' => true,
            ]);
        }

        $availableSections = $this->getAvailableSections();
        $templates = StorefrontTemplate::where('is_active', true)->get();

        return view('admin.page-builder.index', compact('layout', 'availableSections', 'pageName', 'templates'));
    }

    /**
     * Update the page layout
     */
    public function update(Request $request)
    {
        $request->validate([
            'page_name' => 'required|string',
            'sections' => 'required|array',
        ]);

        $activeSettings = \App\Models\ThemeSetting::active()->first();
        $templateId = $activeSettings ? $activeSettings->template_id : null;

        $layout = PageLayout::updateOrCreate(
            [
                'page_name' => $request->page_name,
                'template_id' => $templateId 
            ],
            [
                'sections' => $request->sections,
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Layout saved successfully!',
            'layout' => $layout,
        ]);
    }
    
    // ... saveAsTemplate and loadTemplate ...

    /**
     * Reset layout to defaults
     */
    public function reset(Request $request)
    {
        $pageName = $request->get('page', 'home');
        
        $activeSettings = \App\Models\ThemeSetting::active()->first();
        $templateId = $activeSettings ? $activeSettings->template_id : null;

        $layout = PageLayout::updateOrCreate(
            [
                'page_name' => $pageName,
                'template_id' => $templateId
            ],
            [
                'sections' => PageLayout::getDefaultSections($pageName),
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Layout reset to defaults!',
            'layout' => $layout,
        ]);
    }
    /**
     * Render sections for live preview
     */
    public function render(Request $request)
    {
        $sections = collect($request->input('sections', []));
        $device = $request->input('device', 'desktop');

        // Use Shortcode Helper
        $sections = $sections->map(function ($section) {
            $section['settings'] = \App\Helpers\ShortcodeHelper::parse($section['settings'] ?? []);
            if (isset($section['title'])) {
                $section['title'] = \App\Helpers\ShortcodeHelper::parse($section['title']);
            }
            if (isset($section['content'])) {
                $section['content'] = \App\Helpers\ShortcodeHelper::parse($section['content']);
            }
            return $section;
        });

        // Load data needed (Similar to StorefrontController)
        $data = $this->loadSectionData($sections);
        $data['sections'] = $sections;

        // Generate dynamic CSS
        $css = $this->generateDynamicCSS($sections->toArray(), $device);

        // Render just the loop
        $html = view('admin.page-builder.renderer', $data)->render();

        return response()->json([
            'html' => $html,
            'css' => $css
        ]);
    }

    protected function loadSectionData($sections)
    {
        $data = [];

        foreach ($sections as $section) {
            if (isset($section['enabled']) && !$section['enabled']) continue;

            $type = $section['type'];
            $settings = $section['settings'] ?? [];
            $limit = $settings['limit'] ?? 8;

            if (isset($data[$type])) continue;
            // Simplified loading for preview - reuse Models
            // We use full namespacing/Models as typical
            try {
                switch ($type) {
                    case 'featured_products':
                    case 'products':
                        $data['featured_products'] = \App\Models\Product::where('is_active', true)
                            ->when(isset($settings['category_id']), function($q) use ($settings) {
                                return $q->where('category_id', $settings['category_id']);
                            })
                            ->inRandomOrder()->take($limit)->get();
                        break;
                    case 'new_arrivals':
                        $data['new_arrivals'] = \App\Models\Product::where('is_active', true)->latest()->take($limit)->get();
                        break;
                    case 'flash_sales':
                         // Simplified scope
                         $data['flash_sales'] = \App\Models\Product::where('is_active', true)->where('is_flash_sale', true)->take($limit)->get();
                        break;
                    case 'best_sellers':
                        $data['best_sellers'] = \App\Models\Product::where('is_active', true)->take($limit)->get(); // Mock logic for speed
                        break;
                     case 'categories':
                        $data['categories'] = \App\Models\Category::where('is_active', true)->where('is_featured', true)->take($limit)->get();
                        break;
                }
            } catch (\Exception $e) {
                // Ignore errors in preview to prevent crash
            }
        }
        return $data;
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240', // Max 10MB
        ]);

        if ($request->file('image')) {
            $file = $request->file('image');
            
            // Store in tenant's public disk
            $path = $file->store("uploads/page-builder", 'public');
            
            // Use the tenant media route to serve the file
            $url = '/media?path=' . urlencode($path);
            
            // Get image dimensions
            $fullPath = storage_path('app/public/' . $path);
            $dimensions = @getimagesize($fullPath);
            
            // Generate thumbnail for faster preview (optional)
            $thumbnailUrl = $url; // For now, use same URL
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'thumbnail' => $thumbnailUrl,
                'dimensions' => [
                    'width' => $dimensions[0] ?? null,
                    'height' => $dimensions[1] ?? null,
                ],
                'size' => $file->getSize(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
    }

    /**
     * Generate dynamic CSS from section settings
     */
    public function generateDynamicCSS(array $sections, string $device = 'desktop'): string
    {
        $css = '';
        
        foreach ($sections as $section) {
            $id = $section['id'] ?? uniqid('section-');
            $s = $section['settings'] ?? [];
            
            // Skip disabled sections
            if (isset($section['enabled']) && !$section['enabled']) {
                continue;
            }
            
            // Check device-specific visibility
            if ($device === 'desktop' && ($s['hide_on_desktop'] ?? false)) {
                $css .= "#{$id} { display: none !important; }\n";
                continue;
            }
            if ($device === 'mobile' && ($s['hide_on_mobile'] ?? false)) {
                $css .= "@media (max-width: 768px) { #{$id} { display: none !important; } }\n";
                continue;
            }
            
            // Generate CSS based on device
            if ($device === 'desktop') {
                $css .= $this->generateDesktopCSS($id, $s);
            } else {
                $css .= $this->generateMobileCSS($id, $s);
            }
        }
        
        return $css;
    }
    
    /**
     * Generate desktop-specific CSS
     */
    private function generateDesktopCSS(string $id, array $s): string
    {
        $css = "#{$id} {\n";
        
        // Dimensions
        if (isset($s['min_height_desktop'])) {
            $css .= "  min-height: {$s['min_height_desktop']}px;\n";
        }
        if (isset($s['max_width'])) {
            $css .= "  max-width: {$s['max_width']}px;\n";
        }
        
        // Spacing - Padding
        $paddingTop = $s['padding_top_desktop'] ?? $s['padding_top'] ?? null;
        $paddingRight = $s['padding_right_desktop'] ?? $s['padding_right'] ?? null;
        $paddingBottom = $s['padding_bottom_desktop'] ?? $s['padding_bottom'] ?? null;
        $paddingLeft = $s['padding_left_desktop'] ?? $s['padding_left'] ?? null;
        
        if ($paddingTop !== null || $paddingRight !== null || $paddingBottom !== null || $paddingLeft !== null) {
            $css .= "  padding: ";
            $css .= ($paddingTop ?? 0) . "px ";
            $css .= ($paddingRight ?? 0) . "px ";
            $css .= ($paddingBottom ?? 0) . "px ";
            $css .= ($paddingLeft ?? 0) . "px;\n";
        }
        
        // Typography
        if (isset($s['title_font_size_desktop'])) {
            $css .= "  font-size: {$s['title_font_size_desktop']}px;\n";
        } elseif (isset($s['font_size_desktop'])) {
            $css .= "  font-size: {$s['font_size_desktop']}px;\n";
        }
        
        if (isset($s['title_font_weight'])) {
            $css .= "  font-weight: {$s['title_font_weight']};\n";
        }
        
        if (isset($s['title_line_height'])) {
            $css .= "  line-height: {$s['title_line_height']};\n";
        } elseif (isset($s['line_height'])) {
            $css .= "  line-height: {$s['line_height']};\n";
        }
        
        if (isset($s['title_letter_spacing'])) {
            $css .= "  letter-spacing: {$s['title_letter_spacing']}px;\n";
        }
        
        if (isset($s['title_color'])) {
            $css .= "  color: {$s['title_color']};\n";
        } elseif (isset($s['text_color'])) {
            $css .= "  color: {$s['text_color']};\n";
        }
        
        if (isset($s['title_text_align_desktop'])) {
            $css .= "  text-align: {$s['title_text_align_desktop']};\n";
        } elseif (isset($s['text_align_desktop'])) {
            $css .= "  text-align: {$s['text_align_desktop']};\n";
        }
        
        // Background
        if (isset($s['background_color'])) {
            $css .= "  background-color: {$s['background_color']};\n";
        }
        
        if (isset($s['background_image']) && !empty($s['background_image'])) {
            $css .= "  background-image: url('{$s['background_image']}');\n";
            $css .= "  background-size: " . ($s['background_size'] ?? 'cover') . ";\n";
            
            $posX = $s['background_position_x'] ?? 50;
            $posY = $s['background_position_y'] ?? 50;
            $css .= "  background-position: {$posX}% {$posY}%;\n";
        }
        
        $css .= "}\n";
        
        // Overlay (using ::before pseudo-element)
        if (isset($s['overlay_color']) && isset($s['overlay_opacity']) && $s['overlay_opacity'] > 0) {
            $opacity = $s['overlay_opacity'] / 100;
            $css .= "#{$id}::before {\n";
            $css .= "  content: '';\n";
            $css .= "  position: absolute;\n";
            $css .= "  inset: 0;\n";
            $css .= "  background-color: {$s['overlay_color']};\n";
            $css .= "  opacity: {$opacity};\n";
            $css .= "  pointer-events: none;\n";
            $css .= "}\n";
        }
        
        return $css;
    }
    
    /**
     * Generate mobile-specific CSS
     */
    private function generateMobileCSS(string $id, array $s): string
    {
        $css = "@media (max-width: 768px) {\n";
        $css .= "  #{$id} {\n";
        
        // Dimensions
        if (isset($s['min_height_mobile'])) {
            $css .= "    min-height: {$s['min_height_mobile']}px;\n";
        }
        
        // Spacing - Padding
        $paddingTop = $s['padding_top_mobile'] ?? null;
        $paddingRight = $s['padding_right_mobile'] ?? null;
        $paddingBottom = $s['padding_bottom_mobile'] ?? null;
        $paddingLeft = $s['padding_left_mobile'] ?? null;
        
        if ($paddingTop !== null || $paddingRight !== null || $paddingBottom !== null || $paddingLeft !== null) {
            $css .= "    padding: ";
            $css .= ($paddingTop ?? 0) . "px ";
            $css .= ($paddingRight ?? 0) . "px ";
            $css .= ($paddingBottom ?? 0) . "px ";
            $css .= ($paddingLeft ?? 0) . "px;\n";
        }
        
        // Typography
        if (isset($s['title_font_size_mobile'])) {
            $css .= "    font-size: {$s['title_font_size_mobile']}px;\n";
        } elseif (isset($s['font_size_mobile'])) {
            $css .= "    font-size: {$s['font_size_mobile']}px;\n";
        }
        
        if (isset($s['title_text_align_mobile'])) {
            $css .= "    text-align: {$s['title_text_align_mobile']};\n";
        } elseif (isset($s['text_align_mobile'])) {
            $css .= "    text-align: {$s['text_align_mobile']};\n";
        }
        
        $css .= "  }\n";
        $css .= "}\n";
        
        return $css;
    }

    /**
     * Get available section types
     */
    private function getAvailableSections()
    {
        return [
            // Existing sections
            [
                'type' => 'hero',
                'name' => 'Hero Banner',
                'icon' => '🎨',
                'color' => 'from-purple-500 to-pink-500',
                'description' => 'Large banner with image and text',
            ],
            [
                'type' => 'categories',
                'name' => 'Categories',
                'icon' => '📁',
                'color' => 'from-blue-500 to-cyan-500',
                'description' => 'Product categories grid/carousel',
            ],
            [
                'type' => 'featured_products',
                'name' => 'Featured Products',
                'icon' => '⭐',
                'color' => 'from-amber-500 to-yellow-500',
                'description' => 'Showcase featured products',
            ],
            [
                'type' => 'flash_sales',
                'name' => 'Flash Sales',
                'icon' => '⚡',
                'color' => 'from-red-500 to-orange-500',
                'description' => 'Limited time offers with countdown',
            ],
            [
                'type' => 'new_arrivals',
                'name' => 'New Arrivals',
                'icon' => '🆕',
                'color' => 'from-green-500 to-emerald-500',
                'description' => 'Latest products',
            ],
            [
                'type' => 'best_sellers',
                'name' => 'Best Sellers',
                'icon' => '🏆',
                'color' => 'from-indigo-500 to-purple-500',
                'description' => 'Top selling products',
            ],
            [
                'type' => 'newsletter',
                'name' => 'Newsletter',
                'icon' => '📧',
                'color' => 'from-pink-500 to-rose-500',
                'description' => 'Email subscription form',
            ],
            [
                'type' => 'custom_html',
                'name' => 'Custom HTML',
                'icon' => '💻',
                'color' => 'from-gray-500 to-slate-500',
                'description' => 'Custom HTML content block',
            ],
            
            // Porto / Modern Sections
            [
                'type' => 'info_bar',
                'name' => 'Info / Service Bar',
                'icon' => 'ℹ️',
                'color' => 'from-blue-400 to-indigo-400',
                'description' => 'Service icons strip (Shipping, Support, etc)',
            ],
            [
                'type' => 'bundle_products',
                'name' => 'Bundle Offers',
                'icon' => '📦',
                'color' => 'from-indigo-500 to-violet-500',
                'description' => 'Promote bulk purchases',
            ],
            [
                'type' => 'latest_posts',
                'name' => 'Latest Blog Posts',
                'icon' => '📰',
                'color' => 'from-slate-600 to-gray-600',
                'description' => 'Show recent blog articles',
            ],
            [
                'type' => 'instagram_feed',
                'name' => 'Instagram Feed',
                'icon' => '📸',
                'color' => 'from-pink-500 to-rose-500',
                'description' => 'Grid of Instagram photos',
            ],
            
            // Priority Modern Components
            [
                'type' => 'slider',
                'name' => 'Image Slider',
                'icon' => '🖼️',
                'color' => 'from-violet-500 to-purple-500',
                'description' => 'Full-width image slider with navigation',
            ],
            [
                'type' => 'carousel',
                'name' => 'Carousel',
                'icon' => '🎠',
                'color' => 'from-sky-500 to-blue-500',
                'description' => 'Multi-item carousel for content',
            ],
            [
                'type' => 'accordion',
                'name' => 'Accordion',
                'icon' => '📋',
                'color' => 'from-teal-500 to-cyan-500',
                'description' => 'Expandable content sections',
            ],
            [
                'type' => 'tabs',
                'name' => 'Tabs',
                'icon' => '📑',
                'color' => 'from-orange-500 to-amber-500',
                'description' => 'Tabbed content interface',
            ],
            
            // Additional Modern Components
            [
                'type' => 'testimonials',
                'name' => 'Testimonials',
                'icon' => '💬',
                'color' => 'from-emerald-500 to-green-500',
                'description' => 'Customer reviews and ratings',
            ],
            [
                'type' => 'pricing',
                'name' => 'Pricing Tables',
                'icon' => '💰',
                'color' => 'from-yellow-500 to-orange-500',
                'description' => 'Product/service pricing cards',
            ],
            [
                'type' => 'team',
                'name' => 'Team Members',
                'icon' => '👥',
                'color' => 'from-blue-500 to-indigo-500',
                'description' => 'Team profiles with photos',
            ],
            [
                'type' => 'stats',
                'name' => 'Stats Counter',
                'icon' => '📊',
                'color' => 'from-cyan-500 to-teal-500',
                'description' => 'Animated statistics display',
            ],
            [
                'type' => 'gallery',
                'name' => 'Image Gallery',
                'icon' => '🖼️',
                'color' => 'from-fuchsia-500 to-pink-500',
                'description' => 'Photo gallery with lightbox',
            ],
            [
                'type' => 'video',
                'name' => 'Video Section',
                'icon' => '🎥',
                'color' => 'from-red-500 to-pink-500',
                'description' => 'Embedded video with overlay',
            ],
            [
                'type' => 'cta',
                'name' => 'Call to Action',
                'icon' => '📢',
                'color' => 'from-lime-500 to-green-500',
                'description' => 'Prominent CTA banner',
            ],
            [
                'type' => 'contact_form',
                'name' => 'Contact Form',
                'icon' => '✉️',
                'color' => 'from-indigo-500 to-blue-500',
                'description' => 'Contact form with validation',
            ],
            [
                'type' => 'social_feed',
                'name' => 'Social Media',
                'icon' => '🔗',
                'color' => 'from-purple-500 to-violet-500',
                'description' => 'Social media links/icons',
            ],
            [
                'type' => 'content_block',
                'name' => 'Content Block',
                'icon' => '📝',
                'color' => 'from-slate-500 to-gray-500',
                'description' => 'Rich text with images',
            ],
            [
                'type' => 'spacer',
                'name' => 'Spacer',
                'icon' => '↕️',
                'color' => 'from-gray-400 to-gray-500',
                'description' => 'Visual spacing element',
            ],
        ];
    }
}
