<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenantFromSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If tenant is already identified (e.g. by custom domain middleware), skip subdomain check
        if (app()->bound('tenant')) {
            return $next($request);
        }

        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // Basic subdomain logic: subdomain.domain.com
        // We need to exclude 'www' and the main domain itself if accessed directly
        // Assuming central domain is defined in config or env.
        
        // For simplicity, let's assume standard structure: {slug}.domain.com
        // And we ignore if it matches central domain exactly.
        
        $centralDomain = config('app.url'); // e.g., http://mini.tryquot.com
        $centralHost = parse_url($centralDomain, PHP_URL_HOST);

        // If explicitly hitting central domain, skip
        if ($host === $centralHost) {
            return $next($request);
        }

        // Logic: if host ends with central host, the part before it is the subdomain
        if (str_ends_with($host, $centralHost)) {
            $subdomain = substr($host, 0, -strlen('.' . $centralHost));
            
            if ($subdomain && $subdomain !== 'www') {
                 $tenant = Tenant::where('slug', $subdomain)
                    ->where('is_active', true)
                    ->first();

                if ($tenant) {
                    app()->instance('tenant', $tenant);
                    config(['app.tenant_id' => $tenant->id]);
                    view()->share('tenant', $tenant);

                    // Override Mail Config
                    \App\Providers\TenantMailServiceProvider::overrideForTenant($tenant);
                }
            }
        }
        
        return $next($request);
    }
}
