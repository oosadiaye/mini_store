@extends('admin.layout')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700">
            &larr; Back to Orders
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Order {{ $order->order_number }}</h2>
    </div>
    <div class="flex space-x-3">
        @if($order->order_source !== 'pos' && $order->status !== 'refunded' && $order->status !== 'cancelled')
            <a href="{{ route('admin.orders.returns.create', $order) }}" class="bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2 rounded-lg transition border border-red-200">
                Request Return
            </a>
        @endif
        <a href="{{ route('orders.invoice', $order) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition border border-gray-300 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Download Invoice
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
    
    <!-- Order Details -->
    <div class="lg:col-span-2 space-y-4 md:space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 font-semibold text-gray-700 text-sm md:text-base">
                Order Items
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs uppercase bg-gray-50 text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3 text-right">Price</th>
                        <th class="px-6 py-3 text-center">Qty</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                            @if($item->variant_name)
                                <div class="text-xs text-gray-500">Variant: {{ $item->variant_name }}</div>
                            @endif
                            <div class="text-xs text-gray-400">SKU: {{ $item->product->sku ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600">${{ number_format($item->price, 2) }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 text-sm">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-gray-600">Subtotal</td>
                        <td class="px-6 py-3 text-right font-medium">${{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-gray-600">Shipping</td>
                        <td class="px-6 py-3 text-right font-medium">${{ number_format($order->shipping, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-gray-600">Tax</td>
                        <td class="px-6 py-3 text-right font-medium">${{ number_format($order->tax, 2) }}</td>
                    </tr>
                    <tr class="border-t border-gray-200">
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900 text-base">Total</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 text-base">${{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($order->returns->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-4 md:mt-6">
            <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 bg-gray-50 font-semibold text-gray-700 text-sm md:text-base">
                Returns
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Refund Amount</th>
                        <th class="px-6 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->returns as $return)
                    <tr>
                        <td class="px-6 py-3">{{ $return->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                {{ ucfirst($return->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">${{ number_format($return->refund_amount, 2) }}</td>
                        <td class="px-6 py-3 text-gray-500 truncate max-w-xs">{{ $return->return_reason ?? $return->admin_notes }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-4 md:space-y-6">
        
        <!-- Status Management -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <h3 class="font-semibold text-gray-800 mb-3 md:mb-4 text-sm md:text-base">Order Status</h3>
            
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="mb-4 md:mb-6">
                @csrf
                @method('PATCH')
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Fulfillment Status</label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <select name="status" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs md:text-sm">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                    <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded text-xs md:text-sm hover:bg-indigo-700 transition">Update</button>
                </div>
            </form>

            <form action="{{ route('admin.orders.update-payment', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <select name="payment_status" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs md:text-sm">
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                    <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded text-xs md:text-sm hover:bg-indigo-700 transition">Update</button>
                </div>
            </form>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <h3 class="font-semibold text-gray-800 mb-3 md:mb-4 text-sm md:text-base">Customer</h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <span class="bg-gray-100 rounded-full p-2 block">👤</span>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</div>
                        <div class="text-sm text-gray-500">{{ $order->customer->email }}</div>
                        <div class="text-sm text-gray-500">{{ $order->customer->phone ?? 'No phone' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <h3 class="font-semibold text-gray-800 mb-3 md:mb-4 text-sm md:text-base">Shipping Address</h3>
            @if($order->shippingAddress)
                <address class="not-italic text-sm text-gray-600 space-y-1">
                    <div class="font-medium text-gray-900">{{ $order->customer->name }}</div>
                    <div>{{ $order->shippingAddress->address_line1 }}</div>
                    @if($order->shippingAddress->address_line2)
                        <div>{{ $order->shippingAddress->address_line2 }}</div>
                    @endif
                    <div>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->postal_code }}</div>
                    <div>{{ $order->shippingAddress->country }}</div>
                </address>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="mb-1 text-xs text-gray-500 uppercase">Payment Method</div>
                    <div class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Tracking Information</h4>
                    @if($order->shippingAddress && $order->shippingAddress->tracking_number)
                        <div class="text-sm">
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-500">Carrier:</span>
                                <span class="font-medium text-gray-900">{{ $order->shippingAddress->carrier }}</span>
                            </div>
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-500">Tracking #:</span>
                                <span class="font-medium text-gray-900">{{ $order->shippingAddress->tracking_number }}</span>
                            </div>
                            @if($order->shippingAddress->shipped_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Shipped:</span>
                                <span class="font-medium text-gray-900">{{ $order->shippingAddress->shipped_at->format('M d, Y') }}</span>
                            </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic mb-3">No tracking added yet.</p>
                    @endif
                    
                    <button onclick="document.getElementById('trackingModal').classList.remove('hidden')" class="w-full mt-2 bg-indigo-50 text-indigo-700 py-2 rounded text-sm font-medium hover:bg-indigo-100 transition">
                        {{ $order->shippingAddress && $order->shippingAddress->tracking_number ? 'Update Tracking' : 'Add Tracking' }}
                    </button>
                </div>
            @else
                <p class="text-sm text-gray-500">No shipping information available.</p>
            @endif
        </div>

    </div>
</div>

<!-- Tracking Modal -->
<div id="trackingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Update Tracking Information</h3>
            <form action="{{ route('admin.orders.tracking', $order->id) }}" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carrier</label>
                    <input type="text" name="carrier" value="{{ $order->shippingAddress->carrier ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="e.g. FedEx, DHL">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                    <input type="text" name="tracking_number" value="{{ $order->shippingAddress->tracking_number ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>

                <div class="mb-4">
                     <label class="block text-sm font-medium text-gray-700 mb-1">Shipped Date</label>
                     <input type="date" name="shipped_at" value="{{ $order->shippingAddress->shipped_at ? $order->shippingAddress->shipped_at->format('Y-m-d') : date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="document.getElementById('trackingModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Save Tracking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
