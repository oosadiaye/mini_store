<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Strict Superadmin Check for Central Domain
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
