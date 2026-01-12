@props(['categoryName', 'categorySlug', 'products'])

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20" x-data="cartActions()">
    {{-- Category Header --}}
    <div class="flex items-center justify-between mb-10">
        <h2 class="text-3xl font-bold text-[#0A2540]">{{ $categoryName }}</h2>
        <a href="/{{ $categorySlug }}" class="text-[color:var(--brand-color)] font-medium hover:underline flex items-center gap-2">
            View All
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
        </a>
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
        @forelse($products as $product)
            <x-storefront.product-card :product="$product" />
        @empty
            <div class="col-span-full py-10 text-center text-gray-400 bg-gray-50 rounded-xl">
                <p>No products available in this category yet.</p>
            </div>
        @endforelse
    </div>
</div>
