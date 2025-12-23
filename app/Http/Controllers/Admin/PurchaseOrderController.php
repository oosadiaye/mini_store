<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'warehouse'])
            ->withSum('items as total_quantity', 'quantity_ordered')
            ->latest();

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $orders = $query->paginate(10);
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('admin.purchase_orders.index', compact('orders', 'warehouses'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $products = Product::active()->orderBy('name')->get();
        return view('admin.purchase_orders.create', compact('suppliers', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
        ]);

        $po = DB::transaction(function () use ($request) {
            // Generate PO Number (PO-YYYYMMDD-XXXX)
            $count = \App\Models\PurchaseOrder::whereDate('created_at', today())->count() + 1;
            $poNumber = 'PO-' . now()->format('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
                'status' => 'draft',
                'subtotal' => 0,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'shipping' => $request->shipping ?? 0,
                'total' => 0,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $total = $item['quantity'] * $item['unit_cost'];
                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total,
                ]);
            }

            $this->recalculateTotal($po);
            
            return $po;
        });

        return redirect()->route('admin.purchase-orders.show', $po->id)
            ->with('success', 'Purchase Order Created Successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.product', 'supplier', 'warehouse']);
        $products = Product::active()->get(); // For "Add Item" dropdown
        return view('admin.purchase_orders.show', compact('purchaseOrder', 'products'));
    }
    public function storeItem(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_ordered' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $total = $request->quantity_ordered * $request->unit_cost;

        $purchaseOrder->items()->create([
            'product_id' => $request->product_id,
            'quantity_ordered' => $request->quantity_ordered,
            'unit_cost' => $request->unit_cost,
            'total' => $total,
        ]);

        $this->recalculateTotal($purchaseOrder);

        return back()->with('success', 'Item added.');
    }

    public function destroyItem(PurchaseOrder $purchaseOrder, $itemId)
    {
        $purchaseOrder->items()->where('id', $itemId)->delete();
        $this->recalculateTotal($purchaseOrder);
        return back()->with('success', 'Item removed.');
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Already received.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $totalValue = 0;

            foreach ($purchaseOrder->items as $item) {
                // Update item received qty (assuming full receipt for simplicity now)
                $item->update(['quantity_received' => $item->quantity_ordered]);

                // Update Warehouse Stock
                $stock = \App\Models\WarehouseStock::firstOrCreate(
                    ['warehouse_id' => $purchaseOrder->warehouse_id, 'product_id' => $item->product_id],
                    ['quantity' => 0]
                );
                $stock->increment('quantity', $item->quantity_ordered);

                $totalValue += ($item->quantity_ordered * $item->unit_cost);
            }

            // Post to General Ledger (Goods Receipt)
            // Dr Inventory (Asset)
            // Cr GR/IR (Liability - Unbilled Payables)
            try {
                $jeService = app(\App\Services\JournalEntryService::class);
                
                $inventoryValue = $totalValue; // Net value of goods
                
                // Tax is separate? Usually tax is recorded at Invoice Receipt for Input Tax credit.
                // But if we want to track it now? Standard is at Invoice.
                // WE RECORD STOCK VALUE (Net).
                
                $entries = [
                    [
                        'account_code' => '1200', // Inventory Asset (Debit)
                        'debit' => $inventoryValue,
                        'credit' => 0,
                        'warehouse_id' => $purchaseOrder->warehouse_id,
                    ],
                    [
                        'account_code' => '2020', // GR/IR Clearing (Credit) / Unbilled Payables
                        'debit' => 0,
                        'credit' => $inventoryValue,
                        'warehouse_id' => $purchaseOrder->warehouse_id,
                    ]
                ];

                $jeService->recordTransaction(
                    "Goods Receipt for PO #{$purchaseOrder->po_number}",
                    $entries, 
                    now()
                );
            } catch (\Exception $e) {
                // \Log::error("Accounting Error: " . $e->getMessage());
                // throw $e; // Optional: revert if accounting fails
            }

            $purchaseOrder->update(['status' => 'received']);
        });

        return back()->with('success', 'Stock Received Successfully!');
    }
    
    public function convertToBill(Request $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update([
            'billed_status' => 'billed',
            'invoice_number' => $request->invoice_number ?? $purchaseOrder->po_number . '-INV'
        ]);

        // Accounting: Invoice Receipt
        // Dr GR/IR (2020) - Clear the clearing account
        // Dr Input Tax (1300) - Claim tax
        // Cr Accounts Payable (2000) - Vendor Liability
        // Cr Purchase Discount (if applicable)? Or reduce liability? 
        // Logic: Payables = Total (Subtotal - Discount + Tax + Shipping)
        
        // Let's assume Discount reduces the Inventory Cost? OR it's a separate income?
        // Purchase Discount is usually a reduction in expense or separate income "Purchase Discounts Taken".
        // For simplicity: Cr Accounts Payable = Total Amount to Pay.
        // Dr GR/IR = Subtotal (Inventory Value received)
        // Dr Tax = Tax
        // Dr Shipping = Shipping Expense (if not capitalized) -> let's capitalized or separate expense?
        // Let's expense shipping: 5xxx Freight In.
        // Cr Purchase Discount (Revenue/Contra-Expense) = Discount
        
        try {
            $jeService = app(\App\Services\JournalEntryService::class);
            
            $subtotal = $purchaseOrder->subtotal;
            $tax = $purchaseOrder->tax ?? 0;
            $shipping = $purchaseOrder->shipping ?? 0;
            $discount = $purchaseOrder->discount ?? 0;
            $total = $purchaseOrder->total;

            $entries = [
                [
                    'account_code' => '2020', // GR/IR Clearing (Debit) - Clearing the accrual
                    'debit' => $subtotal,
                    'credit' => 0,
                ],
                [
                    'account_code' => '2000', // Accounts Payable (Credit) - Vendor
                    'debit' => 0,
                    'credit' => $total,
                ]
            ];
            
            if ($tax > 0) {
                $entries[] = [
                    'account_code' => '1300', // Input Tax (Debit)
                    'debit' => $tax,
                    'credit' => 0,
                ];
            }
            
            if ($shipping > 0) {
                $entries[] = [
                    'account_code' => '5100', // Freight In (Debit) - Expense
                    'debit' => $shipping,
                    'credit' => 0,
                ];
            }
            
            if ($discount > 0) {
                 $entries[] = [
                    'account_code' => '5200', // Purchase Discounts (Credit) - Income/Contra
                    'debit' => 0,
                    'credit' => $discount,
                ];
            }
            
            $jeService->recordTransaction(
                "Invoice Receipt for PO #{$purchaseOrder->po_number}",
                $entries,
                now()
            );
        } catch (\Exception $e) {
             // Log
        }

        return back()->with('success', 'Converted to Supplier Invoice.');
    }

    private function recalculateTotal(PurchaseOrder $purchaseOrder)
    {
        $subtotal = $purchaseOrder->items()->sum('total');
        $shipping = $purchaseOrder->shipping ?? 0;
        $tax = $purchaseOrder->tax ?? 0;
        $discount = $purchaseOrder->discount ?? 0;

        $total = max(0, $subtotal - $discount + $tax + $shipping);

        $purchaseOrder->update([
            'subtotal' => $subtotal,
            'total' => $total
        ]);
    }
    
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('admin.purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'Only draft orders can be edited.');
        }

        $purchaseOrder->load(['items.product', 'supplier', 'warehouse']);
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $products = Product::active()->orderBy('name')->get();

        return view('admin.purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'warehouses', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Cannot update non-draft order.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
            ]);

            // Sync Items (Delete all and recreate is simplest for full edit)
            $purchaseOrder->items()->delete();

            foreach ($request->items as $item) {
                $total = $item['quantity'] * $item['unit_cost'];
                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total,
                ]);
            }

            $this->recalculateTotal($purchaseOrder);
        });

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder->id)
            ->with('success', 'Purchase Order Updated.');
    }

    public function placeOrder(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
             return back()->with('error', 'Order is not in draft status.');
        }

        $purchaseOrder->update(['status' => 'ordered']);

        // Optional: Email Supplier logic would go here

        return back()->with('success', 'Order has been placed.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:purchase_orders,id',
            'action' => 'required|in:delete,mark_ordered',
        ]);

        $ids = $request->ids;
        $count = 0;

        if ($request->action === 'delete') {
            // Only delete drafts
            $count = PurchaseOrder::whereIn('id', $ids)->where('status', 'draft')->delete();
            $message = "$count draft orders deleted.";
        } elseif ($request->action === 'mark_ordered') {
            // Only update drafts to ordered - explicitly use string value
            $count = PurchaseOrder::whereIn('id', $ids)
                ->where('status', 'draft')
                ->update(['status' => DB::raw("'ordered'")]);
            $message = "$count orders placed.";
        }

        return back()->with('success', $message);
    }
}
