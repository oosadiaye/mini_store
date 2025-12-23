@extends('storefront.layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="mb-6 text-sm text-gray-600">
        <a href="{{ route('storefront.home') }}" class="hover:text-primary">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('storefront.products') }}" class="hover:text-primary">Products</a>
        <span class="mx-2">/</span>
        <span class="font-semibold">{{ $category->name }}</span>
    </div>

    <!-- Category Header -->
    <div class="text-center mb-12">
        @if($category->image_url)
            <div class="mb-6 relative h-64 w-full rounded-2xl overflow-hidden shadow-lg">
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                    <h1 class="text-4xl font-serif font-bold text-white drop-shadow-md">{{ $category->name }}</h1>
                </div>
            </div>
        @else
            <h1 class="text-4xl font-serif font-bold text-gray-900 mb-4">{{ $category->name }}</h1>
        @endif

        @if($category->description)
            <p class="text-gray-600 max-w-2xl mx-auto">{{ $category->description }}</p>
        @endif
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h3 class="font-bold text-lg mb-4">Filters</h3>
                
                <form method="GET" action="{{ route('storefront.category', $category) }}">
                    <!-- Search -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search in {{ $category->name }}..." 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                                class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                        Apply Filters
                    </button>
                    
                    @if(request()->anyFilled(['search', 'min_price', 'max_price']))
                        <a href="{{ route('storefront.category', $category) }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-gray-700">Clear Filters</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Sort and Results Count -->
            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-600">Showing {{ $products->count() }} of {{ $products->total() }} products</p>
                <form method="GET" action="{{ route('storefront.category', $category) }}" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label class="text-sm text-gray-600">Sort by:</label>
                    <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
                    </select>
                </form>
            </div>

            <!-- Products -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <div class="group cursor-pointer">
                            <div class="relative aspect-[4/5] bg-gray-100 overflow-hidden mb-4">
                                <a href="{{ route('storefront.product.show', $product) }}" class="block w-full h-full">
                                    @if($product->images->isNotEmpty())
                                        <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-700 ease-in-out group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    @if($product->is_featured)
                                        <span class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm text-gray-900 text-[10px] font-bold px-2 py-1 uppercase tracking-wide">Featured</span>
                                    @endif
                                </a>

                                <div class="absolute inset-x-0 bottom-0 p-4 opacity-0 group-hover:opacity-100 transition duration-300 translate-y-2 group-hover:translate-y-0">
                                    <button onclick="addToCart({{ $product->id }})" class="w-full bg-white text-gray-900 py-3 text-sm font-medium uppercase tracking-wide hover:bg-gray-900 hover:text-white transition shadow-lg">
                                        Quick Add
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <a href="{{ route('storefront.product.show', $product) }}">
                                    <h3 class="text-base text-gray-900 font-sans mb-1 group-hover:text-primary transition">{{ $product->name }}</h3>
                                </a>
                                <div class="flex items-center space-x-2">
                                    <p class="text-base font-medium text-gray-900">${{ number_format($product->price, 2) }}</p>
                                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                        <p class="text-sm text-gray-500 line-through">${{ number_format($product->compare_at_price, 2) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found for this category</h3>
                    <p class="text-gray-600">Try adjusting your filters</p>
                    <a href="{{ route('storefront.products') }}" class="inline-block mt-4 text-primary hover:text-indigo-700 font-semibold">View All Products</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function addToCart(productId) {
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart badge if element exists
            const badge = document.querySelector('.cart-badge');
            if(badge) badge.textContent = data.cart_count;
            
            // Simple alert for now, can be replaced with a toast
            alert('Product added to cart!');
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
