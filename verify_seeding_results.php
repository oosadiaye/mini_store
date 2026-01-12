<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tenant = \App\Models\Tenant::find('dplux');
\Stancl\Tenancy\Facades\Tenancy::initialize($tenant);

echo "--- Warehouses ---\n";
foreach (\App\Models\Warehouse::all() as $wh) {
    echo "{$wh->name} ({$wh->code})\n";
}

echo "\n--- Suppliers ---\n";
foreach (\App\Models\Supplier::all() as $sup) {
    echo "{$sup->name}\n";
}

echo "\n--- Accounts ---\n";
foreach (\App\Models\Account::all() as $acc) {
    echo "{$acc->account_code}: {$acc->account_name} ({$acc->account_type})\n";
}
