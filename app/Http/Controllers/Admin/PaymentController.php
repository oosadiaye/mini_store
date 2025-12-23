<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PurchaseOrder;
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
        $receivables = Order::where('payment_status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'refunded')
            ->orderBy('created_at', 'asc')
            ->paginate(20, ['*'], 'receivables_page');

        // Payables: Unpaid Supplier Purchase Orders
        $payables = PurchaseOrder::where('payment_status', '!=', 'paid')
            ->where('status', 'received') // Only received orders are bills really
            ->orderBy('order_date', 'asc')
            ->paginate(20, ['*'], 'payables_page');

        return view('admin.payments.index', compact('receivables', 'payables'));
    }

    public function storeSupplierPayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $currentPaid = $purchaseOrder->amount_paid;
            $newPaid = $currentPaid + $validated['amount'];
            $status = ($newPaid >= $purchaseOrder->total_amount) ? 'paid' : 'partial';

            $purchaseOrder->update([
                'amount_paid' => $newPaid,
                'payment_status' => $status,
            ]);

            // Create Journal Entry for Cash Paid
            // Debit: Accounts Payable (2000)
            // Credit: Cash/Bank (1000) - Simplified, ideally select specific bank account
            
            $description = "Payment to Supplier {$purchaseOrder->supplier->name} for PO #{$purchaseOrder->id}. Ref: " . ($validated['reference'] ?? 'N/A');
            
            $this->journalEntryService->recordTransaction(
                $description,
                [
                    [
                        'account_code' => '2000', // Accounts Payable
                        'debit' => $validated['amount'],
                        'credit' => 0,
                        'description' => "Payment for PO #{$purchaseOrder->id}",
                        'entity_type' => get_class($purchaseOrder->supplier), // Morph to Supplier
                        'entity_id' => $purchaseOrder->supplier_id,
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
            
            DB::commit();

            return redirect()->back()->with('success', 'Payment recorded and posted to journal.');

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

            $currentPaid = $order->amount_paid;
            $newPaid = $currentPaid + $validated['amount'];
            
            $status = $order->payment_status;
            if ($newPaid >= $order->total) {
                $status = 'paid';
            }

            $order->update([
                'amount_paid' => $newPaid,
                'payment_status' => $status,
            ]);

            // Create Journal Entry for Cash Received
            // Debit: Cash/Bank (1000)
            // Credit: Accounts Receivable (1200)

            $description = "Payment from Customer {$order->customer->name} for Order #{$order->order_number}. Ref: " . ($validated['reference'] ?? 'N/A');

            $this->journalEntryService->recordTransaction(
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
                        'entity_type' => get_class($order->customer), // Morph to Customer
                        'entity_id' => $order->customer_id,
                    ]
                ],
                $validated['payment_date']
            );

            DB::commit();

            return back()->with('success', 'Payment recorded and posted to journal.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }
}
