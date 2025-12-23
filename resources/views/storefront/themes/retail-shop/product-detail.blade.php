@extends('storefront.themes.retail-shop.layout')

@section('pageTitle', $product->name)

@section('content')
<div class="bg-white" x-data="{ 
    selectedImage: '{{ $product->image_url }}',
    quantity: 1,
    activeTab: 'description',
    addToCart() {
        fetch('/cart/add/{{ $product->id }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({ quantity: this.quantity })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Ideally show a toast or slide-out cart
                alert('Added to cart'); 
                window.location.reload(); 
            }
        });
    }
}">

    <div class="container mx-auto px-4 md:px-8 py-8 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20">
            
            {{-- Left Column: Images --}}
            <div class="space-y-4">
                {{-- Main Image --}}
                <div class="aspect-[3/4] md:aspect-square bg-gray-100 overflow-hidden relative group">
                    <img :src="selectedImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
                
                {{-- Thumbnails --}}
                @if($product->images->count() > 1)
                    <div class="flex space-x-4 overflow-x-auto pb-2 scrollbar-hide">
                        @foreach($product->images as $image)
                            <button @click="selectedImage = '{{ $image->image_url }}'" class="w-20 md:w-24 aspect-square flex-shrink-0 border border-transparent hover:border-black transition">
                                <img src="{{ $image->image_url }}" alt="Thumbnail" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right Column: Product Details --}}
            <div class="flex flex-col h-full">
                {{-- Breadcrumbs --}}
                <nav class="text-xs text-gray-500 mb-6 uppercase tracking-wider">
                    <a href="/" class="hover:text-black">Home</a> / 
                    <a href="/shop" class="hover:text-black">Shop</a> / 
                    <span class="text-black">{{ $product->name }}</span>
                </nav>

                <h1 class="text-3xl md:text-5xl font-serif font-medium mb-4">{{ $product->name }}</h1>
                
                <div class="flex items-center space-x-4 mb-6">
                    <span class="text-xl md:text-2xl font-light text-gray-900">
                        {{ tenant('currency_symbol') ?? '$' }}{{ number_format($product->price, 2) }}
                    </span>
                    @if($reviewStats['count'] > 0)
                        <div class="flex items-center text-sm">
                            <div class="flex text-yellow-500">
                                @for($i=1; $i<=5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($reviewStats['avg']) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                @endfor
                            </div>
                            <span class="ml-2 text-gray-500">({{ $reviewStats['count'] }} Reviews)</span>
                        </div>
                    @endif
                </div>

                {{-- Add to Cart Form --}}
                <div class="border-t border-b border-gray-100 py-8 mb-8 space-y-6">
                    {{-- Quantity --}}
                    <div class="flex items-center space-x-4">
                        <span class="text-sm uppercase tracking-wide font-bold">Quantity</span>
                        <div class="flex items-center border border-gray-300">
                            <button @click="quantity > 1 ? quantity-- : null" class="w-10 h-10 hover:bg-gray-50 flex items-center justify-center text-gray-600">-</button>
                            <input type="text" x-model="quantity" class="w-12 h-10 text-center border-none focus:ring-0 p-0 text-sm" readonly>
                            <button @click="quantity++" class="w-10 h-10 hover:bg-gray-50 flex items-center justify-center text-gray-600">+</button>
                        </div>
                    </div>

                    {{-- Add to Cart Button (Desktop) --}}
                    <button @click="addToCart" class="hidden md:block w-full bg-black text-white py-4 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition">
                        Add to Cart
                    </button>
                    
                    <p class="text-xs text-gray-500 text-center mt-2">
                        Free shipping on orders over {{ tenant('currency_symbol') ?? '$' }}100
                    </p>
                </div>

                {{-- Description / Reviews Tabs --}}
                <div class="mt-auto">
                    <div class="flex border-b border-gray-200 space-x-8">
                        <button @click="activeTab = 'description'" :class="{ 'border-black text-black': activeTab === 'description', 'border-transparent text-gray-400': activeTab !== 'description' }" class="pb-4 text-sm font-bold uppercase tracking-widest border-b-2 transition">
                            Description
                        </button>
                        <button @click="activeTab = 'reviews'" :class="{ 'border-black text-black': activeTab === 'reviews', 'border-transparent text-gray-400': activeTab !== 'reviews' }" class="pb-4 text-sm font-bold uppercase tracking-widest border-b-2 transition">
                            Reviews
                        </button>
                    </div>

                    <div class="py-8 min-h-[200px]">
                        <div x-show="activeTab === 'description'" x-cloak class="prose max-w-none text-gray-600 font-light leading-relaxed">
                            {!! $product->description ?? 'No description available specific to this theme.' !!}
                        </div>
                        <div x-show="activeTab === 'reviews'" x-cloak>
                            @if($reviews->isEmpty())
                                <p class="text-gray-500">No reviews yet.</p>
                            @else
                                <div class="space-y-6">
                                    @foreach($reviews as $review)
                                    <div class="border-b border-gray-100 pb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h5 class="font-bold text-sm">{{ $review->customer_name }}</h5>
                                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex text-yellow-500 text-xs mb-2">
                                            @for($i=1; $i<=5; $i++)
                                                <span class="">{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                            @endfor
                                        </div>
                                        <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Related Products --}}
        @if($relatedProducts->isNotEmpty())
            <div class="mt-24 border-t border-gray-100 pt-16">
                 <h2 class="text-2xl font-serif mb-8 text-center">You May Also Like</h2>
                 <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                     @foreach($relatedProducts as $related)
                        @include('storefront.themes.modern-minimal.components.product-card', ['product' => $related])
                     @endforeach
                 </div>
            </div>
        @endif
    </div>

    {{-- Mobile Sticky Add to Cart --}}
    <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-200 md:hidden z-40 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <div class="flex space-x-4">
            <div class="flex items-center border border-gray-300 rounded px-2">
                 <button @click="quantity > 1 ? quantity-- : null" class="p-2 text-gray-600">-</button>
                 <span x-text="quantity" class="w-8 text-center text-sm font-medium"></span>
                 <button @click="quantity++" class="p-2 text-gray-600">+</button>
            </div>
            <button @click="addToCart" class="flex-grow bg-black text-white py-3 text-sm font-bold uppercase tracking-widest rounded">
                Add to Cart
            </button>
        </div>
    </div>
</div>
@endsection
