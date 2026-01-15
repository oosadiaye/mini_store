<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\MailConfigService;
use App\Models\GlobalSetting;

// Clear existing settings to be sure
GlobalSetting::where('group', 'mail')->delete();

GlobalSetting::create(['group' => 'mail', 'key' => 'smtp_host', 'value' => 'test.smtp.com']);
GlobalSetting::create(['group' => 'mail', 'key' => 'smtp_port', 'value' => '2525']);

MailConfigService::configure();

echo "Config Host: " . config('mail.mailers.smtp.host') . "\n";
echo "Config Port: " . config('mail.mailers.smtp.port') . "\n";
