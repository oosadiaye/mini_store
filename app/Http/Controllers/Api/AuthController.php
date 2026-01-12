<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\GlobalSetting;
use App\Rules\TurnstileRule;
use App\Rules\RecaptchaRule;

class AuthController extends Controller
{
    /**
     * Handle incoming login request.
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $captchaType = GlobalSetting::where('key', 'captcha_type')->first()?->value ?? 'none';

        if ($captchaType === 'turnstile') {
            $rules['cf-turnstile-response'] = ['required', new TurnstileRule];
        } elseif ($captchaType === 'recaptcha') {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaRule];
        }

        $credentials = $request->validate($rules);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user belongs to a tenant if this is a tenant-context request
            // For now, simple auth
            
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * Handle incoming logout request.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }
}
