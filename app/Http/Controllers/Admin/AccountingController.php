<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $branchId = $request->get('branch_id'); // Null means consolidated

        // Helper query mod
        $journalFilter = function($q) use ($startDate, $endDate, $branchId) {
            $q->whereBetween('entry_date', [$startDate, $endDate]);
            if ($branchId) {
                $q->where('warehouse_id', $branchId);
            }
        };

        // Revenue (Credit - Debit), Expense (Debit - Credit)
        $revenues = Account::where('account_type', 'revenue')->orderBy('account_code')->get()
            ->map(function ($account) use ($journalFilter) {
                // Manually summing to ensure we get the correct lines
                $credit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('credit');
                $debit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('debit');
                $account->balance = $credit - $debit;
                return $account;
            })->filter(fn($a) => abs($a->balance) > 0.001);

        $expenses = Account::where('account_type', 'expense')->orderBy('account_code')->get()
            ->map(function ($account) use ($journalFilter) {
                $credit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('credit');
                $debit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('debit');
                $account->balance = $debit - $credit;
                return $account;
            })->filter(fn($a) => abs($a->balance) > 0.001);

        $totalRevenue = $revenues->sum('balance');
        $totalExpenses = $expenses->sum('balance');
        $netIncome = $totalRevenue - $totalExpenses;
        
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();

        return view('admin.accounting.reports.profit_loss', compact('revenues', 'expenses', 'totalRevenue', 'totalExpenses', 'netIncome', 'startDate', 'endDate', 'warehouses', 'branchId'));
    }

    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->get('date', Carbon::now()->toDateString());
        $branchId = $request->get('branch_id');

        $journalFilter = function($q) use ($asOfDate, $branchId) {
            $q->where('entry_date', '<=', $asOfDate);
            if ($branchId) {
                $q->where('warehouse_id', $branchId);
            }
        };

        // Asset (Debit - Credit)
        $assets = Account::where('account_type', 'asset')->orderBy('account_code')->get()
            ->map(function ($account) use ($journalFilter) {
                $debit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('debit');
                $credit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('credit');
                $account->balance = $debit - $credit;
                return $account;
            })->filter(fn($a) => abs($a->balance) > 0.001);

        // Liability (Credit - Debit)
        $liabilities = Account::where('account_type', 'liability')->orderBy('account_code')->get()
            ->map(function ($account) use ($journalFilter) {
                $debit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('debit');
                $credit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('credit');
                $account->balance = $credit - $debit;
                return $account;
            })->filter(fn($a) => abs($a->balance) > 0.001);

        // Equity (Credit - Debit), excluding computed Net Income logic for now
        $equity = Account::where('account_type', 'equity')->orderBy('account_code')->get()
            ->map(function ($account) use ($journalFilter) {
                $debit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('debit');
                $credit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', $journalFilter)
                    ->sum('credit');
                $account->balance = $credit - $debit;
                return $account;
            })->filter(fn($a) => abs($a->balance) > 0.001);

        // Calculate Net Income (Retained Earnings) up to this date
        // Revenue (All time up to date)
        $revCredit = JournalEntryLine::whereHas('account', fn($q) => $q->where('account_type', 'revenue'))
             ->whereHas('journalEntry', $journalFilter)
             ->sum('credit');
        $revDebit = JournalEntryLine::whereHas('account', fn($q) => $q->where('account_type', 'revenue'))
             ->whereHas('journalEntry', $journalFilter)
             ->sum('debit');
        $totalRevenue = $revCredit - $revDebit;

        $expDebit = JournalEntryLine::whereHas('account', fn($q) => $q->where('account_type', 'expense'))
             ->whereHas('journalEntry', $journalFilter)
             ->sum('debit');
        $expCredit = JournalEntryLine::whereHas('account', fn($q) => $q->where('account_type', 'expense'))
             ->whereHas('journalEntry', $journalFilter)
             ->sum('credit');
        $totalExpense = $expDebit - $expCredit;

        $netIncome = $totalRevenue - $totalExpense;

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance') + $netIncome;

        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();

        return view('admin.accounting.reports.balance_sheet', compact('assets', 'liabilities', 'equity', 'netIncome', 'totalAssets', 'totalLiabilities', 'totalEquity', 'asOfDate', 'warehouses', 'branchId'));
    }

    public function trialBalance(Request $request)
    {
        $asOfDate = $request->get('date', Carbon::now()->toDateString());

        $accounts = Account::orderBy('account_code')->get()
            ->map(function ($account) use ($asOfDate) {
                $debit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('entry_date', '<=', $asOfDate))
                    ->sum('debit');
                $credit = JournalEntryLine::where('account_id', $account->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('entry_date', '<=', $asOfDate))
                    ->sum('credit');
                
                $net = $debit - $credit;
                
                $account->net_debit = $net > 0 ? $net : 0;
                $account->net_credit = $net < 0 ? abs($net) : 0;
                
                return $account;
            })->filter(fn($a) => $a->net_debit > 0.001 || $a->net_credit > 0.001);

        $totalDebit = $accounts->sum('net_debit');
        $totalCredit = $accounts->sum('net_credit');

        return view('admin.accounting.reports.trial_balance', compact('accounts', 'totalDebit', 'totalCredit', 'asOfDate'));
    }
}
