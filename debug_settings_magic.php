<?php

use App\Models\Tenant;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = Tenant::first();
echo "Testing Magic Setter on Tenant: {$tenant->id}\n";

// Set via magic property
$key = 'magic_test_' . time();
$tenant->$key = 'magic_value';
$tenant->save();

echo "Saved.\n";

// Refresh
$tenant->refresh();
echo "Data Column: " . json_encode($tenant->data) . "\n";

if (isset($tenant->$key) && $tenant->$key === 'magic_value') {
    echo "SUCCESS: Magic property persisted.\n";
} else {
    echo "FAILURE: Magic property NOT found.\n";
    // Check inside data array manually
    if (isset($tenant->data[$key])) {
        echo "It IS in the data array though.\n";
    }
}
