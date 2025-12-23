<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('account_code', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort_by', 'account_code');
        $sortDir = $request->get('sort_dir', 'asc');
        
        $validSorts = ['account_code', 'account_name', 'account_type'];
        if (in_array($sortField, $validSorts)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('account_code', 'asc');
        }

        $accounts = $query->with('parent')->paginate(20)->withQueryString();

        return view('admin.accounting.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $parents = Account::orderBy('account_code')->get();
        return view('admin.accounting.accounts.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_code' => 'required|unique:chart_of_accounts,account_code',
            'account_name' => 'required',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'sub_ledger_type' => 'nullable|in:customer,supplier',
        ]);

        Account::create($request->all());

        return redirect()->route('admin.accounts.index')->with('success', 'Account created.');
    }

    public function edit(Account $account)
    {
        $parents = Account::where('id', '!=', $account->id)->orderBy('account_code')->get();
        return view('admin.accounting.accounts.edit', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'account_code' => 'required|unique:chart_of_accounts,account_code,' . $account->id,
            'account_name' => 'required',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'sub_ledger_type' => 'nullable|in:customer,supplier',
        ]);

        $account->update($request->all());

        return redirect()->route('admin.accounts.index')->with('success', 'Account updated.');
    }

    public function copy(Account $account)
    {
        $parents = Account::orderBy('account_code')->get();
        
        // Auto-numbering logic
        // Assumes accounting code blocks (1xxx, 2xxx, etc.) based on type, or just +1 if simple.
        // We'll try to find the next available code that "looks like" the current one.
        
        $prefix = substr($account->account_code, 0, 1); // e.g. '1' from '1010'
        $maxLength = strlen($account->account_code);
        
        // Find highest existing code starting with this prefix
        $latest = Account::where('account_code', 'like', $prefix . '%')
            ->whereRaw('LENGTH(account_code) = ?', [$maxLength])
            ->orderByRaw('CAST(account_code AS UNSIGNED) DESC')
            ->first();
            
        $newCode = $latest ? (string)($latest->account_code + 1) : $account->account_code . '1';

        $copyData = [
            'account_code' => $newCode,
            'account_name' => $account->account_name . ' (Copy)',
            'account_type' => $account->account_type,
            'parent_id' => $account->parent_id
        ];
        
        return view('admin.accounting.accounts.create', compact('parents', 'copyData'));
    }

    public function destroy(Account $account)
    {
        // 1. Check Balance
        if (abs($account->balance) > 0) {
            return back()->with('error', 'Cannot delete account with non-zero balance.');
        }

        // 2. Check Transactions
        if ($account->transactions()->exists()) {
             return back()->with('error', 'Cannot delete account with existing transactions.');
        }

        // 3. Check Children
        if ($account->children()->count() > 0) {
            return back()->with('error', 'Cannot delete account that has sub-accounts.');
        }

        $account->delete();

        return redirect()->route('admin.accounts.index')->with('success', 'Account deleted successfully.');
    }
    public function show(Request $request, Account $account)
    {
        $query = $account->transactions()
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->select('journal_entry_lines.*');

        if ($request->start_date) {
            $query->where('journal_entries.entry_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('journal_entries.entry_date', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('journal_entries.entry_date', 'desc')
                              ->orderBy('journal_entries.id', 'desc')
                              ->with('journalEntry')
                              ->paginate(50)
                              ->withQueryString();

        return view('admin.accounting.accounts.show', compact('account', 'transactions'));
    }
}
