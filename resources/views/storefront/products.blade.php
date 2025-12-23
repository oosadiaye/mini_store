@extends('storefront.layout')

@php
    // Get active theme slug using helper method
    $themeSlug = \App\Models\ThemeSetting::getActiveThemeSlug();
@endphp

@section('content')
    {{-- Render Page Builder Sections for Shop Page --}}
    @if(isset($sections) && $sections->isNotEmpty())
        @foreach($sections as $section)
            @if($section['enabled'] ?? true)
                @php
                    $sectionObj = (object) $section;
                    $sectionObj->settings = $section['settings'] ?? [];
                    $sectionObj->title = $section['title'] ?? '';
                    $sectionObj->content = $section['content'] ?? '';
                @endphp
                @includeIf('storefront.sections.' . $section['type'], ['section' => $sectionObj])
            @endif
        @endforeach
    @endif

<div class="container mx-auto px-4 py-8" x-data="{ mobileFiltersOpen: false }">
    <!-- Breadcrumb -->
    <div class="mb-6 text-sm text-gray-600 flex items-center">
        <a href="{{ route('storefront.home') }}" class="hover:text-primary transition">Home</a>
        <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        @if(request('search'))
            <a href="{{ route('storefront.products') }}" class="hover:text-primary transition">Products</a>
            <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-semibold text-gray-900">Search Results for "{{ request('search') }}"</span>
        @else
            <span class="font-semibold text-gray-900">All Products</span>
        @endif
    </div>

    <!-- Mobile Filter Button -->
    <div class="lg:hidden mb-4">
        <button @click="mobileFiltersOpen = true" class="w-full flex items-center justify-center gap-2 bg-white border-2 border-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:border-primary hover:text-primary transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filters & Sort
        </button>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Desktop Filters Sidebar -->
        <div class="hidden lg:block lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 sticky top-24">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-lg text-gray-900">Filters</h3>
                    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                        <a href="{{ route('storefront.products') }}" class="text-xs text-primary hover:text-indigo-700 font-medium">Clear All</a>
                    @endif
                </div>
                
                <form method="GET" action="{{ route('storefront.products') }}" class="space-y-6">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." 
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition appearance-none bg-white">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Price Range</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                class="w-1/2 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                                class="w-1/2 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-primary to-indigo-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition transform">
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Filters Drawer -->
        <div x-show="mobileFiltersOpen" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" x-cloak>
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="mobileFiltersOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            
            <div class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-xl flex flex-col" 
                 x-transition:enter="transition ease-in-out duration-300 transform" 
                 x-transition:enter-start="translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transition ease-in-out duration-300 transform" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="translate-x-full">
                
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Filters</h3>
                    <button @click="mobileFiltersOpen = false" class="text-gray-500 hover:text-gray-900">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <form method="GET" action="{{ route('storefront.products') }}" class="space-y-6">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price Range</label>
                            <div class="flex gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                    class="w-1/2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                                    class="w-1/2 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>

                        <div class="pt-4 space-y-3">
                            <button type="submit" class="w-full bg-gradient-to-r from-primary to-indigo-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition">
                                Apply Filters
                            </button>
                            @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                                <a href="{{ route('storefront.products') }}" class="block w-full text-center border-2 border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                                    Clear All
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Sort and Results Count -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 bg-white p-4 rounded-lg border border-gray-100">
                <p class="text-gray-700 font-medium">
                    <span class="text-primary font-bold">{{ $products->count() }}</span> of <span class="font-bold">{{ $products->total() }}</span> products
                </p>
                <form method="GET" action="{{ route('storefront.products') }}" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label class="text-sm text-gray-600 font-medium">Sort:</label>
                    <select name="sort" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-white font-medium text-sm">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
                    </select>
                </form>
            </div>

            <!-- Products -->
            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($products as $product)
                        @include("storefront.themes.{$themeSlug}.components.product-card", ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found</h3>
                    <p class="text-gray-600">Try adjusting your filters or search terms</p>
                </div>
            @endif
        </div>
    </div>
</div>


@endsection
