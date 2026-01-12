<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Account;

class EnsureDefaultAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'account_code' => '1000',
                'account_name' => 'Petty Cash',
                'account_type' => 'asset',
                'description' => 'Petty Cash Fund',
                'is_active' => true,
            ],
            [
                'account_code' => '1010',
                'account_name' => 'Cash on Hand',
                'account_type' => 'asset',
                'description' => 'Physical Cash Register',
                'is_active' => true,
            ],
            [
                'account_code' => '1020',
                'account_name' => 'Bank - Checking',
                'account_type' => 'asset',
                'description' => 'Main Checking Account',
                'is_active' => true,
            ],
            [
                'account_code' => '1030',
                'account_name' => 'Bank - Savings',
                'account_type' => 'asset',
                'description' => 'Savings Account',
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $data) {
            Account::firstOrCreate(
                ['account_code' => $data['account_code']],
                $data
            );
        }
    }
}
