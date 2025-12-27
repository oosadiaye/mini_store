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

// Get Enterprise plan
$plan = App\Models\Plan::where('name', 'LIKE', '%Enterprise%')->first();

if (!$plan) {
    echo "Enterprise plan not found. Available plans:\n";
    $plans = App\Models\Plan::all();
    foreach ($plans as $p) {
        echo "  - ID: {$p->id}, Name: {$p->name}\n";
    }
    
    // Use the plan with highest ID (likely Enterprise)
    $plan = App\Models\Plan::orderBy('id', 'desc')->first();
    echo "\nUsing plan: {$plan->name} (ID: {$plan->id})\n";
}

// Create or update subscription
$subscription = App\Models\Subscription::updateOrCreate(
    ['tenant_id' => $tenant->id],
    [
        'plan_id' => $plan->id,
        'status' => 'active',
        'starts_at' => now(),
        'expires_at' => now()->addYears(10), // 10 years subscription
        'auto_renew' => true,
    ]
);

echo "✅ Subscription created/updated successfully!\n";
echo "Tenant: {$tenant->slug}\n";
echo "Plan: {$plan->name} (ID: {$plan->id})\n";
echo "Status: {$subscription->status}\n";
echo "Expires: {$subscription->expires_at}\n";
echo "\n✅ All features should now be accessible!\n";
