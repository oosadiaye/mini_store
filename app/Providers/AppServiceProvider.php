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
        // Storefront view composer removed

        // Register Announcement/Onboarding View Composer for Tenant Admin Layout
        \Illuminate\Support\Facades\View::composer('layouts.app', \App\Http\View\Composers\ActiveAnnouncementComposer::class);
    }
}
