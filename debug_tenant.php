<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Tenant::all() as $t) {
    echo "Tenant ID: " . $t->id . "\n";
    echo "Data: " . json_encode($t->data, JSON_PRETTY_PRINT) . "\n";
    echo "-------------------\n";
}
