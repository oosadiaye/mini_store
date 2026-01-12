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

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_storefront_customer' => 'sometimes|boolean',
        ];

        if ($request->boolean('is_storefront_customer')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        $customerData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'tenant_id' => app('tenant')->id,
            'is_storefront_customer' => $request->boolean('is_storefront_customer'),
        ];

        if ($request->boolean('is_storefront_customer') && isset($validated['password'])) {
            $customerData['password'] = bcrypt($validated['password']);
        }

        $customer = Customer::create($customerData);

        // Save Address if any address field is provided
        if (!empty($validated['address_line1']) || !empty($validated['city'])) {
            $customer->addresses()->create([
                'address_line1' => $validated['address_line1'] ?? '',
                'address_line2' => $validated['address_line2'],
                'city' => $validated['city'] ?? '',
                'state' => $validated['state'],
                'postal_code' => $validated['postal_code'] ?? '',
                'country' => $validated['country'] ?? '',
                'address_type' => 'billing', // Default type
                'is_default' => true
            ]);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully');
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

    /**
     * Store a new customer (Quick creation from POS/Sales)
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'message' => 'Customer created successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Customer created successfully');
    }
}
