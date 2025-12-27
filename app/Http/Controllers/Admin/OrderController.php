<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer')->latest();

        if ($request->filled('source')) {
            $query->where('order_source', $request->source);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $orders = $query->paginate(15);
        $source = $request->source ?? 'all';
        $pageTitle = match($source) {
            'storefront' => 'Online Orders',
            'admin' => 'Sales Orders',
            'pos' => 'POS Orders',
            default => 'All Orders'
        };
            
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();
            
        return view('admin.orders.index', compact('orders', 'pageTitle', 'source', 'warehouses'));
    }

    public function create()
    {
        $customers = \App\Models\Customer::all();
        $products = \App\Models\Product::active()->get();
        return view('admin.orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        // Enforce Plan Limits (Total Orders)
        $limit = app('tenant')->getLimit('orders_limit');
        if ($limit !== -1 && $limit !== null) {
            if (Order::count() >= $limit) {
                return redirect()->back()
                    ->with('error', "You have reached your plan's limit of {$limit} orders. Please upgrade to continue processing sales.")
                    ->withInput();
            }
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed',
            'payment_status' => 'required|in:pending,paid',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            // Simple Tax logic (0% for now)
            $tax = 0;
            $total = $subtotal + $tax;

            // Determine Warehouse (Branch)
            // If explicit in request, use it. Else default to first active or null.
            $warehouseId = $request->warehouse_id;
            if (!$warehouseId) {
                // Should we force a default?
                $defaultWarehouse = \Illuminate\Support\Facades\DB::table('warehouses')->where('is_active', true)->first();
                $warehouseId = $defaultWarehouse ? $defaultWarehouse->id : null;
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(10)),
                'customer_id' => $request->customer_id,
                'status' => $request->status,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => 0, 
                'discount' => 0,
                'total' => $total,
                'payment_method' => 'manual', // Can be enhanced later
                'payment_status' => $request->payment_status,
                'order_source' => 'admin',
                'warehouse_id' => $warehouseId,
            ]);

            $admins = \App\Models\User::role(['admin', 'super-admin'])->get();

            foreach ($request->items as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                
                // Decrement Stock
                if ($product->track_inventory) {
                    $product->decrement('stock_quantity', $item['quantity']);
                    
                    // Check Low Stock
                    if ($product->stock_quantity <= ($product->low_stock_threshold ?? 5)) {
                        foreach($admins as $admin) {
                            try {
                                $admin->notify(new \App\Notifications\LowStockNotification($product));
                            } catch (\Exception $e) {}
                        }
                    }
                }

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }
            
            // Notify Admins of New Order
            foreach($admins as $admin) {
                 try {
                    $admin->notify(new \App\Notifications\NewOrderNotification($order));
                } catch (\Exception $e) {}
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('admin.orders.index', ['source' => 'admin'])->with('success', 'Sales order created successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error creating order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'shippingAddress']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        if ($oldStatus !== $request->status) {
            // Trigger Notification
            try {
                $order->customer->notify(new \App\Notifications\OrderStatusUpdated($order));
            } catch (\Exception $e) {
                // Log error but continue
                \Illuminate\Support\Facades\Log::error('Failed to send order notification: ' . $e->getMessage());
            }

            if ($request->status === 'delivered') {
                 // Auto-update delivered_at if not set
                 $shipping = $order->shippingAddress;
                 if ($shipping && !$shipping->delivered_at) {
                     $shipping->update(['delivered_at' => now()]);
                 }
            }
        }

        return back()->with('success', 'Order status updated successfully.');
    }

    public function updateTracking(Request $request, Order $order)
    {
        $request->validate([
            'carrier' => 'required|string|max:255',
            'tracking_number' => 'required|string|max:255',
            'shipped_at' => 'nullable|date',
        ]);

        $shipping = $order->shippingAddress;
        
        if (!$shipping) {
            // Create if missing (rare but possible)
            $shipping = $order->shippingAddress()->create([
                 'address_line1' => 'N/A', // Placeholder
                 'city' => 'N/A',
                 'postal_code' => 'N/A',
                 'country' => 'N/A'
            ]);
        }

        $shipping->update([
            'carrier' => $request->carrier,
            'tracking_number' => $request->tracking_number,
            'shipped_at' => $request->shipped_at ?? now(),
        ]);
        
        // Auto update status to shipped if currently pending/processing
        if (in_array($order->status, ['pending', 'processing'])) {
             $order->update(['status' => 'shipped']);
             try {
                $order->customer->notify(new \App\Notifications\OrderStatusUpdated($order));
            } catch (\Exception $e) {}
        }

        return back()->with('success', 'Tracking information updated successfully.');
    }
    
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return back()->with('success', 'Payment status updated successfully.');
    }
}
