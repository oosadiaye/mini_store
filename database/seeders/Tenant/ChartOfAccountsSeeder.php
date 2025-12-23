<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Prevent duplicates
        if (Account::count() > 0) {
            return;
        }

        $accounts = [
            // ASSETS (1000-1999)
            // Current Assets
            ['code' => '1010', 'name' => 'Cash on Hand', 'type' => 'asset', 'description' => 'Petty cash and register funds'],
            ['code' => '1020', 'name' => 'Bank - Checking', 'type' => 'asset', 'description' => 'Main business checking account'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset', 'description' => 'Money owed by customers'],
            ['code' => '1200', 'name' => 'Inventory Asset', 'type' => 'asset', 'description' => 'Value of stock on hand'],
            ['code' => '1300', 'name' => 'Input Tax Receivable', 'type' => 'asset', 'description' => 'Tax paid on purchases to be claimed'],
            
            // Fixed Assets
            ['code' => '1500', 'name' => 'Furniture & Fixtures', 'type' => 'asset', 'description' => 'Store furniture and shelving'],
            ['code' => '1600', 'name' => 'Equipment', 'type' => 'asset', 'description' => 'Computers, POS systems, etc.'],

            // LIABILITIES (2000-2999)
            // Current Liabilities
            ['code' => '2010', 'name' => 'Accounts Payable', 'type' => 'liability', 'description' => 'Money owed to suppliers'],
            ['code' => '2020', 'name' => 'GR/IR Clearing', 'type' => 'liability', 'description' => 'Goods Received / Invoice Received Clearing'],
            ['code' => '2100', 'name' => 'Sales Tax Payable', 'type' => 'liability', 'description' => 'Collected tax to be remitted'],
            
            // EQUITY (3000-3999)
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'equity', 'description' => 'Initial capital investment'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'description' => 'Cumulative net income'],

            // REVENUE (4000-4999)
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue', 'description' => 'Income from product sales'],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue', 'description' => 'Income from services'],
            ['code' => '4200', 'name' => 'Shipping Income', 'type' => 'revenue', 'description' => 'Charged shipping fees'],
            ['code' => '4300', 'name' => 'Discounts Given', 'type' => 'revenue', 'description' => 'Contra-revenue account for sales discounts'],

            // COGS (5000-5999)
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'description' => 'Direct cost of products sold'],
            ['code' => '5100', 'name' => 'Freight In', 'type' => 'expense', 'description' => 'Shipping costs for incoming inventory'],

            // EXPENSES (6000-6999)
            ['code' => '6100', 'name' => 'Rent Expense', 'type' => 'expense', 'description' => 'Store rent'],
            ['code' => '6200', 'name' => 'Salaries & Wages', 'type' => 'expense', 'description' => 'Employee payroll'],
            ['code' => '6300', 'name' => 'Utilities', 'type' => 'expense', 'description' => 'Electricity, water, internet'],
            ['code' => '6400', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'description' => 'Promotional costs'],
            ['code' => '6500', 'name' => 'Office Supplies', 'type' => 'expense', 'description' => 'Consumables'],
            ['code' => '6600', 'name' => 'Travel & Meals', 'type' => 'expense', 'description' => 'Business travel'],
        ];

        foreach ($accounts as $acc) {
            Account::create([
                'account_code' => $acc['code'],
                'account_name' => $acc['name'],
                'account_type' => $acc['type'],
                'description' => $acc['description'],
                'is_active' => true,
            ]);
        }
    }
}
