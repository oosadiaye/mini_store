<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders')
            ->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders' => function($query) {
            $query->latest()->take(10);
        }]);

        $stats = [
            'total_spent' => $customer->orders->where('payment_status', 'paid')->sum('total'),
            'total_orders' => $customer->orders->count(),
            'avg_order_value' => $customer->orders->count() > 0 
                ? $customer->orders->where('payment_status', 'paid')->avg('total') 
                : 0,
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function ledger(Customer $customer)
    {
        $transactions = $customer->transactions()
            ->with(['journalEntry', 'account'])
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.entry_date', 'desc')
            ->orderBy('journal_entries.created_at', 'desc')
            ->select('journal_entry_lines.*')
            ->paginate(50);

        return view('admin.customers.ledger', compact('customer', 'transactions'));
    }
}
