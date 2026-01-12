<x-storefront.layout :config="$config">
    <div class="bg-gray-50 min-h-screen py-12"
         x-data="{
            activeImage: '{{ $product->image_url }}',
            quantity: 1,
            loading: false,
            stock: {{ $product->track_inventory ? $product->stock_quantity : 999 }},
            buttonText: 'Add to Cart',
            buttonState: 'idle', // idle, loading, success
            
            async addToCart(productId) {
                // 1. Validation
                if (this.stock > 0 && this.quantity > this.stock) {
                    alert('You cannot add more than we have in stock.');
                    return;
                }

                this.loading = true;
                this.buttonState = 'loading';
                this.buttonText = 'Adding...';

                try {
                    const res = await fetch('{{ route('storefront.cart.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ product_id: productId, quantity: this.quantity })
                    });
                    
                    const data = await res.json();
                    
                    if (res.ok) {
                        // Success Feedback
                        this.buttonState = 'success';
                        this.buttonText = 'Added!';
                        
                        // Update Global Cart Count (if available)
                        if (typeof updateCartCount === 'function') {
                            updateCartCount(data.count);
                        }
                        
                        // Trigger Drawer
                        window.dispatchEvent(new CustomEvent('open-cart-drawer'));

                        // Reset Button after 2 seconds
                        setTimeout(() => {
                            this.buttonState = 'idle';
                            this.buttonText = 'Add to Cart';
                            this.loading = false;
                        }, 2000);

                    } else {
                        alert(data.error || 'Failed to add item');
                        this.loading = false;
                        this.buttonState = 'idle';
                        this.buttonText = 'Add to Cart';
                    }
                } catch (e) {
                    console.error(e);
                    alert('Something went wrong');
                    this.loading = false;
                    this.buttonState = 'idle';
                    this.buttonText = 'Add to Cart';
                }
            }
         }">
        <div class="container mx-auto px-4 max-w-7xl">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('storefront.home', ['tenant' => app('tenant')->slug]) }}" class="hover:text-brand-600">Home</a></li>
                    <li><span class="text-gray-300">/</span></li>
                    <li><a href="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" class="hover:text-brand-600">Shop</a></li>
                    @if($product->category)
                        <li><span class="text-gray-300">/</span></li>
                        <li><a href="{{ route('storefront.category', ['tenant' => app('tenant')->slug, 'slug' => $product->category->slug]) }}" class="hover:text-brand-600">{{ $product->category->name }}</a></li>
                    @endif
                    <li><span class="text-gray-300">/</span></li>
                    <li class="text-gray-900 font-medium truncate max-w-[200px]">{{ $product->name }}</li>
                </ol>
            </nav>

            <div class="lg:grid lg:grid-cols-2 lg:gap-12 xl:gap-16">
                <!-- Left Column: Gallery -->
                <div class="mb-10 lg:mb-0 space-y-4">
                    <!-- Main Image Frame -->
                    <div class="relative bg-white rounded-2xl overflow-hidden aspect-square border border-gray-100 group cursor-zoom-in">
                        <img :src="activeImage" 
                             alt="{{ $product->name }}" 
                             class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 transform group-hover:scale-110 origin-center"
                        >
                        <!-- Badge -->
                        @if($product->isFlashSaleActive())
                            <div class="absolute top-4 left-4">
                                <span class="bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">Flash Sale</span>
                            </div>
                        @elseif($product->price < $product->compare_at_price)
                             <div class="absolute top-4 left-4">
                                <span class="bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">Sale</span>
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnails -->
                    @if($product->images->count() > 0)
                        <div class="flex space-x-4 overflow-x-auto pb-2 scrollbar-hide">
                            <!-- Main fallback thumbnail -->
                            <button @click="activeImage = '{{ $product->image_url }}'" 
                                    class="relative flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-200"
                                    :class="activeImage === '{{ $product->image_url }}' ? 'border-brand-600 ring-2 ring-brand-100' : 'border-transparent hover:border-gray-300'">
                                <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
                            </button>

                            @foreach($product->images as $image)
                                @php
                                    $imgUrl = filter_var($image->image_path, FILTER_VALIDATE_URL) ? $image->image_path : route('tenant.media', ['path' => $image->image_path]);
                                @endphp
                                <button @click="activeImage = '{{ $imgUrl }}'" 
                                        class="relative flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-200"
                                        :class="activeImage === '{{ $imgUrl }}' ? 'border-brand-600 ring-2 ring-brand-100' : 'border-transparent hover:border-gray-300'">
                                    <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right Column: Details -->
                <div class="flex flex-col">
                    <h1 class="text-3xl md:text-4xl font-bold font-heading text-gray-900 mb-2 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Price -->
                    <div class="flex items-baseline gap-4 mb-6 pb-6 border-b border-gray-100">
                        @if($product->isFlashSaleActive())
                            <span class="text-3xl font-bold text-red-600">₦{{ number_format($product->flash_sale_price, 2) }}</span>
                            <span class="text-xl text-gray-400 line-through">₦{{ number_format($product->price, 2) }}</span>
                        @elseif($product->compare_at_price > $product->price)
                            <span class="text-3xl font-bold text-gray-900">₦{{ number_format($product->price, 2) }}</span>
                            <span class="text-xl text-gray-400 line-through">₦{{ number_format($product->compare_at_price, 2) }}</span>
                        @else
                            <span class="text-3xl font-bold text-gray-900">₦{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-8">
                        @if(!$product->track_inventory)
                            <div class="flex items-center gap-2 text-green-600 font-medium">
                                <span class="h-2.5 w-2.5 rounded-full bg-green-600"></span>
                                In Stock
                            </div>
                        @else
                             @if($product->stock_quantity > 10)
                                <div class="flex items-center gap-2 text-green-600 font-medium">
                                    <span class="h-2.5 w-2.5 rounded-full bg-green-600"></span>
                                    In Stock ({{ $product->stock_quantity }})
                                </div>
                             @elseif($product->stock_quantity > 0)
                                <div class="flex items-center gap-2 text-orange-600 font-medium animate-pulse">
                                    <span class="h-2.5 w-2.5 rounded-full bg-orange-600"></span>
                                    Only {{ $product->stock_quantity }} left!
                                </div>
                             @else
                                <div class="flex items-center gap-2 text-red-600 font-bold bg-red-50 px-3 py-1 rounded inline-flex">
                                    <span class="h-2.5 w-2.5 rounded-full bg-red-600"></span>
                                    Sold Out
                                </div>
                             @endif
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <!-- Quantity -->
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden w-fit" x-show="stock > 0">
                            <button @click="quantity > 1 ? quantity-- : null" class="px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors">-</button>
                            <input type="number" x-model="quantity" class="w-16 py-3 text-center border-none focus:ring-0 text-gray-900 font-medium [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-outer-spin-button]:m-0" min="1" :max="stock">
                            <button @click="quantity < stock ? quantity++ : null" class="px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors">+</button>
                        </div>

                        <!-- Add to Cart -->
                         <button @click="addToCart({{ $product->id }})" 
                                class="flex-1 bg-[#0A2540] text-white py-3.5 px-8 rounded-lg font-bold shadow-lg hover:bg-[#1a3a5a] disabled:bg-gray-300 disabled:cursor-not-allowed transition-all transform active:scale-[0.98] flex justify-center items-center gap-2"
                                :class="{'bg-green-600 hover:bg-green-700': buttonState === 'success'}"
                                :disabled="stock <= 0 || loading">
                            
                            <span x-text="buttonText">Add to Cart</span>
                            
                            <!-- Spinner -->
                            <svg x-show="buttonState === 'loading'" class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <!-- Checkmark -->
                            <svg x-show="buttonState === 'success'" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Description -->
                    <div class="prose prose-sm text-gray-600 max-w-none">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-2">Description</h3>
                        <div class="leading-relaxed">
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
                <div class="mt-24 border-t border-gray-100 pt-16">
                    <h2 class="text-2xl font-bold font-heading text-gray-900 mb-8">You might also like</h2>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $related)
                            <x-storefront.product-card :product="$related" />
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-storefront.layout>
