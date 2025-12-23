@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', 'Checkout')

@section('content')
    
    <div class="bg-gray-100 border-b border-gray-200 mb-8">
        <div class="container-custom py-8">
             <h1 class="font-heading font-bold text-3xl text-electro-dark mb-2">Secure Checkout</h1>
              <div class="flex items-center text-xs text-gray-500 gap-2">
                <a href="{{ route('storefront.cart.index') }}" class="hover:text-electro-blue">Cart</a>
                <span>/</span>
                <span>Checkout</span>
            </div>
        </div>
    </div>

    <div class="container-custom pb-16">
        <form action="#" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            
            {{-- Left Column: Details --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Customer Info --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                    <h3 class="font-heading font-bold text-xl text-electro-dark mb-6 flex items-center gap-2">
                        <span class="bg-electro-blue text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                        Customer Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">First Name</label>
                            <input type="text" name="first_name" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Last Name</label>
                            <input type="text" name="last_name" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                        </div>
                         <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Email Address</label>
                            <input type="email" name="email" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                        </div>
                         <div class="md:col-span-2">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="create_account" class="rounded border-gray-300 text-electro-blue focus:ring-electro-blue">
                                <span class="text-sm text-gray-600">Create an account for faster checkout next time</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                    <h3 class="font-heading font-bold text-xl text-electro-dark mb-6 flex items-center gap-2">
                        <span class="bg-electro-blue text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                        Shipping Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Address</label>
                            <input type="text" name="address" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">City</label>
                            <input type="text" name="city" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Postal Code</label>
                            <input type="text" name="zip" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                        </div>
                         <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Country</label>
                            <select name="country" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                                <option>United States</option>
                                <option>Canada</option>
                                <option>United Kingdom</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Phone</label>
                            <input type="tel" name="phone" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                 <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                    <h3 class="font-heading font-bold text-xl text-electro-dark mb-6 flex items-center gap-2">
                        <span class="bg-electro-blue text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                        Payment Method
                    </h3>
                    <div class="space-y-4">
                         <label class="flex items-center gap-4 p-4 border border-electro-blue bg-blue-50 rounded cursor-pointer transition">
                            <input type="radio" name="payment_method" value="card" checked class="text-electro-blue focus:ring-electro-blue w-5 h-5">
                            <span class="font-bold text-gray-800">Credit / Debit Card</span>
                            <div class="ml-auto flex gap-2">
                                <span class="text-xs bg-white px-2 py-1 border rounded text-gray-500">Visa</span>
                                <span class="text-xs bg-white px-2 py-1 border rounded text-gray-500">MasterCard</span>
                            </div>
                        </label>
                        {{-- Stripe Elements Placeholder --}}
                        <div class="p-4 bg-gray-50 rounded border border-gray-200 ml-8 text-sm text-gray-500">
                            [Stripe Elements Form will appear here securely]
                        </div>

                         <label class="flex items-center gap-4 p-4 border border-gray-200 rounded cursor-pointer hover:border-gray-300 transition">
                            <input type="radio" name="payment_method" value="paypal" class="text-electro-blue focus:ring-electro-blue w-5 h-5">
                            <span class="font-bold text-gray-800">PayPal</span>
                        </label>
                    </div>
                </div>

            </div>

            {{-- Right Column: Summary --}}
            <div class="lg:col-span-1">
                 <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-lg sticky top-24">
                    <h4 class="font-heading font-bold text-lg mb-6 border-b border-gray-100 pb-4">Order Summary</h4>
                    
                    <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach(\App\Facades\Cart::items() as $item)
                            <div class="flex gap-4">
                                <div class="w-12 h-12 border border-gray-200 rounded p-0.5 bg-white flex-shrink-0">
                                     @if($item->product && $item->product->primary_image)
                                        <img src="{{ $item->product->primary_image }}" class="w-full h-full object-contain">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-xs font-bold text-gray-800 truncate">{{ $item->product->name }}</h5>
                                    <div class="text-xs text-gray-500">Qty: {{ $item->quantity }}</div>
                                </div>
                                <div class="text-xs font-bold text-gray-800">
                                    {{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format($item->total, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-3 text-sm mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-bold">{{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ \App\Facades\Cart::total() }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="font-bold">Calculated at next step</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                             <span>Tax</span>
                             <span class="font-bold">$12.50</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between text-xl font-heading font-bold text-electro-blue border-t-2 border-dashed border-gray-200 pt-4 mb-8">
                        <span>Total</span>
                        <span>{{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format(1437.50, 2) }}</span>
                    </div>

                    <button type="submit" class="w-full bg-electro-neon text-electro-dark font-heading font-bold uppercase py-4 rounded hover:bg-yellow-400 transition shadow-lg transform hover:-translate-y-0.5 mb-4">
                        Place Order
                    </button>
                    
                    <div class="text-center">
                        <p class="text-[10px] text-gray-400">By placing this order, you agree to our <a href="#" class="underline hover:text-gray-600">Terms of Service</a> and <a href="#" class="underline hover:text-gray-600">Privacy Policy</a>.</p>
                    </div>

                 </div>
            </div>

        </form>
    </div>

@endsection
