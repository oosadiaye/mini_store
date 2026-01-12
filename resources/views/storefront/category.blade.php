<x-storefront.layout :config="$config" :menuCategories="$menuCategories">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ $category->name }}</h1>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-6 xl:gap-x-8">
            @forelse($products as $product)
                <x-storefront.product-card :product="$product" />
            @empty
                <div class="col-span-4 text-center py-12">
                    <p class="text-gray-500">No products found in this category.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-storefront.layout>
