<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorefrontSetting;
use Illuminate\Http\Request;

class StorefrontSettingController extends Controller
{
    public function index()
    {
        // Fetch all settings
        $settings = StorefrontSetting::all()->pluck('value', 'key');

        return view('admin.settings.storefront', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'home_new_arrivals_rows' => 'nullable|integer|min:1|max:4',
            'home_new_arrivals_cols' => 'nullable|integer|min:2|max:6',
            'home_new_arrivals_count' => 'nullable|integer|min:1|max:24',
            
            'home_featured_rows' => 'nullable|integer|min:1|max:4',
            'home_featured_cols' => 'nullable|integer|min:2|max:6',
            'home_featured_count' => 'nullable|integer|min:1|max:24',

            'home_best_sellers_count' => 'nullable|integer|min:1|max:24',
            'home_best_sellers_rows' => 'nullable|integer|min:1|max:4',
            'home_best_sellers_cols' => 'nullable|integer|min:2|max:6',

            'home_recent_viewed_count' => 'nullable|integer|min:1|max:24',
            'home_recent_viewed_rows' => 'nullable|integer|min:1|max:4',
            'home_recent_viewed_cols' => 'nullable|integer|min:2|max:6',

            'social_facebook' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_tiktok' => 'nullable|url',
            'social_youtube' => 'nullable|url',
            'facebook_pixel_id' => 'nullable|string|max:50',
            'enable_product_sharing' => 'nullable|boolean',

            'seo_title_suffix' => 'nullable|string|max:60',
            'seo_meta_description' => 'nullable|string|max:160',
            'seo_meta_keywords' => 'nullable|string|max:255',
            'google_maps_enabled' => 'nullable|boolean',
            'google_maps_embed_iframe' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                StorefrontSetting::set($key, $value);
            }
        }

        return back()->with('success', 'Storefront settings updated successfully!');
    }
}
