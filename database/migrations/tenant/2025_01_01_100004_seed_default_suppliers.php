<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Tenant;
use App\Models\Supplier;

return new class extends Migration
{
    public function up(): void
    {
        // Iterate through all tenants
        Tenant::all()->each(function ($tenant) {
            // Check if tenant has any suppliers
            if (!Supplier::where('tenant_id', $tenant->id)->exists()) {
                // Create Default Supplier
                Supplier::create([
                    'tenant_id' => $tenant->id,
                    'name' => 'General Supplier',
                    'company_name' => 'General Supplier',
                    'email' => $tenant->email, // Use tenant email as contact
                    'phone' => $tenant->phone ?? 'N/A',
                    'is_active' => true,
                ]);
            }
        });
    }

    public function down(): void
    {
        // No down migration logic for seeding data
    }
};
