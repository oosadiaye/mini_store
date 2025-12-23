@extends('storefront.themes.modern-minimal.layout')

@section('pageTitle', 'Your Cart')

@section('content')
<div class="bg-gray-50 min-h-[60vh] py-16" x-data="{
    removeItem(itemId) {
        if(!confirm('Remove this item?')) return;
        fetch('/cart/' + itemId, { 
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        }).then(() => window.location.reload());
    },
    updateQuantity(itemId, qty) {
        if(qty < 1) return;
        fetch('/cart/' + itemId, {
            method: 'PATCH',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
            },
            body: JSON.stringify({ quantity: qty })
        }).then(() => window.location.reload());
    }
}">
    <div class="container mx-auto px-4 max-w-6xl">
        <h1 class="text-3xl md:text-4xl font-serif font-medium mb-12 text-center">Shopping Cart</h1>

        @if(!$cart || $cart->items->isEmpty())
            <div class="text-center py-20 bg-white rounded-sm shadow-sm">
                <p class="text-gray-500 text-lg mb-6">Your cart is empty.</p>
                <a href="/shop" class="inline-block bg-black text-white px-8 py-3 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition">
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="flex flex-col lg:flex-row gap-12">
                {{-- Cart Items --}}
                <div class="flex-grow bg-white p-6 md:p-8 rounded-sm shadow-sm">
                    <div class="hidden md:flex text-xs font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100 pb-4 mb-6">
                        <div class="w-1/2">Product</div>
                        <div class="w-1/4 text-center">Quantity</div>
                        <div class="w-1/4 text-right">Total</div>
                    </div>

                    <ul class="divide-y divide-gray-100">
                        @foreach($cart->items as $item)
                            <li class="flex flex-col md:flex-row items-center py-6 gap-4 md:gap-0">
                                <div class="w-full md:w-1/2 flex items-center space-x-4">
                                    <div class="w-20 h-24 bg-gray-100 flex-shrink-0">
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900"><a href="/products/{{$item->product->id}}">{{ $item->product->name }}</a></h3>
                                        @if($item->variant)
                                            <p class="text-xs text-gray-500 mt-1">{{ $item->variant->name }}</p>
                                        @endif
                                        <button @click="removeItem('{{ $item->id }}')" class="text-xs text-red-500 mt-2 hover:underline">Remove</button>
                                    </div>
                                </div>
                                <div class="w-full md:w-1/4 flex justify-between md:justify-center items-center">
                                    <span class="md:hidden text-xs font-bold uppercase text-gray-400">Qty:</span>
                                    <div class="flex items-center border border-gray-200">
                                        <button @click="updateQuantity('{{$item->id}}', {{ $item->quantity - 1 }})" class="w-8 h-8 flex items-center justify-center hover:bg-gray-50">-</button>
                                        <span class="w-8 text-center text-sm">{{ $item->quantity }}</span>
                                        <button @click="updateQuantity('{{$item->id}}', {{ $item->quantity + 1 }})" class="w-8 h-8 flex items-center justify-center hover:bg-gray-50">+</button>
                                    </div>
                                </div>
                                <div class="w-full md:w-1/4 text-right">
                                    <span class="font-medium">{{ tenant('currency_symbol') ?? '$' }}{{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Summary --}}
                <div class="w-full lg:w-96">
                    <div class="bg-gray-100 p-8 rounded-sm sticky top-24">
                        <h3 class="font-serif text-xl mb-6">Order Summary</h3>
                        
                        <div class="space-y-4 mb-8 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>{{ tenant('currency_symbol') ?? '$' }}{{ number_format($cart->subtotal, 2) }}</span>
                            </div>
                            @if($cart->discount_amount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Discount</span>
                                    <span>-{{ tenant('currency_symbol') ?? '$' }}{{ number_format($cart->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-gray-900 font-bold text-lg pt-4 border-t border-gray-200">
                                <span>Total</span>
                                <span>{{ tenant('currency_symbol') ?? '$' }}{{ number_format($cart->total, 2) }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">Shipping & taxes calculated at checkout.</p>
                        </div>

                        <a href="/checkout" class="block w-full bg-black text-white text-center py-4 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition">
                            Checkout
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
