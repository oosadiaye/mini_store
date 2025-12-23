@extends('admin.layout')

@section('content')
<div class="mb-6 flex items-center space-x-4">
    <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-500 hover:text-gray-700">
        &larr; Back to Order
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Create Return for Order #{{ $order->order_number }}</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <form action="{{ route('admin.orders.returns.store', $order) }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 font-semibold text-gray-700">
                    Select Items to Return
                </div>
                
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3 text-right">Price</th>
                            <th class="px-6 py-3 text-center">Qty Bought</th>
                            <th class="px-6 py-3 text-center">Return Qty</th>
                            <th class="px-6 py-3">Condition</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->variant_name }}</div>
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="number" 
                                       name="items[{{ $loop->index }}][quantity]" 
                                       min="0" 
                                       max="{{ $item->quantity }}" 
                                       value="0" 
                                       class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center text-sm">
                            </td>
                            <td class="px-6 py-4">
                                <select name="items[{{ $loop->index }}][condition]" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="new">New / Unopened</option>
                                    <option value="open_box">Open Box</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Return Reason</label>
                        <textarea name="return_reason" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Customer changed mind, defective, etc."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes (Internal)</label>
                        <textarea name="admin_notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                
                <div class="mt-4 flex items-center">
                    <input type="checkbox" id="restock" name="restock" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="restock" class="ml-2 block text-sm text-gray-900">
                        Restock items to inventory
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Process Return
                </button>
            </div>
        </form>
    </div>

    <!-- Summary / Info Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Return Policy</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Processing a return will record the refunded amount.</p>
                        <p class="mt-1">If "Restock items" is checked, the inventory count for the selected products will be increased automatically.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
