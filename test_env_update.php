<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\EnvService;

$testData = [
    'MAIL_HOST' => 'new.smtp.host',
    'MAIL_PORT' => '2525'
];

EnvService::update($testData);

$content = file_get_contents('.env');
echo $content;
