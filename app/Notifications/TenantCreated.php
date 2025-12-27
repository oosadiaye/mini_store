<?php

namespace App\Notifications;

use App\Models\GlobalSetting;
use App\Models\Tenant;
use App\Services\MailConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class TenantCreated extends Notification
{
    use Queueable;

    public $tenant;
    public $password;

    public function __construct(Tenant $tenant, $password)
    {
        $this->tenant = $tenant;
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Configure mail settings before sending
        MailConfigService::configure($this->tenant);

        // Fetch template from Global Settings
        $subject = GlobalSetting::where('key', 'welcome_email_subject')->value('value') 
                   ?? 'Welcome to Your New Store!';
        
        $bodyTemplate = GlobalSetting::where('key', 'welcome_email_body')->value('value') 
                        ?? "<h2>Hello {{ name }},</h2><p>Your store <strong>{{ store_name }}</strong> is ready!</p><p>Login: <a href='{{ login_url }}'>{{ login_url }}</a></p><p>Email: {{ email }}<br>Password: {{ password }}</p>";

        // Replace placeholders
        $loginUrl = url('/' . $this->tenant->slug . '/admin');
        $body = str_replace(
            ['{{ name }}', '{{ email }}', '{{ password }}', '{{ login_url }}', '{{ store_name }}'],
            [$notifiable->name, $notifiable->email, $this->password, $loginUrl, $this->tenant->name],
            $bodyTemplate
        );

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.tenant-created', [
                'body' => $body,
                'tenant' => $this->tenant,
                'user' => $notifiable,
                'password' => $this->password,
                'loginUrl' => $loginUrl
            ]);
    }
}
