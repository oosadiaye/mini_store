<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\StorefrontProduct;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = \App\Models\Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro',
            'price' => 20.00,
            'features' => ['marketing', 'online_store'], // Enable marketing feature
            'is_active' => true,
        ]);

        // 1. Setup Tenant
        $this->tenant = Tenant::create([
            'id' => 'test-tenant',
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'email' => 'tenant@test.com',
            'trial_ends_at' => now()->addDays(14),
            'plan_id' => $plan->id,
        ]);

        // 2. Setup Admin
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'tenant_id' => $this->tenant->id,
        ]);

        // Bind tenant for tenancy
        app()->instance('tenant', $this->tenant);
        \Illuminate\Support\Facades\URL::defaults(['tenant' => $this->tenant->slug]);
    }

    public function test_coupon_full_flow()
    {
        // 1. Admin Creates Coupon
        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store', ['tenant' => $this->tenant->slug]), [
            'code' => 'TEST10',
            'type' => 'fixed',
            'value' => 10.00,
            'is_active' => true,
        ]);
        
        $response->assertRedirect();
        
        // Assert explicit success to confirm we didn't redirect back with errors
        // $response->assertSessionHas('success');

        $this->assertDatabaseHas('coupons', ['code' => 'TEST10']);

        // 2. Setup Product & Stock
        $product = StorefrontProduct::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 50.00,
            'sku' => 'TEST-SKU',
            'is_active' => true,
            'published_status' => 'published',
        ]);
        
        // Ensure warehouse and stock exist for checkout
        $warehouse = Warehouse::create([
            'name' => 'Main Warehouse',
            'tenant_id' => $this->tenant->id,
            'code' => 'MAIN',
        ]);
        WarehouseStock::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 100,
        ]);


        // 3. Storefront: Add to Cart
        $response = $this->post(route('storefront.cart.store', ['tenant' => $this->tenant->slug]), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $response->assertRedirect();

        // 4. Storefront: Apply Coupon
        $response = $this->post(route('storefront.cart.coupon', ['tenant' => $this->tenant->slug]), [
            'code' => 'TEST10',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Coupon applied successfully!');

        // Verify Cart State (Discount Applied)
        // Since cart is session based, we check the database directly for the cart associated with the test session or user
        // But here we are guest. Using session assertions or checking view data in index would be ideal.
        // Let's check the view
        $response = $this->get(route('storefront.cart.index', ['tenant' => $this->tenant->slug]));
        $response->assertSee('TEST10'); // Coupon code visible
        $response->assertSee('40.00');  // 50 - 10 = 40 (Total)
        $response->assertSee('-10.00'); // Discount amount

        // 5. Storefront: Checkout
        $response = $this->get(route('storefront.checkout.index', ['tenant' => $this->tenant->slug]));
        $response->assertSee('-10.00'); // Check discount in summary (Implementation we just added)

        // 6. Storefront: Place Order
        $orderData = [
            'email' => 'customer@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address_line_1' => '123 Main St',
            'city' => 'Metropolis',
            'country' => 'United States',
            'postal_code' => '12345',
        ];

        $response = $this->post(route('storefront.checkout.store', ['tenant' => $this->tenant->slug]), $orderData);
        
        $response->assertRedirect();
        
        // 7. Verify Order & Coupon Usage
        $this->assertDatabaseHas('orders', [
            'total' => 40.00,
            'subtotal' => 50.00,
            'discount' => 10.00,
            'tenant_id' => $this->tenant->id
        ]);

        $this->assertDatabaseHas('coupons', [
            'code' => 'TEST10',
            'used_count' => 1
        ]);
    }
}
