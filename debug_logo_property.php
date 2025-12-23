<?php

use App\Models\Tenant;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = Tenant::first();
echo "Debug: Testing Logo Property setter on Tenant: {$tenant->id}\n";
echo "Initial Data: " . json_encode($tenant->data) . "\n";

// SImulate Controller Action
$fakePath = 'page-assets/branding/debug_logo_' . time() . '.png';
echo "Setting logo to: $fakePath\n";

$tenant->logo = $fakePath;
$tenant->save();

echo "Saved.\n";

// Reload from DB to verify persistence
$tenant = Tenant::first(); // get fresh instance
echo "Reloaded Data: " . json_encode($tenant->data) . "\n";

if (isset($tenant->logo) && $tenant->logo === $fakePath) {
    echo "SUCCESS: \$tenant->logo persisted correctly.\n";
} elseif (isset($tenant->data['logo']) && $tenant->data['logo'] === $fakePath) {
    echo "SUCCESS: It is in the data array.\n";
} else {
    echo "FAILURE: Logo did not persist.\n";
    echo "Current Logo Value: " . ($tenant->logo ?? 'NULL') . "\n";
}
