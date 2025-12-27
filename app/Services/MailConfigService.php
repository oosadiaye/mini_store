<?php

namespace App\Services;

use App\Models\GlobalSetting;
use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class MailConfigService
{
    public static function configure(Tenant $tenant = null)
    {
        // 1. Check if Tenant has Custom SMTP enabled
        if ($tenant && ($tenant->settings['smtp_enabled'] ?? false)) {
            self::setMailConfig($tenant->settings);
            return;
        }

        // 2. Fallback to Global Settings
        // Helper to fetch all settings efficiently could be added, but for now query directly
        // caching should be implemented here in production
        if (Schema::hasTable('global_settings')) {
            $globalSettings = GlobalSetting::where('group', 'mail')->pluck('value', 'key');
            
            // Map global keys (smtp_host) to config keys
            $config = [
                'smtp_host' => $globalSettings['smtp_host'] ?? config('mail.mailers.smtp.host'),
                'smtp_port' => $globalSettings['smtp_port'] ?? config('mail.mailers.smtp.port'),
                'smtp_username' => $globalSettings['smtp_username'] ?? config('mail.mailers.smtp.username'),
                'smtp_password' => $globalSettings['smtp_password'] ?? config('mail.mailers.smtp.password'),
                'smtp_encryption' => $globalSettings['smtp_encryption'] ?? config('mail.mailers.smtp.encryption'),
                'smtp_from_address' => $globalSettings['smtp_from_address'] ?? config('mail.from.address'),
                'smtp_from_name' => $globalSettings['smtp_from_name'] ?? config('mail.from.name'),
            ];
            
            self::setMailConfig($config);
        }
    }

    private static function setMailConfig($settings)
    {
        Config::set('mail.mailers.smtp.host', $settings['smtp_host'] ?? null);
        Config::set('mail.mailers.smtp.port', $settings['smtp_port'] ?? 587);
        Config::set('mail.mailers.smtp.username', $settings['smtp_username'] ?? null);
        Config::set('mail.mailers.smtp.password', $settings['smtp_password'] ?? null);
        Config::set('mail.mailers.smtp.encryption', $settings['smtp_encryption'] ?? 'tls');
        
        if (isset($settings['smtp_from_address'])) {
            Config::set('mail.from.address', $settings['smtp_from_address']);
        }
        
        if (isset($settings['smtp_from_name'])) {
            Config::set('mail.from.name', $settings['smtp_from_name']);
        }
    }
}
