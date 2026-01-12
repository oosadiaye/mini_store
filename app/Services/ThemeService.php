<?php

namespace App\Services;

use App\Models\StoreConfig;
use App\Models\Category;

class ThemeService
{
    /**
     * Generate the theme settings array based on configuration.
     * 
     * @param StoreConfig $config
     * @return array
     */
    public function generateThemeSettings(StoreConfig $config)
    {
        // SMART STYLING ENGINE: Industry Presets
        $stylePresets = [
            'fashion' => [
                'fonts' => ['heading' => 'Playfair Display', 'body' => 'Lato'],
                'radius' => '0px',
                'vibe' => 'minimalist',
                'placeholder' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80', // Shopping bags/Fashion
                'contentTemplates' => [
                    'hero_title' => 'Redefine Your Style',
                    'hero_subtitle' => 'Exclusive collections for the modern look.',
                    'cta_text' => 'Shop Lookbook',
                    'banner_image_placeholder' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Fashion Model/Clothes
                    'split_banner' => [
                        'image_left' => 'https://images.unsplash.com/photo-1617137968427-b2b045142ee9?ixlib=rb-4.0.3&auto=format&fit=crop&w=687&q=80', // Men's Fashion
                        'image_right' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80', // Women's Fashion
                        'center_text' => 'His & Hers Collections'
                    ],
                    'about_us' => [
                        'title' => 'Our Digital Runway',
                        'content' => 'Founded to bring runway trends to your doorstep. We believe style is personal, and everyone deserves to express themselves through fashion that feels authentic and bold.',
                        'mission_title' => 'Our Mission',
                        'mission_text' => 'To democratize high fashion and make premium style accessible to everyone, everywhere.',
                        'stats' => [
                            ['label' => 'Years of Style', 'value' => '10+'],
                            ['label' => 'Happy Fashionistas', 'value' => '50k+']
                        ]
                    ]
                ]
            ],
            'electronics' => [
                'fonts' => ['heading' => 'Roboto', 'body' => 'Inter'],
                'radius' => '4px',
                'vibe' => 'dark_mode',
                'placeholder' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80', // Music/Tech
                'contentTemplates' => [
                    'hero_title' => 'Next-Gen Tech is Here',
                    'hero_subtitle' => 'Upgrade your world with the latest gadgets.',
                    'cta_text' => 'View Deals',
                    'banner_image_placeholder' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?ixlib=rb-4.0.3&auto=format&fit=crop&w=2101&q=80', // Electronics/Circuit
                    'split_banner' => [
                        'image_left' => 'https://images.unsplash.com/photo-1512499617640-c74ae3a79d37?ixlib=rb-4.0.3&auto=format&fit=crop&w=1374&q=80', // Lifestyle with phone
                        'image_right' => 'https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80', // Workplace with laptop
                        'center_text' => 'Work & Play'
                    ],
                    'about_us' => [
                        'title' => 'Powering the Future',
                        'content' => 'Empowering your digital life with the latest gadgets and smart solutions. We are tech enthusiasts who believe in the transformative power of innovation.',
                        'mission_title' => 'Our Mission',
                        'mission_text' => 'To connect people with technology that enhances productivity, entertainment, and daily life.',
                        'stats' => [
                            ['label' => 'Inovations Launched', 'value' => '500+'],
                            ['label' => 'Global Users', 'value' => '1M+']
                        ]
                    ]
                ]
            ],
            'grocery' => [
                'fonts' => ['heading' => 'Poppins', 'body' => 'Open Sans'],
                'radius' => '8px',
                'vibe' => 'fresh',
                'placeholder' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1374&q=80', // Groceries/Market
                'contentTemplates' => [
                    'hero_title' => 'Freshness Delivered',
                    'hero_subtitle' => 'Organic produce and daily essentials at your door.',
                    'cta_text' => 'Start Shopping',
                    'banner_image_placeholder' => 'https://images.unsplash.com/photo-1506484381205-c79456958111?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Fruits/Vegetables
                    'split_banner' => [
                        'image_left' => 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80', // Fruits
                        'image_right' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1472&q=80', // Bakery
                        'center_text' => 'Fresh & Baked Daily'
                    ],
                    'about_us' => [
                        'title' => 'Farm to Table',
                        'content' => 'From the farm directly to your table. We prioritize freshness and organic sourcing to ensure your family gets the best nutrition possible.',
                        'mission_title' => 'Our Mission',
                        'mission_text' => 'To support local farmers while delivering fresh, healthy, and sustainable food options to our community.',
                        'stats' => [
                            ['label' => 'Local Farms', 'value' => '25+'],
                            ['label' => 'Daily Deliveries', 'value' => '100+']
                        ]
                    ]
                ]
            ],
            'hardware' => [
                'fonts' => ['heading' => 'Oswald', 'body' => 'Roboto Condensed'],
                'radius' => '2px',
                'vibe' => 'bold',
                'placeholder' => 'https://images.unsplash.com/photo-1581235720704-06d3acfcb36f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1632&q=80', // Tools
                'contentTemplates' => [
                    'hero_title' => 'Build With Confidence',
                    'hero_subtitle' => 'Professional grade tools for every project.',
                    'cta_text' => 'Shop Tools',
                    'banner_image_placeholder' => 'https://images.unsplash.com/photo-1530124566582-a618bc2615dc?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80', // Workshop
                    'split_banner' => [
                        'image_left' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1506&q=80', // Tools on table
                        'image_right' => 'https://images.unsplash.com/photo-1581166397740-acd5334f6a92?ixlib=rb-4.0.3&auto=format&fit=crop&w=687&q=80', // Construction
                        'center_text' => 'Pro Grade Gear'
                    ],
                    'about_us' => [
                        'title' => 'Built to Last',
                        'content' => 'We provide the tools that build the world. Whether you are a DIY enthusiast or a professional contractor, we have the gear you need.',
                        'mission_title' => 'Our Mission',
                        'mission_text' => 'To equip creators and builders with durable, high-quality hardware that gets the job done right.',
                        'stats' => [
                            ['label' => 'Projects Completed', 'value' => '10k+'],
                            ['label' => 'Tool Varieties', 'value' => '5k+']
                        ]
                    ]
                ]
            ]
        ];

        // Fallback
        $style = $stylePresets[$config->industry] ?? [
            'fonts' => ['heading' => 'Inter', 'body' => 'Inter'],
            'radius' => '6px',
            'vibe' => 'standard',
            'placeholder' => 'assets/placeholders/default-product.jpg',
            'contentTemplates' => [
                'hero_title' => "Welcome to {$config->store_name}",
                'hero_subtitle' => "Browse our curated collection of premium products.",
                'cta_text' => 'Start Shopping',
                'banner_image_placeholder' => null,
                'split_banner' => [
                    'image_left' => 'assets/placeholders/split-left.jpg',
                    'image_right' => 'assets/placeholders/split-right.jpg', 
                    'center_text' => 'Exclusive Offers'
                ],
                'about_us' => [
                    'title' => 'Our Story',
                    'content' => 'We started with a simple mission: to provide high-quality products at an affordable price. Our journey began in a small garage, and today we serve customers worldwide.',
                    'mission_title' => 'Our Mission',
                    'mission_text' => 'To provide quality products and exceptional customer service.',
                    'stats' => [
                        ['label' => 'Years Active', 'value' => '5+'],
                        ['label' => 'Happy Customers', 'value' => '1000+']
                    ]
                ]
            ]
        ];

        // LAYOUT COMPOSER: Component Stack Definition
        // Maps layout preference to specific ordered components
        $layoutStrategies = [
            'minimal' => ['components' => ['storefront.search-focus', 'storefront.category-grid', 'storefront.featured-product-carousel']], // Default/Legacy
            // New Presets
            'high_volume' => [
                'components' => [
                    'storefront.hero-banner',
                    'storefront.search-focus', 
                    'storefront.category-grid', 
                    'storefront.featured-product-carousel'
                ],
                'settings' => ['grid_columns' => 6]
            ],
            'brand_showcase' => [
                'components' => [
                    'storefront.hero-banner', 
                    'storefront.text-block', 
                    'storefront.featured-product-carousel', 
                    'storefront.newsletter-signup'
                ],
                'settings' => ['hero_height' => 'full']
            ],
            'quick_order' => [
                'components' => [
                    'storefront.hero-banner', 
                    'storefront.quick-order-table'
                ],
                'settings' => ['hero_height' => 'small']
            ],
        ];

        // Default to minimal/high_volume if not found
        $layoutConfig = $layoutStrategies[$config->layout_preference] ?? $layoutStrategies['high_volume'];

        // Content Injection (The "Gluer" Logic)
        $storeName = $config->store_name ?? 'My Store';
        // Fetch actual visible categories from DB (Live Source of Truth)
        $fullCategories = Category::where('is_visible_online', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id, 
                'name' => $c->name, 
                'slug' => $c->slug,
                'public_display_name' => $c->public_display_name ?? $c->name,
                'image' => $c->image_path
            ])
            ->values()
            ->toArray();

