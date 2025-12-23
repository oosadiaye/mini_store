<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tenant = \App\Models\Tenant::find('dplux');
if ($tenant) {
    echo "Tenant found: dplux\n";
    foreach ($tenant->domains as $domain) {
        echo "Domain: " . $domain->domain . "\n";
    }
} else {
    echo "Tenant 'dplux' NOT FOUND\n";
}
