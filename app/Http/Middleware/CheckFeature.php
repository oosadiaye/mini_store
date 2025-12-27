<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = app('tenant');

        if (!$tenant || !$tenant->hasFeature($feature)) {
            \Illuminate\Support\Facades\Log::warning("Feature access denied: {$feature} for tenant: " . ($tenant->id ?? 'unknown'));
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Feature not available in your plan. Please upgrade.'], 403);
            }

            return redirect()->route('admin.dashboard')->with('error', 'This feature requires an upgrade. Please verify your plan.');
        }

        return $next($request);
    }
}
