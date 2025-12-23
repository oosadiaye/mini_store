<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = \App\Models\Tenant::find('dplux');

echo "Initial Data: " . json_encode($tenant->data) . "\n";

// Emulate setting arrays
$data = $tenant->data ?? [];
$data['test_key'] = 'test_value';

$tenant->data = $data;
$tenant->save();

// Re-fetch to confirm
$tenant->refresh();
echo "Post-Save Data: " . json_encode($tenant->data) . "\n";

if (($tenant->data['test_key'] ?? null) === 'test_value') {
    echo "SUCCESS: Persistence works.\n";
} else {
    echo "FAILURE: Persistence failed.\n";
}
