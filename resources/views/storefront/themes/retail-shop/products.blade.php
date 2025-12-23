@extends('storefront.themes.retail-shop.layout')

@section('pageTitle', 'Shop')

@section('content')

    {{-- Header --}}
    <div class="bg-gray-50 border-b border-gray-200 py-8 md:py-12">
        <div class="container mx-auto px-4 md:px-8 text-center md:text-left">
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-gray-900 mb-2">Shop All</h1>
            <p class="text-gray-500 font-light">Explore our curated collection of premium products.</p>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-8 py-12" x-data="{ mobileFiltersOpen: false }">
        <div class="flex flex-col lg:flex-row gap-12">
            
            {{-- Sidebar (Desktop) --}}
            <aside class="hidden lg:block w-64 flex-shrink-0 space-y-10">
                {{-- Categories --}}
                <div>
                    <h3 class="font-serif font-bold text-lg mb-4 pb-2 border-b border-gray-100">Categories</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('storefront.products.index') }}" class="text-sm {{ !request('category') ? 'font-bold text-teal-600' : 'text-gray-600 hover:text-gray-900' }}">All Categories</a></li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="text-sm flex justify-between group {{ request('category') == $cat->slug ? 'font-bold text-teal-600' : 'text-gray-600 hover:text-gray-900' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-gray-300 text-xs group-hover:text-gray-400">{{ $cat->products_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Price --}}
                <div>
                    <h3 class="font-serif font-bold text-lg mb-4 pb-2 border-b border-gray-100">Price</h3>
                    <form action="{{ route('storefront.products.index') }}" method="GET" class="space-y-4">
                        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                        <div class="flex gap-2">
                            <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" class="w-full bg-white border border-gray-200 rounded px-3 py-2 text-sm focus:border-teal-500 focus:ring-teal-500">
                            <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" class="w-full bg-white border border-gray-200 rounded px-3 py-2 text-sm focus:border-teal-500 focus:ring-teal-500">
                        </div>
                        <button type="submit" class="w-full bg-gray-900 text-white text-xs font-bold uppercase py-2 hover:bg-teal-600 transition">Update</button>
                    </form>
                </div>

                {{-- Color Filter (Visual Mockup) --}}
                <div>
                    <h3 class="font-serif font-bold text-lg mb-4 pb-2 border-b border-gray-100">Color</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['#000000', '#ffffff', '#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'] as $color)
                            <button class="w-8 h-8 rounded-full border border-gray-200 shadow-sm hover:scale-110 transition ring-2 ring-transparent hover:ring-teal-500 focus:ring-teal-500" style="background-color: {{ $color }};"></button>
                        @endforeach
                    </div>
                </div>

                {{-- Size Filter (Visual Mockup) --}}
                <div>
                     <h3 class="font-serif font-bold text-lg mb-4 pb-2 border-b border-gray-100">Size</h3>
                     <div class="grid grid-cols-3 gap-2">
                         @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <button class="border border-gray-200 py-2 text-xs font-medium hover:border-black hover:bg-black hover:text-white transition rounded">{{ $size }}</button>
                         @endforeach
                     </div>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1">
                {{-- Toolbar --}}
                <div class="flex justify-between items-center mb-8">
                    <button @click="mobileFiltersOpen = true" class="lg:hidden flex items-center gap-2 text-sm font-bold uppercase tracking-widest border border-gray-300 px-4 py-2 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Filter
                    </button>
                    
                    <div class="ml-auto flex items-center gap-2">
                        <span class="text-sm text-gray-500 hidden md:inline">Sort by:</span>
                        <select onchange="window.location.href=this.value" class="border-none bg-transparent text-sm font-medium focus:ring-0 cursor-pointer pr-8 text-right">
                             <option value="{{ route('storefront.products.index', array_merge(request()->query(), ['sort' => 'newest'])) }}">Newest</option>
                             <option value="{{ route('storefront.products.index', array_merge(request()->query(), ['sort' => 'price_asc'])) }}">Price: Low to High</option>
                             <option value="{{ route('storefront.products.index', array_merge(request()->query(), ['sort' => 'price_desc'])) }}">Price: High to Low</option>
                        </select>
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6 gap-y-10">
                    @forelse($products as $product)
                        @include('storefront.themes.retail-shop.components.product-card', ['product' => $product])
                    @empty
                        <div class="col-span-full py-20 text-center text-gray-500">
                            <p class="text-lg">No products found matching your selection.</p>
                            <a href="{{ route('storefront.products.index') }}" class="text-teal-600 underline mt-2 inline-block">Clear all filters</a>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-12">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>

        {{-- Mobile Filter Drawer --}}
        <div x-show="mobileFiltersOpen" class="fixed inset-0 z-50 flex justify-end lg:hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="mobileFiltersOpen = false" x-transition.opacity></div>
            <div class="relative w-80 bg-white h-full shadow-2xl overflow-y-auto flex flex-col" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-serif text-xl font-bold">Filters</h3>
                    <button @click="mobileFiltersOpen = false" class="text-gray-400 hover:text-black">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-8 flex-1">
                    {{-- Mobile Categories --}}
                    <div>
                        <h4 class="font-bold text-sm uppercase tracking-widest mb-4">Category</h4>
                        <ul class="space-y-3">
                            <li><a href="{{ route('storefront.products.index') }}" class="block p-2 rounded hover:bg-gray-50 text-sm {{ !request('category') ? 'font-bold' : '' }}">All Categories</a></li>
                            @foreach($categories as $cat)
                                <li><a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="block p-2 rounded hover:bg-gray-50 text-sm {{ request('category') == $cat->slug ? 'font-bold' : '' }}">{{ $cat->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    
                    {{-- Mobile Price --}}
                    <div>
                        <h4 class="font-bold text-sm uppercase tracking-widest mb-4">Price</h4>
                        <form action="{{ route('storefront.products.index') }}" method="GET" class="space-y-4">
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            <div class="flex gap-2">
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" class="w-full bg-gray-50 border-transparent rounded p-3 text-sm">
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" class="w-full bg-gray-50 border-transparent rounded p-3 text-sm">
                            </div>
                            <button type="submit" class="w-full bg-black text-white py-3 text-sm font-bold uppercase rounded">Apply Price</button>
                        </form>
                    </div>
                </div>
                
                <div class="p-6 border-t border-gray-100">
                    <button @click="mobileFiltersOpen = false" class="w-full btn-primary bg-teal-600 text-white py-4 font-bold uppercase tracking-widest hover:bg-teal-700 transition">View Results</button>
                </div>
            </div>
        </div>
    </div>

@endsection
