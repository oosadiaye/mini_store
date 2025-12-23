@extends('storefront.themes.retail-shop.layout')

@section('pageTitle', 'Checkout')

@section('content')
<div class="bg-white min-h-screen">
    <div class="container mx-auto px-4 md:px-8 py-12">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-12 lg:gap-24">
            
            {{-- Form Section --}}
            <div class="w-full md:w-3/5 order-2 md:order-1">
                <div class="mb-8 md:hidden">
                    <h1 class="text-2xl font-serif">Checkout</h1>
                    <p class="text-sm text-gray-500">Shop / Cart / Checkout</p>
                </div>

                <form action="/checkout" method="POST" id="checkout-form" class="space-y-8">
                    @csrf
                    
                    {{-- Contact --}}
                    <div>
                        <h2 class="text-lg font-bold uppercase tracking-wide border-b border-gray-100 pb-2 mb-4">Contact</h2>
                        <div class="grid grid-cols-1 gap-4">
                            <input type="email" name="email" placeholder="Email Address" required class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4 text-sm" value="{{ old('email', auth('customer')->user()->email ?? '') }}">
                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div>
                        <h2 class="text-lg font-bold uppercase tracking-wide border-b border-gray-100 pb-2 mb-4">Shipping Address</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="first_name" placeholder="First Name" required class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4 text-sm" value="{{ old('first_name', auth('customer')->user()->first_name ?? '') }}">
                            <input type="text" name="last_name" placeholder="Last Name" required class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4 text-sm" value="{{ old('last_name', auth('customer')->user()->last_name ?? '') }}">
                            <input type="text" name="address" placeholder="Address" required class="col-span-2 w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4 text-sm" value="{{ old('address') }}">
                            <input type="text" name="city" placeholder="City" required class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4 text-sm" value="{{ old('city') }}">
                            <input type="text" name="postal_code" placeholder="Postal Code" required class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4 text-sm" value="{{ old('postal_code') }}">
                            <input type="text" name="country" placeholder="Country" required class="col-span-2 w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4 text-sm" value="{{ old('country') }}">
                            <input type="tel" name="phone" placeholder="Phone (Optional)" class="col-span-2 w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4 text-sm" value="{{ old('phone') }}">
                        </div>
                    </div>

                    {{-- Payment --}}
                    <div>
                        <h2 class="text-lg font-bold uppercase tracking-wide border-b border-gray-100 pb-2 mb-4">Payment</h2>
                        <div class="space-y-3">
                            @foreach($paymentTypes as $type)
                            <label class="flex items-center p-4 border border-gray-200 rounded-sm cursor-pointer hover:border-black transition">
                                <input type="radio" name="payment_type_id" value="{{ $type->id }}" required class="text-black focus:ring-black border-gray-300">
                                <span class="ml-3 font-medium">{{ $type->name }}</span>
                            </label>
                            @endforeach
                            
                            @if($paymentTypes->isEmpty())
                                <p class="text-red-500 text-sm">No payment methods available.</p>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-black text-white py-4 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition shadow-lg mt-8">
                        Place Order
                    </button>
                    
                    @if(Session::has('error'))
                        <div class="bg-red-50 text-red-600 p-4 text-sm mt-4 text-center">
                            {{ Session::get('error') }}
                        </div>
                    @endif
                </form>
            </div>

            {{-- Order Summary --}}
            <div class="w-full md:w-2/5 order-1 md:order-2">
                <div class="bg-gray-50 p-8 sticky top-24 rounded-sm">
                    <h3 class="font-serif text-xl mb-6 flex justify-between items-center">
                        Order Summary
                        <span class="text-sm font-sans text-gray-500 font-normal">{{ $cart->total_items }} Items</span>
                    </h3>
                    
                    <div class="space-y-4 mb-8">
                        @foreach($cart->items as $item)
                        <div class="flex items-start space-x-4">
                            <div class="w-16 h-16 bg-white border border-gray-200 flex-shrink-0 overflow-hidden relative">
                                <span class="absolute top-0 right-0 bg-gray-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full -mt-2 -mr-2 z-10">{{ $item->quantity }}</span>
                                <img src="{{ $item->product->image_url }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-sm font-medium">{{ $item->product->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $item->variant->name ?? '' }}</p>
                            </div>
                            <div class="text-sm font-medium">
                                {{ tenant('currency_symbol') ?? '$' }}{{ number_format($item->price * $item->quantity, 2) }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-6 space-y-4 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>{{ tenant('currency_symbol') ?? '$' }}{{ number_format($cart->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>Calculated next</span>
                        </div>
                        <div class="flex justify-between text-gray-900 font-bold text-xl pt-4 border-t border-gray-200">
                            <span>Total</span>
                            <span>{{ tenant('currency_symbol') ?? '$' }}{{ number_format($cart->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
