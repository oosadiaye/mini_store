<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderReturnController extends Controller
{
    public function create(Order $order)
    {
        // Authorization: Ensure order belongs to logged-in customer
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        // Eligibility check (e.g., delivered status)
        if ($order->status !== 'delivered') {
             return redirect()->route('storefront.customer.order', $order)
                ->with('error', 'Only delivered orders can be returned.');
        }
        
        // Prevent duplicate active returns? Or allow multiple? 
        // For simplicity, let's allow multiple calls but maybe warn.
        
        $order->load(['items.product']);

        return view('storefront.customer.return', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
         if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.reason' => 'required|string|max:255',
            'comments' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalRefundEstimate = 0;
            $itemsToReturn = [];

            foreach ($validated['items'] as $itemData) {
                if (($itemData['quantity'] ?? 0) > 0) {
                    $orderItem = $order->items()->find($itemData['id']);
                    
                    if (!$orderItem) continue;

                    // Verify quantity doesn't exceed purchased
                    if ($itemData['quantity'] > $orderItem->quantity) {
                         return back()->with('error', 'Invalid return quantity for item: ' . $orderItem->product_name);
                    }

                    // Calculate estimated refund
                    $refundAmount = $orderItem->price * $itemData['quantity'];
                    $totalRefundEstimate += $refundAmount;

                    $itemsToReturn[] = [
                        'order_item_id' => $orderItem->id,
                        'quantity_returned' => $itemData['quantity'],
                        'refund_amount' => $refundAmount,
                        'return_reason' => $itemData['reason'],
                    ];
                }
            }

            if (empty($itemsToReturn)) {
                return back()->with('error', 'Please select at least one item to return.');
            }

            // Create Return Record
            $return = OrderReturn::create([
                'order_id' => $order->id,
                'status' => 'pending', // Pending Admin Approval
                'return_reason' => 'Customer Request',
                'admin_notes' => $validated['comments'] ?? null, // Using admin_notes for customer comments or we need a new column? 
                // Let's assume admin_notes is okay or use description if available. 
                // Checks on migration: 'return_reason' is string, 'admin_notes' is text.
                // We'll put summary in return_reason and details in admin_notes for now.
                'refund_amount' => $totalRefundEstimate,
            ]);

            // Create Return Items
            foreach ($itemsToReturn as $item) {
                OrderReturnItem::create([
                    'order_return_id' => $return->id,
                    'order_item_id' => $item['order_item_id'],
                    'quantity_returned' => $item['quantity_returned'],
                    'refund_amount' => $item['refund_amount'],
                    'condition' => 'unknown', // Customer hasn't sent it back yet
                    'restock_inventory' => false, // Pending inspection
                ]);
            }

            DB::commit();

            return redirect()->route('storefront.customer.order', $order)
                ->with('success', 'Return request submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error submitting return: ' . $e->getMessage());
        }
    }
}
