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
                <div class="relative w-28 h-28 md:w-36 md:h-36 rounded-full overflow-hidden shadow-md border-2 border-gray-100 group-hover:border-primary transition-all duration-300 group-hover:shadow-xl">
                    <img src="{{ $category->image_url ?? 'https://via.placeholder.com/300x300?text=' . $category->name }}" 
                         class="w-full h-full object-cover transition duration-500 group-hover:scale-110" 
                         alt="{{ $category->name }}">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                </div>
                <h3 class="mt-4 text-center font-medium text-gray-900 group-hover:text-primary transition-colors duration-300 line-clamp-2">{{ $category->name }}</h3>
            </a>
            @endforeach
        </div>
    </div>
</section>
