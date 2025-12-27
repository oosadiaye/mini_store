<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Prevent duplicates
        if (Permission::count() > 0) {
            return;
        }

        $permissions = [
            // Products
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            
            // Orders
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            
            // Customers
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            
            // Suppliers
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',
            
            // Inventory
            'view_inventory',
            'manage_inventory',
            
            // Purchase Orders
            'view_purchases',
            'create_purchases',
            'edit_purchases',
            'delete_purchases',
            
            // Accounting
            'view_accounting',
            'manage_accounting',
            'view_reports',
            
            // Renters
            'view_renters',
            'create_renters',
            'edit_renters',
            'delete_renters',
            
            // Users & Roles
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            
            // Settings
            'manage_settings',
            'manage_warehouses',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
