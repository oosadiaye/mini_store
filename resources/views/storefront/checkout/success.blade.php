@extends('storefront.layout')

@section('content')
<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center">
            <div class="mb-6 flex justify-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Thank you for your order!</h1>
            <p class="text-gray-600 mb-8">Your order has been placed successfully.</p>

            <div class="border-t border-b border-gray-100 py-6 mb-8 text-left">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Order Number:</span>
                    <span class="font-bold text-gray-900">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Date:</span>
                    <span class="text-gray-900">{{ $order->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-bold text-primary">{{ tenant()->data['currency_symbol'] ?? '₦' }}{{ number_format($order->total, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Status:</span>
                    <span class="font-medium {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }} uppercase text-sm px-2 py-0.5 rounded bg-gray-50 border border-gray-100">
                        {{ $order->payment_status }}
                    </span>
                </div>
            </div>

            @if(session('warning'))
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded p-4 mb-8 text-sm text-left">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded p-4 mb-8 text-sm text-left">
                    {{ session('success') }}
                </div>
            @endif

            <p class="text-gray-600 mb-8">We've sent a confirmation email to <span class="font-medium text-gray-900">{{ $order->customer->email }}</span>.</p>

            <div class="flex justify-center space-x-4">
                <a href="{{ route('storefront.home') }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition font-medium">
                    Return to Home
                </a>
                <a href="{{ route('storefront.products.index') }}" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
