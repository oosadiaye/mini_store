@props(['featuredProducts'])
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" x-data="quickOrderActions()">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Order Form</h2>
    
    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Qty</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($featuredProducts as $product)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($product->primary_image)
                                <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ route('tenant.media', ['path' => $product->primary_image]) }}" alt="">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center mr-3 text-xs">No Img</div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-sm text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $product->slug }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                        ₦{{ number_format($product->price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="number" min="1" value="1" class="w-20 rounded border-gray-300 text-center text-sm" id="qty-{{ $product->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button @click="addToCart({{ $product->id }})" class="text-[color:var(--brand-color)] hover:text-blue-900 font-bold border border-[color:var(--brand-color)] px-3 py-1 rounded hover:bg-blue-50">
                            Add
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function quickOrderActions() {
        return {
            async addToCart(productId) {
                const qtyInput = document.getElementById('qty-' + productId);
                const quantity = qtyInput ? parseInt(qtyInput.value) : 1;

                try {
                    const res = await fetch('{{ route("storefront.cart.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ product_id: productId, quantity: quantity })
                    });
                    
                    if (res.ok) {
                        alert('Added ' + quantity + ' items to cart!');
                    } else {
                        alert('Error adding items');
                    }
                } catch (e) {
                    alert('Network error');
                }
            }
        }
    }
</script>
