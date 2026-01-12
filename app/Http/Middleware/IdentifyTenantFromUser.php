<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class IdentifyTenantFromUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If tenant is already identified by URL, stay with it
        if (app()->bound('tenant')) {
            return $next($request);
        }

        $user = $request->user();
        
        if ($user && $user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant) {
                app()->instance('tenant', $tenant);
                config(['app.tenant_id' => $tenant->id]);
            }
        }

        return $next($request);
    }
}
