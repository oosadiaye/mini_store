<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TenantRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if (!\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
            $this->command->error('Permissions table not found in tenant database!');
            return;
        }

        // 1. Define Permissions
        $permissions = [
            // Dashboard
            'view dashboard',
            'view analytics',

            // Products & Inventory
            'view products',
            'create products',
            'edit products',
            'delete products',
            'manage inventory', // Stock adjustments
            'view brands',
            'manage brands',
            'view categories',
            'manage categories',
            
            // Sales & POS
            'access pos',
            'create orders',
            'view orders',
            'edit orders',
            'delete orders', // Usually restricted
            'manage returns',
            'view pos reports',

            // Customers
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',

            // Purchase & Suppliers
            'view suppliers',
            'manage suppliers',
            'view purchase orders',
            'create purchase orders',
            'edit purchase orders',
            'delete purchase orders',
            'receive items', // GRN

            // Accounting & Finance
            'access accounting',
            'view financial reports',
            'manage accounts',
            'manage journals',
            'view payments',
            'manage payments',

            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'manage roles',

            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']); // Using 'web' as tenant users use 'web' guard in tenant domain context? 
            // Wait, standard guard is 'web'. Tenant users auth uses 'web' guard usually.
        }

        // 2. Define Roles
        
        // Store Admin (Owner)
        $admin = Role::firstOrCreate(['name' => 'Store Admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // Sales Staff
        $sales = Role::firstOrCreate(['name' => 'Sales', 'guard_name' => 'web']);
        $sales->givePermissionTo([
            'view dashboard',
            'view products',
            'view categories',
            'create orders',
            'view orders',
            'edit orders',
            'manage returns',
            'view customers',
            'create customers',
            'edit customers',
        ]);

        // Inventory Manager
        $inventory = Role::firstOrCreate(['name' => 'Inventory', 'guard_name' => 'web']);
        $inventory->givePermissionTo([
            'view dashboard',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'manage inventory',
            'view brands',
            'manage brands',
            'view categories',
            'manage categories',
            'view suppliers',
        ]);

        // Purchase Manager
        $purchase = Role::firstOrCreate(['name' => 'Purchase Manager', 'guard_name' => 'web']);
        $purchase->givePermissionTo([
            'view dashboard',
            'view products',
            'view suppliers',
            'manage suppliers',
            'view purchase orders',
            'create purchase orders',
            'edit purchase orders',
            'delete purchase orders',
            'receive items',
            'manage inventory',
        ]);

        // Accountant
        $accountant = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $accountant->givePermissionTo([
            'view dashboard',
            'access accounting',
            'view financial reports',
            'manage accounts',
            'manage journals',
            'view payments',
            'manage payments',
            'view orders',
            'view purchase orders',
        ]);
        
        // Cashier
        $cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashier->givePermissionTo([
            'access pos',
            'view pos reports',
            'create orders',
            // Typically cashiers can also view products/customers/etc in POS
            'view products',
            'view customers',
            'create customers', 
        ]);
    }
}
