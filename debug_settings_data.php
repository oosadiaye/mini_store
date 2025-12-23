<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get first tenant
$tenant = Tenant::first();

if (!$tenant) {
    echo "No tenant found.\n";
    exit;
}

echo "Tenant Name: {$tenant->name} (ID: {$tenant->id})\n";
echo "Current Data: " . json_encode($tenant->data, JSON_PRETTY_PRINT) . "\n";

// Test Update
echo "Attempting to update 'data' column...\n";
$data = $tenant->data ?? [];
$testKey = 'debug_timestamp_' . time();
$data[$testKey] = 'test_value';

// Directly update
try {
    $tenant->update(['data' => $data]);
    echo "Update called.\n";
} catch (\Exception $e) {
    echo "Update failed: " . $e->getMessage() . "\n";
}

// Refresh from DB
$tenant->refresh();
if (isset($tenant->data[$testKey]) && $tenant->data[$testKey] === 'test_value') {
    echo "SUCCESS: Data column persisted correctly.\n";
    // clean up
    unset($data[$testKey]);
    $tenant->update(['data' => $data]);
} else {
    echo "FAILURE: Data column did NOT persist.\n";
    echo "Fillable attributes: " . implode(', ', $tenant->getFillable()) . "\n";
}

// Check Storage
tenancy()->initialize($tenant);
echo "Tenant Initialized.\n";
echo "Storage Root (public): " . config('filesystems.disks.public.root') . "\n";

if (Storage::disk('public')->exists('page-assets/branding')) {
    echo "Directory 'page-assets/branding' exists.\n";
    $files = Storage::disk('public')->files('page-assets/branding');
    echo "Files in branding: " . implode(', ', $files) . "\n";
} else {
    echo "Directory 'page-assets/branding' NOT found.\n";
    // Try creating it
    Storage::disk('public')->makeDirectory('page-assets/branding');
    echo "Created directory.\n";
}
