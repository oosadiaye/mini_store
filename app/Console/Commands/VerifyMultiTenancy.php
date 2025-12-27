<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class VerifyMultiTenancy extends Command
{
    protected $signature = 'verify:tenancy';
    protected $description = 'Verify multi-tenancy isolation';

    public function handle()
    {
        $this->info("--- STARTING VERIFICATION ---");

        DB::beginTransaction();

        try {
            // Cleanup previous runs
            $this->info("Cleaning up previous test data...");
            Tenant::whereIn('id', ['demo', 'demo2'])->delete();
            User::whereIn('email', ['admin@demo.com', 'admin@demo2.com'])->delete();
            // Products should cascade or be deleted if we want to be thorough, but let's assume tenant delete handles it or we ignore orphans for this test
            Product::whereIn('slug', ['t1-product', 't2-product'])->forceDelete();

            // 1. Create Tenant 1
            $this->info("Creating Tenant 1 (demo)...");
            $t1 = Tenant::create([
                'id' => 'demo',
                'slug' => 'demo',
                'name' => 'Demo Store',
                'email' => 'demo@example.com',
                'is_active' => true,
                'data' => []
            ]);

            // 2. Create User for Tenant 1
            $this->info("Creating Admin for Tenant 1...");
            $u1 = User::create([
                'name' => 'Demo Admin',
                'email' => 'admin@demo.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'tenant_id' => $t1->id
            ]);

            // 3. Set Context to Tenant 1
            $this->info("Setting Context to Tenant 1...");
            app()->instance('tenant', $t1);

            // 4. Create Product for Tenant 1
            $this->info("Creating Product for Tenant 1...");
            $p1 = Product::create([
                'tenant_id' => $t1->id,
                'name' => 'T1 Product',
                'slug' => 't1-product',
                'price' => 100,
                'stock_quantity' => 10, // Added required field
                'manage_stock' => true,  // Added likely required field
            ]);

            // 5. Create Tenant 2
            $this->info("Creating Tenant 2 (demo2)...");
            $t2 = Tenant::create([
                'id' => 'demo2',
                'slug' => 'demo2',
                'name' => 'Demo Store 2',
                'email' => 'demo2@example.com',
                'is_active' => true,
                'data' => []
            ]);

            // 6. Set Context to Tenant 2
            $this->info("Setting Context to Tenant 2...");
            app()->instance('tenant', $t2);

            // 7. Create Product for Tenant 2
            $this->info("Creating Product for Tenant 2...");
            $p2 = Product::create([
                'name' => 'T2 Product',
                'slug' => 't2-product',
                'price' => 200,
                'stock_quantity' => 10,
                'manage_stock' => true,
            ]);

            // 8. Verify Scoping
            $this->info("Verifying Scope (Context: Tenant 2)...");
            $products = Product::all();
            $this->info("Found " . $products->count() . " products.");
            
            if ($products->count() === 1 && $products->first()->name === 'T2 Product') {
                $this->info("PASS: Only T2 products visible.");
            } else {
                $this->error("FAIL: Visible products: " . $products->pluck('name')->implode(', '));
            }

            // 9. Verify Scope Change
            $this->info("Switching Context to Tenant 1...");
            app()->instance('tenant', $t1);
            // New query
            $products = Product::all();
            $this->info("Found " . $products->count() . " products.");

            if ($products->count() === 1 && $products->first()->name === 'T1 Product') {
                $this->info("PASS: Only T1 products visible.");
            } else {
                 $this->error("FAIL: Visible products: " . $products->pluck('name')->implode(', '));
            }

        } catch (\Exception $e) {
            $this->error("ERROR: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }

        DB::rollBack(); // Rollback so we don't junk the DB
        $this->info("\n--- VERIFICATION FINISHED ---");
    }
}
