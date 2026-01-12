<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStorefrontActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app('tenant');

        // Check if the tenant's Plan allows the store
        if (!$tenant->hasFeature('online_store')) {
            abort(404, 'Online store not included in your plan.');
        }

        // Check if the tenant has Toggled ON the store setting
        if (!$tenant->is_storefront_enabled) {
            abort(404, 'The storefront is currently disabled by the owner.');
        }

        return $next($request);
    }
}
