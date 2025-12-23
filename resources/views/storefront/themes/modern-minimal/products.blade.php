@extends('storefront.themes.modern-minimal.layout')

@php
    $settings = \App\Models\ThemeSetting::getSettings();
    $shopSettings = $settings['shop'] ?? [];
    $title = $shopSettings['title'] ?? 'Shop';
    $subtitle = $shopSettings['subtitle'] ?? 'Browse our latest products';
    $colsDesktop = $shopSettings['layout']['desktop_columns'] ?? 4;
    $colsMobile = $shopSettings['layout']['mobile_columns'] ?? 2;
    
    // Map desktop columns to Tailwind classes
    $gridColsClass = match($colsDesktop) {
        3 => 'lg:grid-cols-3',
        5 => 'lg:grid-cols-5',
        default => 'lg:grid-cols-4',
    };
@endphp

@section('pageTitle', $title)

@section('content')
   @include('storefront.themes.modern-minimal.components.page-header', ['title' => $title, 'subtitle' => $subtitle, 'breadcrumbs' => [$title => '#']])

   <div class="container mx-auto px-4 py-8" x-data="{ mobileFiltersOpen: false }">
       
       {{-- Filter Row --}}
       <div class="flex flex-col md:flex-row justify-between items-center mb-8 border-b border-gray-100 pb-4 gap-4">
           
           {{-- Left: Filter Toggle / Category --}}
           <div class="flex items-center gap-4 w-full md:w-auto">
               <button @click="mobileFiltersOpen = true" class="md:hidden flex items-center gap-2 text-sm font-bold uppercase tracking-widest border border-gray-200 px-4 py-3 w-full justify-center">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                   Filter
               </button>

            {{-- Mobile Category Filter (horizontal) --}}
            <div class="flex space-x-2 overflow-x-auto md:hidden mt-2">
                <a href="{{ route('storefront.products.index') }}" class="text-sm {{ !request('category') ? 'font-bold' : 'text-gray-600' }} whitespace-nowrap">All</a>
                @foreach(\App\Models\Category::all() as $cat)
                    <a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="text-sm {{ request('category') == $cat->slug ? 'font-bold' : 'text-gray-600' }} whitespace-nowrap">{{ $cat->name }}</a>
                @endforeach
            </div>
            @if(!empty($shopSettings['filters']['enable_category_filter']))
            <div class="hidden md:block relative group">
                <button class="flex items-center gap-2 text-sm hover:text-gray-600 transition">
                    Category 
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="absolute top-full left-0 bg-white shadow-lg border border-gray-100 mt-2 py-2 min-w-[200px] hidden group-hover:block z-20">
                    <a href="{{ route('storefront.products.index') }}" class="block px-4 py-2 hover:bg-gray-50 text-sm">All Categories</a>
                    @foreach(\App\Models\Category::all() as $cat)
                        <a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="block px-4 py-2 hover:bg-gray-50 text-sm">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </div>
            @endif
           </div>

           {{-- Right: Sort --}}
           @if(!empty($shopSettings['filters']['enable_sorting']))
           <div class="w-full md:w-auto">
               <form id="sortForm" method="GET" class="flex items-center justify-between md:justify-end gap-2">
                   @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                   @endforeach
                   <span class="text-sm text-gray-500 md:hidden">Sort by:</span>
                   <select name="sort" onchange="document.getElementById('sortForm').submit()" class="border-none bg-transparent text-sm font-medium focus:ring-0 cursor-pointer">
                       <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                       <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                       <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                       <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                   </select>
               </form>
           </div>
           @endif
       </div>

       {{-- Product Grid --}}
       <div class="grid grid-cols-2 md:grid-cols-3 {{ $gridColsClass }} gap-x-4 gap-y-12">
            @forelse($products as $product)
                @include('storefront.themes.modern-minimal.components.product-card', ['product' => $product])
            @empty
                <div class="col-span-full py-20 text-center text-gray-500">
                    <p>No products found.</p>
                    <a href="{{ route('storefront.products.index') }}" class="text-black underline mt-2 inline-block">Clear Filters</a>
                </div>
            @endforelse
       </div>

       {{-- Pagination --}}
       <div class="mt-16 text-center">
            @if(($shopSettings['pagination']['type'] ?? 'standard') === 'load_more')
                @if($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" class="inline-block border border-black px-8 py-3 text-sm font-bold uppercase tracking-widest hover:bg-black hover:text-white transition">
                        Load More
                    </a>
                @endif
            @else
                {{ $products->appends(request()->query())->links() }}
            @endif
       </div>

       {{-- Mobile Filter Drawer --}}
       <div x-show="mobileFiltersOpen" class="fixed inset-0 z-50 flex justify-end">
           <div class="absolute inset-0 bg-black/50" @click="mobileFiltersOpen = false" x-transition.opacity></div>
           
           <div class="relative w-80 bg-white h-full shadow-xl p-6 overflow-y-auto" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
               <div class="flex justify-between items-center mb-8">
                   <h3 class="font-serif text-xl">Filters</h3>
                   <button @click="mobileFiltersOpen = false" class="text-gray-400 hover:text-black">&times;</button>
               </div>
               
               <div class="space-y-8">
                   @if(!empty($shopSettings['filters']['enable_category_filter']))
                   <div>
                       <h4 class="font-bold text-sm uppercase tracking-widest mb-4">Category</h4>
                       <ul class="space-y-3"> 
                            <li><a href="{{ route('storefront.products.index') }}" class="text-sm {{ !request('category') ? 'font-bold' : 'text-gray-600' }}">All Categories</a></li>
                            @foreach(\App\Models\Category::all() as $cat)
                            <li><a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="text-sm {{ request('category') == $cat->slug ? 'font-bold' : 'text-gray-600' }}">{{ $cat->name }}</a></li>
                            @endforeach
                       </ul>
                   </div>
                   @endif

                   @if(!empty($shopSettings['filters']['enable_price_filter']))
                   <div>
                       <h4 class="font-bold text-sm uppercase tracking-widest mb-4">Price Range</h4>
                       <form action="{{ route('storefront.products.index') }}" method="GET" class="space-y-4">
                           @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                           @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                           
                           <div class="flex gap-4">
                               <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" class="w-full bg-gray-50 border-transparent text-sm p-3">
                               <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" class="w-full bg-gray-50 border-transparent text-sm p-3">
                           </div>
                           <button type="submit" class="w-full bg-black text-white py-3 text-sm font-bold uppercase">Apply</button>
                       </form>
                   </div>
                   @endif
               </div>
           </div>
       </div>

   </div>
@endsection
