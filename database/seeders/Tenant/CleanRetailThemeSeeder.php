<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\StorefrontTemplate;

class CleanRetailThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StorefrontTemplate::create([
            'name' => 'Clean Retail',
            'slug' => 'clean-retail',
            'description' => 'Practical theme optimized for grocery stores, pharmacies, and everyday retail. Features 4-column grid, category filters, and quick add-to-cart functionality.',
            'is_premium' => false,
            'is_active' => true,
            'thumbnail' => '/themes/previews/clean-retail.jpg',
            'default_settings' => [
                'colors' => [
                    'primary' => '#2E7D32',        // Fresh Green
                    'secondary' => '#FFA726',      // Offer Orange
                    'accent' => '#1976D2',         // Info Blue
                    'background' => '#FAFAFA',     // Light Gray
                    'text' => '#212121',           // Dark Gray
                    'success' => '#43A047',        // Available Green
                    'warning' => '#FB8C00',        // Low Stock Orange
                    'error' => '#E53935',          // Out of Stock Red
                    'border' => '#E0E0E0',         // Border Gray
                    'card_bg' => '#FFFFFF',        // Card White
                ],
                'fonts' => [
                    'heading' => "'Inter', sans-serif",
                    'body' => "'Roboto', sans-serif",
                    'price' => "'Roboto Mono', monospace",
                ],
                'layout_settings' => [
                    // Grid Configuration
                    'grid_columns_desktop' => 4,
                    'grid_columns_tablet' => 3,
                    'grid_columns_mobile' => 1,
                    'grid_gap' => '1.5rem',
                    
                    // Product Card Settings
                    'show_stock_status' => true,
                    'show_quick_add' => true,
                    'show_product_rating' => false,
                    'image_aspect_ratio' => '1:1',
                    
                    // Sidebar Settings
                    'show_category_sidebar' => true,
                    'sidebar_width' => '250px',
                    'show_category_count' => true,
                    
                    // Mobile Settings
                    'mobile_layout' => 'list',     // list or grid
                    'sticky_cart' => true,
                    'sticky_header' => true,
                    
                    // Product Display
                    'price_size' => 'large',
                    'show_unit_price' => true,
                    'show_availability' => true,
                    'show_sku' => false,
                    
                    // Cart Settings
                    'show_cart_count' => true,
                    'cart_icon_style' => 'badge',
                    
                    // Typography
                    'heading_weight' => '600',
                    'body_weight' => '400',
                    'price_weight' => '700',
                ],
            ],
        ]);
    }
}
