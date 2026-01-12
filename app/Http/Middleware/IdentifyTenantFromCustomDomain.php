<?php

namespace App\Http\Middleware;

use App\Models\CustomDomainRequest;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenantFromCustomDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Check if this is a custom domain
        $customDomain = CustomDomainRequest::where('domain', $host)
            ->where('status', 'approved')
            ->first();
        
        if ($customDomain) {
            $tenant = $customDomain->tenant;
            
            if ($tenant && $tenant->is_active) {
                // Set tenant in application container
                app()->instance('tenant', $tenant);
                config(['app.tenant_id' => $tenant->id]);
                view()->share('tenant', $tenant);

                // Override Mail Config
                \App\Providers\TenantMailServiceProvider::overrideForTenant($tenant);
            }
        }
        
        return $next($request);
    }
}
