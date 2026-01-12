<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
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
        $taxCodes = \App\Models\TaxCode::active()->get();
        return view('admin.purchase_orders.create', compact('suppliers', 'warehouses', 'products', 'taxCodes'));
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
            'items.*.tax_code_id' => 'nullable|exists:tax_codes,id',
            'discount' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
        ]);

        $po = DB::transaction(function () use ($request) {
            // Generate PO Number (PO-YYYYMMDD-XXXX)
            $dateStr = now()->format('Ymd');
            $count = PurchaseOrder::where('po_number', 'like', "PO-{$dateStr}-%")->count();
            
            do {
                $count++;
                $poNumber = 'PO-' . $dateStr . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            } while (PurchaseOrder::where('po_number', $poNumber)->exists());

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'tenant_id' => $request->route('tenant'),
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
                'status' => 'draft',
                'subtotal' => 0,
                'tax' => 0, // Will be calculated from items
                'discount' => $request->discount ?? 0,
                'shipping' => $request->shipping ?? 0,
                'total' => 0,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $total = $item['quantity'] * $item['unit_cost'];
                $taxAmount = 0;
                $taxCodeId = $item['tax_code_id'] ?? null;
                
                if ($taxCodeId) {
                    $taxCode = \App\Models\TaxCode::find($taxCodeId);
                    if ($taxCode) {
                        $taxAmount = $total * ($taxCode->rate / 100);
                    }
                }

                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total, // Net total (excluding tax)
                    'tax_code_id' => $taxCodeId,
                    'tax_amount' => $taxAmount,
                ]);
            }

            $this->recalculateTotal($po);
            
            return $po;
        });

        return redirect()->route('admin.purchase-orders.show', $po->id)
            ->with('success', 'Purchase Order Created Successfully.');
    }

    public function show($id)
    {
        \Illuminate\Support\Facades\Log::info("PurchaseOrderController::show reached with ID: " . $id);
        $purchaseOrder = PurchaseOrder::findOrFail($id);
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
            'tax_code_id' => 'nullable|exists:tax_codes,id',
        ]);

        $total = $request->quantity_ordered * $request->unit_cost;
        $taxAmount = 0;
        
        if ($request->tax_code_id) {
             $taxCode = \App\Models\TaxCode::find($request->tax_code_id);
             if ($taxCode) {
                 $taxAmount = $total * ($taxCode->rate / 100);
             }
        }

        $purchaseOrder->items()->create([
            'product_id' => $request->product_id,
            'quantity_ordered' => $request->quantity_ordered,
            'unit_cost' => $request->unit_cost,
            'total' => $total,
            'tax_code_id' => $request->tax_code_id,
            'tax_amount' => $taxAmount,
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
        if ($purchaseOrder->status === 'received_order') {
            return back()->with('error', 'Already received.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $this->processReceipt($purchaseOrder);
        });

        return back()->with('success', 'Stock Received Successfully!');
    }
    
    private function processReceipt(PurchaseOrder $purchaseOrder)
    {
        $totalValue = 0;

        foreach ($purchaseOrder->items as $item) {
            // Update item received qty (assuming full receipt for simplicity now)
            $item->update(['quantity_received' => $item->quantity_ordered]);

            // Update Warehouse Stock
            $product = $item->product;
            if ($product) {
                $product->recordMovement($purchaseOrder->warehouse_id, $item->quantity_ordered, 'purchase', 'purchase_order', $purchaseOrder->id);
            }

            $totalValue += ($item->quantity_ordered * $item->unit_cost);
        }

        // Post to General Ledger (Goods Receipt)
        try {
            $jeService = app(\App\Services\JournalEntryService::class);
            
            $inventoryValue = $totalValue; // Net value of goods
            
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
        }

        $purchaseOrder->update(['status' => 'received_order']);
    }
    
    public function convertToBill(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->processConversion($purchaseOrder, $request->invoice_number);
        return back()->with('success', 'Converted to Supplier Invoice.');
    }

    private function processConversion(PurchaseOrder $purchaseOrder, $invoiceNumber = null)
    {
        if (!$invoiceNumber) {
            $year = now()->format('Y');
            $count = \App\Models\SupplierInvoice::where('invoice_number', 'like', "SI-{$year}-%")->count();
            do {
                $count++;
                $invoiceNumber = 'SI-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            } while (\App\Models\SupplierInvoice::where('invoice_number', $invoiceNumber)->exists());
        }
        
        $purchaseOrder->update([
            'billed_status' => 'billed',
            'invoice_number' => $invoiceNumber
        ]);

        // 1. Create Supplier Invoice
        $invoice = \App\Models\SupplierInvoice::create([
            'tenant_id' => $purchaseOrder->tenant_id,
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id' => $purchaseOrder->supplier_id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $purchaseOrder->subtotal,
            'tax' => $purchaseOrder->tax,
            'discount' => $purchaseOrder->discount,
            'shipping' => $purchaseOrder->shipping,
            'total' => $purchaseOrder->total,
            'status' => 'unpaid',
        ]);

        foreach ($purchaseOrder->items as $item) {
            $invoice->items()->create([
                'product_id' => $item->product_id,
                'description' => $item->product ? $item->product->name : 'Unknown Product',
                'quantity' => $item->quantity_received,
                'unit_price' => $item->unit_cost,
                'tax_amount' => $item->tax_amount,
                'total' => $item->total + $item->tax_amount,
            ]);
        }

        // 2. Accounting Entries
        try {
            $jeService = app(\App\Services\JournalEntryService::class);
            
            $subtotal = $purchaseOrder->subtotal;
            $tax = $purchaseOrder->tax ?? 0;
            $shipping = $purchaseOrder->shipping ?? 0;
            $discount = $purchaseOrder->discount ?? 0;
            $total = $purchaseOrder->total;

            $entries = [
                [
                    'account_code' => '2020', // GR/IR Clearing (Debit) - Removes the liability from receipt
                    'debit' => $subtotal,
                    'credit' => 0,
                    'description' => "Clear GR/IR for PO #{$purchaseOrder->po_number}",
                ],
                [
                    'account_code' => '2000', // Accounts Payable (Credit) - Recognizes the debt to supplier
                    'debit' => 0,
                    'credit' => $total,
                    'entity_type' => 'App\Models\Supplier',
                    'entity_id' => $purchaseOrder->supplier_id,
                    'description' => "Invoice Payable for PO #{$purchaseOrder->po_number}",
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
                    'account_code' => '5100', // Freight In (Debit)
                    'debit' => $shipping,
                    'credit' => 0,
                ];
            }
            
            if ($discount > 0) {
                 $entries[] = [
                    'account_code' => '5200', // Purchase Discounts (Credit)
                    'debit' => 0,
                    'credit' => $discount,
                ];
            }
            
            $jeService->recordTransaction(
                "Invoice Receipt for PO #{$purchaseOrder->po_number} - Invoice #{$invoiceNumber}",
                $entries,
                now()
            );
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error("PO Conversion Accounting Error: " . $e->getMessage());
        }
    }

    private function recalculateTotal(PurchaseOrder $purchaseOrder)
    {
        $subtotal = $purchaseOrder->items()->sum('total');
        $shipping = $purchaseOrder->shipping ?? 0;
        // Tax is now sum of item taxes
        $tax = $purchaseOrder->items()->sum('tax_amount');
        $purchaseOrder->update(['tax' => $tax]); // Update cached tax column
        
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
        $taxCodes = \App\Models\TaxCode::active()->get();

        return view('admin.purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'warehouses', 'products', 'taxCodes'));
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
            'items.*.tax_code_id' => 'nullable|exists:tax_codes,id',
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
                $taxAmount = 0;
                $taxCodeId = $item['tax_code_id'] ?? null;
                
                if ($taxCodeId) {
                    $taxCode = \App\Models\TaxCode::find($taxCodeId);
                    if ($taxCode) {
                        $taxAmount = $total * ($taxCode->rate / 100);
                    }
                }

                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $total,
                    'tax_code_id' => $taxCodeId,
                    'tax_amount' => $taxAmount,
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

        $purchaseOrder->update(['status' => 'created_order']);

        // Optional: Email Supplier logic would go here

        return back()->with('success', 'Order has been placed.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:purchase_orders,id',
            'action' => 'required|in:delete,mark_ordered,receive,convert',
        ]);

        $ids = $request->ids;
        $count = 0;
        $message = "Actions processed.";

        if ($request->action === 'delete') {
            // Only delete drafts
            $count = PurchaseOrder::whereIn('id', $ids)->where('status', 'draft')->delete();
            $message = "$count draft orders deleted.";
        } elseif ($request->action === 'mark_ordered') {
            // Only update drafts to created_order
            $count = PurchaseOrder::whereIn('id', $ids)
                ->where('status', 'draft')
                ->update(['status' => 'created_order']);
            $message = "$count orders placed.";
        } elseif ($request->action === 'receive') {
            // Only receive created_order items
            $orders = PurchaseOrder::whereIn('id', $ids)->where('status', 'created_order')->get();
            foreach ($orders as $order) {
                DB::transaction(function () use ($order) {
                    $this->processReceipt($order);
                });
                $count++;
            }
            $message = "$count orders received.";
        } elseif ($request->action === 'convert') {
            // Only convert received_order items not yet billed
            $orders = PurchaseOrder::whereIn('id', $ids)
                ->where('status', 'received_order')
                ->where('billed_status', '!=', 'billed')
                ->get();
            foreach ($orders as $order) {
                $this->processConversion($order);
                $count++;
            }
            $message = "$count orders converted to invoices.";
        }

        return back()->with('success', $message);
    }
    public function reverseBill(\App\Models\SupplierInvoice $invoice)
    {
        if ($invoice->status === 'paid' || $invoice->amount_paid > 0) {
            return back()->with('error', 'Cannot reverse invoice with payments. Please void/delete payments first.');
        }

        DB::transaction(function () use ($invoice) {
            // 1. Update Invoice Status
            $invoice->update(['status' => 'reversed']);

            // 2. Revert PO Status
            $purchaseOrder = $invoice->purchaseOrder;
            if ($purchaseOrder) {
                $purchaseOrder->update([
                    'billed_status' => null,
                    'invoice_number' => null,
                    // Keeping 'received_order' status as we are just reversing the BILL, not the receipt
                ]);
            }

            // 3. Accounting Reversal
            try {
                $jeService = app(\App\Services\JournalEntryService::class);
                
                // Fetch original amounts from invoice to be accurate
                $subtotal = $invoice->subtotal;
                $tax = $invoice->tax;
                $shipping = $invoice->shipping;
                $discount = $invoice->discount;
                $total = $invoice->total;

                $entries = [
                    [
                        'account_code' => '2000', // Accounts Payable (Debit) - Reduce Liability
                        'debit' => $total,
                        'credit' => 0,
                        'entity_type' => 'App\Models\Supplier',
                        'entity_id' => $invoice->supplier_id,
                        'description' => "Reversal of Invoice #{$invoice->invoice_number}",
                    ],
                    [
                        'account_code' => '2020', // GR/IR Clearing (Credit) - Restore Liability to 'Unbilled'
                        'debit' => 0,
                        'credit' => $subtotal,
                        'description' => "Restore GR/IR for PO #{$purchaseOrder->po_number}",
                    ]
                ];
                
                if ($tax > 0) {
                    $entries[] = [
                        'account_code' => '1300', // Input Tax (Credit)
                        'debit' => 0,
                        'credit' => $tax,
                    ];
                }
                
                if ($shipping > 0) {
                    $entries[] = [
                        'account_code' => '5100', // Freight In (Credit)
                        'debit' => 0,
                        'credit' => $shipping,
                    ];
                }
                
                if ($discount > 0) {
                     $entries[] = [
                        'account_code' => '5200', // Purchase Discounts (Debit)
                        'debit' => $discount,
                        'credit' => 0,
                    ];
                }
                
                $jeService->recordTransaction(
                    "Reversal of Supplier Invoice #{$invoice->invoice_number}",
                    $entries,
                    now()
                );
            } catch (\Exception $e) {
                 \Illuminate\Support\Facades\Log::error("Invoice Reversal Accounting Error: " . $e->getMessage());
            }
        });

        return back()->with('success', 'Supplier Invoice reversed successfully.');
    }

    public function returnsCreate(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'received_order' && $purchaseOrder->status !== 'billed') {
            return back()->with('error', 'Only received orders can be returned.');
        }

        if ($purchaseOrder->billed_status === 'billed') {
            return back()->with('error', 'Cannot return items for a billed order. Please reverse the Supplier Invoice first in the Supplier Ledger.');
        }

        return view('admin.purchase_orders.returns.create', compact('purchaseOrder'));
    }
    
    public function returnsStore(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        $totalRefund = 0;

        try {
            DB::transaction(function () use ($request, $purchaseOrder, &$totalRefund) {
                $purchaseReturn = PurchaseReturn::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'status' => 'completed',
                    'refund_amount' => 0, 
                ]);

                foreach ($request->items as $itemId => $data) {
                    $qty = (int)$data['quantity'];
                    if ($qty <= 0) continue;

                    $poItem = $purchaseOrder->items()->findOrFail($itemId);
                    
                    if ($qty > $poItem->quantity_received) {
                         throw new \Exception("Cannot return more than received for {$poItem->product->name}");
                    }

                    $refundForLine = $qty * $poItem->unit_cost;
                    $totalRefund += $refundForLine;

                    $purchaseReturn->items()->create([
                        'purchase_order_item_id' => $poItem->id,
                        'quantity_returned' => $qty,
                        'refund_amount' => $refundForLine,
                    ]);

                    // Update stock
                    $poItem->product->recordMovement(
                        $purchaseOrder->warehouse_id, 
                        -$qty, 
                        'return', 
                        'purchase_order', 
                        $purchaseOrder->id,
                        "Return for PO #{$purchaseOrder->po_number}"
                    );
                    
                    $poItem->decrement('quantity_received', $qty);
                }

                if ($totalRefund > 0) {
                    $purchaseReturn->update(['refund_amount' => $totalRefund]);
                    $this->processReturnAccounting($purchaseOrder, $totalRefund);
                } else {
                    $purchaseReturn->delete(); // Nothing returned
                    throw new \Exception("No items selected for return.");
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder->id)
            ->with('success', 'Return processed successfully.');
    }

    private function processReturnAccounting(PurchaseOrder $purchaseOrder, $totalRefund)
    {
        $jeService = app(\App\Services\JournalEntryService::class);
        
        // If already billed, reverse AP (2000). If not, reverse GR/IR (2020).
        $payableAccountCode = ($purchaseOrder->billed_status === 'billed') ? '2000' : '2020';
        
        $entries = [
            [
                'account_code' => $payableAccountCode,
                'debit' => $totalRefund, 
                'credit' => 0,
                'entity_type' => 'App\Models\Supplier',
                'entity_id' => $purchaseOrder->supplier_id,
            ],
            [
                'account_code' => '1200', // Inventory Asset
                'debit' => 0,
                'credit' => $totalRefund, 
            ]
        ];

        $jeService->recordTransaction(
            "Purchase Return for PO #{$purchaseOrder->po_number}",
            $entries,
            now()
        );
    }
}
