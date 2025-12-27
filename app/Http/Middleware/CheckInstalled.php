<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the application is already installed
        // We use a simple file check in the storage directory
        $isInstalled = file_exists(storage_path('installed'));

        // If the route is an installation route
        if ($request->is('install') || $request->is('install/*')) {
            // If already installed, redirect to home
            if ($isInstalled) {
                return redirect()->to(url('/'));
            }
            // Otherwise, allow access to installer
            return $next($request);
        }

        // If not installed and trying to access other routes, redirect to installer
        if (!$isInstalled) {
            return redirect()->route('install.welcome');
        }

        return $next($request);
    }
}
