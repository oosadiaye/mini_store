<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class TenantMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 1. Load Global SuperAdmin Settings Overrides
        // We check if the table exists to avoid errors during initial migrations
        if (Schema::hasTable('global_settings')) {
            $this->applyGlobalMailSettings();
        }

        // 2. Load Tenant Settings Overrides
        // We hook into the bootcamp or check if tenant is bound.
        // Since we are likely using a middleware to IdentifyTenant, 
        // we can check if app('tenant') is bound.
        
        // However, this boot() method runs *before* the middleware pipeline in many cases.
        // So we need to listen for when the tenant is resolved, OR check later.
        // But Laravel's config should be set before Mailer is resolved.
        
        // A common approach with Stancl/Tenancy (or custom) is to hook into an event.
        // Since we seem to be using a custom 'app()->instance("tenant", ...)' in IdentifyTenant middleware,
        // we should probably check if 'tenant' is bound, 
        // OR better yet, let the middleware handle the config override.
        
        // BUT, if we want this decoupled, we can do it here if the tenant is somehow already resolved,
        // or register a singleton that resolves lazily.
        
        // Given existing structure, let's try to check binding in a way that respects the request cycle.
        // Actually, the best place for *Tenant* config is in the IdentifyTenant Middleware.
        // But I will put a check here just in case it's available, and also provide a static method
        // that the middleware can call.
    }

    protected function applyGlobalMailSettings()
    {
        try {
            $settings = GlobalSetting::whereIn('group', ['mail'])->pluck('value', 'key');

            if ($settings->isNotEmpty()) {
                if ($settings->get('smtp_host')) {
                    Config::set('mail.mailers.smtp.host', $settings->get('smtp_host'));
                }
                if ($settings->get('smtp_port')) {
                    Config::set('mail.mailers.smtp.port', $settings->get('smtp_port'));
                }
                if ($settings->get('smtp_username')) {
                    Config::set('mail.mailers.smtp.username', $settings->get('smtp_username'));
                }
                if ($settings->get('smtp_password')) {
                    Config::set('mail.mailers.smtp.password', $settings->get('smtp_password'));
                }
                if ($settings->get('smtp_encryption')) {
                    Config::set('mail.mailers.smtp.encryption', $settings->get('smtp_encryption'));
                }
                if ($settings->get('smtp_from_address')) {
                    Config::set('mail.from.address', $settings->get('smtp_from_address'));
                }
                if ($settings->get('smtp_from_name')) {
                    Config::set('mail.from.name', $settings->get('smtp_from_name'));
                }
            }
        } catch (\Exception $e) {
            // Silence errors if DB not ready
        }
    }
    
    /**
     * Static method to be called by Middleware when tenant is identified
     */
    public static function overrideForTenant($tenant)
    {
        if (!$tenant || empty($tenant->data)) return;

        $data = $tenant->data; // Cached JSON data

        if (!empty($data['mail_host'])) {
            Config::set('mail.mailers.smtp.host', $data['mail_host']);
            Config::set('mail.mailers.smtp.port', $data['mail_port'] ?? 587);
            Config::set('mail.mailers.smtp.username', $data['mail_username']);
            Config::set('mail.mailers.smtp.password', $data['mail_password']);
            Config::set('mail.mailers.smtp.encryption', $data['mail_encryption'] ?? 'tls');
            
            if (!empty($data['mail_from_address'])) {
                Config::set('mail.from.address', $data['mail_from_address']);
            }
            if (!empty($data['mail_from_name'])) {
                Config::set('mail.from.name', $data['mail_from_name']);
            }
            
            // Force mailer to use SMTP if configured
            Config::set('mail.default', 'smtp');
            
            // Purge mailer to ensure it re-resolves with new config
            app()->forgetInstance('mailer');
            app()->forgetInstance(\Illuminate\Mail\Mailer::class);
            // Also need to restart the transport manager if it was already resolved
            if (app()->resolved('mail.manager')) {
                app('mail.manager')->forgetMailers();
            }
        }
    }
}
