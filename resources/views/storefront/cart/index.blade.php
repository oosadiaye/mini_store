<x-storefront.layout :config="$config" :menuCategories="\App\Models\StoreCollection::take(5)->get()">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

        @if($cart && $cart->items->count() > 0)
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
                
                <!-- Cart Items List -->
                <section class="lg:col-span-7">
                    <ul role="list" class="border-t border-b border-gray-200 divide-y divide-gray-200">
                        @foreach($cart->items as $item)
                            <li class="flex py-6 sm:py-10">
                                <div class="flex-shrink-0">
                                    @if($item->product->primary_image)
                                        <img src="{{ $item->product->primary_image }}" class="h-24 w-24 rounded-md object-center object-cover sm:h-48 sm:w-48">
                                    @else
                                        <div class="h-24 w-24 bg-gray-100 rounded-md flex items-center justify-center text-gray-400 sm:h-48 sm:w-48">
                                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="ml-4 flex-1 flex flex-col justify-between sm:ml-6">
                                    <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                                        <div>
                                            <div class="flex justify-between">
                                                <h3 class="text-sm">
                                                    <a href="#" class="font-medium text-gray-700 hover:text-gray-800">
                                                        {{ $item->product->name }}
                                                    </a>
                                                </h3>
                                            </div>
                                            <p class="mt-1 text-sm font-medium text-gray-900">{{ number_format($item->price, 2) }}</p>
                                        </div>

                                        <div class="mt-4 sm:mt-0 sm:pr-9">
                                            <form action="{{ route('storefront.cart.update', $item->id) }}" method="POST" class="flex items-center">
                                                @csrf
                                                @method('PATCH')
                                                <label for="quantity-{{ $item->id }}" class="sr-only">Quantity, {{ $item->product->name }}</label>
                                                <select id="quantity-{{ $item->id }}" name="quantity" onchange="this.form.submit()" class="max-w-full rounded-md border-2 border-gray-300 py-1.5 text-base leading-5 font-medium text-gray-700 text-left shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </form>

                                            <div class="absolute top-0 right-0">
                                                <form action="{{ route('storefront.cart.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="-m-2 p-2 inline-flex text-gray-400 hover:text-gray-500">
                                                        <span class="sr-only">Remove</span>
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <!-- Order Summary -->
                <section class="mt-16 bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:p-8 lg:mt-0 lg:col-span-5">
                    <h2 id="summary-heading" class="text-lg font-medium text-gray-900">Order summary</h2>

                    <div class="mt-6 mb-6">
                        @if($cart->coupon)
                            <div class="bg-green-50 border border-green-200 rounded-md p-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-800">Coupon Code: {{ $cart->coupon->code }}</p>
                                    <p class="text-xs text-green-600">{{ $cart->coupon->type == 'fixed' ? 'Fixed amount off' : $cart->coupon->value . '% off' }}</p>
                                </div>
                                <form action="{{ route('storefront.cart.coupon.remove') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('storefront.cart.coupon') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="code" class="block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Discount code">
                                <button type="submit" class="rounded-md bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">Apply</button>
                            </form>
                        @endif
                    </div>

                    <dl class="space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Subtotal</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ number_format($cart->subtotal, 2) }}</dd>
                        </div>
                        
                        @if($cart->discount_amount > 0)
                            <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                <dt class="flex items-center text-sm text-green-600">
                                    <span>Discount</span>
                                </dt>
                                <dd class="text-sm font-medium text-green-600">-{{ number_format($cart->discount_amount, 2) }}</dd>
                            </div>
                        @endif

                        <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                            <dt class="text-base font-medium text-gray-900">Order total</dt>
                            <dd class="text-base font-medium text-gray-900">{{ number_format($cart->total, 2) }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <a href="{{ route('storefront.checkout.index') }}" class="w-full bg-[color:var(--brand-color)] border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-indigo-500 block text-center">
                            Proceed to Checkout
                        </a>
                    </div>
                </section>
            </div>
        @else
            <div class="text-center py-24 bg-gray-50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
                <p class="mt-1 text-sm text-gray-500">Start adding some items to fill it up.</p>
                <div class="mt-6">
                    <a href="{{ route('storefront.home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[color:var(--brand-color)] hover:opacity-90">
                        Continue Shopping
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-storefront.layout>
