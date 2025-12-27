<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Initialize a payment via Paystack
     */
    public function initializePaystack($amount, $email, $reference, $callbackUrl)
    {
        $gateway = PaymentGateway::where('name', 'paystack')->first();
        if (!$gateway || !$gateway->is_active) {
            throw new \Exception('Paystack is not active.');
        }

        $secretKey = $gateway->config['secret_key'] ?? null;
        if (!$secretKey) {
            throw new \Exception('Paystack secret key not configured.');
        }

        // Amount in kobo
        $amountInKobo = $amount * 100;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize', [
            'amount' => $amountInKobo,
            'email' => $email,
            'reference' => $reference,
            'callback_url' => $callbackUrl,
        ]);

        if ($response->successful()) {
            return $response->json()['data']['authorization_url'];
        }

        throw new \Exception('Paystack initialization failed: ' . $response->body());
    }

    /**
     * Verify a payment via Paystack
     */
    public function verifyPaystack($reference)
    {
        $gateway = PaymentGateway::where('name', 'paystack')->first();
        if (!$gateway || !$gateway->is_active) {
            throw new \Exception('Paystack is not active.');
        }

        $secretKey = $gateway->config['secret_key'] ?? null;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
        ])->get("https://api.paystack.co/transaction/verify/{$reference}");

        if ($response->successful()) {
            $data = $response->json()['data'];
            return $data['status'] === 'success';
        }

        return false;
    }

    /**
     * Initialize a payment via Flutterwave
     */
    public function initializeFlutterwave($amount, $email, $reference, $callbackUrl, $tenantName)
    {
        $gateway = PaymentGateway::where('name', 'flutterwave')->first();
        if (!$gateway || !$gateway->is_active) {
            throw new \Exception('Flutterwave is not active.');
        }

        $secretKey = $gateway->config['secret_key'] ?? null;
        if (!$secretKey) {
            throw new \Exception('Flutterwave secret key not configured.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.flutterwave.com/v3/payments', [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => 'NGN', // Assuming NGN for now, needs dynamic later
            'redirect_url' => $callbackUrl,
            'customer' => [
                'email' => $email,
                'name' => $tenantName,
            ],
            'customizations' => [
                'title' => 'Subscription Payment',
                'description' => 'Payment for subscription plan',
            ],
        ]);

        if ($response->successful()) {
            return $response->json()['data']['link'];
        }

        throw new \Exception('Flutterwave initialization failed: ' . $response->body());
    }

    /**
     * Verify a payment via Flutterwave
     */
    public function verifyFlutterwave($transactionId)
    {
        $gateway = PaymentGateway::where('name', 'flutterwave')->first();
        if (!$gateway || !$gateway->is_active) {
            throw new \Exception('Flutterwave is not active.');
        }

        $secretKey = $gateway->config['secret_key'] ?? null;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
        ])->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

        if ($response->successful()) {
            $data = $response->json()['data'];
            return $data['status'] === 'successful';
        }

        return false;
    }
}
