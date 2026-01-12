@props(['mode', 'title', 'layout' => 'grid'])

<section x-data="{
    products: [],
    loading: true,
    async init() {
        try {
            const response = await fetch('{{ route('storefront.api.products') }}?sort={{ $mode }}');
            this.products = await response.json();
            this.loading = false;
        } catch (error) {
            console.error('Failed to load products:', error);
            this.loading = false;
        }
    },
    formatPrice(price) {
        return '₦' + Number(price).toLocaleString('en-NG', { minimumFractionDigits: 2 });
    }
}" class="py-16 px-4 md:px-8 max-w-7xl mx-auto">

    <div class="flex justify-between items-end mb-10">
        <h2 class="text-3xl font-bold text-gray-900">{{ $title }}</h2>
        <a href="/products" class="text-sm border-b border-gray-300 pb-0.5 hover:border-black transition-colors">View All</a>
    </div>

    {{-- Loading State --}}
    <div x-show="loading" class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
        @for($i = 0; $i < 4; $i++)
            <div class="animate-pulse">
                <div class="bg-gray-200 aspect-[4/5] rounded-[32px] mb-4"></div>
                <div class="h-4 bg-gray-200 rounded w-3/4 mx-auto mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mx-auto"></div>
            </div>
        @endfor
    </div>

    {{-- Products Render --}}
    <div x-show="!loading" style="display: none;">
        
        @if($layout === 'slider')
            {{-- Slider Layout (Horizontal Scroll) --}}
            <div class="relative group">
                <div class="flex overflow-x-auto space-x-6 pb-8 snap-x snap-mandatory hide-scrollbar" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <template x-for="product in products" :key="product.id">
                        <div class="min-w-[280px] md:min-w-[320px] snap-start">
                             <div class="group/card relative flex flex-col bg-white rounded-[32px] overflow-hidden border border-gray-50 transition-all duration-500 hover:shadow-lg">
                                {{-- Image --}}
                                <div class="relative aspect-[4/5] overflow-hidden bg-gray-50">
                                    <img :src="product.image_url" :alt="product.name" class="h-full w-full object-cover object-center group-hover/card:scale-110 transition-transform duration-700">
                                    {{-- Quick Add Overlay --}}
                                    <div class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover/card:translate-y-0 transition-transform duration-500">
                                        <button class="w-full bg-[#0A2540] text-white py-3 rounded-xl font-bold shadow-xl flex justify-center items-center hover:bg-[#1a3a5a] transition-colors text-sm">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                                {{-- Details --}}
                                <div class="p-6 text-center">
                                    <h3 class="text-lg font-bold text-[#0A2540] mb-1 leading-tight truncate" x-text="product.name"></h3>
                                    <p class="text-base font-bold text-[#0A2540]/60" x-text="formatPrice(product.price)"></p>
                                </div>
                             </div>
                        </div>
                    </template>
                </div>
            </div>
        @else
            {{-- Grid Layout (Default) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <template x-for="product in products" :key="product.id">
                    <div class="group relative flex flex-col bg-white rounded-[32px] overflow-hidden border border-gray-50 transition-all duration-500 hover:shadow-[0_40px_80px_-15px_rgba(10,37,64,0.12)] hover:-translate-y-2">
                        {{-- Image --}}
                        <div class="relative aspect-[4/5] overflow-hidden bg-gray-50">
                            <img :src="product.image_url" :alt="product.name" class="h-full w-full object-cover object-center group-hover:scale-110 transition-transform duration-700">
                             <div x-show="product.stock_quantity < 5 && product.stock_quantity > 0" class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur shadow-sm text-[#0A2540] text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full" x-text="'Only ' + product.stock_quantity + ' left'"></span>
                            </div>
                            <div class="absolute inset-x-0 bottom-0 p-6 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                <button class="w-full bg-[#0A2540] text-white py-4 rounded-2xl font-bold shadow-xl flex justify-center items-center hover:bg-[#1a3a5a] transition-colors">
                                    <span class="flex items-center gap-2">
                                        <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Add to Cart
                                    </span>
                                </button>
                            </div>
                        </div>
                        {{-- Details --}}
                        <div class="p-8 pb-10 flex flex-col items-center text-center">
                            <h3 class="text-xl font-bold text-[#0A2540] mb-2 leading-tight" x-text="product.name"></h3>
                            <div class="flex items-center gap-3">
                                 <p class="text-lg font-bold text-[#0A2540]/60" x-text="formatPrice(product.price)"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>
    
    {{-- Empty State --}}
    <div x-show="!loading && products.length === 0" class="text-center py-20 text-gray-500 bg-gray-50 rounded-lg">
        <p>No products found in this collection.</p>
    </div>
</section>
