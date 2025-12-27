<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'Free',
                'price' => 0,
                'duration_days' => 30,
                'trial_days' => 0,
                'features' => [
                    // Core Features
                    'products',
                    'categories',
                    'customers',
                    'suppliers',
                    'sales',
                    'purchases',
                    'inventory',
                    'pos_retail',
                    'accounting_core',
                    'reports_basic',
                ],
                'caps' => [
                    'products_limit' => 50,
                    'orders_limit' => 100,
                    'users_limit' => 1,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Starter',
                'price' => 5000, // Naira
                'duration_days' => 30,
                'trial_days' => 14,
                'features' => [
                    // Core Features
                    'products',
                    'categories',
                    'customers',
                    'suppliers',
                    'sales',
                    'purchases',
                    'inventory',
                    'pos_retail',
                    'online_store',
                    'accounting_core',
                    'reports_basic',
                    'marketing',
                ],
                'caps' => [
                    'products_limit' => 500,
                    'orders_limit' => 1000,
                    'users_limit' => 3,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Growth',
                'price' => 15000,
                'duration_days' => 30,
                'trial_days' => 14,
                'features' => [
                    // Core Features
                    'products',
                    'categories',
                    'customers',
                    'suppliers',
                    'sales',
                    'purchases',
                    'inventory',
                    'inventory_advanced', // Warehouses, Transfers, Stock Adjustments
                    'pos_retail',
                    'online_store',
                    'accounting_core',
                    'accounting_advanced', // Expenses, Incomes, Advanced Reports
                    'crm',
                    'marketing',
                    'reports_advanced',
                    'team_management',
                    'custom_domain',
                ],
                'caps' => [
                    'products_limit' => 5000,
                    'orders_limit' => 10000,
                    'users_limit' => 10,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'price' => 45000,
                'duration_days' => 30,
                'trial_days' => 30,
                'features' => [
                    // All Features
                    'products',
                    'categories',
                    'customers',
                    'suppliers',
                    'sales',
                    'purchases',
                    'inventory',
                    'inventory_advanced',
                    'pos_retail',
                    'online_store',
                    'accounting_core',
                    'accounting_advanced',
                    'crm',
                    'marketing',
                    'reports_basic',
                    'reports_advanced',
                    'reports_inventory', // Stock Reports
                    'team_management',
                    'priority_support',
                    'custom_domain',
                ],
                'caps' => [
                    'products_limit' => null, // Unlimited
                    'orders_limit' => null,
                    'users_limit' => null,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
