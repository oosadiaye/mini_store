<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class AutoLoginController extends Controller
{
    /**
     * Handle the auto-login request via a signed URL.
     */
    public function login(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired login link.');
        }

        $userId = $request->user_id;
        $user = User::withoutGlobalScope(\App\Scopes\TenantScope::class)->find($userId);

        if (! $user) {
            abort(404, 'User not found.');
        }

        // Log the user in
        Auth::login($user);

        // Redirect to dashboard
        return redirect()->intended(url('/' . $request->tenant_slug . '/admin'));
    }
}
