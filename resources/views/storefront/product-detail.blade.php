@extends('storefront.layout')

@section('content')
@php
    // Prepare variants for Alpine.js
    $variantsJson = $product->variants->map(function($v) {
        return [
            'id' => $v->id,
            'price' => $v->price,
            'stock' => $v->stock_quantity,
            'sku' => $v->sku,
            'attributes' => $v->attributes, // ['Color' => 'Red', 'Size' => 'M']
        ];
    });
    
    // Extract unique attributes for selectors
    $attributes = [];
    foreach($product->variants as $variant) {
        if($variant->attributes) {
            foreach($variant->attributes as $key => $val) {
                $attributes[$key][] = $val;
            }
        }
    }
    foreach($attributes as $key => $vals) {
        $attributes[$key] = array_unique($vals);
    }

    $avgRating = $product->reviews->where('status', 'approved')->avg('rating') ?? 0;
    $reviewCount = $product->reviews->where('status', 'approved')->count();

    $productImages = $product->images->count() > 0 
        ? $product->images->pluck('url')->toArray() 
        : [$product->image_url];
@endphp

<div class="bg-white min-h-screen pb-20" 
     x-data="productPage()"
     x-init="initVariants({{ $variantsJson }}, {{ json_encode($attributes) }}); initImages({{ json_encode($productImages) }})">

    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b border-gray-100 mb-8">
        <div class="container mx-auto px-4 py-4 max-w-7xl">
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('storefront.home') }}" class="hover:text-primary transition">Home</a>
                <svg class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('storefront.products') }}" class="hover:text-primary transition">Products</a>
                <svg class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-900 font-medium truncate">{{ $product->name }}</span>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 xl:gap-20">
            <!-- Left: Image Gallery -->
            <div class="space-y-4">
                <!-- Main Image Stage -->
                <div class="relative bg-white rounded-3xl overflow-hidden aspect-square group border border-gray-200 shadow-2xl"
                     @mousemove="zoomImage($event)" @mouseleave="resetZoom()">
                    
                    <img :src="activeImage" 
                         class="w-full h-full object-cover transition-transform duration-200 origin-center"
                         :style="zoomStyle"
                         alt="{{ $product->name }}">
                        
                    <!-- Zoom Hint -->
                    <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-full text-xs font-medium text-gray-600 shadow-sm opacity-0 group-hover:opacity-100 transition duration-300 pointer-events-none flex items-center gap-1 z-10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Hover to Zoom
                    </div>

                    <!-- Slider Controls -->
                    <div class="absolute inset-0 flex items-center justify-between p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        <!-- Prev Button -->
                        <button @click.prevent.stop="prevImage()" 
                                class="pointer-events-auto bg-white/80 backdrop-blur hover:bg-white text-gray-800 p-2 rounded-full shadow-lg transform transition hover:scale-110 focus:outline-none"
                                x-show="images.length > 1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        
                        <!-- Next Button -->
                        <button @click.prevent.stop="nextImage()" 
                                class="pointer-events-auto bg-white/80 backdrop-blur hover:bg-white text-gray-800 p-2 rounded-full shadow-lg transform transition hover:scale-110 focus:outline-none"
                                x-show="images.length > 1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>

                    <!-- Image Counter -->
                    <div class="absolute top-4 right-4 bg-black/50 text-white text-xs px-2 py-1 rounded-full backdrop-blur-sm pointer-events-none" x-show="images.length > 1">
                        <span x-text="activeImageIndex + 1"></span> / <span x-text="images.length"></span>
                    </div>
                </div>

                <!-- Thumbnails -->
                <div class="grid grid-cols-4 sm:grid-cols-5 gap-3" x-show="images.length > 1">
                    <template x-for="(image, index) in images" :key="index">
                        <button @click="setImage(index)" 
                                class="relative aspect-square rounded-xl overflow-hidden border-2 transition shadow-md"
                                :class="activeImageIndex === index ? 'border-primary ring-2 ring-primary/20' : 'border-transparent ring-1 ring-gray-100 hover:border-gray-300'">
                            <img :src="image" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Right: Product Info -->
            <div class="flex flex-col">
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 tracking-tight leading-tight mb-4">{{ $product->name }}</h1>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex text-yellow-500 text-lg">
                        @for($i=1; $i<=5; $i++)
                            <span class="{{ $i <= round($avgRating) ? 'text-yellow-500' : 'text-gray-300' }}">★</span>
                        @endfor
                    </div>
                    <span class="text-sm text-gray-500 font-medium">({{ $reviewCount }} Reviews)</span>
                </div>

                <div class="flex items-center gap-4 mb-8">
                    <span class="text-3xl font-bold text-primary" x-text="'{{ tenant('data')['currency_symbol'] ?? '₦' }}' + currentPrice"></span>
                    @if($product->compare_at_price > $product->price)
                        <span class="text-xl text-gray-400 line-through">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($product->compare_at_price, 2) }}</span>
                        <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Sale</span>
                    @endif
                </div>

                <!-- Variants -->
                <div class="space-y-6 mb-8 border-t border-gray-100 pt-6" x-show="hasVariants">
                    <template x-for="(vals, key) in availableAttributes" :key="key">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-3" x-text="key"></label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="val in vals" :key="val">
                                    <button @click="selectAttribute(key, val)"
                                            class="px-4 py-2 text-sm border rounded-lg transition-all duration-200"
                                            :class="selectedAttributes[key] === val 
                                                ? 'border-primary bg-primary text-white shadow-md' 
                                                : 'border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50'">
                                        <span x-text="val"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Short Description -->
                <div class="prose prose-sm text-gray-600 mb-8 leading-relaxed">
                    {{ $product->short_description }}
                </div>

                <!-- Actions -->
                <div class="mt-auto pt-6 border-t border-gray-100">
                    <form @submit.prevent="addToCart" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex items-center border border-gray-300 rounded-xl px-4 py-3 bg-white sm:w-32 hover:border-primary transition group">
                            <span class="text-xs font-bold text-gray-500 mr-3 uppercase tracking-wider group-focus-within:text-primary">Qty</span>
                            <input type="number" x-model="quantity" min="1" :max="currentStock" 
                                   class="w-full bg-transparent border-none text-center font-bold text-gray-900 focus:ring-0 p-0 text-lg">
                        </div>
                        
                        <button type="submit" 
                                :disabled="!canAddToCart"
                                class="flex-1 bg-primary text-white rounded-xl px-8 py-4 font-bold text-lg shadow-lg shadow-primary/30 hover:shadow-xl hover:translate-y-[-2px] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                            <span x-text="addToCartText"></span>
                        </button>
                    </form>
                    <div class="mt-4 flex items-center gap-2 text-sm" :class="isLowStock ? 'text-orange-600' : 'text-green-600'">
                        <div class="w-2 h-2 rounded-full" :class="isLowStock ? 'bg-orange-600' : 'bg-green-600'"></div>
                        <span x-text="stockMessage"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description & Reviews Tabs -->
        <div class="mt-20" x-data="{ activeTab: 'description' }">
            <div class="flex border-b border-gray-200">
                <button @click="activeTab = 'description'" 
                        class="px-8 py-4 font-medium text-lg border-b-2 transition"
                        :class="activeTab === 'description' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'">
                    Description
                </button>
                <button @click="activeTab = 'reviews'" 
                        class="px-8 py-4 font-medium text-lg border-b-2 transition"
                        :class="activeTab === 'reviews' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'">
                    Reviews ({{ $reviewCount }})
                </button>
            </div>

            <div class="py-12">
                <!-- Description Tab -->
                <div x-show="activeTab === 'description'" x-cloak class="prose max-w-none text-gray-600 leading-loose">
                    {!! nl2br(e($product->description)) !!}
                    
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-gray-50 rounded-2xl">
                         <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">SKU</span>
                            <span class="text-lg font-medium text-gray-900">{{ $product->sku }}</span>
                         </div>
                         @if($product->category)
                         <div>
                            <span class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Category</span>
                            <span class="text-lg font-medium text-gray-900">{{ $product->category->name }}</span>
                         </div>
                         @endif
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div x-show="activeTab === 'reviews'" x-cloak>
                    <!-- Review Form -->
                    <div class="mb-10 bg-gray-50 rounded-2xl p-6 border border-gray-100">
                        @auth('customer')
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Write a Review</h3>
                            <form action="{{ route('storefront.product.reviews.store', $product) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                    <div class="flex gap-4">
                                        @for($i=1; $i<=5; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" required>
                                            <span class="text-2xl text-gray-300 peer-checked:text-yellow-500 hover:text-yellow-400 transition">★</span>
                                        </label>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
                                    <textarea name="comment" id="comment" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring-primary" placeholder="Share your thoughts..." required></textarea>
                                </div>
                                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-xl font-bold hover:shadow-lg hover:shadow-primary/30 transition">Submit Review</button>
                            </form>
                        @else
                            <div class="text-center py-4">
                                <p class="text-gray-600">Please <a href="{{ route('storefront.login') }}" class="text-primary font-bold hover:underline">login</a> to write a review.</p>
                            </div>
                        @endauth
                    </div>

                    @if($product->reviews->where('status', 'approved')->count() > 0)
                        <div class="space-y-8">
                            @foreach($product->reviews->where('status', 'approved') as $review)
                                <div class="border-b border-gray-100 pb-8 last:border-0">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center font-bold text-gray-500">
                                                {{ substr($review->name ?? 'Guest', 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900">{{ $review->title }}</h4>
                                                <div class="flex text-xs text-yellow-500">
                                                    @for($i=1; $i<=5; $i++)
                                                        <span class="{{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-200' }}">★</span>
                                                    @endfor
                                                    <span class="ml-2 text-gray-400 text-xs font-medium">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 leading-relaxed">{{ $review->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <p class="text-gray-500 mb-4">No reviews yet. Be the first to share your thoughts!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="border-t border-gray-200 pt-16 mt-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">You Might Also Like</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($relatedProducts as $related)
                        <div class="group">
                            <div class="relative overflow-hidden rounded-2xl aspect-[4/5] mb-4 bg-gray-100">
                                <a href="{{ route('storefront.product.show', $related) }}">
                                    @if($related->primaryImage())
                                        <img src="{{ $related->primaryImage()->url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl">📦</div>
                                    @endif
                                    @if($related->compare_at_price > $related->price)
                                        <span class="absolute top-3 left-3 bg-white text-black text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">SALE</span>
                                    @endif
                                </a>
                                <!-- Quick Add (Optional) -->
                                <button class="absolute bottom-4 right-4 bg-white text-black p-3 rounded-full shadow-lg translate-y-full opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition duration-300 hover:bg-primary hover:text-white"
                                        onclick="window.location.href='{{ route('storefront.product.show', $related) }}'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                            <h3 class="font-semibold text-lg text-gray-900 group-hover:text-primary transition">
                                <a href="{{ route('storefront.product.show', $related) }}">{{ $related->name }}</a>
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="font-bold text-gray-900">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($related->price, 2) }}</span>
                                @if($related->compare_at_price > $related->price)
                                    <span class="text-sm text-gray-400 line-through">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($related->compare_at_price, 2) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function productPage() {
    return {
        activeImageIndex: 0,
        images: [],
        zoomStyle: 'transform: scale(1)',
        variants: [],
        availableAttributes: {},
        selectedAttributes: {},
        currentPrice: {{ $product->price }},
        currentStock: {{ $product->stock_quantity }},
        selectedVariantId: null,
        quantity: 1,

        get activeImage() {
            if (this.images.length === 0) return '';
            return this.images[this.activeImageIndex];
        },
        
        get hasVariants() {
            return this.variants.length > 0;
        },

        initImages(files) {
            this.images = files;
        },

        nextImage() {
            this.activeImageIndex = (this.activeImageIndex + 1) % this.images.length;
            this.resetZoom();
        },

        prevImage() {
            this.activeImageIndex = (this.activeImageIndex - 1 + this.images.length) % this.images.length;
            this.resetZoom();
        },

        setImage(index) {
            this.activeImageIndex = index;
            this.resetZoom();
        },

        get isLowStock() {
            return this.currentStock > 0 && this.currentStock < {{ $product->low_stock_threshold ?? 10 }};
        },

        get canAddToCart() {
            if (this.hasVariants && !this.selectedVariantId) return false;
            return this.currentStock > 0;
        },

        get addToCartText() {
            if (this.currentStock <= 0) return 'Out of Stock';
            if (this.hasVariants && !this.selectedVariantId) return 'Select Options';
            return 'Add to Cart';
        },

        get stockMessage() {
            if (this.currentStock <= 0) return 'Out of Stock';
            if (this.isLowStock) return `Only ${this.currentStock} left!`;
            return 'In Stock';
        },

        initVariants(variants, attributes) {
            this.variants = variants;
            this.availableAttributes = attributes; // { "Color": ["Red", "Blue"], "Size": ["S", "M"] }
            
            // Pre-select first attribute values if only one option exists
            for (const [key, values] of Object.entries(this.availableAttributes)) {
                if (values instanceof Array && values.length === 1) {
                    this.selectAttribute(key, values[0]);
                }
            }
        },

        selectAttribute(key, value) {
            this.selectedAttributes[key] = value;
            this.checkVariantMatch();
        },

        checkVariantMatch() {
            // Check if we have selections for all required attributes
            const requiredKeys = Object.keys(this.availableAttributes);
            const selectedKeys = Object.keys(this.selectedAttributes);
            
            if (requiredKeys.length !== selectedKeys.length) return;

            // Find matching variant
            const match = this.variants.find(v => {
                // v.attributes is {Color: Red, Size: M}
                // Check if all selected attributes match this variant
                return Object.entries(this.selectedAttributes).every(([k, val]) => v.attributes[k] === val);
            });

            if (match) {
                this.selectedVariantId = match.id;
                this.currentPrice = match.price;
                this.currentStock = match.stock;
            } else {
                this.selectedVariantId = null;
                // Optional: Reset price/stock or show "Unavailable"
                // For now keep default base price
            }
        },

        zoomImage(e) {
            const el = e.currentTarget;
            const { left, top, width, height } = el.getBoundingClientRect();
            const x = (e.clientX - left) / width;
            const y = (e.clientY - top) / height;
            
            // Move origin
            el.querySelector('img').style.transformOrigin = `${x * 100}% ${y * 100}%`;
            this.zoomStyle = 'transform: scale(2)'; // 2x Zoom
        },

        resetZoom() {
            this.zoomStyle = 'transform: scale(1)';
        },

        addToCart() {
            if (!this.canAddToCart) return;

            let url = '/cart/add/{{ $product->id }}';
            // If variant is selected, we should probably handle that in the backend controller.
            // Currently backend expects product ID.
            // I will send variant_id in body.
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    quantity: parseInt(this.quantity),
                    variant_id: this.selectedVariantId 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update header cart count
                    const badge = document.getElementById('cart-count');
                    if(badge) badge.textContent = data.cart_count;
                    alert('Added to cart!');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
}
</script>
@endsection
