<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ConfigureTenantMail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (function_exists('tenant') && $tenant = tenant()) {
            $data = $tenant->data ?? [];
            
            if (!empty($data['mail_host'])) {
                Config::set('mail.mailers.smtp.transport', 'smtp');
                Config::set('mail.mailers.smtp.host', $data['mail_host']);
                Config::set('mail.mailers.smtp.port', $data['mail_port'] ?? 587);
                Config::set('mail.mailers.smtp.encryption', $data['mail_encryption'] ?? 'tls');
                Config::set('mail.mailers.smtp.username', $data['mail_username']);
                Config::set('mail.mailers.smtp.password', $data['mail_password']);
                
                // Force config cache clear effect for this request
                // (Laravel mailer might have already been resolved, so we might need to purge it if we want it to pick up new config immediately)
                // app('mailer')->forceReconnection()? Or just setting config is enough if accessed later.
                // Usually setting config is enough if Mail hasn't provided the instance yet.
            }

            if (!empty($data['mail_from_address'])) {
                Config::set('mail.from.address', $data['mail_from_address']);
                Config::set('mail.from.name', $data['mail_from_name'] ?? $tenant->name);
            }
        }

        return $next($request);
    }
}
