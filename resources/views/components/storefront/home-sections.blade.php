<div x-data="{
    sections: [],
    loading: true,
    async init() {
        try {
            const response = await fetch('{{ route('storefront.api.sections') }}');
            this.sections = await response.json();
            this.loading = false;
        } catch (error) {
            console.error('Failed to load home sections:', error);
            this.loading = false;
        }
    },
    formatPrice(price) {
        return '₦' + Number(price).toLocaleString('en-NG', { minimumFractionDigits: 2 });
    }
}" class="w-full">

    {{-- Loading Skeleton --}}
    <template x-if="loading">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">
            <template x-for="i in 2">
                <div>
                    <div class="flex justify-between items-end mb-8">
                        <div class="h-8 bg-gray-200 rounded w-48 animate-pulse"></div>
                        <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <template x-for="j in 4">
                            <div class="animate-pulse">
                                <div class="bg-gray-200 aspect-[4/5] rounded-[20px] mb-4"></div>
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- Sections Loop --}}
    <template x-for="(section, index) in sections" :key="index">
        <div class="w-full">
            
            {{-- Category Section --}}
            <template x-if="section.type === 'category_section'">
                <section :class="index % 2 === 1 ? 'bg-[#f9f9f9]' : 'bg-white'" class="py-12 lg:py-16 mb-16 lg:mb-20">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        
                        {{-- Header --}}
                        <div class="flex justify-between items-end mb-10">
                            <h2 class="text-3xl font-bold text-gray-900" x-text="section.title"></h2>
                            <a :href="section.link_slug" class="text-sm font-medium text-[#0A2540] border-b border-gray-200 hover:border-[#0A2540] transition-colors pb-0.5">
                                View All <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>

                        {{-- Product Grid --}}
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                            <template x-for="product in section.products" :key="product.id">
                                <div class="group relative flex flex-col h-full bg-white rounded-[24px] overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                    <div class="relative aspect-square overflow-hidden bg-gray-100">
                                        <img :src="product.image_url" :alt="product.name" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                        <button class="absolute bottom-3 right-3 bg-white p-3 rounded-full shadow-md text-[#0A2540] hover:bg-[#0A2540] hover:text-white transition-colors" title="Add to Cart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="p-5 flex flex-col flex-1">
                                        <h3 class="text-base font-medium text-gray-900 line-clamp-2 mb-2 flex-grow">
                                            <a :href="'/' + '{{ $tenant->slug }}' + '/product/' + product.slug" x-text="product.name"><span aria-hidden="true" class="absolute inset-0"></span></a>
                                        </h3>
                                        <div class="mt-auto flex items-baseline gap-2 pt-2 border-t border-gray-50">
                                            <span class="text-lg font-bold text-[#0A2540]" x-text="formatPrice(product.price)"></span>
                                            <template x-if="product.compare_at_price > product.price">
                                                <span class="text-sm text-gray-400 line-through" x-text="formatPrice(product.compare_at_price)"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>
                </section>
                </section>
            </template>

            {{-- Product Slider Section (New Arrivals) --}}
            <template x-if="section.type === 'product_slider'">
                <section class="py-12 bg-white mb-16 lg:mb-20 border-b border-gray-100"
                    x-data="{
                        init() {
                            let container = this.$refs.slider;
                            this.interval = setInterval(() => {
                                if (!container) return;
                                if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 10) {
                                    container.scrollTo({ left: 0, behavior: 'smooth' });
                                } else {
                                    container.scrollBy({ left: 300, behavior: 'smooth' });
                                }
                            }, 3000);
                        },
                        interval: null,
                        pause() { clearInterval(this.interval); },
                        resume() { this.init(); }
                    }"
                    @mouseenter="pause()"
                    @mouseleave="resume()"
                >
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-2xl font-bold text-gray-900" x-text="section.title"></h2>
                            <a :href="section.link_slug" class="text-sm font-medium text-[#0A2540] hover:text-indigo-600">View All &rarr;</a>
                        </div>
                        
                        <div class="relative group">
                            <!-- Scroll Container -->
                            <div x-ref="slider" class="flex overflow-x-auto gap-6 pb-6 snap-x snap-mandatory scrollbar-hide" style="scrollbar-width: none; -ms-overflow-style: none;">
                                <template x-for="product in section.products" :key="product.id">
                                    <div class="flex-none w-[280px] snap-start group/card flex flex-col h-full bg-white">
                                        <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 mb-4">
                                            <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-105">
                                            <a :href="'/' + '{{ $tenant->slug }}' + '/product/' + product.slug" class="absolute inset-0"></a>
                                        </div>
                                        <h3 class="text-base font-medium text-gray-900 line-clamp-1 flex-grow">
                                            <a :href="'/' + '{{ $tenant->slug }}' + '/product/' + product.slug" x-text="product.name"></a>
                                        </h3>
                                        <div class="flex items-baseline gap-2 mt-auto pt-2">
                                            <span class="font-bold text-[#0A2540]" x-text="formatPrice(product.price)"></span>
                                            <template x-if="product.compare_at_price > product.price">
                                                <span class="text-sm text-gray-400 line-through" x-text="formatPrice(product.compare_at_price)"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </section>
            </template>

            {{-- Split Banner Section --}}
            <template x-if="section.type === 'split_banner' && (section.data.image_left || section.data.image_right)">
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 mb-16 lg:mb-20">
                     <div class="relative rounded-3xl overflow-hidden shadow-2xl">
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                            {{-- Left Half --}}
                            <div class="relative h-[250px] lg:h-[380px] overflow-hidden group">
                                 <template x-if="section.data.image_left">
                                     <img :src="section.data.image_left" class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-1000 group-hover:scale-110" alt="Banner Left">
                                 </template>
                                 <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition-colors"></div>
                            </div>

                            {{-- Right Half --}}
                            <div class="relative h-[250px] lg:h-[380px] overflow-hidden group">
                                 <template x-if="section.data.image_right">
                                     <img :src="section.data.image_right" class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-1000 group-hover:scale-110" alt="Banner Right">
                                 </template>
                                 <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition-colors"></div>
                            </div>
                         </div>

                         {{-- Center Overlay Panel --}}
                         <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                             <div class="bg-white/95 backdrop-blur-md p-8 md:p-12 text-center max-w-lg mx-4 shadow-xl rounded-2xl pointer-events-auto transform hover:scale-105 transition-transform duration-300 border border-white/50">
                                 <h2 class="text-3xl font-serif font-bold text-[#0A2540] mb-3" x-text="section.data.center_text.title"></h2>
                                 <p class="text-gray-600 mb-6 font-medium tracking-wide uppercase text-xs" x-text="section.data.center_text.subtitle"></p>
                                 <a href="#" class="inline-block bg-[#0A2540] text-white px-8 py-3 rounded-full font-bold uppercase tracking-wider text-sm hover:bg-[#1a3a5a] transition-all shadow-lg hover:shadow-xl">
                                     <span x-text="section.data.center_text.cta || 'Shop Now'"></span>
                                 </a>
                             </div>
                         </div>
                     </div>
                </section>
            </template>

        </div>
    </template>
</div>
