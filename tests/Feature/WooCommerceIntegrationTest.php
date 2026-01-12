<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Order;
use App\Services\WooCommerceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Mockery;

class WooCommerceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create tenant and user
        $this->tenant = Tenant::create([
            'id' => '1',
            'name' => 'Test Tenant', 
            'slug' => '1',
            'email' => 'admin@test-tenant.com',
            'is_active' => true
        ]);
        
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_settings_page_loads()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.woocommerce.index', ['tenant' => $this->tenant->slug]));

        $response->assertStatus(200);
        $response->assertSee('WooCommerce Integration');
    }

    public function test_can_save_settings()
    {
        // Mock Service
        $this->mock(WooCommerceService::class, function ($mock) {
            $mock->shouldReceive('setCredentialsFromTenant')->andReturnNull();
            $mock->shouldReceive('testConnection')->andReturnTrue();
        });

        $response = $this->actingAs($this->user)
            ->post(route('admin.woocommerce.settings', ['tenant' => $this->tenant->slug]), [
                'woocommerce_url' => 'https://example.com',
                'woocommerce_consumer_key' => 'ck_test',
                'woocommerce_consumer_secret' => 'cs_test',
                'woocommerce_enabled' => '1',
                'woocommerce_sync_direction' => 'import',
            ]);

        $response->assertRedirect();
        $this->tenant->refresh();
        $this->assertEquals('https://example.com', $this->tenant->settings['woocommerce_url']);
        $this->assertEquals('ck_test', $this->tenant->settings['woocommerce_consumer_key']);
        $this->assertTrue($this->tenant->settings['woocommerce_enabled']);
    }

    public function test_manual_sync_dispatches_job()
    {
        Queue::fake();

        $this->actingAs($this->user)
            ->post(route('admin.woocommerce.sync', ['tenant' => $this->tenant->slug]));

        Queue::assertPushed(\App\Jobs\SyncWooCommerceOrders::class);
    }

    public function test_webhook_receives_and_processes_order()
    {
        $this->withoutExceptionHandling();
        
        // 1. Setup Tenant Secret
        $settings = $this->tenant->settings ?? [];
        $settings['woocommerce_webhook_secret'] = 'test_secret';
        $this->tenant->settings = $settings;
        $this->tenant->save();

        // 2. Prepare Payload
        $payload = [
            'id' => 12345,
            'number' => '1001',
            'status' => 'processing',
            'total' => '50.00',
            'date_paid' => '2023-01-01T12:00:00',
            'billing' => [
                'email' => 'customer@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '1234567890',
            ],
            'line_items' => [
                [
                    'id' => 1,
                    'name' => 'Test Product',
                    'quantity' => 1,
                    'price' => '50.00',
                    'total' => '50.00',
                    'sku' => 'TP-001',
                ]
            ],
            'meta_data' => []
        ];
        
        $content = json_encode($payload);
        $signature = base64_encode(hash_hmac('sha256', $content, 'test_secret', true));

        // 3. Send Webhook Request
        $response = $this->postJson(route('api.woocommerce.webhook', ['tenant' => $this->tenant->slug]), $payload, [
            'x-wc-webhook-topic' => 'order.created',
            'x-wc-webhook-resource' => 'order',
            'x-wc-webhook-event' => 'created',
            'x-wc-webhook-signature' => $signature,
        ]);

        // 4. Assert Success and DB
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('orders', [
            'tenant_id' => $this->tenant->id,
            'woocommerce_id' => 12345,
            'total' => '50.00',
            'status' => 'paid', // mapped from processing
        ]);
        
        $this->assertDatabaseHas('customers', [
            'tenant_id' => $this->tenant->id,
            'email' => 'customer@example.com',
        ]);
    }
}
