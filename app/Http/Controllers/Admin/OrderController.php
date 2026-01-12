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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($orders);
        }

        $source = $request->source ?? 'all';
        $pageTitle = 'Omni Channel Orders';
            
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();
            
        return view('admin.orders.index', compact('orders', 'pageTitle', 'source', 'warehouses'));
    }

    public function bulkAction(Request $request, \App\Services\AccountingService $accountingService)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:orders,id',
            'action' => 'required|in:status_pending,status_processing,status_shipped,status_delivered,status_completed,status_cancelled,delete',
        ]);

        $orderIds = $request->order_ids;
        $action = $request->action;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            if ($action === 'delete') {
                Order::whereIn('id', $orderIds)->delete();
                $message = count($orderIds) . ' orders deleted successfully.';
            } else {
                $status = str_replace('status_', '', $action);
                $orders = Order::whereIn('id', $orderIds)->get();

                foreach ($orders as $order) {
                    $oldStatus = $order->status;
                    $order->update(['status' => $status]);

                    if ($oldStatus !== $status) {
                        // Trigger Accounting if Completed
                        if ($status === 'completed') {
                            $accountingService->recordSale($order);
                        }

                        // Trigger Notification (Silent fail to avoid breaking bulk)
                        try {
                            $order->customer->notify(new \App\Notifications\OrderStatusUpdated($order));
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send order notification in bulk: ' . $e->getMessage());
                        }

                        if ($status === 'delivered') {
                            $shipping = $order->shippingAddress;
                            if ($shipping && !$shipping->delivered_at) {
                                $shipping->update(['delivered_at' => now()]);
                            }
                        }
                    }
                }
                $message = count($orderIds) . ' orders updated to ' . $status . ' successfully.';
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error processing bulk action: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $tenant = app('tenant');
        $currencySymbol = $tenant->currency ?? '₦'; 
        return view('admin.orders.create', compact('currencySymbol'));
    }

    public function store(Request $request, \App\Services\AccountingService $accountingService)
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
                // Use Model to ensure tenant scope
                $defaultWarehouse = \App\Models\Warehouse::active()->first();
                $warehouseId = $defaultWarehouse ? $defaultWarehouse->id : null;
            }

            \Illuminate\Support\Facades\Log::info('Order Store Debug:', [
                'tenant_bound' => app()->bound('tenant'),
                'tenant_obj' => app('tenant'),
                'warehouse_id' => $warehouseId,
                'user_id' => auth()->id(),
            ]);

            // Determine next order number
            $maxOrderNumber = \App\Models\Order::selectRaw('MAX(CAST(order_number AS UNSIGNED)) as max_num')
                ->whereRaw('order_number REGEXP "^[0-9]+$"')
                ->value('max_num');

            $nextOrderNumber = $maxOrderNumber ? intval($maxOrderNumber) + 1 : 1001;

            $order = Order::create([
                'order_number' => $nextOrderNumber,
                'customer_id' => $request->customer_id,
                'status' => $request->status,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => 0, 
                'discount' => 0,
                'total' => $total,
                'payment_method' => 'manual', 
                'payment_status' => 'pending', // Enforced Credit Sale
                'order_source' => 'admin',
                'warehouse_id' => $warehouseId,
            ]);
            
            \Illuminate\Support\Facades\Log::info('Order Created:', $order->toArray());

            $admins = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Super Admin', 'Admin', 'Manager', 'admin', 'super-admin']);
            })->get();

            foreach ($request->items as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                
                // Decrement Stock
                if ($product->track_inventory) {
                    $product->recordMovement($warehouseId, -$item['quantity'], 'sale', 'order', $order->id);
                    
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
                    'tax_amount' => 0,
                    'tax_rate' => 0,
                ]);
            }
            
            // Notify Admins of New Order
            foreach($admins as $admin) {
                 try {
                    $admin->notify(new \App\Notifications\NewOrderNotification($order));
                } catch (\Exception $e) {}
            }

            // Trigger Accounting if Completed immediately (rare but possible)
            if ($order->status === 'completed') {
                $accountingService->recordSale($order);
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($request->wantsJson()) {
                 return response()->json([
                     'message' => 'Sales order created successfully.',
                     'order' => $order,
                     'redirect' => route('admin.orders.index', ['source' => 'admin'])
                 ]);
            }

            return redirect()->route('admin.orders.index', ['source' => 'admin'])->with('success', 'Sales order created successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Error creating order: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error creating order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'shippingAddress']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return redirect()->route('admin.orders.show', $order->id)
            ->with('info', 'Order editing is handled via status updates and actions on this page.');
    }

    public function updateStatus(Request $request, Order $order, \App\Services\AccountingService $accountingService)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        if ($oldStatus !== $request->status) {
            
            // Trigger Accounting if Completed
            if ($request->status === 'completed') {
                $accountingService->recordSale($order);
            }

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
    public function invoice(Order $order)
    {
        $order->load(['customer', 'items.product', 'shippingAddress']);
        $tenant = app('tenant');
        return view('admin.orders.invoice', compact('order', 'tenant'));
    }

    public function getCustomers()
    {
        return response()->json(\App\Models\Customer::orderBy('name')->get(['id', 'name', 'phone', 'email']));
    }

    public function getProducts()
    {
        return response()->json(\App\Models\Product::active()->orderBy('name')->get(['id', 'name', 'price', 'stock_quantity', 'track_inventory']));
    }
}
