<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenantId = 'Dplux-Technologies';
echo "Checking media for tenant: $tenantId\n";

$data = json_decode(DB::table('tenants')->where('id', $tenantId)->value('data') ?? '{}', true);

if (empty($data['logo'])) {
    echo "No logo set in database for this tenant.\n";
} else {
    echo "Logo path in DB: " . $data['logo'] . "\n";
    
    $disk = Storage::disk('public');
    
    // Check if file exists via Storage facade
    if ($disk->exists($data['logo'])) {
        echo "File EXISTS on 'public' disk.\n";
        $fullPath = $disk->path($data['logo']);
        echo "Full Path: " . $fullPath . "\n";
        echo "URL: " . $disk->url($data['logo']) . "\n";
        
        if (file_exists($fullPath)) {
            echo "PHP file_exists() returns TRUE.\n";
            echo "Mime Type: " . mime_content_type($fullPath) . "\n";
            echo "Size: " . filesize($fullPath) . " bytes\n";
        } else {
            echo "PHP file_exists() returns FALSE (Storage facade lied?).\n";
        }

    } else {
        echo "File DOES NOT EXIST on 'public' disk.\n";
        echo "Expected Path: " . $disk->path($data['logo']) . "\n";
    }

    // Check Tenant Feature
    $tenant = \App\Models\Tenant::find($tenantId);
    echo "Tenant found: " . ($tenant ? 'Yes' : 'No') . "\n";
    if ($tenant) {
        // Mock plan relation manually if needed or just check plan data
        $planId = $tenant->plan_id;
        echo "Plan ID: $planId\n";
        $plan = \App\Models\Plan::find($planId);
        echo "Plan found: " . ($plan ? 'Yes' : 'No') . "\n";
        if ($plan) {
            echo "Features: " . json_encode($plan->features) . "\n";
            echo "Has 'online_store': " . (in_array('online_store', $plan->features) ? 'Yes' : 'No') . "\n";
        }
    }

}
