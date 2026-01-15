<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

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
        // Storefront view composer removed

        // Register Announcement/Onboarding View Composer for Tenant Admin Layout
        \Illuminate\Support\Facades\View::composer('layouts.app', \App\Http\View\Composers\ActiveAnnouncementComposer::class);

        // Share categories with storefront layout (for Navigation)
        \Illuminate\Support\Facades\View::composer('components.storefront.layout', function ($view) {
            if (app()->bound('tenant')) {
                $tenant = app('tenant');
                $categories = \App\Models\Category::where('tenant_id', $tenant->id)
                    ->where('is_active', true)
                    ->where('show_on_storefront', true)
                    ->orderBy('sort_order', 'asc')
                    ->get();
                $view->with('menuCategories', $categories);
            }
        });

        // Load Single-DB Tenant Migrations
        $this->loadMigrationsFrom(database_path('migrations/tenant'));

        // Define Global API Rate Limiter (60 requests/min per IP)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Define Login Rate Limiter (5 attempts / 15 mins per IP)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(15, 5)->by($request->ip());
        });

        // Create Application Macros
        
        // Register Observers
        \App\Models\Order::observe(\App\Observers\WooCommerceObserver::class);

        // Configure Mail Settings (Global)
        \App\Services\MailConfigService::configure();
    }
}
