<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function initializeTransaction(Order $order, string $provider)
    {
        $settings = app('tenant')->data;
        $amount = $order->total;
        $email = $order->customer->email;
        $callbackUrl = route('storefront.checkout.callback');
        $reference = $order->order_number . '_' . time(); // Unique ref

        switch ($provider) {
            case 'opay':
                return $this->initializeOpay($order, $amount, $email, $callbackUrl, $reference, $settings);
            case 'moniepoint':
                return $this->initializeMoniepoint($order, $amount, $email, $callbackUrl, $reference, $settings);
            default:
                throw new \Exception("Unsupported payment provider: {$provider}");
        }
    }

    public function verifyTransaction(string $reference, string $provider)
    {
        $settings = app('tenant')->data;

        switch ($provider) {
            case 'paystack':
                return $this->verifyPaystack($reference, $settings);
            case 'flutterwave':
                return $this->verifyFlutterwave($reference, $settings);
            case 'opay':
                return $this->verifyOpay($reference, $settings);
            case 'moniepoint':
                return $this->verifyMoniepoint($reference, $settings);
            default:
                throw new \Exception("Unsupported payment provider: {$provider}");
        }
    }

    // ... existing Paystack/Flutterwave methods ...

    private function initializePaystack($order, $amount, $email, $callbackUrl, $reference, $settings)
    {
        $secretKey = $settings['gateway_paystack_secret_key'] ?? null;
        if (!$secretKey) throw new \Exception("Paystack Secret Key not configured.");

        $response = Http::withToken($secretKey)->post('https://api.paystack.co/transaction/initialize', [
            'amount' => $amount * 100, // Kobo
            'email' => $email,
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]
        ]);

        if (!$response->successful()) {
            Log::error('Paystack Init Error', $response->json());
             throw new \Exception("Paystack Initialization Failed: " . ($response->json()['message'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'checkout_url' => $response->json()['data']['authorization_url'],
            'reference' => $reference
        ];
    }

    private function verifyPaystack($reference, $settings)
    {
        $secretKey = $settings['gateway_paystack_secret_key'] ?? null;
        if (!$secretKey) throw new \Exception("Paystack Secret Key not configured.");

        $response = Http::withToken($secretKey)->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (!$response->successful()) {
             return ['success' => false, 'message' => 'Verification failed'];
        }

        $data = $response->json()['data'];
        
        if ($data['status'] === 'success') {
            return [
                'success' => true,
                'status' => 'success',
                'amount' => $data['amount'] / 100,
                'reference' => $data['reference'],
                'gateway_response' => $data
            ];
        }

        return ['success' => false, 'status' => $data['status']];
    }

    private function initializeFlutterwave($order, $amount, $email, $callbackUrl, $reference, $settings)
    {
         $secretKey = $settings['gateway_flutterwave_secret_key'] ?? null;
        if (!$secretKey) throw new \Exception("Flutterwave Secret Key not configured.");
        
        $response = Http::withToken($secretKey)->post('https://api.flutterwave.com/v3/payments', [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => $settings['currency_code'] ?? 'NGN',
            'redirect_url' => $callbackUrl,
            'customer' => [
                'email' => $email,
                'name' => $order->customer->name,
                'phonenumber' => $order->customer->phone
            ],
            'meta' => [
                'order_id' => $order->id
            ]
        ]);

         if (!$response->successful()) {
            Log::error('Flutterwave Init Error', $response->json());
             throw new \Exception("Flutterwave Initialization Failed: " . ($response->json()['message'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'checkout_url' => $response->json()['data']['link'],
            'reference' => $reference
        ];
    }
    
    private function verifyFlutterwave($transaction_id, $settings)
    {
         // Note: Callbacks usually send transaction_id (id) and tx_ref
         $secretKey = $settings['gateway_flutterwave_secret_key'] ?? null;
         if (!$secretKey) throw new \Exception("Flutterwave Secret Key not configured.");
         
         $response = Http::withToken($secretKey)->get("https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify");
         
         if (!$response->successful()) {
             return ['success' => false, 'message' => 'Verification failed'];
        }

        $data = $response->json()['data'];
        
        if ($data['status'] === 'successful') {
             return [
                'success' => true,
                'status' => 'success',
                'amount' => $data['amount'],
                'reference' => $data['tx_ref'],
                'gateway_response' => $data
            ];
        }
        
        return ['success' => false, 'status' => $data['status']];
    }

    private function initializeOpay($order, $amount, $email, $callbackUrl, $reference, $settings)
    {
        $publicKey = $settings['gateway_opay_public_key'] ?? null;
        $merchantId = $settings['gateway_opay_merchant_id'] ?? null;

        if (!$publicKey || !$merchantId) throw new \Exception("Opay Credentials (Public Key / Merchant ID) not configured.");

        // Opay requires generic usage. Using standard Cashier API endpoint.
        $response = Http::post('https://cashierapi.opayweb.com/api/v3/cashier/initialize', [
            'merchantId' => $merchantId,
            'reference' => $reference,
            'amount' => $amount * 100, // Kobo
            'currency' => 'NGN', 
            'product' => [
                'description' => "Order #{$order->order_number}",
                'name' => "Order Payment"
            ],
            'returnUrl' => $callbackUrl,
            'userInfo' => [
                'userEmail' => $email,
                'userId' => $order->customer_id,
                'userMobile' => $order->customer->phone
            ],
            'payMethod' => 'BankCard', // Default
        ]);

        if (!$response->successful()) {
             // Fallback for mocked environment or error
             Log::error("Opay Init Failed", $response->json());
             throw new \Exception("Opay Initialization Failed.");
        }

        return [
           'success' => true,
           'checkout_url' => $response->json()['data']['cashierUrl'],
           'reference' => $reference
        ];
    }

    private function verifyOpay($reference, $settings)
    {
         // Opay verification typically via webhook, but query status check:
         $merchantId = $settings['gateway_opay_merchant_id'] ?? null;
         $publicKey = $settings['gateway_opay_public_key'] ?? null; // Usually uses private/secret for backend verify

         if (!$merchantId) throw new \Exception("Opay Merchant ID missing.");

         $response = Http::post('https://cashierapi.opayweb.com/api/v3/cashier/status', [
             'merchantId' => $merchantId,
             'reference' => $reference,
             'orderNo' => $reference
         ]);

         if ($response->successful() && $response->json()['data']['status'] === 'OS00') { // OS00 = Success (Example)
             return [
                 'success' => true,
                 'status' => 'success',
                 'amount' => $response->json()['data']['amount'] / 100,
                 'reference' => $reference,
                 'gateway_response' => $response->json()
             ];
         }

         return ['success' => false, 'status' => 'failed'];
    }

    private function initializeMoniepoint($order, $amount, $email, $callbackUrl, $reference, $settings)
    {
        // Using Monery/Monnify endpoint often used by Moniepoint
        $apiKey = $settings['gateway_moniepoint_public_key'] ?? null; // Usually API Key
        $contractCode = $settings['gateway_moniepoint_merchant_id'] ?? null; // Contract Code

        if (!$apiKey || !$contractCode) throw new \Exception("Moniepoint/Monnify Credentials not configured.");

        $encodedKey = base64_encode($apiKey . ':' . ($settings['gateway_moniepoint_secret_key'] ?? ''));

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $encodedKey
        ])->post('https://api.monnify.com/api/v1/merchant/transactions/init-transaction', [
            'amount' => $amount,
            'customerName' => $order->customer->name,
            'customerEmail' => $email,
            'paymentReference' => $reference,
            'paymentDescription' => "Order #{$order->order_number}",
            'currencyCode' => 'NGN',
            'contractCode' => $contractCode,
            'redirectUrl' => $callbackUrl,
            'paymentMethods' => ['CARD', 'ACCOUNT_TRANSFER']
        ]);

        if (!$response->successful()) {
            Log::error("Moniepoint Init Failed", $response->json());
            throw new \Exception("Moniepoint/Monnify Init Failed: " . ($response->json()['responseMessage'] ?? 'Unknown'));
        }

        return [
            'success' => true,
            'checkout_url' => $response->json()['responseBody']['checkoutUrl'],
            'reference' => $reference
        ];
    }

    private function verifyMoniepoint($reference, $settings)
    {
        // Monnify Verify
        $apiKey = $settings['gateway_moniepoint_public_key'] ?? null;
        $encodedKey = base64_encode($apiKey . ':' . ($settings['gateway_moniepoint_secret_key'] ?? ''));

        // Monnify often uses transactionReference which we set as $reference
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $encodedKey
        ])->get("https://api.monnify.com/api/v1/merchant/transactions/query?paymentReference={$reference}");

        if ($response->successful() && $response->json()['responseBody']['paymentStatus'] === 'PAID') {
            return [
                 'success' => true,
                 'status' => 'success',
                 'amount' => $response->json()['responseBody']['amountPaid'],
                 'reference' => $reference,
                 'gateway_response' => $response->json()
            ];
        }

        return ['success' => false, 'status' => 'failed'];
    }
}
