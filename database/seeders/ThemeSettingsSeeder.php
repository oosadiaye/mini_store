<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThemeSetting;
use App\Models\StorefrontTemplate;

class ThemeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themeDefaults = [
            'clean-retail' => [
                'primary_color' => '#2E7D32',
                'secondary_color' => '#FFA726',
                'accent_color' => '#1976D2',
                'product_card_style' => 'detailed',
                'layout_type' => 'full-width',
                'spacing_scale' => 'normal',
                'border_radius' => 8,
                'show_shadows' => true,
                'enable_animations' => false,
            ],
            'elegant-boutique' => [
                'primary_color' => '#1A1A1A',
                'secondary_color' => '#C9A961',
                'accent_color' => '#8B7355',
                'product_card_style' => 'featured',
                'layout_type' => 'boxed',
                'spacing_scale' => 'relaxed',
                'border_radius' => 2,
                'show_shadows' => true,
                'enable_animations' => true,
            ],
            'modern-minimal' => [
                'primary_color' => '#000000',
                'secondary_color' => '#666666',
                'accent_color' => '#333333',
                'product_card_style' => 'minimal',
                'layout_type' => 'full-width',
                'spacing_scale' => 'relaxed',
                'border_radius' => 0,
                'show_shadows' => false,
                'enable_animations' => false,
            ],
            'organic-fresh' => [
                'primary_color' => '#4A7C59',
                'secondary_color' => '#8B6F47',
                'accent_color' => '#7BA05B',
                'product_card_style' => 'detailed',
                'layout_type' => 'full-width',
                'spacing_scale' => 'normal',
                'border_radius' => 20,
                'show_shadows' => true,
                'enable_animations' => true,
            ],
            'tech-geeks' => [
                'primary_color' => '#00D9FF',
                'secondary_color' => '#7B2CBF',
                'accent_color' => '#0096FF',
                'product_card_style' => 'compact',
                'layout_type' => 'fluid',
                'spacing_scale' => 'compact',
                'border_radius' => 4,
                'show_shadows' => true,
                'enable_animations' => true,
            ],
        ];

        foreach ($themeDefaults as $slug => $settings) {
            $template = StorefrontTemplate::where('slug', $slug)->first();
            
            if ($template) {
                ThemeSetting::updateOrCreate(
                    ['theme_slug' => $slug],
                    array_merge($settings, [
                        'template_id' => $template->id,
                        'is_active' => $slug === 'clean-retail', // Default active theme
                    ])
                );
                
                $this->command->info("✓ Created/Updated settings for theme: {$slug}");
            } else {
                $this->command->warn("⚠ Template not found for slug: {$slug}");
            }
        }
    }
}
