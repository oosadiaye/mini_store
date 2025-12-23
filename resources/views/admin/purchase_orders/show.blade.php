@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <!-- Header Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <h2 class="text-2xl font-bold text-gray-800">PO #{{ substr($purchaseOrder->id, 0, 8) }}</h2>
                @if($purchaseOrder->status === 'draft')
                    <span class="bg-gray-100 text-gray-800 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide">Draft</span>
                @elseif($purchaseOrder->status === 'received')
                    <span class="bg-green-100 text-green-800 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide">Received</span>
                @endif
            </div>
            <div class="text-sm text-gray-500">
                Created on {{ $purchaseOrder->order_date->format('M d, Y') }} &bull; Supplier: <span class="font-medium text-gray-900">{{ $purchaseOrder->supplier->name }}</span> &bull; To: <span class="font-medium text-gray-900">{{ $purchaseOrder->warehouse->name }}</span>
            </div>
        </div>

        <div class="mt-4 md:mt-0 flex items-center space-x-3">
             @if($purchaseOrder->status === 'draft')
                <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder->id) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Order
                </a>
                
                <form action="{{ route('admin.purchase-orders.place', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Place this order? The supplier will be notified (simulation).')">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-bold shadow-sm hover:bg-indigo-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Place Order
                    </button>
                </form>

                <form action="{{ route('admin.purchase-orders.destroy', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Delete this draft order?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium px-3">Delete</button>
                </form>
                <form action="{{ route('admin.purchase-orders.receive', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Confirm receipt? This will add stock to inventory.')">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-lg font-bold shadow-sm hover:bg-green-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Receive Stock
                    </button>
                </form>
            @elseif($purchaseOrder->status === 'received')
                <a href="{{ route('admin.purchase-orders.returns.create', $purchaseOrder->id) }}" class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg transition border border-red-200 border-2 font-medium">
                    Return to Supplier
                </a>

                @if($purchaseOrder->billed_status !== 'billed')
                <form action="{{ route('admin.purchase-orders.convert', $purchaseOrder->id) }}" method="POST" id="convert-form-{{$purchaseOrder->id}}">
                    @csrf
                    <input type="hidden" name="invoice_number" id="invoice_input-{{$purchaseOrder->id}}">
                    <button type="button" onclick="let inv = prompt('Enter Supplier Invoice Number (e.g. INV-123):'); if(inv) { document.getElementById('invoice_input-{{$purchaseOrder->id}}').value = inv; document.getElementById('convert-form-{{$purchaseOrder->id}}').submit(); }" class="bg-purple-600 text-white px-5 py-2 rounded-lg font-bold shadow-sm hover:bg-purple-700 flex items-center transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Convert to Bill
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <!-- Items List -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 font-bold text-gray-800">
                    Order Items
                </div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3 text-right">Cost</th>
                            <th class="px-6 py-3 text-right">Qty</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            @if($purchaseOrder->status !== 'received' && $purchaseOrder->status !== 'ordered')
                            <th class="px-6 py-3 text-right">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td class="px-6 py-3 font-medium text-gray-900">
                                {{ $item->product->name }}
                                <div class="text-xs text-gray-400">{{ $item->variant_sku }}</div>
                            </td>
                            <td class="px-6 py-3 text-right">${{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-6 py-3 text-right font-mono">{{ $item->quantity_ordered }}</td>
                            <td class="px-6 py-3 text-right font-bold">${{ number_format($item->total, 2) }}</td>
                            @if($purchaseOrder->status === 'draft')
                            <td class="px-6 py-3 text-right">
                                <form action="{{ route('admin.purchase-orders.items.destroy', [$purchaseOrder->id, $item->id]) }}" method="POST" onsubmit="return confirm('Remove item?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs uppercase font-bold">Remove</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-600">Total Amount:</td>
                            <td class="px-6 py-3 text-right font-extrabold text-indigo-900 text-lg">${{ number_format($purchaseOrder->total, 2) }}</td>
                            @if($purchaseOrder->status === 'draft')<td></td>@endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
