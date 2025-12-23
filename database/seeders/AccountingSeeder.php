<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Account;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets
            ['code' => '1000', 'name' => 'Assets', 'type' => 'asset', 'children' => [
                ['code' => '1100', 'name' => 'Current Assets', 'type' => 'asset', 'children' => [
                    ['code' => '1110', 'name' => 'Cash', 'type' => 'asset'],
                    ['code' => '1120', 'name' => 'Bank', 'type' => 'asset'],
                    ['code' => '1130', 'name' => 'Accounts Receivable', 'type' => 'asset'],
                    ['code' => '1140', 'name' => 'Inventory Asset', 'type' => 'asset'],
                ]],
                ['code' => '1200', 'name' => 'Fixed Assets', 'type' => 'asset', 'children' => [
                    ['code' => '1210', 'name' => 'Equipment', 'type' => 'asset'],
                    ['code' => '1220', 'name' => 'Furniture', 'type' => 'asset'],
                ]],
            ]],

            // Liabilities
            ['code' => '2000', 'name' => 'Liabilities', 'type' => 'liability', 'children' => [
                ['code' => '2100', 'name' => 'Current Liabilities', 'type' => 'liability', 'children' => [
                    ['code' => '2110', 'name' => 'Accounts Payable', 'type' => 'liability'],
                    ['code' => '2120', 'name' => 'Sales Tax Payable', 'type' => 'liability'],
                ]],
            ]],

            // Equity
            ['code' => '3000', 'name' => 'Equity', 'type' => 'equity', 'children' => [
                ['code' => '3100', 'name' => 'Owner\'s Equity', 'type' => 'equity'],
                ['code' => '3200', 'name' => 'Retained Earnings', 'type' => 'equity'],
            ]],

            // Revenue
            ['code' => '4000', 'name' => 'Revenue', 'type' => 'revenue', 'children' => [
                ['code' => '4100', 'name' => 'Sales Revenue', 'type' => 'revenue'],
                ['code' => '4200', 'name' => 'Service Revenue', 'type' => 'revenue'],
            ]],

            // Expenses
            ['code' => '5000', 'name' => 'Expenses', 'type' => 'expense', 'children' => [
                ['code' => '5100', 'name' => 'Cost of Goods Sold', 'type' => 'expense'],
                ['code' => '5200', 'name' => 'Operating Expenses', 'type' => 'expense', 'children' => [
                    ['code' => '5210', 'name' => 'Rent Expense', 'type' => 'expense'],
                    ['code' => '5220', 'name' => 'Salaries Expense', 'type' => 'expense'],
                    ['code' => '5230', 'name' => 'Utilities Expense', 'type' => 'expense'],
                    ['code' => '5240', 'name' => 'Marketing Expense', 'type' => 'expense'],
                ]],
            ]],
        ];

        foreach ($accounts as $account) {
            $this->createAccount($account);
        }
    }

    private function createAccount($data, $parentId = null)
    {
        $account = Account::firstOrCreate(
            ['account_code' => $data['code']],
            [
                'account_name' => $data['name'],
                'account_type' => $data['type'],
                'parent_id' => $parentId,
            ]
        );

        if (isset($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->createAccount($child, $account->id);
            }
        }
    }
}
