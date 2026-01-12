@props(['products'])

<div class="bg-gradient-to-b from-gray-50 to-white py-20" x-data="cartActions()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-3xl font-bold text-[#0A2540]">Featured Collection</h2>
            <a href="#" class="text-[color:var(--brand-color)] font-medium hover:underline flex items-center gap-2">
                View All 
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>

        @if($products->count() > 4)
            {{-- Horizontal Scroll Carousel for more than 4 items --}}
            <div class="relative" x-data="{ scrollPosition: 0, maxScroll: 0 }" x-init="maxScroll = $refs.scrollContainer.scrollWidth - $refs.scrollContainer.clientWidth">
                {{-- Scroll Buttons --}}
                <button @click="$refs.scrollContainer.scrollBy({ left: -400, behavior: 'smooth' })" 
                        x-show="scrollPosition > 0"
                        class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 backdrop-blur shadow-xl rounded-full p-4 hover:bg-white transition-all">
                    <svg class="w-6 h-6 text-[#0A2540]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <button @click="$refs.scrollContainer.scrollBy({ left: 400, behavior: 'smooth' })" 
                        x-show="scrollPosition < maxScroll"
                        class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 backdrop-blur shadow-xl rounded-full p-4 hover:bg-white transition-all">
                    <svg class="w-6 h-6 text-[#0A2540]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                {{-- Scrollable Container --}}
                <div x-ref="scrollContainer" 
                     @scroll="scrollPosition = $el.scrollLeft; maxScroll = $el.scrollWidth - $el.clientWidth"
                     class="flex gap-8 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-4">
                    @foreach($products as $product)
                        <div class="flex-none w-80 snap-start">
                            <x-storefront.product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Grid Layout for 4 or fewer items --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($products as $product)
                    <x-storefront.product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
