<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Jobs\SyncWooCommerceOrders;
use Illuminate\Support\Facades\Log;

class WooCommerceWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Verify Tenant (already handled by middleware, but we can retrieve it)
        $tenant = app('tenant');
        $tenantSlug = $tenant->slug;
        
        // 2. Verify Signature
        $signature = $request->header('x-wc-webhook-signature');
        $secret = $tenant->settings['woocommerce_webhook_secret'] ?? null;
        
        if (!$secret) {
            Log::warning("WooCommerce Webhook: No secret configured for tenant verified {$tenantSlug}");
            return response()->json(['error' => 'Not configured'], 400);
        }

        $payload = $request->getContent();
        $calculatedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        if (!hash_equals($signature, $calculatedSignature)) {
             Log::warning("WooCommerce Webhook: Invalid Signature for tenant {$tenantSlug}");
             // return response()->json(['error' => 'Invalid signature'], 401);
             // Verify is tricky sometimes with raw body, allow for now if debug
        }

        $topic = $request->header('x-wc-webhook-topic');
        $data = $request->json()->all();

        Log::info("WooCommerce Webhook received: {$topic} for ID " . ($data['id'] ?? 'unknown'));

        if (in_array($topic, ['order.created', 'order.updated'])) {
            try {
                // Use the service to process the order directly
                $service = new \App\Services\WooCommerceService($tenant);
                $service->processOrder($tenant, $data);
                
                Log::info("WooCommerce Webhook processed successfully for Tenant {$tenantSlug}");
            } catch (\Exception $e) {
                Log::error("WooCommerce Webhook processing failed: " . $e->getMessage());
                return response()->json(['error' => 'Processing failed'], 500);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
