<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get tenant
$tenant = App\Models\Tenant::where('slug', 'Dplux-Technologies')->first();

if (!$tenant) {
    echo "Tenant not found!\n";
    exit(1);
}

// Get plan
$plan = App\Models\Plan::find($tenant->plan_id);

if (!$plan) {
    echo "Plan not found!\n";
    exit(1);
}

// Calculate subscription end date based on plan duration
$durationDays = $plan->duration_days;
$tenant->subscription_ends_at = now()->addDays($durationDays);
$tenant->save();

echo "✅ Subscription activated!\n";
echo "Tenant: {$tenant->slug}\n";
echo "Plan: {$plan->name}\n";
echo "Duration: {$durationDays} days\n";
echo "Started: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Expires: {$tenant->subscription_ends_at}\n";
echo "\n✅ Subscription will expire in {$durationDays} days and need renewal.\n";
