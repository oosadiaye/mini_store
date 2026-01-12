<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // If not logged in or no tenant identified, skip
        if (!$user || !app()->bound('tenant')) {
            return $next($request);
        }

        $tenant = app('tenant');

        // Superadmins can access any tenant
        if ($user->is_superadmin) {
            return $next($request);
        }

        // Check if user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized tenant access.');
        }

        return $next($request);
    }
}
