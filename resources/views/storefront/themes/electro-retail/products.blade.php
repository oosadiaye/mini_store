@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', 'Shop')

@section('content')
    
    {{-- Shop Header --}}
    <div class="bg-gray-100 border-b border-gray-200 mb-8">
        <div class="container-custom py-8">
            <h1 class="font-heading font-bold text-3xl text-electro-dark mb-2">Shop All Products</h1>
             <div class="flex items-center text-xs text-gray-500 gap-2">
                <a href="{{ route('storefront.home') }}" class="hover:text-electro-blue">Home</a>
                <span>/</span>
                <span>Shop</span>
            </div>
        </div>
    </div>

    <div class="container-custom grid grid-cols-1 lg:grid-cols-4 gap-8 mb-16" x-data="{ mobileFiltersOpen: false }">
        
        {{-- Filters (Sidebar) --}}
        <aside class="hidden lg:block space-y-8 pr-4">
             {{-- Categories --}}
             <div>
                <h4 class="font-heading font-bold text-lg mb-4 border-l-4 border-electro-blue pl-3">Categories</h4>
                <div class="space-y-2 text-sm text-gray-600">
                    <a href="{{ route('storefront.products.index') }}" class="block {{ !request('category') ? 'text-electro-blue font-bold pl-2 border-l-2 border-electro-blue' : 'hover:text-electro-blue hover:pl-2 hover:border-l-2 hover:border-gray-200 transition-all border-l-2 border-transparent' }}">All Categories</a>
                    @foreach(\App\Models\Category::active()->get() as $cat)
                        <a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="block {{ request('category') == $cat->slug ? 'text-electro-blue font-bold pl-2 border-l-2 border-electro-blue' : 'hover:text-electro-blue hover:pl-2 hover:border-l-2 hover:border-gray-200 transition-all border-l-2 border-transparent' }}">
                            {{ $cat->name }} <span class="text-xs text-gray-400">({{ $cat->products->count() }})</span>
                        </a>
                    @endforeach
                </div>
             </div>

             {{-- Price Filter --}}
             <div>
                <h4 class="font-heading font-bold text-lg mb-4 border-l-4 border-electro-blue pl-3">Price</h4>
                <div class="space-y-2 text-sm text-gray-600">
                     <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="rounded border-gray-300 text-electro-blue focus:ring-electro-blue">
                        <span class="group-hover:text-electro-blue transition">Under $50</span>
                     </label>
                     <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="rounded border-gray-300 text-electro-blue focus:ring-electro-blue">
                        <span class="group-hover:text-electro-blue transition">$50 - $100</span>
                     </label>
                     <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="rounded border-gray-300 text-electro-blue focus:ring-electro-blue">
                        <span class="group-hover:text-electro-blue transition">$100 - $300</span>
                     </label>
                     <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="rounded border-gray-300 text-electro-blue focus:ring-electro-blue">
                        <span class="group-hover:text-electro-blue transition">$300+</span>
                     </label>
                </div>
             </div>

              {{-- Brand --}}
              <div>
                <h4 class="font-heading font-bold text-lg mb-4 border-l-4 border-electro-blue pl-3">Brands</h4>
                <div class="space-y-2 text-sm text-gray-600">
                    @foreach(\App\Models\Brand::take(5)->get() as $brand)
                         <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" class="rounded border-gray-300 text-electro-blue focus:ring-electro-blue">
                            <span class="group-hover:text-electro-blue transition">{{ $brand->name }}</span>
                         </label>
                    @endforeach
                </div>
             </div>

        </aside>

        {{-- Mobile Filter Drawer --}}
        <div x-show="mobileFiltersOpen" class="fixed inset-0 z-50 flex lg:hidden bg-black/50 backdrop-blur-sm" style="display: none;">
             <div class="bg-white w-3/4 max-w-sm ml-auto h-full shadow-2xl flex flex-col" @click.away="mobileFiltersOpen = false">
                <div class="p-4 bg-electro-dark text-white flex justify-between items-center">
                    <span class="font-heading font-bold text-xl">Filters</span>
                    <button @click="mobileFiltersOpen = false"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                 <div class="p-6 flex-1 overflow-y-auto space-y-8">
                     {{-- Cloned Category List --}}
                     <div>
                        <h4 class="font-heading font-bold text-lg mb-4 text-electro-blue">Categories</h4>
                         <div class="space-y-2 text-sm text-gray-600">
                            <a href="{{ route('storefront.products.index') }}" class="block {{ !request('category') ? 'text-electro-blue font-bold pl-2 border-l-2 border-electro-blue' : 'hover:text-electro-blue hover:pl-2 hover:border-l-2 hover:border-gray-200 transition-all border-l-2 border-transparent' }}">All Categories</a>
                            @foreach(\App\Models\Category::active()->get() as $cat)
                                <a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="block {{ request('category') == $cat->slug ? 'text-electro-blue font-bold pl-2 border-l-2 border-electro-blue' : 'hover:text-electro-blue hover:pl-2 hover:border-l-2 hover:border-gray-200 transition-all border-l-2 border-transparent' }}">
                                    {{ $cat->name }} <span class="text-xs text-gray-400">({{ $cat->products->count() }})</span>
                                </a>
                            @endforeach
                         </div>
                     </div>
                 </div>
                 <div class="p-4 border-t border-gray-100">
                     <button @click="mobileFiltersOpen = false" class="block w-full bg-electro-blue text-white font-bold uppercase py-3 rounded text-center">Show Results</button>
                 </div>
             </div>
        </div>

        {{-- Product Grid Area --}}
        <div class="lg:col-span-3">
            
            {{-- Toolbar --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 bg-white border border-gray-100 p-3 rounded-lg gap-4">
                <div class="flex items-center gap-4">
                     <button @click="mobileFiltersOpen = true" class="lg:hidden flex items-center gap-2 text-sm font-bold uppercase text-electro-blue border border-electro-blue px-4 py-2 rounded hover:bg-electro-blue hover:text-white transition">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                         Filters
                     </button>
                    <span class="text-sm text-gray-500 font-bold hidden md:inline">Found {{ $products->total() }} Products</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <form id="sortForm" method="GET" class="flex items-center gap-2">
                        @foreach(request()->except('sort') as $key => $val)
                             <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <span class="text-xs text-gray-500 font-bold uppercase hidden md:inline">Sort By:</span>
                        <select name="sort" onchange="document.getElementById('sortForm').submit()" class="text-sm border-gray-200 rounded focus:ring-electro-blue focus:border-electro-blue py-1.5 pl-3 pr-8">
                             <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                             <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                             <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </form>
                </div>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                @forelse($products as $product)
                    @include('storefront.themes.electro-retail.components.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full py-20 text-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <h3 class="font-heading font-bold text-xl text-gray-400">No products found</h3>
                        <p class="text-gray-400 mb-6">Try adjusting your filters or search query.</p>
                        <a href="{{ route('storefront.products.index') }}" class="inline-block bg-electro-blue text-white px-6 py-2 rounded font-bold uppercase text-sm hover:bg-blue-600 transition">Clear Filters</a>
                    </div>
                @endforelse
            </div>

             {{-- Pagination --}}
            <div class="mt-12">
                {{ $products->appends(request()->query())->links() }}
            </div>

        </div>

    </div>

@endsection
