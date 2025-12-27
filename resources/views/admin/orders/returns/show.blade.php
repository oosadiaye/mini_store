@extends('admin.layout')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h2 class="text-2xl font-bold text-gray-800">Return #RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide
                    {{ $return->status === 'completed' ? 'bg-green-100 text-green-700' : 
                       ($return->status === 'rejected' ? 'bg-red-100 text-red-700' : 
                       ($return->status === 'approved' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                    {{ $return->status }}
                </span>
            </div>
            <p class="text-gray-500 text-sm">Created on {{ $return->created_at->format('M d, Y, h:i A') }}</p>
        </div>
        <a href="{{ route('admin.returns.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Returns
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Items Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Returned Items</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($return->items as $item)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-start gap-4">
                            <!-- Image -->
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden border border-gray-200">
                                @if($item->orderItem->product && $item->orderItem->product->primaryImage())
                                    <img src="{{ $item->orderItem->product->primaryImage()->url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Product Info -->
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 mb-1">
                                    {{ $item->orderItem->product->name ?? 'Deleted Product' }}
                                </h4>
                                <div class="flex flex-wrap gap-2 text-xs">
                                    <span class="text-gray-500">Qty: <span class="font-bold text-gray-900">{{ $item->quantity_returned }}</span></span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-gray-500 md:inline">Condition: <span class="capitalize font-medium text-gray-700">{{ $item->condition }}</span></span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-gray-500">Restocked: 
                                        {!! $item->restock_inventory ? '<span class="text-green-600 font-bold">Yes</span>' : '<span class="text-red-500">No</span>' !!}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Price -->
                        <div class="text-right">
                            <div class="font-bold text-gray-900">
                                {{ $tenant->data['currency_symbol'] ?? '$' }}{{ number_format($item->refund_amount, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Footer Total -->
                <div class="px-6 py-4 bg-gray-50 flex justify-between items-center border-t border-gray-100">
                    <span class="text-sm font-bold text-gray-600">Total Refund Amount</span>
                    <span class="text-xl font-bold text-gray-900">{{ $tenant->data['currency_symbol'] ?? '$' }}{{ number_format($return->refund_amount, 2) }}</span>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4">Reason & Notes</h3>
                
                <div class="mb-4">
                    <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Customer Reason</label>
                    <div class="p-3 bg-gray-50 rounded-lg text-sm text-gray-700 border border-gray-200">
                        {{ $return->return_reason ?? 'No reason provided.' }}
                    </div>
                </div>

                <form action="{{ route('admin.returns.update', $return->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs uppercase text-gray-500 font-bold mb-1">Admin Notes</label>
                        <textarea name="admin_notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Internal notes for this return...">{{ $return->admin_notes }}</textarea>
                    </div>
                    
                    @if(!in_array($return->status, ['completed', 'rejected']))
                        <div class="mt-4 flex gap-3">
                            <button type="submit" name="status" value="completed" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex justify-center items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Mark Completed
                            </button>
                            <button type="submit" name="status" value="rejected" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex justify-center items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Reject Return
                            </button>
                        </div>
                    @else
                        <div class="mt-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                Save Notes
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Right Column: Order Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-900 mb-4 border-b pb-2">Order Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs text-gray-500 uppercase">Order ID</label>
                        <a href="{{ route('admin.orders.show', $return->order_id) }}" class="text-indigo-600 hover:underline font-bold">
                            #{{ $return->order->order_number }}
                        </a>
                    </div>
                    
                    <div>
                        <label class="block text-xs text-gray-500 uppercase">Customer</label>
                        <div class="font-medium text-gray-900">{{ $return->order->customer->name ?? 'Guest Customer' }}</div>
                        <div class="text-sm text-gray-500">{{ $return->order->customer->email ?? $return->order->customer_email }}</div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 uppercase">Original Order Date</label>
                        <div class="text-sm text-gray-900">{{ $return->order->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
