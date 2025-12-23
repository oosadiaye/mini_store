<section id="{{ $section_id ?? '' }}" class="py-24 bg-gray-50">
    <div class="container mx-auto max-w-8xl px-4">
            <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-4xl font-serif font-black text-gray-900 mb-2">Fresh Drops</h2>
                <p class="text-lg text-gray-500 font-medium">Be the first to wear it.</p>
            </div>
            <a href="{{ route('storefront.products') }}" class="text-indigo-600 font-bold hover:text-indigo-800 transition border-b-2 border-indigo-200 hover:border-indigo-600 pb-1">View All &rarr;</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($new_arrivals as $product)
            <div class="group relative bg-white rounded-3xl p-3 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 card">
                <div class="relative aspect-[4/5] bg-gray-200 rounded-2xl overflow-hidden mb-4">
                    <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wide z-10 shadow-sm animate-bounce">New</span>
                    <a href="{{ route('storefront.product.show', $product) }}">
                        @if($product->images->isNotEmpty())
                            <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-100"><span class="text-4xl">✨</span></div>
                        @endif
                    </a>
                        <div class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition duration-300">
                        <button onclick="addToCart({{ $product->id }})" class="w-full bg-black text-white py-4 rounded-xl font-bold uppercase tracking-wider hover:bg-gray-800 shadow-xl h-btn">Add to Cart</button>
                    </div>
                </div>
                <div class="px-2 pb-2">
                    <a href="{{ route('storefront.product.show', $product) }}">
                        <h3 class="text-lg font-bold text-gray-900 mb-1 leading-tight group-hover:text-indigo-600 transition">{{ $product->name }}</h3>
                    </a>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xl font-black text-indigo-600">${{ number_format($product->price, 0) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
