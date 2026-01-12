<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\SecureFileUploader;

class SettingsController extends Controller
{
    /**
     * @var SecureFileUploader
     */
    protected $uploader;

    public function __construct(SecureFileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function index()
    {
        $tenant = app('tenant');
        // Access JSON data column safely
        $settings = $tenant->data ?? [];
        $paymentTypes = \App\Models\PaymentType::with('account')->get();
        $assetAccounts = \App\Models\Account::where('account_type', 'asset')->where('is_active', true)->orderBy('account_code')->get();

        return view('admin.settings.index', compact('tenant', 'settings', 'paymentTypes', 'assetAccounts'));
    }

    public function update(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Settings Update Request Initiated', [
            'all' => $request->except(['logo', 'hero_banner', 'favicon']),
            'files' => $request->allFiles(),
            'hero_banner_file' => $request->file('hero_banner') ? [
                'valid' => $request->file('hero_banner')->isValid(),
                'size' => $request->file('hero_banner')->getSize(),
                'mime' => $request->file('hero_banner')->getMimeType(),
                'error' => $request->file('hero_banner')->getError(),
            ] : 'Not present',
        ]);

        try {
            $request->validate([
            'store_name' => 'required|string|max:255',
            'currency_code' => 'nullable|string|max:3',
            'currency_symbol' => 'nullable|string|max:10',
            'logo' => 'nullable|image|max:5120', // 5MB Max
            'favicon' => 'nullable|image|max:2048', // 2MB Max
            'hero_banner' => 'nullable|image|max:10240', // 10MB Max
            'primary_color' => 'nullable|string|max:7',
            'hide_powered_by' => 'nullable|boolean',
            'guest_checkout' => 'nullable|boolean',
            // SMTP
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            // Pixels
            'facebook_pixel_id' => 'nullable|string',
            'google_analytics_id' => 'nullable|string',
            'tiktok_pixel_id' => 'nullable|string',
            // PWA
            'pwa_name' => 'nullable|string',
            'pwa_short_name' => 'nullable|string',
            'pwa_theme_color' => 'nullable|string',
            'pwa_background_color' => 'nullable|string',
            'pwa_icon' => 'nullable|image|max:1024',
            // Tax
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'enable_pos_tax' => 'nullable|boolean',
            'enable_purchase_tax' => 'nullable|boolean',
            // Contact Info
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email' => 'nullable|email',
            // Defaults
            'po_prefix' => 'nullable|string|max:10',
            'invoice_prefix' => 'nullable|string|max:10',
            // Shipping
            'shipping_cost' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|max:5120',
            // Geo
            'store_country' => 'nullable|string|max:100',
            'store_region' => 'nullable|string|max:100',
            'store_timezone' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|string|max:500',
        ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Settings Validation Failed', $e->errors());
            if ($request->wantsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $tenant = app('tenant');
        $data = $tenant->data ?? [];
        
        // Settings to update mapped from request
        $settingsToUpdate = [
            'currency_code' => $request->currency_code,
            'currency_symbol' => $request->currency_symbol,
            'company_address' => $request->company_address,
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email,
            'po_prefix' => $request->po_prefix,
            'invoice_prefix' => $request->invoice_prefix,
            'shipping_cost' => $request->shipping_cost,
            'free_shipping_threshold' => $request->free_shipping_threshold,
            'primary_color' => $request->primary_color,
            'hide_powered_by' => $request->boolean('hide_powered_by'),
            'guest_checkout' => $request->boolean('guest_checkout'),
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,
            'mail_encryption' => $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,
            'facebook_pixel_id' => $request->facebook_pixel_id,
            'google_analytics_id' => $request->google_analytics_id,
            'tiktok_pixel_id' => $request->tiktok_pixel_id,
            'pwa_name' => $request->pwa_name,
            'pwa_short_name' => $request->pwa_short_name,
            'pwa_theme_color' => $request->pwa_theme_color,
            'pwa_background_color' => $request->pwa_background_color,
            'tax_rate' => $request->tax_rate,
            'enable_pos_tax' => $request->boolean('enable_pos_tax'),
            'enable_purchase_tax' => $request->boolean('enable_purchase_tax'),
            // Hero Banner Text
            'hero_badge' => $request->hero_badge,
            'hero_heading' => $request->hero_heading,
            'hero_description' => $request->hero_description,
            'hero_button_text' => $request->hero_button_text,
            // Side Banners Text
            'side_banner_1_title' => $request->side_banner_1_title,
            'side_banner_1_link_text' => $request->side_banner_1_link_text,
            'side_banner_2_title' => $request->side_banner_2_title,
            'side_banner_2_link_text' => $request->side_banner_2_link_text,
            // SEO
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            // Geo
            'store_country' => $request->store_country,
            'store_region' => $request->store_region,
            'store_timezone' => $request->store_timezone,
            'google_maps_url' => $request->google_maps_url,
        ];

        // Payment Gateways
        $gateways = ['opay', 'moniepoint', 'paystack', 'flutterwave'];
        foreach ($gateways as $gw) {
            $settingsToUpdate["gateway_{$gw}_active"] = $request->boolean("gateway_{$gw}_active");
            $settingsToUpdate["gateway_{$gw}_public_key"] = $request->input("gateway_{$gw}_public_key");
            $settingsToUpdate["gateway_{$gw}_secret_key"] = $request->input("gateway_{$gw}_secret_key");
            $settingsToUpdate["gateway_{$gw}_merchant_id"] = $request->input("gateway_{$gw}_merchant_id");
        }

        // File Uploads
        if ($request->hasFile('logo')) {
            $settingsToUpdate['logo'] = $this->uploader->upload($request->file('logo'), 'page-assets/branding', 'public');
            \Log::info("Logo uploaded. Stored path: " . $settingsToUpdate['logo']);
        }
        if ($request->hasFile('favicon')) {
            $settingsToUpdate['favicon'] = $this->uploader->upload($request->file('favicon'), 'page-assets/branding', 'public');
            \Log::info("Favicon uploaded. Stored path: " . $settingsToUpdate['favicon']);
        }
        if ($request->hasFile('hero_banner')) {
            $settingsToUpdate['hero_banner'] = $this->uploader->upload($request->file('hero_banner'), 'page-assets/branding', 'tenant');
            \Log::info("Hero banner uploaded. Stored path: " . $settingsToUpdate['hero_banner']);
        }
        if ($request->hasFile('side_banner_1')) {
            $settingsToUpdate['side_banner_1'] = $this->uploader->upload($request->file('side_banner_1'), 'page-assets/banners', 'tenant');
            \Log::info("Side banner 1 uploaded. Stored path: " . $settingsToUpdate['side_banner_1']);
        }
        if ($request->hasFile('side_banner_2')) {
            $settingsToUpdate['side_banner_2'] = $this->uploader->upload($request->file('side_banner_2'), 'page-assets/banners', 'tenant');
            \Log::info("Side banner 2 uploaded. Stored path: " . $settingsToUpdate['side_banner_2']);
        }
        if ($request->hasFile('pwa_icon')) {
             $settingsToUpdate['pwa_icon'] = $this->uploader->upload($request->file('pwa_icon'), 'page-assets/pwa', 'tenant');
             \Log::info("PWA icon uploaded. Stored path: " . $settingsToUpdate['pwa_icon']);
        }
        if ($request->hasFile('og_image')) {
            $settingsToUpdate['og_image'] = $this->uploader->upload($request->file('og_image'), 'page-assets/branding', 'tenant');
            \Log::info("OG image uploaded. Stored path: " . $settingsToUpdate['og_image']);
       }

        // Fetch current data via Raw SQL to prevent stale data
        $currentData = json_decode(\Illuminate\Support\Facades\DB::table('tenants')->where('id', $tenant->id)->value('data') ?? '{}', true);
        
        // Merge with existing data
        $finalData = array_merge($currentData, $settingsToUpdate);
        
        // Log what we're about to save
        \Log::info("Saving tenant data", [
            'logo' => $finalData['logo'] ?? 'not set',
            'favicon' => $finalData['favicon'] ?? 'not set'
        ]);

        // Update Store Name and Data via Raw SQL
        \Illuminate\Support\Facades\DB::connection(config('tenancy.database.central_connection'))->table('tenants')
            ->where('id', $tenant->id)
            ->update([
                'name' => $request->store_name,
                'data' => json_encode($finalData)
            ]);

        if ($request->wantsJson()) {
             return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully.',
                'settings' => $finalData
             ]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }

    public function domain(Request $request)
    {
        $tenant = app('tenant');
        $currentRequest = $tenant->customDomainRequests()->whereIn('status', ['pending', 'approved'])->latest()->first();

        // Used for Vue component prop
        $domainData = [
            'storeUrl' => request()->getSchemeAndHttpHost(),
            'appHost' => parse_url(config('app.url'), PHP_URL_HOST),
            'currentRequest' => $currentRequest ? [
                'id' => $currentRequest->id,
                'domain' => $currentRequest->domain,
                'status' => $currentRequest->status,
                'created_at' => $currentRequest->created_at->format('M d, Y'),
                // Include formatted date for Vue as well if needed or handle in JS
            ] : null,
            'config' => [
                 'app_url' => config('app.url')
            ]
        ];
        
        if ($request->wantsJson()) {
            return response()->json($domainData);
        }
        
        return view('admin.settings.domain', compact('tenant', 'currentRequest', 'domainData'));
    }

    public function requestDomain(Request $request)
    {
        try {
            $request->validate([
                'domain' => ['required', 'string', 'regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', 'unique:custom_domain_requests,domain'],
            ], [
                'domain.regex' => 'Please enter a valid domain name (e.g., myshop.com) without http/https.',
                'domain.unique' => 'This domain has already been requested or taken.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        // Check for existing pending requests (approved requests mean they already have one, maybe they want to switch? For now prevent multiple)
        $tenant = app('tenant');
        
        // If they already have an APPROVED domain, they might want to change it. 
        // If they have PENDING, they should wait or cancel.
        if ($tenant->customDomainRequests()->where('status', 'pending')->exists()) {
             if ($request->wantsJson()) {
                 return response()->json(['message' => 'You already have a pending domain request.'], 403);
             }
            return back()->with('error', 'You already have a pending domain request. Please wait for approval or cancel it to submit a new one.');
        }

        // If they have approved, we might allow requesting a replacement (which would deactivate the old one upon approval).
        // For simplicity now: Allow request, but warn. 
        // Logic: just create pending.

        $domainRequest = $tenant->customDomainRequests()->create([
            'domain' => $request->domain,
            'status' => 'pending',
        ]);

         if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Domain request submitted successfully.',
                'request' => $domainRequest
            ]);
         }

        return back()->with('success', 'Domain request submitted successfully. Waiting for system admin verification.');
    }

    public function cancelDomainRequest(Request $request, $id)
    {
        $tenant = app('tenant');
        $domainRequest = $tenant->customDomainRequests()->where('id', $id)->where('status', 'pending')->firstOrFail();
        
        $domainRequest->delete();

         if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Domain request cancelled.'
            ]);
         }

        return back()->with('success', 'Domain request cancelled.');
    }

