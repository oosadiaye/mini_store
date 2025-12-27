<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Renter;
use App\Services\JournalEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RenterController extends Controller
{
    protected $jeService;

    public function __construct(JournalEntryService $jeService)
    {
        $this->jeService = $jeService;
    }

    public function index(Request $request)
    {
        $query = Renter::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $renters = $query->latest()->paginate(15);

        return view('admin.renters.index', compact('renters'));
    }

    public function create()
    {
        return view('admin.renters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'id_number' => 'nullable|string|max:50',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'rental_amount' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'security_deposit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $renter = Renter::create($validated);

            // Record security deposit if provided
            if ($validated['security_deposit'] > 0) {
                $this->jeService->recordTransaction(
                    "Security Deposit from {$renter->name}",
                    [
                        ['account_code' => '1000', 'debit' => $validated['security_deposit'], 'credit' => 0], // Cash
                        ['account_code' => '2200', 'debit' => 0, 'credit' => $validated['security_deposit']], // Security Deposits Payable
                    ],
                    now()
                );
            }

            DB::commit();

            return redirect()->route('admin.renters.show', $renter)->with('success', 'Renter created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating renter: ' . $e->getMessage());
        }
    }

    public function show(Renter $renter)
    {
        $renter->load(['transactions.journalEntry', 'transactions.account']);
        
        return view('admin.renters.show', compact('renter'));
    }

    public function edit(Renter $renter)
    {
        return view('admin.renters.edit', compact('renter'));
    }

    public function update(Request $request, Renter $renter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'id_number' => 'nullable|string|max:50',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'rental_amount' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'status' => 'required|in:active,inactive,terminated',
            'notes' => 'nullable|string',
        ]);

        $renter->update($validated);

        return redirect()->route('admin.renters.show', $renter)->with('success', 'Renter updated successfully');
    }

    public function destroy(Renter $renter)
    {
        $renter->update(['status' => 'terminated']);
        $renter->delete();

        return redirect()->route('admin.renters.index')->with('success', 'Renter terminated successfully');
    }

    /**
     * Generate rental invoice
     */
    public function generateInvoice(Request $request, Renter $renter)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'invoice_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Post to AR
            $lines = $this->jeService->recordTransaction(
                $validated['description'] ?? "Rental Invoice for {$renter->name}",
                [
                    ['account_code' => '1300', 'debit' => $validated['amount'], 'credit' => 0], // AR - Renters
                    ['account_code' => '4100', 'debit' => 0, 'credit' => $validated['amount']], // Rental Revenue
                ],
                $validated['invoice_date']
            );

            // Link AR line to renter
            foreach ($lines as $line) {
                if ($line->account->account_code == '1300') {
                    $line->update(['renter_id' => $renter->id]);
                }
            }

            DB::commit();

            return back()->with('success', 'Invoice generated and posted to AR');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error generating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Record payment from renter
     */
    public function recordPayment(Request $request, Renter $renter)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Determine cash/bank account based on payment method
            $cashAccount = $validated['payment_method'] === 'bank' ? '1010' : '1000';

            // Post payment
            $lines = $this->jeService->recordTransaction(
                "Payment from {$renter->name} - {$validated['notes']}",
                [
                    ['account_code' => $cashAccount, 'debit' => $validated['amount'], 'credit' => 0], // Cash/Bank
                    ['account_code' => '1300', 'debit' => 0, 'credit' => $validated['amount']], // AR - Renters
                ],
                $validated['payment_date']
            );

            // Link AR line to renter
            foreach ($lines as $line) {
                if ($line->account->account_code == '1300') {
                    $line->update(['renter_id' => $renter->id]);
                }
            }

            DB::commit();

            return back()->with('success', 'Payment recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }
}
