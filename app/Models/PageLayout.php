<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageLayout extends Model
{
    protected $fillable = [
        'page_name',
        'template_id',
        'sections',
        'is_active',
    ];

    public function template()
    {
        return $this->belongsTo(StorefrontTemplate::class);
    }

    protected $casts = [
        'sections' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get sections ordered by their order property
     */
    public function getSectionsByOrder()
    {
        return collect($this->sections)->sortBy('order')->values();
    }

    /**
     * Get only enabled sections
     */
    public function getEnabledSections()
    {
        return $this->getSectionsByOrder()->where('enabled', true);
    }

    /**
     * Get default sections for a page
     */
    public static function getDefaultSections($pageName = 'home')
    {
        $defaults = [
            'home' => [
                ['id' => 'hero-1', 'type' => 'hero', 'enabled' => true, 'order' => 1, 'settings' => []],
                ['id' => 'info-bar-1', 'type' => 'info_bar', 'enabled' => true, 'order' => 2, 'settings' => [
                    'item_1_icon' => 'truck', 'item_1_title' => 'Free Shipping & Returns', 'item_1_text' => 'For all orders over $99',
                    'item_2_icon' => 'dollar-sign', 'item_2_title' => 'Money Back Guarantee', 'item_2_text' => '100% money back guarantee',
                    'item_3_icon' => 'headphones', 'item_3_title' => 'Online Support 24/7', 'item_3_text' => 'Dedicated support',
                    'item_4_icon' => 'credit-card', 'item_4_title' => 'Secure Payment', 'item_4_text' => '100% secure payment',
                ]],
                ['id' => 'categories-1', 'type' => 'categories', 'enabled' => true, 'order' => 3, 'settings' => ['limit' => 6]],
                ['id' => 'flash-sales-1', 'type' => 'flash_sales', 'enabled' => true, 'order' => 4, 'settings' => ['limit' => 4, 'title' => 'Hurry Up Deals', 'end_date' => date('Y-m-d', strtotime('+7 days'))]],
                ['id' => 'new-arrivals-1', 'type' => 'new_arrivals', 'enabled' => true, 'order' => 5, 'settings' => ['limit' => 8]],
                ['id' => 'bundle-1', 'type' => 'bundle_products', 'enabled' => true, 'order' => 6, 'settings' => ['title' => 'Bundle Products']],
                ['id' => 'featured-1', 'type' => 'featured_products', 'enabled' => true, 'order' => 7, 'settings' => ['limit' => 8, 'title' => 'Featured Products']],
                ['id' => 'posts-1', 'type' => 'latest_posts', 'enabled' => true, 'order' => 8, 'settings' => ['limit' => 4, 'title' => 'Latest Posts']],
                ['id' => 'testimonials-1', 'type' => 'testimonials', 'enabled' => true, 'order' => 9, 'settings' => ['limit' => 3]],
                ['id' => 'insta-1', 'type' => 'instagram_feed', 'enabled' => true, 'order' => 10, 'settings' => ['limit' => 6, 'title' => 'From Instagram']],
                ['id' => 'newsletter-1', 'type' => 'newsletter', 'enabled' => true, 'order' => 11, 'settings' => []],
            ],
            'about' => [
                ['id' => 'hero-about-1', 'type' => 'hero', 'enabled' => true, 'order' => 1, 'settings' => []],
                ['id' => 'content-1', 'type' => 'content_block', 'enabled' => true, 'order' => 2, 'settings' => []],
                ['id' => 'stats-1', 'type' => 'stats', 'enabled' => true, 'order' => 3, 'settings' => []],
                ['id' => 'team-1', 'type' => 'team', 'enabled' => true, 'order' => 4, 'settings' => ['limit' => 6]],
                ['id' => 'cta-1', 'type' => 'cta', 'enabled' => true, 'order' => 5, 'settings' => []],
            ],
            'contact' => [
                ['id' => 'hero-contact-1', 'type' => 'hero', 'enabled' => true, 'order' => 1, 'settings' => []],
                ['id' => 'contact-form-1', 'type' => 'contact_form', 'enabled' => true, 'order' => 2, 'settings' => []],
                ['id' => 'social-1', 'type' => 'social_feed', 'enabled' => true, 'order' => 3, 'settings' => []],
            ],
            'shop' => [
                ['id' => 'categories-shop-1', 'type' => 'categories', 'enabled' => true, 'order' => 1, 'settings' => ['limit' => 8]],
                ['id' => 'featured-shop-1', 'type' => 'featured_products', 'enabled' => true, 'order' => 2, 'settings' => ['limit' => 12]],
                ['id' => 'best-sellers-1', 'type' => 'best_sellers', 'enabled' => true, 'order' => 3, 'settings' => ['limit' => 12]],
                ['id' => 'flash-sales-1', 'type' => 'flash_sales', 'enabled' => false, 'order' => 4, 'settings' => ['limit' => 8]],
            ],
        ];

        return $defaults[$pageName] ?? [];
    }
}
