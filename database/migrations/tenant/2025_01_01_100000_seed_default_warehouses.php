<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Tenant;
use App\Models\Warehouse;

return new class extends Migration
{
    public function up(): void
    {
        // Iterate over all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Check if warehouse exists
            if (Warehouse::withoutGlobalScope(\App\Scopes\TenantScope::class)->where('tenant_id', $tenant->id)->exists()) {
                continue;
            }

            // Create default warehouse
            Warehouse::create([
                'tenant_id' => $tenant->id,
                'name' => ($tenant->store_name ?? $tenant->name ?? 'Main') . ' - Main',
                'code' => 'MAIN',
                'is_active' => true,
            ]);
        }
    }

    public function down(): void
    {
        // No down action to avoid deleting user data accidentally
    }
};
