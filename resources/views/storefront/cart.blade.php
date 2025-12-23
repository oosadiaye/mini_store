@include('storefront.themes.' . \App\Models\ThemeSetting::getActiveThemeSlug() . '.cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

    @if($cart && $cart->items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    @foreach($cart->items as $item)
                        <div id="cart-item-{{ $item->id }}" class="cart-item-row flex items-center gap-4 p-6 border-b last:border-b-0">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                @if($item->product->primaryImage())
                                    <img src="{{ $item->product->primaryImage()->url }}" alt="{{ $item->product->name }}" class="w-24 h-24 object-cover rounded">
                                @else
                                    <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-3xl">
                                        📦
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1">
                                <a href="{{ route('storefront.product.show', $item->product) }}" class="text-lg font-semibold text-gray-800 hover:text-primary">
                                    {{ $item->product->name }}
                                </a>
                                @if($item->variant)
                                    <p class="text-sm text-gray-600">Variant: {{ $item->variant->name }}</p>
                                @endif
                                <p class="text-primary font-bold mt-1">{{ tenant('currency_symbol') ?? '$' }}{{ number_format($item->price, 2) }}</p>
                            </div>

                            <!-- Quantity -->
                            <div class="flex items-center gap-2">
                                <button id="btn-minus-{{ $item->id }}" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                    class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300 transition disabled:opacity-50"
                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                    -
                                </button>
                                <span id="quantity-{{ $item->id }}" class="w-12 text-center font-semibold">{{ $item->quantity }}</span>
                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                    class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300 transition">
                                    +
                                </button>
                            </div>

                            <!-- Line Total -->
                            <div class="text-right">
                                <p id="line-total-{{ $item->id }}" class="text-lg font-bold text-gray-800">{{ tenant('currency_symbol') ?? '$' }}{{ number_format($item->line_total, 2) }}</p>
                                <button onclick="removeItem({{ $item->id }})" class="text-red-600 hover:text-red-800 text-sm mt-2">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <form action="{{ route('storefront.cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your cart?')">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal ({{ $cart->total_items }} items)</span>
                            <span class="cart-subtotal">{{ tenant('currency_symbol') ?? '$' }}{{ number_format($cart->subtotal, 2) }}</span>
                        </div>
                        
                        @if($cart->coupon)
                            <div class="flex justify-between text-green-600 font-medium bg-green-50 p-2 rounded">
                                <span>Discount ({{ $cart->coupon->code }})</span>
                                <div>
                                    <span>-{{ tenant('currency_symbol') ?? '$' }}{{ number_format($cart->discount_amount, 2) }}</span>
                                    <form action="{{ route('storefront.cart.coupon.remove') }}" method="POST" class="inline ml-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 underline">Remove</button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600">Free</span>
                        </div>
                        
                        <div class="border-t pt-3 flex justify-between text-lg font-bold text-gray-800">
                            <span>Total</span>
                            <span class="cart-total text-primary">{{ tenant('currency_symbol') ?? '$' }}{{ number_format($cart->total, 2) }}</span>
                        </div>

                        <!-- Coupon Input -->
                        @if(!$cart->coupon)
                            <div class="mt-4 pt-4 border-t">
                                <form action="{{ route('storefront.cart.coupon') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="code" placeholder="Coupon Code" class="flex-1 border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary uppercase font-mono">
                                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-black transition">Apply</button>
                                </form>
                                @error('coupon')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <button class="w-full bg-primary text-white py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition mb-3">
                        Proceed to Checkout
                    </button>
                    
                    <a href="{{ route('storefront.products') }}" class="block text-center text-primary hover:underline">
                        Continue Shopping
                    </a>

                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t space-y-2 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <span>🔒</span>
                            <span>Secure checkout</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>↩️</span>
                            <span>30-day returns</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>🚚</span>
                            <span>Free shipping over {{ tenant('currency_symbol') ?? '$' }}50</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="text-8xl mb-6">🛒</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h2>
            <p class="text-gray-600 mb-8">Looks like you haven't added anything to your cart yet</p>
            <a href="{{ route('storefront.products') }}" class="bg-primary text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition inline-block">
                Start Shopping
            </a>
        </div>
    @endif
</div>

<script>
    const csrfToken = '{{ csrf_token() }}';
    const currencySymbol = '{{ tenant('currency_symbol') ?? '$' }}';

    function updateQuantity(itemId, quantity) {
        if (quantity < 1) return;
        
        fetch(`/cart/update/${itemId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update Line Total
                const lineTotalEl = document.getElementById(`line-total-${itemId}`);
                if (lineTotalEl) lineTotalEl.innerText = currencySymbol + data.line_total;

                // Update Cart Totals
                updateCartTotals(data);
                
                // Update Quantity Display
                const quantityEl = document.getElementById(`quantity-${itemId}`);
                if (quantityEl) quantityEl.innerText = quantity;

                // Update Buttons
                const minusBtn = document.getElementById(`btn-minus-${itemId}`);
                if (minusBtn) minusBtn.disabled = quantity <= 1;

                window.showToast('Cart updated');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function removeItem(itemId) {
        if (!confirm('Remove this item from cart?')) return;
        
        fetch(`/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove Row
                const row = document.getElementById(`cart-item-${itemId}`);
                if (row) {
                    row.remove();
                    // Check if cart is empty (simple check: if no rows left, reload or show empty state)
                    // For now, simpler to reload if empty, or just update totals
                    if (document.querySelectorAll('.cart-item-row').length === 0) {
                        location.reload(); 
                    }
                }
                
                updateCartTotals(data);
                window.showToast('Item removed');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateCartTotals(data) {
        // Update Subtotal/Total
        const subtotalEls = document.querySelectorAll('.cart-subtotal');
        subtotalEls.forEach(el => el.innerText = currencySymbol + data.cart_subtotal);
        
        const totalEls = document.querySelectorAll('.cart-total');
        totalEls.forEach(el => el.innerText = currencySymbol + data.cart_total);
        
        // Update Badge
        const badgeEls = document.querySelectorAll('.cart-count-badge');
        badgeEls.forEach(el => {
            el.innerText = data.cart_count;
            if(data.cart_count > 0) el.classList.remove('hidden');
            else el.classList.add('hidden');
        });
    }
</script>
@endsection