        $highlightCategories = array_slice($fullCategories, 0, 3);

        $injectedData = [
            'hero_title' => $style['contentTemplates']['hero_title'] ?? "Welcome to $storeName",
            'hero_subtitle' => $style['contentTemplates']['hero_subtitle'] ?? "Browse our curated collection of premium products.",
            'highlight_categories' => $highlightCategories,
            'all_visible_categories' => $fullCategories, // User Request: Full list for menu
            'newsletter_title' => "Join the $storeName Community",
            'newsletter_text' => "Get exclusive offers and updates.",
            'banner_image' => $style['contentTemplates']['banner_image_placeholder'] ?? null,
            'cta_text' => $style['contentTemplates']['cta_text'] ?? 'Shop Now',
            'contact_info' => [
                'email' => $config->store_email ?? null,
                'social_links' => $config->social_links ?? []
            ]
        ];

        return [
            'layout_mode' => $config->layout_preference,
            'identity' => [
                'name' => $config->store_name,
                'logo' => $config->logo_path,
                'primary_color' => $config->brand_color,
            ],
            'navigation' => [
                'all_categories' => $fullCategories,
                'menu_items' => collect($fullCategories)->map(function ($cat) {
                    return [
                        'id' => $cat['id'],
                        'label' => $cat['public_display_name'] ?? $cat['name'],
                        'slug' => $cat['slug'],
                    ];
                })->values()->toArray(),
            ],
            'sections' => [
                [
                    'type' => 'hero_banner',
                    'data' => [
                        'title' => $injectedData['hero_title'],
                        'subtitle' => $injectedData['hero_subtitle'],
                        'cta_text' => $injectedData['cta_text'],
                        'image' => $injectedData['banner_image'],
                    ]
                ],
                [
                    'type' => 'product_grid',
                    'mode' => 'new_arrivals',
                    'title' => 'New Arrivals',
                    'layout' => 'slider'
                ],
                [
                    'type' => 'product_grid',
                    'mode' => 'best_sellers',
                    'title' => 'Crowd Favorites'
                ],
                [
                    'type' => 'split_banner',
                    'data' => [
                        'image_left' => $style['contentTemplates']['split_banner']['image_left'] ?? $style['placeholder'],
                        'image_right' => $style['contentTemplates']['split_banner']['image_right'] ?? ($style['contentTemplates']['banner_image_placeholder'] ?? $style['placeholder']),
                        'center_text' => [
                            'title' => 'Limited Edition',
                            'subtitle' => $style['contentTemplates']['split_banner']['center_text'] ?? 'Shop the exclusive collection.',
                            'cta' => 'Shop Now'
                        ]
                    ]
                ]
            ],
            'design' => [
                'fonts' => $style['fonts'],
                'radius' => $style['radius'],
                'vibe' => $style['vibe'],
                'layout_mode' => $config->layout_preference, 
                'components' => $layoutConfig['components'], 
                'layout_settings' => $layoutConfig['settings'] ?? [],
            ],
            'injected_data' => $injectedData,
            'catalog' => [
                'visible_categories' => $fullCategories, 
                'placeholders' => [
                    'product' => $style['placeholder'],
                ],
            ],
            'catalog' => [
                'visible_categories' => $fullCategories, 
                'placeholders' => [
                    'product' => $style['placeholder'],
                ],
            ],
            'pages' => [
                'about_us' => [
                    'title' => $style['contentTemplates']['about_us']['title'],
                    'hero_image' => $style['placeholder'], // Default hero
                    'content' => $style['contentTemplates']['about_us']['content'],
                    'mission_title' => $style['contentTemplates']['about_us']['mission_title'],
                    'mission_text' => $style['contentTemplates']['about_us']['mission_text'],
                    'stats' => $style['contentTemplates']['about_us']['stats']
                ]
            ],
            'generated_at' => now()->toIso8601String(),
        ];
    }
}
