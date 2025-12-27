@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Order Returns</h2>
    </div>

    <!-- Returns Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Return ID</th>
                        <th class="px-6 py-4">Order Ref</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($returns as $return)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            #RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $return->order_id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                #{{ $return->order->order_number ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $return->order->customer->name ?? 'Guest' }}
                        </td>
                         <td class="px-6 py-4 text-gray-600">
                            {{ $return->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide
                                {{ $return->status === 'completed' ? 'bg-green-100 text-green-700' : 
                                   ($return->status === 'rejected' ? 'bg-red-100 text-red-700' : 
                                   ($return->status === 'approved' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                {{ $return->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">
                            {{ $tenant->data['currency_symbol'] ?? '$' }}{{ number_format($return->refund_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.returns.show', $return->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium bg-indigo-50 px-3 py-1 rounded-lg transition hover:bg-indigo-100">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <p class="text-base font-medium text-gray-900">No returns found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection
