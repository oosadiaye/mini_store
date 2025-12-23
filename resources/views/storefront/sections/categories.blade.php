<section class="py-24 px-4 bg-white relative">
    <div class="container mx-auto max-w-7xl">
        <div class="flex justify-between items-end mb-8 px-4">
            <h2 class="text-3xl font-serif font-bold text-gray-900">Shop by Category</h2>
            <a href="{{ route('storefront.search') }}" class="text-primary hover:text-primary-dark font-medium transition flex items-center gap-1 group">
                View All
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        
        <div class="flex overflow-x-auto pb-8 gap-6 px-4 snap-x hide-scrollbar scroll-smooth">
            @foreach($categories as $category)
            <a href="{{ route('storefront.category', $category->id) }}" class="group flex flex-col items-center flex-shrink-0 snap-start w-32 md:w-40">
                <div class="relative w-28 h-28 md:w-36 md:h-36 rounded-xl overflow-hidden bg-gray-50 shadow-sm border border-gray-100 group-hover:border-primary transition-all duration-300 group-hover:shadow-md">
                    <img src="{{ $category->image_url ?? 'https://via.placeholder.com/300x300?text=' . $category->name }}" 
                         class="w-full h-full object-contain p-2 transition duration-500 group-hover:scale-105" 
                         alt="{{ $category->name }}">
                </div>
                <h3 class="mt-3 text-center font-bold text-sm text-gray-800 group-hover:text-primary transition-colors duration-300">{{ $category->name }}</h3>
                <span class="text-xs text-gray-400">{{ $category->products_count ?? 0 }} Products</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
