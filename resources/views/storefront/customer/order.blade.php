@extends('storefront.layout')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('storefront.customer.profile') }}" class="text-gray-500 hover:text-indigo-600 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Orders
            </a>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2 flex-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                            @if($order->shippingAddress && $order->shippingAddress->tracking_number)
                                <div class="mt-1 text-sm text-indigo-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tracking: <span class="font-medium ml-1">{{ $order->shippingAddress->tracking_number }}</span>
                                    <span class="text-gray-400 mx-2">|</span>
                                    {{ $order->shippingAddress->carrier }}
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                             <span class="px-3 py-1 rounded-full text-xs font-medium 
                                {{ $order->status === 'completed' || $order->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'cancelled' || $order->status === 'refunded' ? 'bg-red-100 text-red-800' : 
                                   ($order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            
                            @if($order->status === 'delivered')
                                <a href="{{ route('storefront.order.return.create', $order) }}" class="text-xs text-red-600 hover:text-red-800 underline">Request Return</a>
                            @endif
                            
                            <a href="{{ route('storefront.customer.invoice', $order) }}" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Invoice
                            </a>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                        <div class="p-6 flex items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-md flex-shrink-0 overflow-hidden">
                                @if($item->product && $item->product->images->count() > 0)
                                    <img src="{{ $item->product->images->first()->url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">📦</div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="font-medium text-gray-900">{{ $item->product_name }}</h3>
                                @if($item->variant_name)
                                    <p class="text-sm text-gray-500">Variant: {{ $item->variant_name }}</p>
                                @endif
                                <div class="mt-1 flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Qty: {{ $item->quantity }}</span>
                                    <span class="font-medium text-gray-900">${{ number_format($item->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="bg-gray-50 p-6 border-t border-gray-100 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Shipping</span>
                            <span>${{ number_format($order->shipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tax</span>
                            <span>${{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200 mt-2">
                            <span>Total</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="w-full lg:w-1/3 space-y-6">
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Shipping Address</h3>
                    @if($order->shippingAddress)
                        <div class="text-sm text-gray-600">
                            <p class="font-medium text-gray-900 mb-1">{{ $order->customer->name }}</p>
                            <p>{{ $order->shippingAddress->address_line1 }}</p>
                            @if($order->shippingAddress->address_line2)
                                <p>{{ $order->shippingAddress->address_line2 }}</p>
                            @endif
                            <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->postal_code }}</p>
                            <p>{{ $order->shippingAddress->country }}</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No shipping info available</p>
                    @endif
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Payment Information</h3>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Method</span>
                        <span class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="px-2 py-1 rounded text-xs font-medium 
                            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 
                               'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
