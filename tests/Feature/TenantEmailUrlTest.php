<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Mail\CustomerWelcome;
use App\Mail\OrderPlacedCustomer;
use App\Mail\OrderPlacedAdmin;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TenantEmailUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_welcome_email_contains_tenant_url()
    {
        $tenant = Tenant::create(['id' => 'test-store', 'slug' => 'test-store', 'name' => 'Test Store', 'email' => 'store@example.com', 'is_active' => true]);
        $customer = (object) ['name' => 'John Doe', 'email' => 'john@example.com'];

        $mailable = new CustomerWelcome($customer, $tenant);
        $mailable->assertSeeInHtml(config('app.url') . '/test-store');
        $this->assertEquals('Test Store', $mailable->tenant->name);
    }

    public function test_order_confirmation_email_contains_tenant_url()
    {
        $tenant = Tenant::create(['id' => 'test-store', 'slug' => 'test-store', 'name' => 'Test Store', 'email' => 'store@example.com', 'is_active' => true]);
        $order = (object) [
            'order_number' => 'ORD-123',
            'total' => 1000,
            'customer' => (object) ['name' => 'John Doe'],
            'items' => []
        ];

        $mailable = new OrderPlacedCustomer($order, $tenant);
        $mailable->assertSeeInHtml(config('app.url') . '/test-store/orders/ORD-123');
    }

    public function test_admin_notification_email_contains_tenant_admin_url()
    {
        $tenant = Tenant::create(['id' => 'test-store', 'slug' => 'test-store', 'name' => 'Test Store', 'email' => 'store@example.com', 'is_active' => true]);
        $order = (object) [
            'id' => 1,
            'order_number' => 'ORD-123',
            'total' => 1000,
            'payment_method' => 'Stripe',
            'customer' => (object) ['name' => 'John Doe', 'email' => 'john@example.com'],
            'items' => []
        ];

        $mailable = new OrderPlacedAdmin($order, $tenant);
        $mailable->assertSeeInHtml(config('app.url') . '/test-store/admin/orders/1');
    }
}
