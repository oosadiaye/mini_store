<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class CustomerAuthController extends Controller
{
    public function showLoginForm()
    {
        $theme = session('theme', 'modern-minimal');
        return view("storefront.themes.{$theme}.login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('storefront.customer.profile'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        $theme = session('theme', 'modern-minimal');
        return view("storefront.themes.{$theme}.signup");
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('customer')->login($customer);

        return redirect(route('storefront.customer.profile'));
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('storefront.home'));
    }

    public function profile()
    {
        $orders = Auth::guard('customer')->user()->orders()->latest()->paginate(5);
        
        return view('storefront.customer.profile', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
        
        $order->load(['items.product', 'shippingAddress']);
        
        return view('storefront.customer.order', compact('order'));
    }
}
