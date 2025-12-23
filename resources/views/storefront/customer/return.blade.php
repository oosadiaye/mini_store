@extends('storefront.layout')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('storefront.customer.order', $order->id) }}" class="text-gray-500 hover:text-indigo-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Order #{{ $order->order_number }}
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50">
                    <h1 class="text-xl font-bold text-gray-900">Request Return</h1>
                    <p class="text-sm text-gray-600">Select the items you wish to return from Order #{{ $order->order_number }}</p>
                </div>

                <form action="{{ route('storefront.order.return.store', $order->id) }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="space-y-6">
                        @foreach($order->items as $item)
                        <div class="flex items-start p-4 border rounded-lg hover:bg-gray-50 transition border-gray-200">
                            <div class="mr-4 pt-1">
                                <input type="checkbox" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}" 
                                    class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                    onchange="document.getElementById('qty_container_{{ $loop->index }}').classList.toggle('hidden'); document.getElementById('qty_{{ $loop->index }}').disabled = !this.checked; document.getElementById('reason_{{ $loop->index }}').disabled = !this.checked;">
                            </div>
                            
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <h3 class="font-medium text-gray-900">{{ $item->product_name }}</h3>
                                    <span class="text-sm text-gray-500">${{ number_format($item->price, 2) }}</span>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">Variant: {{ $item->variant_name ?? 'N/A' }}</p>
                                
                                <div id="qty_container_{{ $loop->index }}" class="hidden mt-3 space-y-3 bg-white p-3 rounded border border-gray-100">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantity to Return (Max: {{ $item->quantity }})</label>
                                        <select name="items[{{ $loop->index }}][quantity]" id="qty_{{ $loop->index }}" disabled class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @for($i = 1; $i <= $item->quantity; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Reason</label>
                                        <select name="items[{{ $loop->index }}][reason]" id="reason_{{ $loop->index }}" disabled class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select a reason</option>
                                            <option value="Damaged">Damaged / Defective</option>
                                            <option value="Wrong Item">Received Wrong Item</option>
                                            <option value="Changed Mind">Changed Mind</option>
                                            <option value="Size/Fit">Size / Fit Issue</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Comments</label>
                        <textarea name="comments" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Please provide any additional details..."></textarea>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('storefront.customer.order', $order->id) }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">Submit Return Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
