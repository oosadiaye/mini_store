@php
    $categories = \App\Models\Category::take(6)->get(); // Fetch up to 6 categories
@endphp

<section class="py-16 md:py-24 bg-white">
    <div class="container mx-auto px-4 md:px-8">
        <div class="flex items-end justify-between mb-12">
            <h2 class="text-3xl font-serif">Shop by Category</h2>
            <a href="/shop" class="hidden md:inline-block text-sm font-bold uppercase tracking-widest border-b border-black pb-1 hover:opacity-75">View All</a>
        </div>

        {{-- Horizontal Slider Layout (CSS Only for simplicity, or Swiper if added later) --}}
        <div class="flex overflow-x-auto space-x-6 pb-6 scrollbar-hide snap-x snap-mandatory">
            @foreach($categories as $category)
                <a href="/shop?category={{ $category->id }}" class="flex-shrink-0 w-64 md:w-80 group snap-start">
                    <div class="aspect-[3/4] overflow-hidden bg-gray-100 relative mb-4">
                        <img src="{{ $category->image_url ?? 'https://via.placeholder.com/400x533?text=' . urlencode($category->name) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-out">
                    </div>
                    <h3 class="font-serif text-lg">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $category->products_count ?? 0 }} Products</p>
                </a>
            @endforeach
            
            @if($categories->isEmpty())
                 <div class="w-full text-center py-12 text-gray-500">
                    <p>No categories found. Add categories in your admin panel.</p>
                 </div>
            @endif
        </div>
        
        <div class="mt-8 text-center md:hidden">
             <a href="/shop" class="text-sm font-bold uppercase tracking-widest border-b border-black pb-1">View All</a>
        </div>
    </div>
</section>
