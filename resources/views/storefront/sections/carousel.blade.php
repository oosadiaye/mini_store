@php
    $settings = $section->settings ?? [];
    $itemsPerView = $settings['items_per_view'] ?? 4;
    $autoplay = $settings['autoplay'] ?? false;
    $loop = $settings['loop'] ?? true;
    
    // Sample items - in real implementation, these would come from database
    $items = $settings['items'] ?? [
        ['image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400', 'title' => 'Product 1', 'price' => (tenant('data')['currency_symbol'] ?? '₦') . '99'],
        ['image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400', 'title' => 'Product 2', 'price' => (tenant('data')['currency_symbol'] ?? '₦') . '149'],
        ['image' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', 'title' => 'Product 3', 'price' => (tenant('data')['currency_symbol'] ?? '₦') . '199'],
        ['image' => 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=400', 'title' => 'Product 4', 'price' => (tenant('data')['currency_symbol'] ?? '₦') . '129'],
        ['image' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=400', 'title' => 'Product 5', 'price' => (tenant('data')['currency_symbol'] ?? '₦') . '179'],
        ['image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400', 'title' => 'Product 6', 'price' => (tenant('data')['currency_symbol'] ?? '₦') . '159'],
    ];
@endphp

<section class="py-16 bg-gray-50" x-data="carousel({{ json_encode($items) }}, {{ $itemsPerView }}, {{ $loop ? 'true' : 'false' }})">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $section->title ?? 'Featured Items' }}</h2>
            <p class="text-gray-600">{{ $section->content ?? 'Discover our curated collection' }}</p>
        </div>
        
        <!-- Carousel Container -->
        <div class="relative">
            <!-- Navigation Buttons -->
            <button @click="prev()" 
                    :disabled="!canGoPrev"
                    :class="!canGoPrev ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                    class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-indigo-600 text-white p-3 rounded-full shadow-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="next()" 
                    :disabled="!canGoNext"
                    :class="!canGoNext ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                    class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-indigo-600 text-white p-3 rounded-full shadow-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            
            <!-- Items Container -->
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-out" 
                     :style="'transform: translateX(-' + (currentIndex * (100 / itemsPerView)) + '%)'">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex-shrink-0 px-3" 
                             :style="'width: ' + (100 / itemsPerView) + '%'">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                                <div class="aspect-square overflow-hidden">
                                    <img :src="item.image" 
                                         :alt="item.title"
                                         class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 mb-1" x-text="item.title"></h3>
                                    <p class="text-indigo-600 font-bold" x-text="item.price"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Progress Indicators -->
            <div class="flex justify-center gap-2 mt-6">
                <template x-for="(dot, index) in Math.ceil(items.length / itemsPerView)" :key="index">
                    <button @click="goToSlide(index)"
                            :class="Math.floor(currentIndex / itemsPerView) === index ? 'bg-indigo-600 w-8' : 'bg-gray-300 w-3'"
                            class="h-3 rounded-full transition-all duration-300"></button>
                </template>
            </div>
        </div>
    </div>
</section>

<script>
function carousel(items, itemsPerView, loop) {
    return {
        items: items,
        itemsPerView: itemsPerView,
        loop: loop,
        currentIndex: 0,
        
        get maxIndex() {
            return this.items.length - this.itemsPerView;
        },
        
        get canGoPrev() {
            return this.loop || this.currentIndex > 0;
        },
        
        get canGoNext() {
            return this.loop || this.currentIndex < this.maxIndex;
        },
        
        next() {
            if (this.canGoNext) {
                if (this.currentIndex >= this.maxIndex && this.loop) {
                    this.currentIndex = 0;
                } else {
                    this.currentIndex++;
                }
            }
        },
        
        prev() {
            if (this.canGoPrev) {
                if (this.currentIndex <= 0 && this.loop) {
                    this.currentIndex = this.maxIndex;
                } else {
                    this.currentIndex--;
                }
            }
        },
        
        goToSlide(slideIndex) {
            this.currentIndex = slideIndex * this.itemsPerView;
        }
    }
}
</script>
