<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant = tenant();
        // Access JSON data column safely
        $settings = $tenant->data ?? [];
        $paymentTypes = \App\Models\PaymentType::with('account')->get();

        return view('admin.settings.index', compact('tenant', 'settings', 'paymentTypes'));
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
        ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Settings Validation Failed', $e->errors());
            throw $e;
        }

        $tenant = tenant();
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
            $settingsToUpdate['logo'] = $request->file('logo')->store('page-assets/branding', 'public');
            \Log::info("Logo uploaded. Stored path: " . $settingsToUpdate['logo']);
        }
        if ($request->hasFile('favicon')) {
            $settingsToUpdate['favicon'] = $request->file('favicon')->store('page-assets/branding', 'public');
            \Log::info("Favicon uploaded. Stored path: " . $settingsToUpdate['favicon']);
        }
        if ($request->hasFile('hero_banner')) {
            $settingsToUpdate['hero_banner'] = $request->file('hero_banner')->store('page-assets/branding', 'public');
            \Log::info("Hero banner uploaded. Stored path: " . $settingsToUpdate['hero_banner']);
        }
        if ($request->hasFile('side_banner_1')) {
            $settingsToUpdate['side_banner_1'] = $request->file('side_banner_1')->store('page-assets/banners', 'public');
            \Log::info("Side banner 1 uploaded. Stored path: " . $settingsToUpdate['side_banner_1']);
        }
        if ($request->hasFile('side_banner_2')) {
            $settingsToUpdate['side_banner_2'] = $request->file('side_banner_2')->store('page-assets/banners', 'public');
            \Log::info("Side banner 2 uploaded. Stored path: " . $settingsToUpdate['side_banner_2']);
        }
        if ($request->hasFile('pwa_icon')) {
             $settingsToUpdate['pwa_icon'] = $request->file('pwa_icon')->store('page-assets/pwa', 'public');
             \Log::info("PWA icon uploaded. Stored path: " . $settingsToUpdate['pwa_icon']);
        }

        // Fetch current data via Raw SQL to prevent stale data or Stancl interference
        $currentData = json_decode(\Illuminate\Support\Facades\DB::connection(config('tenancy.database.central_connection'))->table('tenants')->where('id', $tenant->id)->value('data') ?? '{}', true);
        
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

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
