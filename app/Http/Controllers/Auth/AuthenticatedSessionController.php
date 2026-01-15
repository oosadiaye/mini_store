<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (app()->bound('tenant')) {
            return redirect()->intended(route('admin.dashboard', ['tenant' => app('tenant')->slug]));
        }

        // If logged in from central domain, check if user belongs to a tenant
        $user = $request->user();
        if ($user->tenant_id) {
            $tenant = \App\Models\Tenant::find($user->tenant_id);
            if ($tenant) {
                // If the user is on the central domain but belongs to a tenant, 
                // we redirect them to their specific tenant dashboard.
                // For subdomains, this is easy. For custom domains, later we'll use signed URLs.
                $host = $request->getHost();
                $centralDomain = parse_url(config('app.url'), PHP_URL_HOST);

                if ($host === $centralDomain) {
                    $primaryDomain = $tenant->getPrimaryDomain();
                    
                    // If the primary domain is different from the central domain (custom domain or different subdomain)
                    if ($primaryDomain !== $centralDomain) {
                        $autoLoginUrl = URL::signedRoute('auto-login', [
                            'user_id' => $user->id,
                            'tenant_slug' => $tenant->slug
                        ], now()->addMinutes(1));

                        // Adjust the URL to use the primary domain
                        $autoLoginUrl = str_replace($centralDomain, $primaryDomain, $autoLoginUrl);

                        return redirect()->away($autoLoginUrl);
                    }

                    // Fallback to standard slug-based redirect for subdomains if wildcard cookie is not used
                    return redirect()->intended(url('/' . $tenant->slug . '/admin'));
                }
            }
        }

        // Strict Superadmin Check for Central Domain (only if not a tenant user)
        if (!$request->user()->is_superadmin) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'email' => 'Access denied. Only Superadmins can access this dashboard.',
            ]);
        }

        return redirect()->intended(route('superadmin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($tenant) {
            return redirect()->route('tenant.login', ['tenant' => $tenant->slug]);
        }

        return redirect('/');
    }
}
