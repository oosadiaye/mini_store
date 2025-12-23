@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', 'Shopping Cart')

@section('content')

    <div class="bg-gray-100 border-b border-gray-200 mb-8">
        <div class="container-custom py-8">
             <h1 class="font-heading font-bold text-3xl text-electro-dark mb-2">Shopping Cart</h1>
             <div class="flex items-center text-xs text-gray-500 gap-2">
                <a href="{{ route('storefront.home') }}" class="hover:text-electro-blue">Home</a>
                <span>/</span>
                <span>Cart</span>
            </div>
        </div>
    </div>

    <div class="container-custom pb-16">
        @if(\App\Facades\Cart::count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-bold border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Product</th>
                                    <th class="px-6 py-4 text-center">Qty</th>
                                    <th class="px-6 py-4 text-right">Total</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach(\App\Facades\Cart::items() as $item)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-16 h-16 border border-gray-200 rounded p-1 bg-white">
                                                    @if($item->product && $item->product->primary_image)
                                                        <img src="{{ $item->product->primary_image }}" class="w-full h-full object-contain">
                                                    @endif
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-sm text-gray-800 mb-1">
                                                        <a href="{{ route('storefront.product', $item->product) }}" class="hover:text-electro-blue">{{ $item->product->name }}</a>
                                                    </h3>
                                                    <div class="text-xs text-gray-500">Unit: {{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format($item->price, 2) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex items-center border border-gray-300 rounded overflow-hidden">
                                                <button class="px-3 py-1 hover:bg-gray-100 text-gray-600">-</button>
                                                <input type="text" value="{{ $item->quantity }}" class="w-10 text-center text-sm border-none py-1 focus:ring-0">
                                                <button class="px-3 py-1 hover:bg-gray-100 text-gray-600">+</button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-electro-blue">
                                            {{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format($item->total, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-gray-400 hover:text-red-500 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <a href="{{ route('storefront.products.index') }}" class="text-xs font-bold uppercase text-electro-blue hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                            Continue Shopping
                        </a>
                        <button class="text-xs font-bold uppercase text-gray-500 hover:text-red-500 transition">Clear Cart</button>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h4 class="font-heading font-bold text-lg mb-6 text-electro-dark border-b border-gray-200 pb-2">Cart Summary</h4>
                        
                        <div class="space-y-4 mb-6 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-800">{{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ \App\Facades\Cart::total() }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping (Standard)</span>
                                <span class="font-bold text-gray-800">$10.00</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Tax (10%)</span>
                                <span class="font-bold text-gray-800">$12.50</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-4 text-electro-blue">
                                <span>Total</span>
                                <span>{{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format(1437.50, 2) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('storefront.checkout.index', ['theme_slug' => 'electro-retail']) }}" class="block w-full bg-electro-neon text-electro-dark font-heading font-bold uppercase text-center py-4 rounded shadow-lg hover:bg-yellow-400 transition transform hover:-translate-y-0.5">
                            Proceed to Checkout
                        </a>
                        
                        <div class="mt-6 text-center">
                             <p class="text-xs text-gray-400 mb-2">We accept</p>
                             <div class="flex justify-center gap-2 opacity-60">
                                 <div class="w-8 h-5 bg-gray-300 rounded"></div>
                                 <div class="w-8 h-5 bg-gray-300 rounded"></div>
                                 <div class="w-8 h-5 bg-gray-300 rounded"></div>
                             </div>
                        </div>
                    </div>
                      {{-- Coupon --}}
                     <div class="mt-6">
                        <h4 class="text-xs font-bold uppercase text-gray-400 mb-2">Discount Code</h4>
                        <div class="flex">
                            <input type="text" placeholder="Coupon Code" class="flex-1 bg-white border border-gray-300 rounded-l px-3 py-2 text-sm focus:ring-1 focus:ring-electro-blue">
                            <button class="bg-gray-800 text-white px-4 py-2 rounded-r text-xs font-bold uppercase hover:bg-gray-700">Apply</button>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="font-heading font-bold text-2xl text-gray-800 mb-2">Your Cart is Empty</h3>
                <p class="text-gray-500 mb-8">Looks like you haven't added any tech yet.</p>
                <a href="{{ route('storefront.products.index') }}" class="inline-block bg-electro-blue text-white font-heading font-bold uppercase px-8 py-3 rounded shadow hover:bg-blue-600 transition">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>

@endsection
