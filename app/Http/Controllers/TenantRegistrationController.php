<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class TenantRegistrationController extends Controller
{
    /**
     * Show tenant registration form
     */
    public function create()
    {
        return view('tenant-registration');
    }

    /**
     * Register a new tenant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tenants'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subdomain' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:domains,domain', 'not_in:admin,superadmin,www,mail,test,localhost'],
            'custom_domain' => ['nullable', 'string', 'max:255', 'unique:domains,domain', 'regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'id' => Str::slug($validated['subdomain']),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'plan' => 'trial',
            'trial_ends_at' => now()->addDays(14), // 14-day trial
        ]);

        // Create subdomain for tenant
        $tenant->domains()->create([
            'domain' => $validated['subdomain'] . '.' . config('app.domain', 'localhost'),
        ]);

        // Create custom domain if provided
        if (!empty($validated['custom_domain'])) {
            $tenant->domains()->create([
                'domain' => $validated['custom_domain'],
            ]);
        }

        // Initialize Tenancy to switch context
        tenancy()->initialize($tenant);

        // Create admin user using Eloquent
        // using forceCreate to bypass fillable protection if 'role' is not in fillable
        \App\Models\User::forceCreate([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        tenancy()->end();

        return redirect()
            ->route('tenant.success')
            ->with('success', 'Your store has been created! You can now login.');
    }

    /**
     * Show success page
     */
    public function success()
    {
        return view('tenant-success');
    }
}
