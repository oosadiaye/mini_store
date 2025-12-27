<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxCode;

class TaxCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxCodes = [
            [
                'code' => 'NO_TAX',
                'name' => 'No Tax',
                'rate' => 0.00,
                'description' => 'No tax applicable',
                'is_active' => true,
            ],
            [
                'code' => 'VAT_5',
                'name' => 'VAT 5%',
                'rate' => 5.00,
                'description' => 'Value Added Tax at 5%',
                'is_active' => true,
            ],
            [
                'code' => 'VAT_7.5',
                'name' => 'VAT 7.5%',
                'rate' => 7.50,
                'description' => 'Value Added Tax at 7.5%',
                'is_active' => true,
            ],
            [
                'code' => 'VAT_10',
                'name' => 'VAT 10%',
                'rate' => 10.00,
                'description' => 'Value Added Tax at 10%',
                'is_active' => true,
            ],
            [
                'code' => 'VAT_15',
                'name' => 'VAT 15%',
                'rate' => 15.00,
                'description' => 'Value Added Tax at 15%',
                'is_active' => true,
            ],
        ];

        foreach ($taxCodes as $taxCode) {
            TaxCode::updateOrCreate(
                ['code' => $taxCode['code']],
                $taxCode
            );
        }
    }
}
