    {{-- Shop by Category (On Top) --}}
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 md:px-8">
            <div class="flex justify-between items-end mb-12">
                <h2 class="text-3xl md:text-4xl font-serif font-bold">Shop by Category</h2>
                <a href="{{ route('storefront.products.index') }}" class="hidden md:block text-primary font-medium hover:text-secondary transition">View All Categories &rarr;</a>
            </div>

            {{-- Grid / Slider --}}
            <div class="relative">
                <div class="flex md:grid md:grid-cols-3 gap-6 overflow-x-auto md:overflow-visible pb-8 md:pb-0 snap-x snap-mandatory no-scrollbar">
                    @foreach($categories->take(6) as $category)
                        <a href="{{ route('storefront.products.index', ['category' => $category->slug]) }}" class="flex-shrink-0 w-[85vw] md:w-auto snap-center group">
                            <div class="glass-card rounded-xl overflow-hidden relative aspect-square md:aspect-[4/3] transition-all duration-500 hover:shadow-2xl">
                                @if($category->image_url)
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-300">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80 group-hover:opacity-100 transition-opacity"></div>
                                <div class="absolute bottom-0 left-0 p-6 md:p-8 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                    <h3 class="text-2xl text-white font-serif font-bold mb-1">{{ $category->name }}</h3>
                                    <span class="text-gray-200 text-sm font-light tracking-wide group-hover:text-white transition-colors">{{ $category->products_count ?? 0 }} Products</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('storefront.products.index') }}" class="text-primary font-medium hover:text-secondary transition">View All Categories &rarr;</a>
            </div>
        </div>
    </section>
