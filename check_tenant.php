<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = App\Models\Tenant::where('slug', 'dplux')->first();
if ($tenant) {
    echo "Tenant: " . $tenant->name . "\n";
    echo "Plan: " . ($tenant->currentPlan ? $tenant->currentPlan->name : 'No Plan') . "\n";
    echo "Features: " . json_encode($tenant->currentPlan ? $tenant->currentPlan->features : []) . "\n";
    echo "Storefront Enabled: " . ($tenant->is_storefront_enabled ? 'Yes' : 'No') . "\n";
} else {
    echo "Tenant dplux not found\n";
}
