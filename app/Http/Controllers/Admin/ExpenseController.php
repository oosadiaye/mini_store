<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('account')->latest('transaction_date')->paginate(15);
        return view('admin.accounting.expenses.index', compact('expenses'));
    }

    public function create()
    {
        // Expense accounts for categorization
        $expenseAccounts = Account::where('account_type', 'expense')->where('is_active', true)->orderBy('account_code')->get();
        // Payment Types
        $paymentTypes = \App\Models\PaymentType::where('is_active', true)->with('account')->get();
        // Suppliers
        $suppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get();
        // Warehouses
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();
        
        return view('admin.accounting.expenses.create', compact('expenseAccounts', 'paymentTypes', 'suppliers', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'expense_account_id' => 'required|exists:chart_of_accounts,id', // Debit
            'payment_type_id' => 'required|exists:payment_types,id', // Credit source
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'vendor_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $paymentType = \App\Models\PaymentType::findOrFail($request->payment_type_id);
            $creditAccountId = $paymentType->gl_account_id;

            // Check if credit account requires subledger
            $creditAccount = Account::find($creditAccountId);
            if (!$creditAccount) {
                 throw new \Exception("The selected payment type does not have a linked GL Account.");
            }

            if ($creditAccount->sub_ledger_type == 'supplier' && !$request->supplier_id) {
                 throw new \ValidationException("The selected payment method (Credit Source) requires a Supplier to be selected.");
            }

            $refNumber = 'EXP-' . strtoupper(Str::random(8));

            // 1. Create Expense Record
            $expense = Expense::create([
                'reference_number' => $refNumber,
                'transaction_date' => $request->transaction_date,
                'account_id' => $request->expense_account_id,
                'amount' => $request->amount,
                'payment_method' => $paymentType->name, 
                'description' => $request->description,
                'vendor_name' => $request->vendor_name, // Legacy
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'created_by' => auth()->id(),
            ]);

            // 2. Create Journal Entry
            $entry = JournalEntry::create([
                'entry_number' => 'TEMP-' . Str::uuid(),
                'entry_date' => $request->transaction_date,
                'description' => "Expense: " . ($request->description ?? 'Expense Recorded'),
                'warehouse_id' => $request->warehouse_id,
                'created_by' => auth()->id(),
            ]);
            $entry->update(['entry_number' => str_pad($entry->id, 6, '0', STR_PAD_LEFT)]);

            // 3. Debit Expense - Increase Expense
            $entry->lines()->create([
                'account_id' => $request->expense_account_id,
                'debit' => $request->amount,
                'credit' => 0,
                'description' => 'Expense for ' . $refNumber
            ]);

            // 4. Credit Asset (Payment Type Account) - Decrease Asset
            $entry->lines()->create([
                'account_id' => $creditAccountId,
                'debit' => 0,
                'credit' => $request->amount,
                'description' => 'Payment made via ' . $paymentType->name,
                'entity_type' => ($creditAccount->sub_ledger_type == 'supplier' && $request->supplier_id) ? \App\Models\Supplier::class : null,
                'entity_id' => ($creditAccount->sub_ledger_type == 'supplier' && $request->supplier_id) ? $request->supplier_id : null,
            ]);
        });

        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded and journal entry posted.');
    }
}
