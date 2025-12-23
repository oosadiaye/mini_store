<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // This will run in tenant context automatically
        DB::table('page_layouts')->updateOrInsert(
            ['page_name' => 'home'],
            [
                'page_name' => 'home',
                'is_active' => true,
                'sections' => json_encode([
                    [
                        'type' => 'hero',
                        'title' => 'Welcome to Our Store',
                        'content' => 'Discover Amazing Products at Unbeatable Prices',
                        'enabled' => true,
                        'settings' => [
                            'button_text' => 'Shop Now',
                            'button_link' => '/shop'
                        ],
                        'order' => 1
                    ],
                    [
                        'type' => 'categories',
                        'title' => 'Shop by Category',
                        'content' => 'Browse our wide selection of products',
                        'enabled' => true,
                        'settings' => ['limit' => 6],
                        'order' => 2
                    ],
                    [
                        'type' => 'featured_products',
                        'title' => 'Featured Products',
                        'content' => 'Check out our handpicked selection',
                        'enabled' => true,
                        'settings' => ['limit' => 8],
                        'order' => 3
                    ],
                    [
                        'type' => 'carousel',
                        'title' => 'Trending Now',
                        'content' => 'Popular products this week',
                        'enabled' => true,
                        'settings' => [
                            'items_per_view' => 4,
                            'autoplay' => true,
                            'loop' => true
                        ],
                        'order' => 4
                    ],
                    [
                        'type' => 'stats',
                        'title' => 'Our Achievements',
                        'content' => 'Numbers that speak for themselves',
                        'enabled' => true,
                        'settings' => [],
                        'order' => 5
                    ],
                    [
                        'type' => 'testimonials',
                        'title' => 'What Our Customers Say',
                        'content' => 'Real reviews from real customers',
                        'enabled' => true,
                        'settings' => ['limit' => 6],
                        'order' => 6
                    ],
                    [
                        'type' => 'cta',
                        'title' => 'Join Our Newsletter',
                        'content' => 'Get exclusive deals and updates delivered to your inbox',
                        'enabled' => true,
                        'settings' => [
                            'layout' => 'centered',
                            'button_text' => 'Subscribe Now',
                            'button_link' => '#newsletter'
                        ],
                        'order' => 7
                    ],
                    [
                        'type' => 'new_arrivals',
                        'title' => 'New Arrivals',
                        'content' => 'Fresh products just for you',
                        'enabled' => true,
                        'settings' => ['limit' => 8],
                        'order' => 8
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    public function down()
    {
        DB::table('page_layouts')->where('page_name', 'home')->delete();
    }
};
