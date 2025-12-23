@extends('admin.layout')

@section('content')
<div class="mb-6 flex items-center space-x-4">
    <a href="{{ route('admin.purchase-orders.show', $purchaseOrder) }}" class="text-gray-500 hover:text-gray-700">
        &larr; Back to Order
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Return to Supplier - PO #{{ $purchaseOrder->po_number }}</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <form action="{{ route('admin.purchase-orders.returns.store', $purchaseOrder) }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden overflow-x-auto mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 font-semibold text-gray-700">
                    Select Items to Return
                </div>
                
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3 text-right">Cost</th>
                            <th class="px-6 py-3 text-center">Qty Received</th>
                            <th class="px-6 py-3 text-center">Return Qty</th>
                            <th class="px-6 py-3">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product ? $item->product->name : 'Unknown Product' }}</div>
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">
                                {{ number_format($item->unit_cost, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                {{ $item->quantity_received }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="number" 
                                       name="items[{{ $loop->index }}][quantity]" 
                                       min="0" 
                                       max="{{ $item->quantity_received }}" 
                                       value="0" 
                                       class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center text-sm">
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="items[{{ $loop->index }}][return_reason]" placeholder="Defective, etc." class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                    <textarea name="admin_notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Create Return & Deduct Inventory
                </button>
            </div>
        </form>
    </div>

    <!-- Summary / Info Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Inventory Warning</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Processing this return will <strong>immediately deduct</strong> the selected quantity from your warehouse inventory.</p>
                        <p class="mt-1">This action cannot be easily undone.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
