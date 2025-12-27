<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.superadmin-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // Check if user is superadmin
        if (!auth()->user()->is_superadmin) {
             Auth::guard('web')->logout();
             return back()->withErrors(['email' => 'Access denied. SuperAdmin privileges required.']);
        }

        return redirect()->intended(route('superadmin.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
