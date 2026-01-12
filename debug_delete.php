<?php

use Illuminate\Support\Facades\Schema;
use App\Models\Tenant;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tenants = Tenant::all();
    if ($tenants->isEmpty()) {
        die("No tenants found.\n");
    }
    
    foreach ($tenants as $tenant) {
        echo "Attempting to delete tenant: " . $tenant->id . " (" . $tenant->name . ")\n";
        try {
            DB::beginTransaction();
            if (method_exists($tenant, 'domains')) {
                $tenant->domains()->delete();
                echo "...Domains deleted\n";
            }
            $tenant->delete();
            DB::commit();
            echo "Deletion successful for " . $tenant->id . ".\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "FAILED for " . $tenant->id . ": [" . get_class($e) . "]: " . $e->getMessage() . "\n";
            if (method_exists($e, 'getQuery')) {
                echo "QUERY: " . $e->getQuery() . "\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "GENERAL ERROR: " . $e->getMessage() . "\n";
}
