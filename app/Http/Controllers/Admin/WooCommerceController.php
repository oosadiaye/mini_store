<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Services\WooCommerceService;
use App\Jobs\SyncWooCommerceOrders;
use Illuminate\Support\Facades\Log;

class WooCommerceController extends Controller
{
    protected $wooService;

    public function __construct(WooCommerceService $wooService)
    {
        $this->wooService = $wooService;
    }

    public function index()
    {
        $tenant = tenant();
        $settings = $tenant->settings ?? [];
        
        $hasCredentials = !empty($settings['woocommerce_consumer_key']);
        $lastSync = $settings['woocommerce_last_sync'] ?? 'Never';
        
        // Stats
        $syncedOrdersCount = Order::where('tenant_id', $tenant->id)
            ->whereNotNull('woocommerce_id')
            ->count();

        return view('admin.woocommerce.index', compact('settings', 'hasCredentials', 'lastSync', 'syncedOrdersCount'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'woocommerce_url' => 'required|url',
            'woocommerce_consumer_key' => 'required|string',
            'woocommerce_consumer_secret' => 'required|string',
            'woocommerce_enabled' => 'boolean',
            'woocommerce_sync_interval' => 'nullable|integer|min:5', // minutes
            'woocommerce_sync_direction' => 'required|in:import,export,both',
        ]);

        $tenant = tenant();
        $settings = $tenant->settings ?? [];
        
        $settings = array_merge($settings, [
            'woocommerce_url' => rtrim($validated['woocommerce_url'], '/'),
            'woocommerce_consumer_key' => $validated['woocommerce_consumer_key'],
            'woocommerce_consumer_secret' => $validated['woocommerce_consumer_secret'],
            'woocommerce_enabled' => $request->has('woocommerce_enabled'),
            'woocommerce_sync_interval' => $validated['woocommerce_sync_interval'] ?? 60,
            'woocommerce_sync_direction' => $validated['woocommerce_sync_direction'],
        ]);

        $tenant->settings = $settings;
        $tenant->save();

        // Test Connection
        $this->wooService->setCredentialsFromTenant($tenant);
        if ($this->wooService->testConnection()) {
            return redirect()->back()->with('success', 'Settings saved and connection successful!');
        } else {
            return redirect()->back()->with('warning', 'Settings saved but connection failed. Please check your credentials.');
        }
    }

    public function manualSync()
    {
        $tenant = tenant();
        
        // Dispatch Sync Job
        SyncWooCommerceOrders::dispatch($tenant);

        return redirect()->back()->with('success', 'Sync process started in background.');
    }
    
    public function orders()
    {
        $orders = Order::where('tenant_id', tenant()->id)
            ->whereNotNull('woocommerce_id')
            ->latest()
            ->paginate(20);

        // Stats for Info Cards
        $stats = [
            'total' => Order::where('tenant_id', tenant()->id)->whereNotNull('woocommerce_id')->count(),
            'processing' => Order::where('tenant_id', tenant()->id)->whereNotNull('woocommerce_id')->where('woocommerce_status', 'processing')->count(),
            'shipped' => Order::where('tenant_id', tenant()->id)->whereNotNull('woocommerce_id')->where('woocommerce_status', 'shipped')->count(),
            'pending' => Order::where('tenant_id', tenant()->id)->whereNotNull('woocommerce_id')->whereIn('woocommerce_status', ['pending', 'on-hold'])->count(),
            'completed' => Order::where('tenant_id', tenant()->id)->whereNotNull('woocommerce_id')->where('woocommerce_status', 'completed')->count(),
        ];
            
        return view('admin.woocommerce.orders', compact('orders', 'stats'));
    }

    public function setupWebhooks()
    {
        $tenant = tenant();
        $this->wooService->setCredentialsFromTenant($tenant);
        
        $secret = \Illuminate\Support\Str::random(32);
        $deliveryUrl = route('api.woocommerce.webhook', ['tenant' => $tenant->slug]);
        
        try {
            // Create Order Created Webhook
            $this->wooService->createWebhook('order.created', $deliveryUrl, $secret);
            $this->wooService->createWebhook('order.updated', $deliveryUrl, $secret);
            
            // Save secret to verify later
            $settings = $tenant->settings;
            $settings['woocommerce_webhook_secret'] = $secret;
            $tenant->settings = $settings;
            $tenant->save();

            return redirect()->back()->with('success', 'Webhooks registered successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to register webhooks: ' . $e->getMessage());
        }
    }
}
