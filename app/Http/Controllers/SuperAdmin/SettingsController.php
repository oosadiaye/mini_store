<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
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
            'brand_favicon' => 'branding', 
            'brand_primary_color' => 'branding',
            'email_banner' => 'templates',
            
            'currency_code' => 'currency',
            'currency_symbol' => 'currency',
            'app_name' => 'general',
            'currency' => 'general',
            'timezone' => 'general',
            
            'cookie_consent_enabled' => 'cookie',
            'cookie_consent_message' => 'cookie',
            
            'smtp_host' => 'mail',
            'smtp_port' => 'mail',
            'smtp_username' => 'mail',
            'smtp_password' => 'mail',
            'smtp_encryption' => 'mail',
            'smtp_from_address' => 'mail',
            'smtp_from_name' => 'mail',
            
            'welcome_email_subject' => 'templates',
            'welcome_email_body' => 'templates',

            'turnstile_site_key' => 'security',
            'turnstile_secret' => 'security',
            'recaptcha_site_key' => 'security',
            'recaptcha_secret' => 'security',
            'captcha_type' => 'security',
        ];

        // Process explicit keys
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
            $path = $this->uploader->upload($request->file('brand_logo'), 'global/branding', 'public');
            GlobalSetting::updateOrCreate(
                ['key' => 'brand_logo'],
                ['value' => $path, 'group' => 'branding']
            );
        }

        // Handle File Uploads (Favicon)
        if ($request->hasFile('brand_favicon')) {
            $path = $this->uploader->upload($request->file('brand_favicon'), 'global/branding', 'public');
            GlobalSetting::updateOrCreate(
                ['key' => 'brand_favicon'],
                ['value' => $path, 'group' => 'branding']
            );
        }
        
        // Handle Email Banner Upload
        if ($request->hasFile('email_banner')) {
            $path = $this->uploader->upload($request->file('email_banner'), 'global/templates', 'public');
            GlobalSetting::updateOrCreate(
                ['key' => 'email_banner'],
                ['value' => $path, 'group' => 'templates']
            );
        }

        \App\Helpers\AuditHelper::log('update_global_settings', 'Updated global settings configuration.');

        return redirect()->back()->with('success', 'Global settings updated successfully.');
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($request->test_email)->send(new \App\Mail\TestEmail());
            return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
