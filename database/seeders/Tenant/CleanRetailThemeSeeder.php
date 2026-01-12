<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CleanRetailThemeSeeder extends Seeder
{
    public function run(): void
    {
        $schema = [
            'injected_data' => [
                'hero_title' => 'Welcome to Our Store',
                'hero_subtitle' => 'Quality products for your lifestyle.',
                'cta_text' => 'Shop Now',
                'contact_info' => [
                    'email' => 'support@example.com',
                    'social_links' => []
                ]
            ],
            'sections' => [
                [
                    'type' => 'hero_banner',
                    'data' => [
                        'title' => 'Welcome to Our Store',
                        'subtitle' => 'Quality products for your lifestyle.',
                        'cta_text' => 'Shop Now',
                        'image' => null
                    ]
                ],
                [
                    'type' => 'product_grid',
                    'mode' => 'new_arrivals',
                    'title' => 'Fresh Drops'
                ]
            ],
            'pages' => [
                'about_us' => [
                    'title' => 'About Us',
                    'content' => '<p>Welcome to our store. We are dedicated to providing the best products and service to our customers.</p>',
                    'mission_title' => 'Our Mission',
                    'mission_text' => 'To bring quality and joy to every customer.'
                ]
            ],
            'policies' => [
                'faq' => "<h2>Frequently Asked Questions</h2>
<p><strong>Q: How long does shipping take?</strong><br>
A: We typically process orders within 1-2 business days. Standard shipping usually takes 3-5 business days.</p>

<p><strong>Q: Do you offer international shipping?</strong><br>
A: Yes, we ship globally! Shipping rates are calculated at checkout.</p>",
                'shipping' => "<h2>Shipping Policy</h2>
<p>All orders are processed within 1-2 business days. Shipping charges for your order will be calculated and displayed at checkout.</p>",
                'returns' => "<h2>Returns & Refunds</h2>
<p>If you are not entirely satisfied with your purchase, we're here to help. You have 30 calendar days to return an item from the date you received it.</p>"
            ]
        ];

        Storage::disk('tenant')->put('generated_theme_schema.json', json_encode($schema, JSON_PRETTY_PRINT));
        Storage::disk('tenant')->put('theme_settings.json', json_encode($schema, JSON_PRETTY_PRINT));
    }
}
