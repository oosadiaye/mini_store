<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // Content Security Policy
        $isLocal = app()->environment('local');
        $csp = "default-src 'self'; ";
        
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://unpkg.com";
        $styleSrc = "'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdnjs.cloudflare.com";
        $connectSrc = "'self' https: ws: wss:";
        
        if ($isLocal) {
            $scriptSrc .= " http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 'unsafe-inline' 'unsafe-eval'";
            $styleSrc .= " http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174 'unsafe-inline'";
            $connectSrc .= " ws://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5174 ws://127.0.0.1:5174 http://localhost:5173 http://127.0.0.1:5173 http://localhost:5174 http://127.0.0.1:5174";
        }

        $csp .= "script-src $scriptSrc; ";
        $csp .= "style-src $styleSrc; ";
        $csp .= "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdnjs.cloudflare.com; ";
        $csp .= "img-src 'self' data: https: blob:; ";
        $csp .= "connect-src $connectSrc; ";

        $response->headers->set('Content-Security-Policy', $csp);
        
        // HSTS (Only if using HTTPS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
