@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Theme Customizer</h1>
    </div>

    <form action="{{ route('admin.settings.storefront.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- New Arrivals Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">New Arrivals Section</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Items to Show</label>
                    <input type="number" name="home_new_arrivals_count" value="{{ $settings['home_new_arrivals_count'] ?? 8 }}" min="1" max="24" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rows (Grid Layout)</label>
                    <input type="number" name="home_new_arrivals_rows" value="{{ $settings['home_new_arrivals_rows'] ?? 1 }}" min="1" max="4" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Columns (Visible at once)</label>
                    <input type="number" name="home_new_arrivals_cols" value="{{ $settings['home_new_arrivals_cols'] ?? 4 }}" min="2" max="6" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
        </div>

        <!-- Featured Products Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Featured Products Section</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Items to Show</label>
                    <input type="number" name="home_featured_count" value="{{ $settings['home_featured_count'] ?? 8 }}" min="1" max="24" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rows</label>
                    <input type="number" name="home_featured_rows" value="{{ $settings['home_featured_rows'] ?? 1 }}" min="1" max="4" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Columns</label>
                    <input type="number" name="home_featured_cols" value="{{ $settings['home_featured_cols'] ?? 4 }}" min="2" max="6" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
        </div>

        <!-- Best Sellers Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Best Sellers Section</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Items to Show</label>
                    <input type="number" name="home_best_sellers_count" value="{{ $settings['home_best_sellers_count'] ?? 8 }}" min="1" max="24" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rows</label>
                    <input type="number" name="home_best_sellers_rows" value="{{ $settings['home_best_sellers_rows'] ?? 1 }}" min="1" max="4" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Columns</label>
                    <input type="number" name="home_best_sellers_cols" value="{{ $settings['home_best_sellers_cols'] ?? 4 }}" min="2" max="6" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
        </div>

        <!-- Social Media Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Social Media Links</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-facebook text-blue-600"></i> Facebook URL</label>
                    <input type="url" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}" placeholder="https://facebook.com/yourpage" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-instagram text-pink-600"></i> Instagram URL</label>
                    <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}" placeholder="https://instagram.com/yourpage" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-twitter text-sky-500"></i> Twitter / X URL</label>
                    <input type="url" name="social_twitter" value="{{ $settings['social_twitter'] ?? '' }}" placeholder="https://twitter.com/yourpage" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-tiktok text-black"></i> TikTok URL</label>
                    <input type="url" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}" placeholder="https://tiktok.com/@yourpage" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fab fa-youtube text-red-600"></i> YouTube URL</label>
                    <input type="url" name="social_youtube" value="{{ $settings['social_youtube'] ?? '' }}" placeholder="https://youtube.com/@yourchannel" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
        </div>

        <!-- Marketing Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Marketing & Tracking</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Pixel ID</label>
                    <input type="text" name="facebook_pixel_id" value="{{ $settings['facebook_pixel_id'] ?? '' }}" placeholder="e.g. 1234567890" class="w-full px-3 py-2 border rounded-md">
                    <p class="text-xs text-gray-500 mt-1">Found in your Facebook Business Manager -> Events Manager.</p>
                </div>
                
                <div class="flex items-center space-x-3 mt-8">
                     <input type="hidden" name="enable_product_sharing" value="0">
                     <input type="checkbox" name="enable_product_sharing" value="1" id="share_toggle" {{ ($settings['enable_product_sharing'] ?? 0) ? 'checked' : '' }} class="h-5 w-5 text-blue-600 rounded">
                     <label for="share_toggle" class="text-sm font-medium text-gray-700">Enable Product Sharing Icons (fb, twitter, whatsapp)</label>
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">SEO Settings</h2>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title Suffix</label>
                    <input type="text" name="seo_title_suffix" value="{{ $settings['seo_title_suffix'] ?? '' }}" placeholder="| My Shop Name" class="w-full px-3 py-2 border rounded-md">
                    <p class="text-xs text-gray-500 mt-1">Appended to the page title. Keep it short.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default Meta Description</label>
                    <textarea name="seo_meta_description" rows="3" class="w-full px-3 py-2 border rounded-md" maxlength="160">{{ $settings['seo_meta_description'] ?? '' }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Used if a page doesn't have a specific description. Max 160 chars.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                    <input type="text" name="seo_meta_keywords" value="{{ $settings['seo_meta_keywords'] ?? '' }}" placeholder="shop, fashion, best deals" class="w-full px-3 py-2 border rounded-md">
                    <p class="text-xs text-gray-500 mt-1">Comma separated list of keywords.</p>
                </div>
            </div>
        </div>

        <!-- Google Maps Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Google Maps Integration</h2>
            <div class="space-y-4">
                 <div class="flex items-center space-x-3">
                     <input type="hidden" name="google_maps_enabled" value="0">
                     <input type="checkbox" name="google_maps_enabled" value="1" id="maps_toggle" {{ ($settings['google_maps_enabled'] ?? 0) ? 'checked' : '' }} class="h-5 w-5 text-blue-600 rounded">
                     <label for="maps_toggle" class="text-sm font-medium text-gray-700">Enable Google Map on Contact Page</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Embed Code (Iframe)</label>
                    <textarea name="google_maps_embed_iframe" rows="4" class="w-full px-3 py-2 border rounded-md font-mono text-sm" placeholder='<iframe src="https://www.google.com/maps/embed?..."></iframe>'>{{ $settings['google_maps_embed_iframe'] ?? '' }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Paste the "Embed a map" HTML code from Google Maps here.</p>
                </div>
            </div>
        </div>

        <!-- Recently Viewed Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Recently Viewed Section</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Items to Show</label>
                    <input type="number" name="home_recent_viewed_count" value="{{ $settings['home_recent_viewed_count'] ?? 4 }}" min="1" max="24" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rows</label>
                    <input type="number" name="home_recent_viewed_rows" value="{{ $settings['home_recent_viewed_rows'] ?? 1 }}" min="1" max="4" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Columns</label>
                    <input type="number" name="home_recent_viewed_cols" value="{{ $settings['home_recent_viewed_cols'] ?? 4 }}" min="2" max="6" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg">
                Save Configuration
            </button>
        </div>
    </form>
</div>
@endsection
