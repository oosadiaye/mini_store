<?php
namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Announcement;
use App\Models\Cart;
use App\Models\Page;
use App\Models\Post;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class GlobalScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcements_are_scoped_to_tenant()
    {
        $plan = \App\Models\Plan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic-plan',
            'price' => 1000,
            'duration_days' => 30,
            'trial_days' => 7,
            'features' => [],
            'caps' => [],
            'is_active' => true
        ]);

        $tenant1 = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'T1', 'slug' => 'tenant-1', 'email' => 't1@example.com', 'plan_id' => $plan->id]);
        $tenant2 = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'T2', 'slug' => 'tenant-2', 'email' => 't2@example.com', 'plan_id' => $plan->id]);

        app()->instance('tenant', $tenant1);
        Announcement::create(['title' => 'T1 Announcement', 'tenant_id' => $tenant1->id, 'content' => 'test', 'type' => 'info']);

        app()->instance('tenant', $tenant2);
        Announcement::create(['title' => 'T2 Announcement', 'tenant_id' => $tenant2->id, 'content' => 'test', 'type' => 'info']);

        $this->assertEquals(1, Announcement::count());
        $this->assertEquals('T2 Announcement', Announcement::first()->title);

        app()->instance('tenant', $tenant1);
        $this->assertEquals(1, Announcement::count());
        $this->assertEquals('T1 Announcement', Announcement::first()->title);
    }

    public function test_carts_are_scoped_to_tenant()
    {
        $plan = \App\Models\Plan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic-plan-2',
            'price' => 1000,
            'duration_days' => 30,
            'trial_days' => 7,
            'features' => [],
            'caps' => [],
            'is_active' => true
        ]);

        $tenant1 = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'T1', 'slug' => 't1', 'email' => 't1@example.com', 'plan_id' => $plan->id]);
        $tenant2 = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'T2', 'slug' => 't2', 'email' => 't2@example.com', 'plan_id' => $plan->id]);

        app()->instance('tenant', $tenant1);
        Cart::create(['session_id' => 's1', 'tenant_id' => $tenant1->id]);

        app()->instance('tenant', $tenant2);
        Cart::create(['session_id' => 's2', 'tenant_id' => $tenant2->id]);

        $this->assertEquals(1, Cart::count());
        $this->assertEquals('s2', Cart::first()->session_id);
    }

    public function test_pages_are_scoped_to_tenant()
    {
        $plan = \App\Models\Plan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic-plan-3',
            'price' => 1000,
            'duration_days' => 30,
            'trial_days' => 7,
            'features' => [],
            'caps' => [],
            'is_active' => true
        ]);

        $tenant1 = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'T1', 'slug' => 't1', 'email' => 't1@example.com', 'plan_id' => $plan->id]);
        $tenant2 = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'T2', 'slug' => 't2', 'email' => 't2@example.com', 'plan_id' => $plan->id]);

        app()->instance('tenant', $tenant1);
        Page::create(['title' => 'P1', 'slug' => 'p1', 'tenant_id' => $tenant1->id]);

        app()->instance('tenant', $tenant2);
        Page::create(['title' => 'P2', 'slug' => 'p2', 'tenant_id' => $tenant2->id]);

        $this->assertEquals(1, Page::count());
        $this->assertEquals('P2', Page::first()->title);
    }
}
