<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WooCommerceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PushOrderToWooCommerce implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $action;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, $action = 'create')
    {
        $this->order = $order;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(WooCommerceService $service): void
    {
        // Ensure tenant context is set for the service
        if ($this->order->tenant) {
            $service->setCredentialsFromTenant($this->order->tenant);
        }

        if (!$service->isConfigured()) {
            return;
        }

        $data = $this->mapOrderToWooCommerce($this->order);
        
        try {
            if ($this->order->woocommerce_id) {
                // Update
                $response = $service->updateOrder($this->order->woocommerce_id, $data);
                Log::info("Synced Order {$this->order->order_number} to WooCommerce (Update): " . json_encode($response));
            } else {
                // Create
                $response = $service->createOrder($data);
                if (isset($response['id'])) {
                    $this->order->woocommerce_id = $response['id'];
                    $this->order->woocommerce_data = $response;
                    $this->order->woocommerce_status = $response['status'];
                    $this->order->saveQuietly(); // Avoid triggering observer again
                    Log::info("Synced Order {$this->order->order_number} to WooCommerce (Create): ID " . $response['id']);
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to push order {$this->order->id} to WooCommerce: " . $e->getMessage());
        }
    }

    protected function mapOrderToWooCommerce(Order $order)
    {
        // Map local order fields to WooCommerce schema
        // This accepts a basic mapping for now.
        return [
            'status' => $this->mapStatus($order->status),
            'currency' => $order->tenant->currency_code ?? 'NGN',
            'line_items' => $order->items->map(function($item) {
                return [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => (string)$item->price,
                    'total' => (string)$item->total,
                ];
            })->toArray(),
            // add billing/shipping info...
        ];
    }
    
    protected function mapStatus($status)
    {
        // Map local status to WC status
        // Local: pending, paid, shipped, delivered, cancelled
        // WC: pending, processing, on-hold, completed, cancelled, refunded, failed
        return match($status) {
            'pending' => 'pending',
            'paid' => 'processing',
            'shipped' => 'completed', 
            'delivered' => 'completed',
            'cancelled' => 'cancelled',
            default => 'processing',
        };
    }
}