    public function sendTestEmail(Request $request)
    {
        try {
            $request->validate([
                'test_email' => 'required|email',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
             if ($request->wantsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $tenant = app('tenant');
        $settings = $tenant->data ?? [];

        // Check if SMTP settings are present
        if (empty($settings['mail_host'])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Please configure and save your SMTP settings first.'], 400);
            }
            return back()->with('error', 'Please configure and save your SMTP settings first.');
        }

        try {
            // Dynamic Mailer Configuration
            $config = [
                'transport' => 'smtp',
                'host' => $settings['mail_host'],
                'port' => $settings['mail_port'] ?? 587,
                'encryption' => $settings['mail_encryption'] ?? 'tls',
                'username' => $settings['mail_username'],
                'password' => $settings['mail_password'],
                'timeout' => null,
                'auth_mode' => null,
            ];

            // Set configuration dynamically
            config(['mail.mailers.tenant_smtp' => $config]);
            config(['mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address')]);
            config(['mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name')]);

            // Send Email using the dynamic mailer
            \Illuminate\Support\Facades\Mail::mailer('tenant_smtp')
                ->to($request->test_email)
                ->send(new \App\Mail\TestEmail());

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Test email sent successfully to ' . $request->test_email]);
            }

            return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Tenant Test Email Failed', ['error' => $e->getMessage()]);
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Failed to send email: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
