<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupplierInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $journalEntryService;

    public function __construct(\App\Services\JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }

    public function index()
    {
        // Receivables: Unpaid Customer Orders
        $receivables = Order::with('customer')
            ->where('payment_status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'refunded')
            ->orderBy('created_at', 'asc')
            ->paginate(20, ['*'], 'receivables_page');

        // Payables: Unpaid Supplier Invoices (Bills)
        $payables = SupplierInvoice::with('supplier')
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->paginate(20, ['*'], 'payables_page');

        // Unallocated Payments
        $unallocatedPayments = \App\Models\Payment::with('entity')
            ->where('unallocated_amount', '>', 0)
            ->orderBy('payment_date', 'desc')
            ->paginate(20, ['*'], 'unallocated_page');

        $customers = \App\Models\Customer::orderBy('name')->get(['id', 'name']);
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.payments.index', compact('receivables', 'payables', 'unallocatedPayments', 'customers', 'suppliers'));
    }

    public function storeSupplierPayment(Request $request, SupplierInvoice $supplierInvoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Record Journal Entry
            $description = "Payment to Supplier {$supplierInvoice->supplier->name} for Bill #{$supplierInvoice->invoice_number}. Ref: " . ($validated['reference'] ?? 'N/A');
            
            $journalEntry = $this->journalEntryService->recordTransaction(
                $description,
                [
                    [
                        'account_code' => '2000', // Accounts Payable
                        'debit' => $validated['amount'],
                        'credit' => 0,
                        'description' => "Payment for Bill #{$supplierInvoice->invoice_number}",
                        'entity_type' => get_class($supplierInvoice->supplier),
                        'entity_id' => $supplierInvoice->supplier_id,
                    ],
                    [
                        'account_code' => '1000', // Cash / Bank
                        'debit' => 0,
                        'credit' => $validated['amount'],
                        'description' => "Cash Outflow",
                    ]
                ],
                $validated['payment_date']
            );

            // 2. Create Payment Record
            $payment = \App\Models\Payment::create([
                'entity_type' => get_class($supplierInvoice->supplier),
                'entity_id' => $supplierInvoice->supplier_id,
                'amount' => $validated['amount'],
                'unallocated_amount' => 0, // Fully allocated to this invoice
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'],
                'journal_entry_id' => $journalEntry->id,
            ]);

            // 3. Create Allocation
            \App\Models\PaymentAllocation::create([
                'payment_id' => $payment->id,
                'allocatable_type' => get_class($supplierInvoice),
                'allocatable_id' => $supplierInvoice->id,
                'amount' => $validated['amount'],
            ]);

            // 4. Update Invoice
            $currentPaid = $supplierInvoice->amount_paid;
            $newPaid = $currentPaid + $validated['amount'];
            $status = ($newPaid >= $supplierInvoice->total) ? 'paid' : 'partially_paid';

            $supplierInvoice->update([
                'amount_paid' => $newPaid,
                'status' => $status,
            ]);

            // Update associated PO if exists
            if ($supplierInvoice->purchase_order_id) {
                 $po = $supplierInvoice->purchaseOrder;
                 if ($po) {
                     $po->update([
                         'amount_paid' => $po->amount_paid + $validated['amount'],
                         'payment_status' => ($po->amount_paid + $validated['amount'] >= $po->total) ? 'paid' : 'partial'
                     ]);
                 }
            }
            
            DB::commit();

            return redirect()->back()->with('success', 'Payment recorded and fully allocated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    public function storeCustomerPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Record Journal Entry
            $description = "Payment from Customer {$order->customer->name} for Order #{$order->order_number}. Ref: " . ($validated['reference'] ?? 'N/A');

            $journalEntry = $this->journalEntryService->recordTransaction(
                $description,
                [
                    [
                        'account_code' => '1000', // Cash / Bank
                        'debit' => $validated['amount'],
                        'credit' => 0,
                        'description' => "Cash Inflow",
                    ],
                    [
                        'account_code' => '1200', // Accounts Receivable
                        'debit' => 0,
                        'credit' => $validated['amount'],
                        'description' => "Payment for Order #{$order->order_number}",
                        'entity_type' => get_class($order->customer),
                        'entity_id' => $order->customer_id,
                    ]
                ],
                $validated['payment_date']
            );

            // 2. Create Payment Record
            $payment = \App\Models\Payment::create([
                'entity_type' => get_class($order->customer),
                'entity_id' => $order->customer_id,
                'amount' => $validated['amount'],
                'unallocated_amount' => 0, // Fully allocated to this order
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'],
                'journal_entry_id' => $journalEntry->id,
            ]);

            // 3. Create Allocation
            \App\Models\PaymentAllocation::create([
                'payment_id' => $payment->id,
                'allocatable_type' => get_class($order),
                'allocatable_id' => $order->id,
                'amount' => $validated['amount'],
            ]);

            // 4. Update Order
            $currentPaid = $order->amount_paid;
            $newPaid = $currentPaid + $validated['amount'];
            
            $status = ($newPaid >= $order->total) ? 'paid' : 'partial';

            $order->update([
                'amount_paid' => $newPaid,
                'payment_status' => $status,
            ]);

            DB::commit();

            return back()->with('success', 'Payment recorded and fully allocated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    public function storeAdvancePayment(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:customer,supplier',
            'entity_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $entityClass = $validated['type'] === 'customer' ? \App\Models\Customer::class : \App\Models\Supplier::class;
            $entity = $entityClass::findOrFail($validated['entity_id']);
            
            $accountCode = $validated['type'] === 'customer' ? '1200' : '2000'; // AR for customer, AP for supplier
            
            $description = "Advance Payment from/to {$entity->name}. Ref: " . ($validated['reference'] ?? 'N/A');

            $lines = [];
            if ($validated['type'] === 'customer') {
                $lines = [
                    ['account_code' => '1000', 'debit' => $validated['amount'], 'credit' => 0, 'description' => "Cash Inflow"],
                    ['account_code' => '1200', 'debit' => 0, 'credit' => $validated['amount'], 'description' => "Unallocated Credit", 'entity_type' => $entityClass, 'entity_id' => $entity->id]
                ];
            } else {
                $lines = [
                    ['account_code' => '2000', 'debit' => $validated['amount'], 'credit' => 0, 'description' => "Unallocated Debit", 'entity_type' => $entityClass, 'entity_id' => $entity->id],
                    ['account_code' => '1000', 'debit' => 0, 'credit' => $validated['amount'], 'description' => "Cash Outflow"]
                ];
            }

            $journalEntry = $this->journalEntryService->recordTransaction($description, $lines, $validated['payment_date']);

            $payment = \App\Models\Payment::create([
                'entity_type' => $entityClass,
                'entity_id' => $entity->id,
                'amount' => $validated['amount'],
                'unallocated_amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'journal_entry_id' => $journalEntry->id,
            ]);

            DB::commit();

            return back()->with('success', 'Advance payment recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function allocatePayment(Request $request, \App\Models\Payment $payment)
    {
        $validated = $request->validate([
            'allocatable_type' => 'required|string',
            'allocatable_id' => 'required',
            'amount' => 'required|numeric|min:0.01|max:' . $payment->unallocated_amount,
        ]);

        try {
            DB::beginTransaction();

            $allocatableClass = $validated['allocatable_type'] === 'Order' ? \App\Models\Order::class : \App\Models\SupplierInvoice::class;
            $allocatable = $allocatableClass::findOrFail($validated['allocatable_id']);

            // 1. Create Allocation record
            \App\Models\PaymentAllocation::create([
                'payment_id' => $payment->id,
                'allocatable_type' => $allocatableClass,
                'allocatable_id' => $allocatable->id,
                'amount' => $validated['amount'],
            ]);

            // 2. Update Payment unallocated amount
            $payment->decrement('unallocated_amount', $validated['amount']);

            // 3. Update Order/Invoice amount_paid
            $newPaid = $allocatable->amount_paid + $validated['amount'];
            
            if ($validated['allocatable_type'] === 'Order') {
                $status = ($newPaid >= $allocatable->total) ? 'paid' : 'partial';
                $allocatable->update(['amount_paid' => $newPaid, 'payment_status' => $status]);
            } else {
                $status = ($newPaid >= $allocatable->total) ? 'paid' : 'partially_paid';
                $allocatable->update(['amount_paid' => $newPaid, 'status' => $status]);
            }

            DB::commit();
            return back()->with('success', 'Payment allocated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error allocating payment: ' . $e->getMessage());
        }
    }
}
