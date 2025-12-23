<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/login',
            '/logout',
        ]);
        
        // Configure authentication redirects to maintain tenant context
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            // Check if the request is for an admin route
            if ($request->is('admin') || $request->is('admin/*')) {
                return url('/admin/login');
            }
            
            // Default to customer login for other routes
            return url('/login');
        });
        
        $middleware->redirectUsersTo(function () {
            // Check if we're in a tenant context
            if (function_exists('tenant') && tenant()) {
                return url('/admin/dashboard');
            }
            // Central domain - redirect to superadmin dashboard
            return url('/superadmin/dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
