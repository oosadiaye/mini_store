<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;

echo "Restoring 'dplux' tenant...\n";

if (Tenant::find('dplux')) {
    echo "Tenant 'dplux' already exists.\n";
    exit;
}

$tenant = Tenant::create([
    'id' => 'dplux',
    'name' => 'Dplux Store',
    'email' => 'osadiaye4real@gmail.com',
    'plan' => 'trial',
    'trial_ends_at' => now()->addDays(14),
]);

$tenant->domains()->create([
    'domain' => 'dplux.' . config('app.domain', 'localhost'),
]);

echo "✅ Tenant 'dplux' created successfully.\n";
