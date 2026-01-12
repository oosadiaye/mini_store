<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DefaultRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Manager - All permissions
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions(Permission::all());

        // Sales
        $sales = Role::firstOrCreate(['name' => 'Sales', 'guard_name' => 'web']);
        $sales->syncPermissions([
            'view_dashboard',
            'create_orders',
            'view_orders',
            'edit_orders',
            'delete_orders',
            'view_customers',
            'create_customers',
            'edit_customers',
            'view_products',
            'view_categories',
            'view_brands',
        ]);

        // Inventory
        $inventory = Role::firstOrCreate(['name' => 'Inventory', 'guard_name' => 'web']);
        $inventory->syncPermissions([
            'view_dashboard',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_inventory',
            'view_warehouses',
            'view_brands',
            'manage_brands',
            'view_categories',
            'manage_categories',
            'view_stock_movements', // assuming this exists or is covered by manage_inventory, adding broadly
            // If strict 'view_stock_movements' wasn't in PermissionSeeder, we might miss it. 
            // PermissionSeeder has 'view_dashboard', 'view_analytics', etc. 
            // Let's stick to what we added in PermissionSeeder.
        ]);
        
        // Accounts
        $accounts = Role::firstOrCreate(['name' => 'Accounts', 'guard_name' => 'web']);
        $accounts->syncPermissions([
            'view_dashboard',
            'access_accounting',
            'view_financial_reports',
            'manage_accounts',
            'manage_journals',
            'view_payments',
            'manage_payments',
            'view_orders',
            'view_purchase_orders',
            'view_users', // Often needed to check who sold what
        ]);

        // POs (Purchase Orders Manager)
        $pos = Role::firstOrCreate(['name' => 'POs', 'guard_name' => 'web']);
        $pos->syncPermissions([
            'view_dashboard',
            'view_suppliers',
            'manage_suppliers',
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'delete_purchase_orders',
            'receive_items',
            'view_products',
            'view_warehouses',
        ]);

        // Cashier
        $cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions([
            'access_pos',
            'create_orders',
            'view_orders', // usually just their own, but permission is binary
            'view_products',
            'view_customers',
            'create_customers',
            'view_pos_reports', // maybe restricted
        ]);

        // Store (Store Manager / Storefront)
        $store = Role::firstOrCreate(['name' => 'Store', 'guard_name' => 'web']);
        $store->syncPermissions([
            'view_dashboard',
            'view_products',
            'manage_storefront',
            'manage_settings', // Maybe too broad? Keeping it based on request context usually implying Store Management
            'view_orders',
        ]);
    }
}
