<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('cart', function ($app) {
            return new \App\Services\CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(['storefront.*', 'components.*'], function ($view) {
            // Check for Preview Mode
            if (request()->has('preview_template_id')) {
                $templateId = request('preview_template_id');
                $template = \App\Models\StorefrontTemplate::find($templateId);
                
                if ($template) {
                    // Get settings for preview theme using theme-scoped query
                    $settings = \App\Models\ThemeSetting::forTheme($template->slug)->first();
                    
                    if (!$settings) {
                        // Create temporary settings object from template defaults
                        $settings = new \App\Models\ThemeSetting();
                        $settings->theme_slug = $template->slug;
                        $settings->settings = $template->default_settings; 
                    }
                    
                    $view->with('themeSettings', $settings);
                    return;
                }
            }
            
            // Default: Active Settings using theme-scoped query
            $themeSlug = \App\Models\ThemeSetting::getActiveThemeSlug();
            $activeSettings = \App\Models\ThemeSetting::forTheme($themeSlug)->first() 
                ?? new \App\Models\ThemeSetting();
            
            $view->with('themeSettings', $activeSettings);
        });
    }
}
