<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenantFromPath
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If tenant is already identified (e.g. by custom domain middleware), skip path check
        if (app()->bound('tenant')) {
            return $next($request);
        }

        // Extract tenant slug from route parameter
        $tenantSlug = $request->route('tenant');
        
        // If no tenant slug in route, skip tenant initialization
        if (!$tenantSlug) {
            return $next($request);
        }
        
        // Find tenant by slug
        $tenant = Tenant::where('slug', $tenantSlug)
            ->where('is_active', true)
            ->first();
        
        // If tenant not found or inactive, return 404
        if (!$tenant) {
            abort(404, 'Store not found');
        }
        
        // Set tenant in application container
        app()->instance('tenant', $tenant);
        
        // Set tenant ID in config for easy access
        config(['app.tenant_id' => $tenant->id]);
        
        // Share tenant with all views
        view()->share('tenant', $tenant);

        // Override Mail Config
        \App\Providers\TenantMailServiceProvider::overrideForTenant($tenant);

        // Set default tenant parameter for all routes
        \Illuminate\Support\Facades\URL::defaults(['tenant' => $tenant->slug]);
        
        // Forget the tenant parameter so it doesn't get passed to controllers
        $request->route()->forgetParameter('tenant');
        
        return $next($request);
    }
}
