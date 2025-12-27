@extends('admin.layout')

@section('content')
<div class="mb-4 md:mb-6 flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Orders</h2>
        <p class="text-gray-600 text-xs md:text-sm">Manage and track your store's orders.</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
        <a href="{{ route('admin.orders.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 md:px-4 py-2 rounded-lg transition flex items-center justify-center shadow-sm text-sm">
             <svg class="h-4 w-4 md:h-5 md:w-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
             New Sales Order
        </a>
        <a href="{{ route('admin.pos.index') }}" class="bg-gray-800 hover:bg-gray-900 text-white px-3 md:px-4 py-2 rounded-lg transition flex items-center justify-center shadow-sm border border-gray-700 text-sm">
             <svg class="h-4 w-4 md:h-5 md:w-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
             Open POS
        </a>
    </div>
</div>

<div class="mb-4 md:mb-6 bg-white rounded-lg p-2 border border-gray-200 shadow-sm flex flex-col gap-3">
    <nav class="flex space-x-1.5 md:space-x-2 overflow-x-auto pb-1" aria-label="Tabs">
        <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['source' => null])) }}" 
           class="{{ request('source') === null ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }} px-2 md:px-3 py-1.5 md:py-2 font-medium text-xs md:text-sm rounded-md transition whitespace-nowrap">
            All Orders
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['source' => 'storefront'])) }}" 
           class="{{ request('source') === 'storefront' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }} px-2 md:px-3 py-1.5 md:py-2 font-medium text-xs md:text-sm rounded-md transition whitespace-nowrap">
            Online Orders
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['source' => 'admin'])) }}" 
           class="{{ request('source') === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }} px-2 md:px-3 py-1.5 md:py-2 font-medium text-xs md:text-sm rounded-md transition whitespace-nowrap">
            Sales Orders
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['source' => 'pos'])) }}" 
           class="{{ request('source') === 'pos' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:text-gray-700' }} px-2 md:px-3 py-1.5 md:py-2 font-medium text-xs md:text-sm rounded-md transition whitespace-nowrap">
            POS Orders
        </a>
    </nav>

    <form method="GET" class="flex items-center gap-2 px-2">
        @if(request('source'))
            <input type="hidden" name="source" value="{{ request('source') }}">
        @endif
        <select name="warehouse_id" onchange="this.form.submit()" class="w-full md:w-auto text-xs md:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">All Branches</option>
            @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                    {{ $wh->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>

<!-- Orders Table (Desktop) -->
<div class="hidden lg:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">Order #</th>
                <th class="p-4 border-b border-gray-100">Customer</th>
                <th class="p-4 border-b border-gray-100">Date</th>
                <th class="p-4 border-b border-gray-100">Status</th>
                <th class="p-4 border-b border-gray-100">Payment</th>
                <th class="p-4 border-b border-gray-100 text-right">Total</th>
                <th class="p-4 border-b border-gray-100 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 font-mono text-sm font-medium text-gray-900">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:underline">
                        {{ $order->order_number }}
                    </a>
                </td>
                <td class="p-4 text-sm text-gray-700">
                    <div class="font-medium">{{ $order->customer->name }}</div>
                    <div class="text-xs text-gray-500">{{ $order->customer->email }}</div>
                </td>
                <td class="p-4 text-sm text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</td>
                <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                           ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
                <td class="p-4 text-sm text-gray-900 font-medium text-right">{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($order->total, 2) }}</td>
                <td class="p-4 text-center">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-gray-500 hover:text-indigo-600 mx-1">
                        View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-8 text-center text-gray-500">
                    No orders found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($orders->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<!-- Orders Cards (Mobile) -->
<div class="lg:hidden space-y-3">
    @forelse($orders as $order)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-2">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono text-sm font-semibold text-indigo-600 hover:underline">
                    {{ $order->order_number }}
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            
            <!-- Customer Info -->
            <div class="mb-3">
                <div class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</div>
                <div class="text-xs text-gray-500">{{ $order->customer->email }}</div>
            </div>
            
            <!-- Order Details -->
            <div class="space-y-2 py-3 border-t border-gray-100">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500 flex items-center">
                        <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Date
                    </span>
                    <span class="text-gray-900 font-medium">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500 flex items-center">
                        <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Payment
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                           ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500 flex items-center">
                        <svg class="h-3.5 w-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Total
                    </span>
                    <span class="text-sm font-bold text-gray-900">{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
            
            <!-- Card Footer -->
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="block w-full text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-3 py-2 rounded text-sm font-medium transition">
                    <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    View Details
                </a>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <p class="text-gray-500 text-sm">No orders found.</p>
        </div>
    @endforelse
</div>
@endsection
