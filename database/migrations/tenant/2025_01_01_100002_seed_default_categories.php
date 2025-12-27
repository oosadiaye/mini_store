<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Tenant;
use App\Models\Category;

return new class extends Migration
{
    public function up(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Check if ANY category exists for this tenant
            if (Category::withoutGlobalScope(\App\Scopes\TenantScope::class)->where('tenant_id', $tenant->id)->exists()) {
                continue;
            }

            // Create default General category
            Category::create([
                'tenant_id' => $tenant->id,
                'name' => 'General',
                'slug' => 'general',
                'is_active' => true,
                'show_on_storefront' => true,
            ]); // tenant_id is automatically handled if we use loop with scope, but here we explicitly set it or use create with tenant_id provided TenantScope is globally applied. 
            // Better to be explicit like in the Warehouse seeder.
        }
    }

    public function down(): void
    {
    }
};
