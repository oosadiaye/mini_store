<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Fetch all settings and group them by 'group' key for easier access in view
        $settings = GlobalSetting::all()->mapWithKeys(function ($item) {
            return [$item->key => $item->value];
        });

        return view('superadmin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Define all allowed keys and their groups to ensure categorized storage
        $keys = [
            'mail_host' => 'mail',
            'mail_port' => 'mail',
            'mail_username' => 'mail',
            'mail_password' => 'mail',
            'mail_encryption' => 'mail',
            'mail_from_address' => 'mail',
            'mail_from_name' => 'mail',
            
            'gateway_opay_public' => 'payment',
            'gateway_opay_secret' => 'payment',
            'gateway_paystack_public' => 'payment',
            'gateway_paystack_secret' => 'payment',
            'gateway_flutterwave_public' => 'payment',
            'gateway_flutterwave_secret' => 'payment',

            'brand_name' => 'branding',
            'brand_logo' => 'branding',
            'brand_primary_color' => 'branding',
            
            'currency_code' => 'currency',
            'currency_symbol' => 'currency',
            
            'cookie_consent_enabled' => 'cookie',
            'cookie_consent_message' => 'cookie',
        ];

        foreach ($keys as $key => $group) {
            if ($request->has($key)) {
                GlobalSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key), 'group' => $group]
                );
            }
        }

        // Handle File Uploads (Logo)
        if ($request->hasFile('brand_logo')) {
            $path = $request->file('brand_logo')->store('global/branding', 'public');
            GlobalSetting::updateOrCreate(
                ['key' => 'brand_logo'],
                ['value' => $path, 'group' => 'branding']
            );
        }

        \App\Helpers\AuditHelper::log('update_global_settings', 'Updated global settings configuration.');

        return redirect()->back()->with('success', 'Global settings updated successfully.');
    }
}
