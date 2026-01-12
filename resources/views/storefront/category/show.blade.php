<x-storefront.layout :config="$config" :menuCategories="$menuCategories">
    <!-- Category Hero -->
    <x-storefront.category-hero :category="$category" :brandColor="$config->brand_color ?? '#0A2540'" />

    <!-- Main Content with Sidebar -->
    <div class="bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
            
            <div class="lg:grid lg:grid-cols-12 lg:gap-12">
                
                <!-- Sidebar Filter (Desktop) -->
                <aside class="hidden lg:block lg:col-span-3 border-r border-gray-100 pr-8">
                    <x-storefront.category-filter-sidebar />
                </aside>

                <!-- Product Grid -->
                <main class="lg:col-span-9">
                    
                    <!-- Mobile Filter Toggle -->
                    <div class="lg:hidden mb-6">
                        <button class="w-full flex items-center justify-center gap-2 bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filters & Sort
                        </button>
                    </div>

                    @if($products->count() > 0)
                        <!-- Results Count -->
                        <div class="mb-6">
                            <p class="text-sm text-gray-600">
                                Showing <span class="font-semibold">{{ $products->firstItem() }}</span> 
                                to <span class="font-semibold">{{ $products->lastItem() }}</span> 
                                of <span class="font-semibold">{{ $products->total() }}</span> products
                            </p>
                        </div>

                        <!-- Premium 3-Column Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" x-data="cartActions()">
                            @foreach($products as $product)
                                <x-storefront.product-card :product="$product->toArray()" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-12">
                            {{ $products->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-24 bg-white rounded-2xl">
                            <div class="bg-gray-50 rounded-full h-24 w-24 flex items-center justify-center mx-auto mb-6">
                                <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">No products found</h3>
                            <p class="text-gray-500 mb-6">Check back later for new arrivals in this collection.</p>
                            <a href="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" 
                               class="inline-block bg-[#0A2540] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#1a3a5a] transition-colors">
                                Browse All Products
                            </a>
                        </div>
                    @endif

                </main>

            </div>

        </div>
    </div>
</x-storefront.layout>
