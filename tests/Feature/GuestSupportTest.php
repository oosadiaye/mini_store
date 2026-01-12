<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GuestSupportTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create Tenant
        $this->tenant = Tenant::create([
            'id' => 'test-tenant',
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'email' => 'admin@test-tenant.com',
            'is_active' => true,
        ]);

        // Create Ticket Category
        TicketCategory::create(['name' => 'General Support']);
    }

    public function test_guest_can_access_support_form()
    {
        $response = $this->get(route('tenant.support.guest', ['tenant' => $this->tenant->slug]));
        $response->assertStatus(200);
        $response->assertSee('Contact Support');
    }

    public function test_guest_can_submit_ticket()
    {
        $category = TicketCategory::first();
        
        $response = $this->post(route('tenant.support.guest.store', ['tenant' => $this->tenant->slug]), [
            'name' => 'John Guest',
            'email' => 'guest@example.com',
            'category_id' => $category->id,
            'subject' => 'Help me login',
            'priority' => 'high',
            'message' => 'I cannot access my account.',
        ]);

        $response->assertRedirect(route('tenant.support.guest.success', ['tenant' => $this->tenant->slug]));
        
        $this->assertDatabaseHas('support_tickets', [
            'tenant_id' => $this->tenant->id,
            'contact_email' => 'guest@example.com',
            'subject' => 'Help me login',
        ]);
        
        $this->assertDatabaseHas('ticket_messages', [
            'message' => 'I cannot access my account.',
            'user_id' => null,
        ]);
    }
}
