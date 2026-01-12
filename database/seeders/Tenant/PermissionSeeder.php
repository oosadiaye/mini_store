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
            // Dashboard
            'view_dashboard',
            'view_analytics',

            // Products & Inventory
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_inventory', // Stock adjustments
            'view_warehouses',
            'manage_warehouses',
            'view_brands',
            'manage_brands',
            'view_categories',
            'manage_categories',
            
            // Sales & POS
            'access_pos',
            'create_orders',
            'view_orders',
            'edit_orders',
            'delete_orders',
            'manage_returns',
            'view_pos_reports',
            
            // Customers
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            
            // Suppliers & POs
            'view_suppliers',
            'manage_suppliers', // create/edit/delete
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'delete_purchase_orders',
            'receive_items', // GRN
            
            // Accounting
            'access_accounting',
            'view_financial_reports',
            'manage_accounts',
            'manage_journals',
            'view_payments',
            'manage_payments',
            
            // Storefront
            'manage_storefront', // Banners, settings
            
            // Users & Roles
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_roles',
            'manage_roles',
            
            // Settings
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
