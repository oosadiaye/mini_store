<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use App\Models\Product; // Assuming Product model exists for inventory
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderReturnController extends Controller
{
    // Show form to create a return
    public function create(Order $order)
    {
        // Prevent return for POS orders
        if ($order->order_source === 'pos') {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'Returns for POS orders must be handled at the POS terminal.');
        }

        $order->load('items.product');
        return view('admin.orders.returns.create', compact('order'));
    }

    // Store the return request
    public function store(Request $request, Order $order)
    {
        if ($order->order_source === 'pos') {
            abort(403, 'POS orders cannot be returned online.');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.condition' => 'nullable|string',
            'restock' => 'boolean',
            'return_reason' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total refund
            $totalRefund = 0;
            $itemsToReturn = [];

            foreach ($validated['items'] as $itemData) {
                if ($itemData['quantity'] > 0) {
                    $orderItem = $order->items()->find($itemData['id']);
                    // Calculate pro-rated refund amount based on item price vs total
                    // Keep it simple: unit price * quantity
                    $refundAmount = $orderItem->unit_price * $itemData['quantity'];
                    $totalRefund += $refundAmount;

                    $itemsToReturn[] = [
                        'order_item_id' => $orderItem->id,
                        'quantity_returned' => $itemData['quantity'],
                        'refund_amount' => $refundAmount,
                        'condition' => $itemData['condition'] ?? 'new',
                        'restock_inventory' => $request->boolean('restock'),
                    ];
                }
            }

            if (empty($itemsToReturn)) {
                return back()->with('error', 'No items selected for return.');
            }

            // Create Return Record
            $return = OrderReturn::create([
                'order_id' => $order->id,
                'status' => 'approved', // Auto-approve for admin action
                'return_reason' => $validated['return_reason'],
                'admin_notes' => $validated['admin_notes'],
                'refund_amount' => $totalRefund,
            ]);

            // Create Return Items & Handle Inventory
            foreach ($itemsToReturn as $item) {
                OrderReturnItem::create([
                    'order_return_id' => $return->id,
                    'order_item_id' => $item['order_item_id'],
                    'quantity_returned' => $item['quantity_returned'],
                    'refund_amount' => $item['refund_amount'],
                    'condition' => $item['condition'],
                    'restock_inventory' => $item['restock_inventory'],
                ]);

                if ($item['restock_inventory']) {
                    $orderItem = $order->items()->find($item['order_item_id']);
                    if ($orderItem->product && $orderItem->product->track_inventory) {
                        $orderItem->product->increment('stock_quantity', $item['quantity_returned']);
                    }
                }
            }

            // Update Order Payment Status if fully refunded?
            // For now, just mark return created. logic can be expanded.
            
            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Return processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing return: ' . $e->getMessage());
        }
    }
    // List all returns
    public function index()
    {
        $returns = OrderReturn::with(['order.customer', 'items'])
            ->latest()
            ->paginate(15);
            
        return view('admin.orders.returns.index', compact('returns'));
    }

    // Show return details
    public function show(OrderReturn $return) // Logic binding
    {
        $return->load(['order.customer', 'items.orderItem.product']);
        return view('admin.orders.returns.show', compact('return'));
    }

    // Update return status
    public function update(Request $request, OrderReturn $return)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed,refunded',
            'admin_notes' => 'nullable|string',
        ]);

        $return->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $return->admin_notes,
        ]);

        return back()->with('success', 'Return updated successfully.');
    }
}
