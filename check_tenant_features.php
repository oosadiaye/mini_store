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

echo "Tenant: {$tenant->slug}\n";
echo "Plan ID: {$tenant->plan_id}\n";

// Get plan
$plan = App\Models\Plan::find($tenant->plan_id);

if (!$plan) {
    echo "Plan not found!\n";
    exit(1);
}

echo "Plan Name: {$plan->name}\n";
echo "Plan Features: " . json_encode($plan->features, JSON_PRETTY_PRINT) . "\n";
echo "\nHas custom_domain feature: " . ($tenant->hasFeature('custom_domain') ? 'YES' : 'NO') . "\n";

// Check if custom_domain is in plan features
echo "custom_domain in plan features: " . (in_array('custom_domain', $plan->features) ? 'YES' : 'NO') . "\n";

// Check subscription
$subscription = $tenant->subscription;
if ($subscription) {
    echo "\nSubscription Status: {$subscription->status}\n";
    echo "Subscription Expires: {$subscription->expires_at}\n";
} else {
    echo "\nNo active subscription found!\n";
}
