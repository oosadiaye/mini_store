<x-storefront.layout :config="\App\Models\StoreConfig::first()" :menuCategories="\App\Models\StoreCollection::take(5)->get()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <div class="mb-8">
            <a href="{{ route('storefront.orders.track') }}" class="text-sm text-gray-500 hover:text-gray-900 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Track Another Order
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-6 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
                    <p class="text-sm text-gray-500 mt-1">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div class="flex items-center">
                     <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                           ($order->status === 'shipped' ? 'bg-indigo-100 text-indigo-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <!-- Progress Bar (Simple Implementation) -->
            <div class="px-6 py-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-between">
                        @php
                            $steps = ['pending', 'processing', 'shipped', 'delivered'];
                            $currentStepIndex = array_search(in_array($order->status, $steps) ? $order->status : 'pending', $steps);
                            if ($order->status == 'completed') $currentStepIndex = 3; 
                        @endphp
                        
                        @foreach($steps as $index => $step)
                            <div>
                                <div class="relative flex h-8 w-8 items-center justify-center rounded-full {{ $index <= $currentStepIndex ? 'bg-[#0A2540] hover:bg-[#0A2540]' : 'bg-gray-200' }}">
                                    @if($index < $currentStepIndex)
                                        <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <span class="text-xs font-bold {{ $index <= $currentStepIndex ? 'text-white' : 'text-gray-500' }}">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs font-medium text-center {{ $index <= $currentStepIndex ? 'text-[#0A2540]' : 'text-gray-500' }}">{{ ucfirst($step) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="px-6 py-6 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Shipping Info -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Information</h3>
                    @if($order->shippingAddress)
                        <address class="not-italic text-sm text-gray-600">
                            {{ $order->shippingAddress->address_line1 }}<br>
                            @if($order->shippingAddress->address_line2)
                                {{ $order->shippingAddress->address_line2 }}<br>
                            @endif
                            {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->postal_code }}<br>
                            {{ $order->shippingAddress->country }}
                        </address>
                        
                        @if($order->shippingAddress->tracking_number)
                            <div class="mt-4 p-4 bg-gray-50 rounded-md border border-gray-100">
                                <p class="text-sm font-medium text-gray-900">Tracking Number:</p>
                                <p class="text-lg font-mono text-indigo-600">{{ $order->shippingAddress->tracking_number }}</p>
                                <p class="text-xs text-gray-500 mt-1">Carrier: {{ $order->shippingAddress->carrier }}</p>
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-gray-500">No shipping address recorded.</p>
                    @endif
                </div>

                <!-- Order Summary -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                    <div class="flow-root">
                        <ul role="list" class="-my-4 divide-y divide-gray-200">
                            @foreach($order->items as $item)
                            <li class="flex py-4">
                                <div class="ml-0 flex-1 flex flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>{{ $item->product_name }}</h3>
                                            <p class="ml-4">{{ $config->currency_symbol ?? '$' }}{{ number_format($item->total, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <p class="text-gray-500">Qty {{ $item->quantity }}</p>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="border-t border-gray-200 mt-6 pt-4">
                        <div class="flex justify-between text-base font-medium text-gray-900">
                            <p>Total</p>
                            <p>{{ $config->currency_symbol ?? '$' }}{{ number_format($order->total, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-storefront.layout>
