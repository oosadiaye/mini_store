<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Essential for VPS/Load Balancers
        $middleware->trustProxies(at: '*');
        
        // Remove CSRF exceptions for sensitive endpoints
        $middleware->validateCsrfTokens(except: [
            // No exceptions for login/logout (Security risk)
            // Add external webhooks here if needed
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\CheckInstalled::class,
            \App\Http\Middleware\IdentifyTenantFromCustomDomain::class,
            \App\Http\Middleware\IdentifyTenantFromSubdomain::class,
            \App\Http\Middleware\SecurityHeaders::class, // Global security headers
        ]);

        $middleware->alias([
            'superadmin' => \App\Http\Middleware\CheckSuperAdmin::class,
            'feature' => \App\Http\Middleware\CheckFeature::class,
            'subscription' => \App\Http\Middleware\EnsureTenantHasSubscription::class,
            'tenant.access' => \App\Http\Middleware\TenantAccess::class, // Tenant isolation
            'storefront_active' => \App\Http\Middleware\EnsureStorefrontActive::class,
        ]);
        
        // API Rate Limiting
        $middleware->throttleApi();
        
        // Configure authentication redirects to maintain tenant context
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            // Check for tenant binding first
            if (app()->bound('tenant')) {
                return route('tenant.login', ['tenant' => app('tenant')->slug]);
            }

            // Fallback: Check if URL path implies a tenant admin route
            // Pattern: /{tenant_slug}/admin...
            $segments = $request->segments();
            if (count($segments) >= 2 && $segments[1] === 'admin') {
                $tenantSlug = $segments[0];
                return url($tenantSlug . '/login');
            }

            // Check if the request is for an admin route (Superadmin)
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('login');
            }
            
            // Default to login
            return route('login');
        });
        
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();

            // Check if we're in a tenant context
            if (app()->bound('tenant') && tenant()) {
                return url('/admin/dashboard');
            }

            // Central domain - redirect to superadmin dashboard ONLY if superadmin
            if ($user && $user->is_superadmin) {
                return url('/superadmin/dashboard');
            }

            // Otherwise, redirect to a safe landing page or back home
            return url('/');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
