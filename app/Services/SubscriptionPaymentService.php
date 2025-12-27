<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscriptionPaymentService
{
    public function initialize(Tenant $tenant, Plan $plan)
    {
        $gateway = PaymentGateway::where('is_active', true)->first();

        if (!$gateway) {
            throw new \Exception("No payment gateway configured by SuperAdmin.");
        }

        switch ($gateway->name) {
            case 'paystack':
                return $this->initializePaystack($tenant, $plan, $gateway);
            case 'flutterwave':
                return $this->initializeFlutterwave($tenant, $plan, $gateway);
            case 'bank_transfer':
                return [
                    'success' => true,
                    'is_manual' => true,
                    'gateway_name' => 'bank_transfer',
                    'bank_details' => $gateway->config
                ];
            default:
                throw new \Exception("Unsupported payment provider: {$gateway->name}");
        }
    }

    public function verify($reference)
    {
        // We might not know the provider just from reference in all cases, 
        // but typically we can infer or pass it. 
        // For simplicity, let's check the active gateway again or loop.
        // Usually verification happens on a callback where we know the provider.
        
        $gateway = PaymentGateway::where('is_active', true)->first();
        if (!$gateway) return ['success' => false, 'message' => 'No active gateway'];

        switch ($gateway->name) {
            case 'paystack':
                return $this->verifyPaystack($reference, $gateway);
            case 'flutterwave':
                return $this->verifyFlutterwave($reference, $gateway);
             default:
                return ['success' => false, 'message' => 'Unsupported provider'];
        }
    }

    private function initializePaystack(Tenant $tenant, Plan $plan, PaymentGateway $gateway)
    {
        $secretKey = $gateway->config['secret_key'] ?? null;
        if (!$secretKey) throw new \Exception("Paystack Secret Key not configured in SuperAdmin settings.");

        $callbackUrl = route('tenant.subscription.callback', ['tenant' => $tenant->slug]);
        $reference = 'sub_' . $tenant->id . '_' . time();
        $email = $tenant->email; // Tenant Admin Email

        $response = Http::withToken($secretKey)->post('https://api.paystack.co/transaction/initialize', [
            'amount' => $plan->price * 100, // Kobo
            'email' => $email,
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'metadata' => [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'type' => 'subscription'
            ]
        ]);

        if (!$response->successful()) {
            Log::error('Paystack Subscription Init Error', $response->json());
            throw new \Exception("Payment Initialization Failed: " . ($response->json()['message'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'checkout_url' => $response->json()['data']['authorization_url'],
            'reference' => $reference
        ];
    }

    private function verifyPaystack($reference, PaymentGateway $gateway)
    {
        $secretKey = $gateway->config['secret_key'] ?? null;
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
                'metadata' => $data['metadata'] ?? [],
                'gateway_response' => $data
            ];
        }

        return ['success' => false, 'status' => $data['status']];
    }

    private function initializeFlutterwave(Tenant $tenant, Plan $plan, PaymentGateway $gateway)
    {
         $secretKey = $gateway->config['secret_key'] ?? null;
        if (!$secretKey) throw new \Exception("Flutterwave Secret Key not configured.");
        
        $callbackUrl = route('tenant.subscription.callback', ['tenant' => $tenant->slug]);
        $reference = 'sub_' . $tenant->id . '_' . time();
        
        $response = Http::withToken($secretKey)->post('https://api.flutterwave.com/v3/payments', [
            'tx_ref' => $reference,
            'amount' => $plan->price,
            'currency' => 'NGN',
            'redirect_url' => $callbackUrl,
            'customer' => [
                'email' => $tenant->email,
                'name' => $tenant->name,
            ],
            'meta' => [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'type' => 'subscription'
            ]
        ]);

         if (!$response->successful()) {
            Log::error('Flutterwave Subscription Init Error', $response->json());
             throw new \Exception("Payment Initialization Failed: " . ($response->json()['message'] ?? 'Unknown error'));
        }

        return [
            'success' => true,
            'checkout_url' => $response->json()['data']['link'],
            'reference' => $reference
        ];
    }

    private function verifyFlutterwave($transaction_id, PaymentGateway $gateway)
    {
         $secretKey = $gateway->config['secret_key'] ?? null;
         if (!$secretKey) throw new \Exception("Flutterwave Secret Key not configured.");
         
         // Flutterwave verification uses ID, not ref, usually passed as query param 'transaction_id'
         // But validation might receive text_ref if we used that. 
         // Let's assume the controller passes the correct ID.
         
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
                'metadata' => $data['meta'] ?? [], // Flutterwave uses 'meta' not 'metadata' in some responses, check docs.
                'gateway_response' => $data
            ];
        }
        
        return ['success' => false, 'status' => $data['status']];
    }
}
