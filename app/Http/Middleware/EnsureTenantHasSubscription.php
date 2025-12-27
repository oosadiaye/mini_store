<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantHasSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app('tenant');

        if (!$tenant) {
            return $next($request); // Should ideally not happen if IdentifyTenantFromPath runs first
        }

        // Check if on subscription selection page to avoid infinite loop
        if ($request->routeIs('tenant.subscription.*')) {
            return $next($request);
        }

        // Check if tenant has a plan assigned
        if (!$tenant->plan_id) {
             return redirect()->route('tenant.subscription.index', ['tenant' => $tenant->slug]);
        }

        // Optional: Check for expired subscription (if we strictly block access)
        // if (!$tenant->canAccess()) {
        //     return redirect()->route('tenant.subscription.index', ['tenant' => $tenant->slug])
        //         ->with('error', 'Your subscription has expired. Please renew to continue.');
        // }

        return $next($request);
    }
}
