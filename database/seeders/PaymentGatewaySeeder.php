<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'paystack',
                'display_name' => 'Paystack',
                'is_active' => false,
                'config' => [
                    'public_key' => '',
                    'secret_key' => '',
                ],
            ],
            [
                'name' => 'flutterwave',
                'display_name' => 'Flutterwave',
                'is_active' => false,
                'config' => [
                    'public_key' => '',
                    'secret_key' => '',
                    'encryption_key' => '',
                ],
            ],
            [
                'name' => 'bank_transfer',
                'display_name' => 'Bank Transfer',
                'is_active' => true,
                'config' => [
                    'bank_name' => 'First Bank',
                    'account_name' => 'Mini Store SuperAdmin',
                    'account_number' => '2004567890',
                    'instructions' => 'Please include your Tenant Name in the transfer description.',
                ],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['name' => $gateway['name']],
                $gateway
            );
        }
    }
}
