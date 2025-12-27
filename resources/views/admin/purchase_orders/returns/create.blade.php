@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Return Items to Supplier</h3>
        <a href="{{ route('admin.purchase-orders.show', $purchaseOrder->id) }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
    </div>

    <form action="{{ route('admin.purchase-orders.returns.store', $purchaseOrder->id) }}" method="POST" class="p-6">
        @csrf
        
        <div class="mb-6">
             <h4 class="font-medium text-gray-700 mb-2">PO #{{ $purchaseOrder->po_number }} - {{ $purchaseOrder->supplier->name }}</h4>
             <p class="text-sm text-gray-500">Select items and quantities to return.</p>
        </div>

        <table class="w-full text-sm text-left mb-6">
            <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                <tr>
                    <th class="px-4 py-2">Item</th>
                    <th class="px-4 py-2 text-right">Ordered</th>
                    <th class="px-4 py-2 text-right">Received</th>
                    <th class="px-4 py-2 text-right w-32">Return Qty</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($purchaseOrder->items as $item)
                <tr>
                    <td class="px-4 py-3">{{ $item->product->name }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->quantity_ordered }}</td>
                    <td class="px-4 py-3 text-right">{{ $item->quantity_received }}</td>
                    <td class="px-4 py-3 text-right">
                        <input type="number" 
                               name="items[{{ $item->id }}][quantity]" 
                               value="0" 
                               min="0" 
                               max="{{ $item->quantity_received }}" 
                               class="w-full text-right border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="flex justify-end gap-3">
             <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700">
                 Process Return
             </button>
        </div>
    </form>
</div>
@endsection
