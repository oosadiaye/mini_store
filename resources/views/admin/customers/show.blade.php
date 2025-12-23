@extends('admin.layout')

@section('content')
<div class="mb-6 flex items-center space-x-4">
    <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700">
        &larr; Back to Customers
    </a>
    <h2 class="text-2xl font-bold text-gray-800">{{ $customer->name }}</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Customer Profile & Stats -->
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
            <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 text-indigo-600 text-3xl font-bold">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <h3 class="text-lg font-bold text-gray-900">{{ $customer->name }}</h3>
            <p class="text-gray-500 text-sm mb-4">{{ $customer->email }}</p>
            <p class="text-xs text-gray-400">Customer since {{ $customer->created_at->format('M Y') }}</p>
            <div class="mt-4">
                <a href="{{ route('admin.customers.ledger', $customer) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    View Ledger
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">Lifetime Stats</h4>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Total Spent</span>
                    <span class="font-bold text-gray-900">${{ number_format($stats['total_spent'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Total Orders</span>
                    <span class="font-bold text-gray-900">{{ $stats['total_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Avg. Order Value</span>
                    <span class="font-bold text-gray-900">${{ number_format($stats['avg_order_value'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Order History -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 font-semibold text-gray-700">
                Recent Orders
            </div>
            
            @if($customer->orders->count() > 0)
            <table class="w-full text-left">
                <thead class="text-xs uppercase bg-gray-50 text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Order</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($customer->orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono text-sm text-indigo-600">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                            ${{ number_format($order->total, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-gray-400 hover:text-indigo-600">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center text-gray-500">
                This customer hasn't placed any orders yet.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
