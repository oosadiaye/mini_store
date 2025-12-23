@php
    $settings = $section->settings ?? [];
    $limit = $settings['limit'] ?? 4;
    
    // Use controller-provided data if available, otherwise query
    $products = $featuredProducts ?? \App\Models\Product::where('is_featured', true)
        ->where('is_active', true)
        ->take($limit)
        ->get();
@endphp

<section id="{{ $section_id ?? '' }}" class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $section->title }}</h2>
            @if($section->content)
            <p class="text-gray-600 max-w-2xl mx-auto">{{ $section->content }}</p>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
            <div class="group">
                <div class="relative overflow-hidden rounded-lg mb-4">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                    @if($product->sale_price)
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">SALE</span>
                    @endif
                    <!-- Add to Cart Overlay -->
                    <div class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition duration-300">
                        <button onclick="addToCart({{ $product->id }})" class="w-full bg-white text-gray-900 py-2 rounded shadow-lg font-bold hover:bg-indigo-600 hover:text-white transition">Add to Cart</button>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition">
                    <a href="{{ route('storefront.product.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="text-gray-900 font-bold">${{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price)
                        <span class="text-gray-400 line-through text-sm">${{ number_format($product->compare_price, 2) }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
