<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Models\GlobalSetting;
use App\Rules\TurnstileRule;
use App\Rules\RecaptchaRule;

class TenantRegistrationController extends Controller
{
    /**
     * Show tenant registration form
     */
    public function create()
    {
        $branding = \App\Models\GlobalSetting::where('group', 'branding')->pluck('value', 'key');
        return view('tenant-registration', compact('branding'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'store_name' => ['required', 'string', 'max:255', 'unique:tenants,name'],
            'subdomain' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:tenants,slug'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        $captchaType = GlobalSetting::where('key', 'captcha_type')->first()?->value ?? 'none';

        if ($captchaType === 'turnstile') {
            $rules['cf-turnstile-response'] = ['required', new TurnstileRule];
        } elseif ($captchaType === 'recaptcha') {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaRule];
        }

        $validated = $request->validate($rules);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $tenantId = $validated['subdomain']; // Use subdomain as ID/Slug

            // Create Tenant
            $tenant = Tenant::create([
                'id' => $tenantId, // ID is used as foreign key
                'slug' => $tenantId,
                'name' => $validated['store_name'],
                'email' => $validated['email'],
                'plan' => 'free',
                'is_active' => true,
                'is_active' => true,
                'data' => [
                    'currency_code' => 'NGN',
                    'currency_symbol' => '₦',
                    // Default PWA Settings
                    'pwa_name' => $validated['store_name'],
                    'pwa_short_name' => Str::limit($validated['store_name'], 12, ''),
                    'pwa_theme_color' => '#4f46e5',
                    'pwa_background_color' => '#ffffff',
                ],
            ]);

            // Create Default Warehouse
            \App\Models\Warehouse::create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name . ' - Main',
                'code' => 'MAIN', 
                'is_active' => true,
            ]);

            // Create Default Category
            \App\Models\Category::create([
                'tenant_id' => $tenant->id,
                'name' => 'General',
                'slug' => 'general',
                'is_active' => true,
                'show_on_storefront' => true,
            ]);

            // Create Default Supplier
            \App\Models\Supplier::create([
                'tenant_id' => $tenant->id,
                'name' => 'General Supplier',
                'company_name' => 'General Supplier',
                'email' => $validated['email'], // Use tenant email for default supplier contact
                'phone' => $validated['phone'],
                'is_active' => true,
            ]);

            // Seed Chart of Accounts (All GL Accounts across all modules)
            app()->instance('tenant', $tenant); // Set tenant context for seeder
            (new \Database\Seeders\Tenant\ChartOfAccountsSeeder())->run();

            // Create Domain
            // Even with path-based routing, we might want to store the "intended" subdomain or custom domain
            // for future use. For now, we rely on the slug in the tenant table.
            
            // Create Admin User for this Tenant
            // We set the tenant_id directly on the user in the central database
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'role' => 'admin',
                'tenant_id' => $tenant->id,
            ]);

            \Illuminate\Support\Facades\DB::commit();

            // Send Welcome Email
            $user->notify(new \App\Notifications\TenantCreated($tenant, $validated['password']));

            // Login the user immediately
            \Illuminate\Support\Facades\Auth::login($user);

            // Redirect to subscription selection
            return redirect()->route('tenant.subscription.index', ['tenant' => $tenant->slug])
                ->with('success', 'Store created! Please select a subscription plan to continue.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Failed to create store: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show success page
     */
    public function success()
    {
        return view('tenant-success');
    }
}
