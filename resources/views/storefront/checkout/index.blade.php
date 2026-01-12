<x-storefront.layout :config="$config" :menuCategories="\App\Models\StoreCollection::take(5)->get()">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
            
            <!-- Checkout Form -->
            <section class="lg:col-span-7 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <form action="{{ route('storefront.checkout.store') }}" method="POST">
                    @csrf
                    
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Contact Information</h2>
                    <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                            <input type="email" name="email" id="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[color:var(--brand-color)] focus:border-[color:var(--brand-color)] sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First name</label>
                            <input type="text" name="first_name" id="first_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[color:var(--brand-color)] focus:border-[color:var(--brand-color)] sm:text-sm">
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
                            <input type="text" name="last_name" id="last_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[color:var(--brand-color)] focus:border-[color:var(--brand-color)] sm:text-sm">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address_line_1" class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="address_line_1" id="address_line_1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[color:var(--brand-color)] focus:border-[color:var(--brand-color)] sm:text-sm">
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="city" id="city" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[color:var(--brand-color)] focus:border-[color:var(--brand-color)] sm:text-sm">
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal code</label>
                            <input type="text" name="postal_code" id="postal_code" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[color:var(--brand-color)] focus:border-[color:var(--brand-color)] sm:text-sm">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                            <select id="country" name="country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[color:var(--brand-color)] focus:border-[color:var(--brand-color)] sm:text-sm">
                                <option>United States</option>
                                <option>Canada</option>
                                <option>United Kingdom</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-6">Payment Method</h2>
                        <div class="space-y-4">
                            @if(isset($gateways) && count($gateways) > 0)
                                @foreach($gateways as $index => $gateway)
                                    <div class="relative flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="payment_method_{{ $gateway }}" name="payment_method" type="radio" value="{{ $gateway }}" class="focus:ring-[color:var(--brand-color)] h-4 w-4 text-[color:var(--brand-color)] border-gray-300" {{ $index === 0 ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="payment_method_{{ $gateway }}" class="font-medium text-gray-700">Pay with {{ ucfirst($gateway) }}</label>
                                            <p class="text-gray-500">Secure online payment via {{ ucfirst($gateway) }}.</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="payment_method_cod" name="payment_method" type="radio" value="cod" class="focus:ring-[color:var(--brand-color)] h-4 w-4 text-[color:var(--brand-color)] border-gray-300" {{ (!isset($gateways) || count($gateways) == 0) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="payment_method_cod" class="font-medium text-gray-700">Cash on Delivery</label>
                                    <p class="text-gray-500">Pay with cash upon delivery.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <button type="submit" class="w-full bg-[color:var(--brand-color)] border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[color:var(--brand-color)]">
                            Place Order
                        </button>
                    </div>
                </form>
            </section>

            <!-- Order Summary -->
            <section class="mt-16 lg:mt-0 lg:col-span-5 bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:p-8">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Order Summary</h2>
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($cart->items as $item)
                        <li class="flex py-4">
                            <div class="flex-1 flex flex-col">
                                <div>
                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                        <h3>{{ $item->product->name }}</h3>
                                        <p class="ml-4">{{ number_format($item->line_total, 2) }}</p>
                                    </div>
                                </div>
                                <div class="flex-1 flex items-end justify-between text-sm">
                                    <p class="text-gray-500">Qty {{ $item->quantity }}</p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                <div class="border-t border-gray-200 pt-4 flex items-center justify-between mt-6">
                    <dt class="text-sm text-gray-600">Subtotal</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ number_format($cart->subtotal, 2) }}</dd>
                </div>

                @if($cart->discount_amount > 0)
                    <div class="flex items-center justify-between pt-4">
                        <dt class="flex items-center text-sm text-green-600">
                            <span>Discount</span>
                            @if($cart->coupon)
                                <span class="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">{{ $cart->coupon->code }}</span>
                            @endif
                        </dt>
                        <dd class="text-sm font-medium text-green-600">-{{ number_format($cart->discount_amount, 2) }}</dd>
                    </div>
                @endif

                <div class="flex items-center justify-between pt-4">
                    <dt class="text-sm text-gray-600">Shipping</dt>
                    <dd class="text-sm font-medium text-gray-900">
                        @if($cart->shipping_cost == 0)
                            Free
                        @else
                            {{ number_format($cart->shipping_cost, 2) }}
                        @endif
                    </dd>
                </div>
                
                <div class="border-t border-gray-200 pt-4 flex items-center justify-between mt-6">
                    <dt class="text-base font-bold text-gray-900">Total</dt>
                    <dd class="text-base font-bold text-gray-900">{{ number_format($cart->total, 2) }}</dd>
                </div>
            </section>
        </div>
    </div>
</x-storefront.layout>
