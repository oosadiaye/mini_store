@extends('admin.layout')

@section('title', 'Customers')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
        <a href="{{ route('admin.customers.create', $tenant->slug) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
            + Add Customer
        </a>
    </div>
    <div class="w-1/3">
        <form action="{{ route('admin.customers.index') }}" method="GET">
            <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" 
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">Name</th>
                <th class="p-4 border-b border-gray-100">Email</th>
                <th class="p-4 border-b border-gray-100 text-center">Orders</th>
                <th class="p-4 border-b border-gray-100">Joined Date</th>
                <th class="p-4 border-b border-gray-100 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($customers as $customer)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 text-sm font-medium text-gray-900">
                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="flex items-center group">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 font-bold text-xs group-hover:bg-indigo-200 transition">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <span class="group-hover:text-indigo-600 transition">{{ $customer->name }}</span>
                    </a>
                </td>
                <td class="p-4 text-sm text-gray-600">{{ $customer->email }}</td>
                <td class="p-4 text-sm text-gray-600 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $customer->orders_count }}
                    </span>
                </td>
                <td class="p-4 text-sm text-gray-500">{{ $customer->created_at->format('M d, Y') }}</td>
                <td class="p-4 text-center">
                    <a href="{{ route('admin.customers.ledger', $customer->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-2">
                        Ledger
                    </a>
                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        View Details
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">
                    No customers found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($customers->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
