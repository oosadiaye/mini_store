<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with('account')->latest('transaction_date')->paginate(15);
        return view('admin.accounting.incomes.index', compact('incomes'));
    }

    public function create()
    {
        // Revenue accounts for categorization
        $revenueAccounts = Account::where('account_type', 'revenue')->where('is_active', true)->orderBy('account_code')->get();
        // Payment Types
        $paymentTypes = \App\Models\PaymentType::where('is_active', true)->with('account')->get();
        // Customers
        $customers = \App\Models\Customer::orderBy('name')->get();
        // Warehouses
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();
        
        return view('admin.accounting.incomes.create', compact('revenueAccounts', 'paymentTypes', 'customers', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'revenue_account_id' => 'required|exists:chart_of_accounts,id', // Credit
            'payment_type_id' => 'required|exists:payment_types,id',
            'customer_id' => 'nullable|exists:customers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'customer_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $paymentType = \App\Models\PaymentType::findOrFail($request->payment_type_id);
            $debitAccountId = $paymentType->gl_account_id;
            
            // Check if debit account requires subledger
            $debitAccount = Account::find($debitAccountId);
            if (!$debitAccount) {
                throw new \Exception("The selected payment type does not have a linked GL Account.");
            }

            if ($debitAccount->sub_ledger_type == 'customer' && !$request->customer_id) {
                 throw new \ValidationException("The selected payment method (Asset Account) requires a Customer to be selected.");
            }

            $refNumber = 'INC-' . strtoupper(Str::random(8));

            // 1. Create Income Record
            $income = Income::create([
                'reference_number' => $refNumber,
                'transaction_date' => $request->transaction_date,
                'account_id' => $request->revenue_account_id,
                'amount' => $request->amount,
                'payment_method' => $paymentType->name, 
                'description' => $request->description,
                'customer_name' => $request->customer_name, // Legacy/Fallback
                'customer_id' => $request->customer_id,
                'warehouse_id' => $request->warehouse_id,
                'created_by' => auth()->id(),
            ]);

            // 2. Create Journal Entry
            $entry = JournalEntry::create([
                'entry_number' => 'TEMP-' . Str::uuid(),
                'entry_date' => $request->transaction_date,
                'description' => "Income: " . ($request->description ?? 'Revenue Recorded'),
                'warehouse_id' => $request->warehouse_id,
                'created_by' => auth()->id(),
            ]);
            $entry->update(['entry_number' => str_pad($entry->id, 6, '0', STR_PAD_LEFT)]);

            // 3. Debit Asset (Payment Type Account)
            $entry->lines()->create([
                'account_id' => $debitAccountId,
                'debit' => $request->amount,
                'credit' => 0,
                'description' => 'Payment Received via ' . $paymentType->name,
                'entity_type' => ($debitAccount->sub_ledger_type == 'customer' && $request->customer_id) ? \App\Models\Customer::class : null,
                'entity_id' => ($debitAccount->sub_ledger_type == 'customer' && $request->customer_id) ? $request->customer_id : null,
            ]);

            // 4. Credit Revenue - Increase Revenue
            $entry->lines()->create([
                'account_id' => $request->revenue_account_id,
                'debit' => 0,
                'credit' => $request->amount,
                'description' => 'Revenue for ' . $refNumber
            ]);
        });

        return redirect()->route('admin.incomes.index')->with('success', 'Income recorded and journal entry posted.');
    }
}
