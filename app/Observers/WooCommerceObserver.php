<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class WooCommerceObserver
{
    public function created(Order $order): void
    {
        $this->pushToWooCommerce($order, 'create');
    }

    public function updated(Order $order): void
    {
        // Avoid triggering on internal sync updates
        if ($order->wasChanged('woocommerce_id') || $order->wasChanged('woocommerce_status')) {
            return;
        }
        $this->pushToWooCommerce($order, 'update');
    }

    protected function pushToWooCommerce(Order $order, $action)
    {
        // Need to load tenant to checks settings
        // Ideally we are already in tenant context.
        $tenant = tenant(); 
        
        if (!$tenant) {
            return;
        }

        $settings = $tenant->settings ?? [];
        $enabled = $settings['woocommerce_enabled'] ?? false;
        $syncDirection = $settings['woocommerce_sync_direction'] ?? 'import'; // import, export, both

        if (!$enabled || !in_array($syncDirection, ['export', 'both'])) {
            return;
        }

        // Avoid loops: check if this update is result of an import
        // We can check if `woocommerce_data` was just updated, but safer to use a transient flag on model if possible.
        // For now, we rely on the check above (woocommerce_id changed).

        \App\Jobs\PushOrderToWooCommerce::dispatch($order, $action);
    }
}
