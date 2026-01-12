<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerWelcome;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    private function getViewData()
    {
        $tenant = app('tenant');
        
        // Load Configuration
        $config = \App\Models\StoreConfig::firstOrCreate(['id' => 1], [
            'store_name' => $tenant->name,
            'layout_preference' => 'minimal',
            'brand_color' => '#0A2540',
            'is_completed' => false
        ]);

        // Fetch Menu Categories
        $menuCategories = \App\Models\Category::where('is_active', true)->get();

        // Load Schema
        $schema = [];
        if (\Illuminate\Support\Facades\Storage::disk('tenant')->exists('generated_theme_schema.json')) {
            $schema = json_decode(\Illuminate\Support\Facades\Storage::disk('tenant')->get('generated_theme_schema.json'), true);
        }

        return compact('config', 'menuCategories', 'schema');
    }

    public function showLoginForm()
    {
        return view('storefront.auth.login', $this->getViewData());
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('storefront.home', ['tenant' => app('tenant')->slug]));
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function showRegisterForm()
    {
        return view('storefront.auth.register', $this->getViewData());
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Send Welcome Email
        try {
            Mail::to($customer->email)->send(new CustomerWelcome($customer, app('tenant')));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Welcome email failed: ' . $e->getMessage());
        }

        Auth::guard('customer')->login($customer);

        return redirect()->route('storefront.home', ['tenant' => app('tenant')->slug]);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.home', ['tenant' => app('tenant')->slug]);
    }
}
