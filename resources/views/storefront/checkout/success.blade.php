<x-storefront.layout :menuCategories="\App\Models\StoreCollection::take(5)->get()">
    <div class="max-w-2xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-100 text-center">
            
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Thank you!</h1>
            <p class="text-lg text-gray-500 mb-8">Your order <span class="font-mono font-bold text-gray-800">#{{ $order->order_number }}</span> has been received.</p>

            <div class="bg-gray-50 rounded-lg p-6 text-left mb-8">
                <h3 class="text-sm font-medium text-gray-900 uppercase tracking-wide mb-4">Order Details</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Shipping Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($order->shippingAddress)
                                {{ $order->shippingAddress->address_line1 }}<br>
                                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->postal_code }}<br>
                                {{ $order->shippingAddress->country }}
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                        <dd class="mt-1 text-lg font-bold text-[#0A2540]">
                            {{ number_format($order->total, 2) }}
                        </dd>
                    </div>
                </dl>
            </div>

            <p class="text-sm text-gray-500 mb-8">We've sent a confirmation email to <strong>{{ $order->customer->email }}</strong>.</p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#0A2540] hover:bg-gray-800 transition-colors">
                    Continue Shopping
                </a>
                <a href="{{ route('storefront.home', ['tenant' => app('tenant')->slug]) }}" class="inline-flex justify-center items-center px-6 py-3 border-2 border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</x-storefront.layout>
