<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\StoreConfig;
use App\Models\PaymentGateway;
use App\Services\StorefrontService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class TenantScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('tenant');
    }

    public function test_store_config_is_scoped_to_tenant()
    {
        $t1 = Tenant::create([
            'id' => 't1',
            'slug' => 't1',
            'name' => 'Tenant One',
            'email' => 't1@example.com',
            'is_active' => true,
        ]);

        $t2 = Tenant::create([
            'id' => 't2',
            'slug' => 't2',
            'name' => 'Tenant Two',
            'email' => 't2@example.com',
            'is_active' => true,
        ]);

        app()->instance('tenant', $t1);
        StoreConfig::create([
            'tenant_id' => $t1->id,
            'store_name' => 'Store One',
        ]);

        app()->instance('tenant', $t2);
        StoreConfig::create([
            'tenant_id' => $t2->id,
            'store_name' => 'Store Two',
        ]);

        app()->instance('tenant', $t1);
        $config1 = StoreConfig::first();
        $this->assertEquals('Store One', $config1->store_name);

        app()->instance('tenant', $t2);
        $config2 = StoreConfig::first();
        $this->assertEquals('Store Two', $config2->store_name);
    }

    public function test_payment_gateway_is_scoped_to_tenant()
    {
        $t1 = Tenant::create(['id' => 't1_pg', 'slug' => 't1_pg', 'name' => 'T1 PG', 'email' => 't1pg@example.com', 'is_active' => true]);
        $t2 = Tenant::create(['id' => 't2_pg', 'slug' => 't2_pg', 'name' => 'T2 PG', 'email' => 't2pg@example.com', 'is_active' => true]);

        app()->instance('tenant', $t1);
        PaymentGateway::create(['tenant_id' => $t1->id, 'name' => 'stripe', 'display_name' => 'Stripe T1']);

        app()->instance('tenant', $t2);
        PaymentGateway::create(['tenant_id' => $t2->id, 'name' => 'stripe', 'display_name' => 'Stripe T2']);

        app()->instance('tenant', $t1);
        $this->assertEquals(1, PaymentGateway::count());
        $this->assertEquals('Stripe T1', PaymentGateway::first()->display_name);

        app()->instance('tenant', $t2);
        $this->assertEquals(1, PaymentGateway::count());
        $this->assertEquals('Stripe T2', PaymentGateway::first()->display_name);
    }

    public function test_storefront_service_respects_scoping()
    {
        $tenant = Tenant::create(['id' => 'ts_service', 'slug' => 'ts_service', 'name' => 'Service Test', 'email' => 'ts@example.com', 'is_active' => true]);
        
        app()->instance('tenant', $tenant);
        StoreConfig::create([
            'tenant_id' => $tenant->id,
            'store_name' => 'Custom Store Name',
        ]);

        $service = new StorefrontService();
        $data = $service->getHomeData($tenant);

        $this->assertEquals('Custom Store Name', $data['hero_data']['title']);
    }
}
