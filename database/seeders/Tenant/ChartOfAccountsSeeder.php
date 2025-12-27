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
            // Current Assets (1000-1499)
            ['code' => '1000', 'name' => 'Petty Cash', 'type' => 'asset', 'description' => 'Cash on hand for small expenses'],
            ['code' => '1010', 'name' => 'Cash on Hand', 'type' => 'asset', 'description' => 'Register and till cash'],
            ['code' => '1020', 'name' => 'Bank - Checking', 'type' => 'asset', 'description' => 'Main business checking account'],
            ['code' => '1030', 'name' => 'Bank - Savings', 'type' => 'asset', 'description' => 'Business savings account'],
            ['code' => '1100', 'name' => 'Accounts Receivable - Customers', 'type' => 'asset', 'description' => 'Money owed by customers'],
            ['code' => '1110', 'name' => 'Accounts Receivable - Renters', 'type' => 'asset', 'description' => 'Money owed by renters'],
            ['code' => '1200', 'name' => 'Inventory Asset', 'type' => 'asset', 'description' => 'Value of stock on hand'],
            ['code' => '1300', 'name' => 'Input Tax Receivable', 'type' => 'asset', 'description' => 'VAT/Tax paid on purchases to be claimed'],
            ['code' => '1400', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'description' => 'Expenses paid in advance'],
            
            // Fixed Assets (1500-1999)
            ['code' => '1500', 'name' => 'Furniture & Fixtures', 'type' => 'asset', 'description' => 'Store furniture and shelving'],
            ['code' => '1510', 'name' => 'Accumulated Depreciation - Furniture', 'type' => 'asset', 'description' => 'Contra-asset for furniture depreciation'],
            ['code' => '1600', 'name' => 'Equipment', 'type' => 'asset', 'description' => 'Computers, POS systems, machinery'],
            ['code' => '1610', 'name' => 'Accumulated Depreciation - Equipment', 'type' => 'asset', 'description' => 'Contra-asset for equipment depreciation'],
            ['code' => '1700', 'name' => 'Vehicles', 'type' => 'asset', 'description' => 'Company vehicles'],
            ['code' => '1710', 'name' => 'Accumulated Depreciation - Vehicles', 'type' => 'asset', 'description' => 'Contra-asset for vehicle depreciation'],

            // LIABILITIES (2000-2999)
            // Current Liabilities (2000-2499)
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'description' => 'Money owed to suppliers'],
            ['code' => '2020', 'name' => 'GR/IR Clearing', 'type' => 'liability', 'description' => 'Goods Received / Invoice Received Clearing'],
            ['code' => '2100', 'name' => 'Sales Tax Payable', 'type' => 'liability', 'description' => 'Collected tax to be remitted'],
            ['code' => '2110', 'name' => 'Security Deposits Payable', 'type' => 'liability', 'description' => 'Security deposits held from renters'],
            ['code' => '2200', 'name' => 'Accrued Expenses', 'type' => 'liability', 'description' => 'Expenses incurred but not yet paid'],
            ['code' => '2300', 'name' => 'Unearned Revenue', 'type' => 'liability', 'description' => 'Payments received for future services'],
            
            // Long-term Liabilities (2500-2999)
            ['code' => '2500', 'name' => 'Loans Payable', 'type' => 'liability', 'description' => 'Long-term business loans'],
            ['code' => '2600', 'name' => 'Notes Payable', 'type' => 'liability', 'description' => 'Promissory notes'],

            // EQUITY (3000-3999)
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'equity', 'description' => 'Initial capital investment'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'description' => 'Cumulative net income'],
            ['code' => '3200', 'name' => 'Owner\'s Drawings', 'type' => 'equity', 'description' => 'Owner withdrawals'],

            // REVENUE (4000-4999)
            ['code' => '4000', 'name' => 'Sales Revenue - Products', 'type' => 'revenue', 'description' => 'Income from product sales'],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue', 'description' => 'Income from services'],
            ['code' => '4110', 'name' => 'Rental Revenue', 'type' => 'revenue', 'description' => 'Income from rental services'],
            ['code' => '4200', 'name' => 'Shipping Income', 'type' => 'revenue', 'description' => 'Charged shipping fees'],
            ['code' => '4300', 'name' => 'Sales Discounts', 'type' => 'revenue', 'description' => 'Contra-revenue: discounts given to customers'],
            ['code' => '4400', 'name' => 'Sales Returns & Allowances', 'type' => 'revenue', 'description' => 'Contra-revenue: returns and refunds'],
            ['code' => '4500', 'name' => 'Interest Income', 'type' => 'revenue', 'description' => 'Interest earned on bank accounts'],
            ['code' => '4600', 'name' => 'Other Income', 'type' => 'revenue', 'description' => 'Miscellaneous income'],

            // COST OF GOODS SOLD (5000-5999)
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'description' => 'Direct cost of products sold'],
            ['code' => '5100', 'name' => 'Freight In', 'type' => 'expense', 'description' => 'Shipping costs for incoming inventory'],
            ['code' => '5200', 'name' => 'Purchase Discounts', 'type' => 'expense', 'description' => 'Contra-expense: discounts received from suppliers'],

            // OPERATING EXPENSES (6000-6999)
            // Selling Expenses (6000-6299)
            ['code' => '6000', 'name' => 'Advertising & Marketing', 'type' => 'expense', 'description' => 'Marketing and promotional costs'],
            ['code' => '6100', 'name' => 'Sales Commissions', 'type' => 'expense', 'description' => 'Sales staff commissions'],
            ['code' => '6200', 'name' => 'Delivery Expenses', 'type' => 'expense', 'description' => 'Delivery and shipping costs'],
            
            // Administrative Expenses (6300-6699)
            ['code' => '6300', 'name' => 'Salaries & Wages', 'type' => 'expense', 'description' => 'Employee salaries and wages'],
            ['code' => '6310', 'name' => 'Employee Benefits', 'type' => 'expense', 'description' => 'Health insurance, retirement, etc.'],
            ['code' => '6320', 'name' => 'Payroll Taxes', 'type' => 'expense', 'description' => 'Employer payroll tax obligations'],
            ['code' => '6400', 'name' => 'Rent Expense', 'type' => 'expense', 'description' => 'Office and store rent'],
            ['code' => '6410', 'name' => 'Utilities', 'type' => 'expense', 'description' => 'Electricity, water, gas, internet'],
            ['code' => '6420', 'name' => 'Insurance', 'type' => 'expense', 'description' => 'Business insurance premiums'],
            ['code' => '6430', 'name' => 'Repairs & Maintenance', 'type' => 'expense', 'description' => 'Equipment and facility maintenance'],
            ['code' => '6440', 'name' => 'Office Supplies', 'type' => 'expense', 'description' => 'Stationery and office materials'],
            ['code' => '6450', 'name' => 'Telephone & Internet', 'type' => 'expense', 'description' => 'Communication expenses'],
            ['code' => '6460', 'name' => 'Professional Fees', 'type' => 'expense', 'description' => 'Legal, accounting, consulting fees'],
            ['code' => '6470', 'name' => 'Bank Charges & Fees', 'type' => 'expense', 'description' => 'Bank service charges'],
            ['code' => '6480', 'name' => 'Depreciation Expense', 'type' => 'expense', 'description' => 'Asset depreciation'],
            ['code' => '6490', 'name' => 'Bad Debt Expense', 'type' => 'expense', 'description' => 'Uncollectible receivables'],
            
            // Other Expenses (6700-6999)
            ['code' => '6700', 'name' => 'Interest Expense', 'type' => 'expense', 'description' => 'Interest on loans and credit'],
            ['code' => '6800', 'name' => 'Travel & Entertainment', 'type' => 'expense', 'description' => 'Business travel and meals'],
            ['code' => '6900', 'name' => 'Miscellaneous Expenses', 'type' => 'expense', 'description' => 'Other operating expenses'],
        ];

        foreach ($accounts as $account) {
            Account::create([
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'account_type' => $account['type'],
                'description' => $account['description'],
                'is_active' => true,
            ]);
        }
    }
}
