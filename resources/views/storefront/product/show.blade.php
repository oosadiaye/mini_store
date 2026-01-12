<x-storefront.layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ 
        quantity: 1,
        activeImage: '{{ $product->image_url }}',
        loading: false,
        activeTab: 'description',
        
        addToCart() {
            this.loading = true;
            // Logic handled by cartActions() in layout, but we need to call it if available
            // Since we are inside x-data, we can't easily access the parent scope's cartActions unless we use $dispatch
            // or if cartActions is global.
            // The layout exposes cartActions globally window.cartActions? No, it's an Alpine component.
            // We'll trust the global event bus or just implementing a simple fetch as layout might not be exposing it directly to children scopes.
            // Let's rely on a direct fetch here similar to product-card.
            
            fetch('/{{ request()->route('tenant') }}/storefront/cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }},
                    quantity: this.quantity
                })
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                // Show toast via global event
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Added to cart!', type: 'success' } }));
            })
            .catch(err => {
                this.loading = false;
                alert('Failed to add to cart');
            });
        }
    }">

        <!-- Breadcrumbs -->
        <nav class="flex items-center text-sm text-gray-500 mb-8 overflow-x-auto whitespace-nowrap">
            <a href="{{ route('storefront.home', ['tenant' => request()->route('tenant')]) }}" class="hover:text-[#0A2540]">Home</a>
            @if($product->category)
                <span class="mx-2">/</span>
                <a href="{{ route('storefront.category.show', ['tenant' => request()->route('tenant'), 'slug' => $product->category->slug]) }}" class="hover:text-[#0A2540]">{{ $product->category->name }}</a>
            @endif
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">{{ $product->name }}</span>
        </nav>

        <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-start">
            <!-- Image Gallery -->
            <div class="flex flex-col-reverse lg:flex-row gap-4">
                <!-- Thumbnails (Hidden on mobile if only 1 image) -->
                @if($product->images->count() > 0)
                    <div class="flex lg:flex-col gap-4 overflow-x-auto lg:overflow-y-auto lg:w-24 lg:h-[500px] scrollbar-hide py-2 lg:py-0">
                        <!-- Main Image thumbnail -->
                        @if($product->image_url)
                            <button @click="activeImage = '{{ $product->image_url }}'" 
                                    class="relative h-20 w-20 flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all hover:opacity-100"
                                    :class="activeImage === '{{ $product->image_url }}' ? 'border-[#0A2540] opacity-100' : 'border-transparent opacity-60'">
                                <img src="{{ $product->image_url }}" class="h-full w-full object-cover">
                            </button>
                        @endif
                        
                        @foreach($product->images as $image)
                            <button @click="activeImage = '{{ $image->url }}'" 
                                    class="relative h-20 w-20 flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all hover:opacity-100"
                                    :class="activeImage === '{{ $image->url }}' ? 'border-[#0A2540] opacity-100' : 'border-transparent opacity-60'">
                                <img src="{{ $image->url }}" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
                
                <!-- Main Image -->
                <div class="w-full relative aspect-[4/5] rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 shadow-sm">
                     <img :src="activeImage" class="w-full h-full object-cover object-center transition-opacity duration-300">
                     
                     <!-- Badges -->
                     @if($product->is_flash_sale && $product->isFlashSaleActive())
                        <div class="absolute top-4 left-4">
                             <span class="bg-red-500 text-white text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-lg">
                                ⚡ Flash Sale
                             </span>
                        </div>
                     @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="mt-10 lg:mt-0">
                <h1 class="text-3xl sm:text-4xl font-bold text-[#0A2540] tracking-tight mb-4">{{ $product->name }}</h1>
                
                <div class="flex items-center gap-4 mb-6">
                    @if($product->is_flash_sale && $product->isFlashSaleActive())
                        <p class="text-3xl font-bold text-red-500">
                            ₦{{ number_format($product->flash_sale_price, 2) }}
                        </p>
                        <p class="text-xl text-gray-400 line-through">
                            ₦{{ number_format($product->price, 2) }}
                        </p>
                    @elseif($product->compare_at_price)
                        <p class="text-3xl font-bold text-[#0A2540]">
                            ₦{{ number_format($product->price, 2) }}
                        </p>
                        <p class="text-xl text-gray-400 line-through">
                            ₦{{ number_format($product->compare_at_price, 2) }}
                        </p>
                    @else
                        <p class="text-3xl font-bold text-[#0A2540]">
                            ₦{{ number_format($product->price, 2) }}
                        </p>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="flex items-center gap-2 mb-8 text-sm">
                    @if($product->track_inventory)
                        @if($product->stock_quantity > 0)
                            <div class="h-2.5 w-2.5 rounded-full bg-green-500"></div>
                            <span class="text-green-700 font-medium">In Stock ({{ $product->stock_quantity }} available)</span>
                        @else
                            <div class="h-2.5 w-2.5 rounded-full bg-red-500"></div>
                            <span class="text-red-700 font-medium">Out of Stock</span>
                        @endif
                    @else
                        <div class="h-2.5 w-2.5 rounded-full bg-green-500"></div>
                        <span class="text-green-700 font-medium">In Stock</span>
                    @endif
                </div>

                <div class="border-t border-gray-100 py-6">
                     <!-- Quantity & Add to Cart -->
                     <div class="flex items-center gap-4">
                         <div class="flex items-center border border-gray-200 rounded-xl">
                             <button @click="quantity > 1 ? quantity-- : null" class="p-3 text-gray-500 hover:text-[#0A2540] disabled:opacity-50" :disabled="quantity <= 1">
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                             </button>
                             <span class="w-12 text-center font-bold text-[#0A2540]" x-text="quantity"></span>
                             <button @click="quantity++" class="p-3 text-gray-500 hover:text-[#0A2540]">
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                             </button>
                         </div>

                         <button @click="addToCart" 
                                 class="flex-1 bg-[#0A2540] text-white py-3.5 px-8 rounded-xl font-bold shadow-lg hover:bg-[#1a3a5a] hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                 :disabled="loading || ({{ $product->track_inventory }} && {{ $product->stock_quantity }} <= 0)">
                             <span x-show="!loading">Add to Cart</span>
                             <span x-show="loading" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></span>
                         </button>
                     </div>
                </div>

                <!-- Tabs (Description / Details) -->
                <div class="mt-8">
                    <div class="flex border-b border-gray-100">
                        <button class="pb-3 px-4 font-medium text-sm transition-colors border-b-2"
                                :class="activeTab === 'description' ? 'border-[#0A2540] text-[#0A2540]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                                @click="activeTab = 'description'">
                            Description
                        </button>
                        <button class="pb-3 px-4 font-medium text-sm transition-colors border-b-2"
                                :class="activeTab === 'details' ? 'border-[#0A2540] text-[#0A2540]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                                @click="activeTab = 'details'">
                            Additional Information
                        </button>
                    </div>

                    <div class="py-6">
                        <div x-show="activeTab === 'description'" x-transition:enter.opacity.duration.300ms class="prose prose-sm prose-blue text-gray-600 max-w-none">
                            {!! $product->description ?? '<p>No description available.</p>' !!}
                        </div>
                        <div x-show="activeTab === 'details'" x-transition:enter.opacity.duration.300ms class="text-sm text-gray-600">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                @if($product->sku)
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">SKU</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->sku }}</dd>
                                    </div>
                                @endif
                                @if($product->weight)
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Weight</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->weight }} kg</dd>
                                    </div>
                                @endif
                                @if($product->brand)
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Brand</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $product->brand->name }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-storefront.layout>
