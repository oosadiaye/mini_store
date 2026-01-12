<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    // Show form to create a return
    public function create(PurchaseOrder $purchaseOrder)
    {
        // Ensure PO is received before returning? usually yes.
        // Assuming 'received' or 'completed' status implies items are in inventory.
        
        $purchaseOrder->load('items.product');
        return view('admin.purchase-orders.returns.create', compact('purchaseOrder'));
    }

    // Store the return request
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.return_reason' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalRefund = 0;
            $itemsToReturn = [];

            foreach ($validated['items'] as $itemData) {
                if ($itemData['quantity'] > 0) {
                    $poItem = $purchaseOrder->items()->find($itemData['id']);
                    
                    // Validation: Cannot return more than received/ordered
                    if ($itemData['quantity'] > $poItem->quantity_received) {
                        return back()->with('error', 'Cannot return more items than received for ' . $poItem->product->name);
                    }

                    // Calculate refund based on unit cost
                    $refundAmount = $poItem->unit_cost * $itemData['quantity'];
                    $totalRefund += $refundAmount;

                    $itemsToReturn[] = [
                        'purchase_order_item_id' => $poItem->id,
                        'quantity_returned' => $itemData['quantity'],
                        'refund_amount' => $refundAmount,
                        'return_reason' => $itemData['return_reason'] ?? null,
                    ];
                }
            }

            if (empty($itemsToReturn)) {
                return back()->with('error', 'No items selected for return.');
            }

            // Create Return Record
            $return = PurchaseReturn::create([
                'purchase_order_id' => $purchaseOrder->id,
                'status' => 'sent', // defaulted to sent
                'admin_notes' => $validated['admin_notes'],
                'refund_amount' => $totalRefund,
            ]);

            // Create Return Items & Deduct Inventory
            foreach ($itemsToReturn as $item) {
                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'purchase_order_item_id' => $item['purchase_order_item_id'],
                    'quantity_returned' => $item['quantity_returned'],
                    'refund_amount' => $item['refund_amount'],
                    'return_reason' => $item['return_reason'],
                ]);

                // Deduct from inventory
                $poItem = $purchaseOrder->items()->find($item['purchase_order_item_id']);
                if ($poItem->product && $poItem->product->track_inventory) {
                    $poItem->product->recordMovement(
                        $purchaseOrder->warehouse_id, 
                        -$item['quantity_returned'], 
                        'return', 
                        'purchase_return', 
                        $return->id, 
                        "Return to supplier via Return #{$return->id}"
                    );
                }
            }

            DB::commit();

            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Return created and inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing purchase return: ' . $e->getMessage());
        }
    }
}
