<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TaxCode;
use App\Models\Account;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaxCodeScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tax_codes_are_scoped_to_tenant()
    {
        $tenant1 = Tenant::create(['id' => 't1', 'slug' => 't1', 'name' => 'T1', 'email' => 't1@example.com']);
        $tenant2 = Tenant::create(['id' => 't2', 'slug' => 't2', 'name' => 'T2', 'email' => 't2@example.com']);

        // Set tenant 1 context
        app()->instance('tenant', $tenant1);
        config(['app.tenant_id' => $tenant1->id]);

        TaxCode::create([
            'code' => 'VAT-T1',
            'name' => 'VAT T1',
            'rate' => 10,
            'type' => 'sales'
        ]);

        // Set tenant 2 context
        app()->instance('tenant', $tenant2);
        config(['app.tenant_id' => $tenant2->id]);

        TaxCode::create([
            'code' => 'VAT-T2',
            'name' => 'VAT T2',
            'rate' => 20,
            'type' => 'sales'
        ]);

        // Verify isolation
        $this->assertEquals(1, TaxCode::count());
        $this->assertEquals('VAT-T2', TaxCode::first()->code);

        // Switch back to T1
        app()->instance('tenant', $tenant1);
        config(['app.tenant_id' => $tenant1->id]);
        
        $this->assertEquals(1, TaxCode::count());
        $this->assertEquals('VAT-T1', TaxCode::first()->code);
    }
}
