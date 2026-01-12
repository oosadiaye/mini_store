<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class WooCommerceService
{
    protected $url;
    protected $consumerKey;
    protected $consumerSecret;

    public function __construct(Tenant $tenant = null)
    {
        if ($tenant) {
            $this->setCredentialsFromTenant($tenant);
        }
    }

    public function setCredentialsFromTenant(Tenant $tenant)
    {
        $settings = $tenant->settings ?? [];
        $this->url = rtrim($settings['woocommerce_url'] ?? '', '/');
        $this->consumerKey = $settings['woocommerce_consumer_key'] ?? null;
        $this->consumerSecret = $settings['woocommerce_consumer_secret'] ?? null;
    }

    public function isConfigured()
    {
        return !empty($this->url) && !empty($this->consumerKey) && !empty($this->consumerSecret);
    }

    protected function client()
    {
        if (!$this->isConfigured()) {
            throw new \Exception("WooCommerce credentials not configured.");
        }

        return Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->baseUrl($this->url . '/wp-json/wc/v3/');
    }

    public function testConnection()
    {
        try {
            $response = $this->client()->get('system_status');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WooCommerce Connection Error: " . $e->getMessage());
            return false;
        }
    }

    public function fetchOrders($params = [])
    {
        $response = $this->client()->get('orders', $params);
        return $response->json();
    }

    public function fetchOrder($id)
    {
        $response = $this->client()->get("orders/{$id}");
        return $response->json();
    }

    public function createWebhook($topic, $deliveryUrl, $secret)
    {
        $response = $this->client()->post('webhooks', [
            'name' => 'Mini Store Sync ' . $topic,
            'topic' => $topic,
            'delivery_url' => $deliveryUrl,
            'secret' => $secret,
            'status' => 'active'
        ]);

        return $response->json();
    }
    
    public function createOrder(array $data)
    {
        $response = $this->client()->post('orders', $data);
        return $response->json();
    }
    
    public function updateOrder($id, array $data)
    {
        $response = $this->client()->put("orders/{$id}", $data);
        return $response->json();
    }
    
    // Additional helpers for products if needed
    public function fetchProductBySku($sku)
    {
        $response = $this->client()->get('products', ['sku' => $sku]);
        $data = $response->json();
        return count($data) > 0 ? $data[0] : null;
    }

    public function processOrder(Tenant $tenant, $wcData)
    {
        // Check if already exists
        $exists = \App\Models\Order::where('tenant_id', $tenant->id)
            ->where('woocommerce_id', $wcData['id'])
            ->exists();
            
        if ($exists) {
            // Update status if changed
            $order = \App\Models\Order::where('tenant_id', $tenant->id)
                ->where('woocommerce_id', $wcData['id'])
                ->first();
                
            if ($order && $order->woocommerce_status !== $wcData['status']) {
                $order->update([
                    'woocommerce_status' => $wcData['status'],
                    // bidirectional: update local status too?
                    // 'status' => $this->mapWcStatusToLocal($wcData['status']),
                ]);
            }
            return;
        }

        // Create Order
        // 1. Find or Create Customer
        $customer = \App\Models\Customer::firstOrCreate(
            ['tenant_id' => $tenant->id, 'email' => $wcData['billing']['email']],
            [
                'name' => $wcData['billing']['first_name'] . ' ' . $wcData['billing']['last_name'],
                'phone' => $wcData['billing']['phone'],
            ]
        );

        // 2. Create Order
        $order = \App\Models\Order::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'order_number' => 'WC-' . $wcData['number'], // Prefix to distinguish
            'woocommerce_id' => $wcData['id'],
            'woocommerce_data' => $wcData,
            'woocommerce_status' => $wcData['status'],
            'status' => $this->mapWcStatusToLocal($wcData['status']),
            'total' => $wcData['total'],
            'amount_paid' => !empty($wcData['date_paid']) ? $wcData['total'] : 0,
            'info_status' => 'pending', // default
            'payment_status' => !empty($wcData['date_paid']) ? 'paid' : 'pending',
            'order_source' => 'storefront', // or 'woocommerce' ideally
        ]);

        // 3. Create Items
        foreach ($wcData['line_items'] as $item) {
            // Find Product locally by SKU or Name
            // If SKU matches, link it.
            $product = null;
            if (!empty($item['sku'])) {
                $product = \App\Models\Product::where('tenant_id', $tenant->id)
                    ->where('sku', $item['sku'])->first();
            }

            \App\Models\OrderItem::create([
                'tenant_id' => $tenant->id,
                'order_id' => $order->id,
                'product_id' => $product ? $product->id : null,
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'],
                'tax_amount' => $item['total_tax'] ?? 0,
            ]);
        }
        
        Log::info("Imported WooCommerce Order ID {$wcData['id']} as Local Order {$order->id}");
    }

    public function mapWcStatusToLocal($wcStatus)
    {
        return match($wcStatus) {
            'completed' => 'delivered',
            'processing' => 'paid',
            'pending' => 'pending',
            'cancelled' => 'cancelled',
            'refunded' => 'cancelled',
            default => 'pending',
        };
    }
}
