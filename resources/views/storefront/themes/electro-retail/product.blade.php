@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', $product->name)

@section('content')

    {{-- Breadcrumbs (Tech Style) --}}
    <div class="bg-gray-100 border-b border-gray-200 py-3 text-xs text-gray-500">
        <div class="container-custom flex items-center gap-2">
            <a href="{{ route('storefront.home') }}" class="hover:text-electro-blue">Home</a>
            <span>/</span>
            <a href="{{ route('storefront.products.index') }}" class="hover:text-electro-blue">Shop</a>
            <span>/</span>
            @if($product->category)
                <a href="{{ route('storefront.products.index', ['category' => $product->category->slug]) }}" class="hover:text-electro-blue">{{ $product->category->name }}</a>
                <span>/</span>
            @endif
            <span class="text-gray-800 font-bold truncate max-w-xs">{{ $product->name }}</span>
        </div>
    </div>

    <div class="container-custom py-8 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            
            {{-- Product Gallery --}}
            <div x-data="{ activeImage: '{{ $product->primary_image }}' }" class="space-y-4">
                {{-- Main Image --}}
                <div class="aspect-square bg-white border border-gray-200 rounded-xl overflow-hidden flex items-center justify-center p-8 relative group">
                    <img :src="activeImage" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain transition-transform duration-500 cursor-zoom-in" x-on:mousemove="
                        $el.style.transformOrigin = (($event.offsetX / $el.offsetWidth) * 100) + '% ' + (($event.offsetY / $el.offsetHeight) * 100) + '%'; 
                        $el.style.transform = 'scale(1.5)';
                    " x-on:mouseleave="$el.style.transform = 'scale(1)';">
                    
                    {{-- Badges --}}
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        @if($product->price < $product->compare_price)
                            <span class="bg-electro-neon text-electro-dark text-xs font-bold px-3 py-1 uppercase tracking-wide rounded shadow-sm">-{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%</span>
                        @endif
                    </div>
                </div>

                {{-- Thumbnails --}}
                <div class="flex gap-4 overflow-x-auto pb-2">
                    <button @click="activeImage = '{{ $product->primary_image }}'" class="w-20 h-20 flex-shrink-0 border-2 rounded-lg p-2 bg-white hover:border-electro-blue transition" :class="activeImage === '{{ $product->primary_image }}' ? 'border-electro-blue' : 'border-gray-200'">
                        <img src="{{ $product->primary_image }}" class="w-full h-full object-contain">
                    </button>
                    @foreach($product->images as $image)
                        <button @click="activeImage = '{{ $image->url }}'" class="w-20 h-20 flex-shrink-0 border-2 rounded-lg p-2 bg-white hover:border-electro-blue transition" :class="activeImage === '{{ $image->url }}' ? 'border-electro-blue' : 'border-gray-200'">
                            <img src="{{ $image->url }}" class="w-full h-full object-contain">
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Info --}}
            <div>
                {{-- Brand/Category --}}
                <div class="text-xs font-bold uppercase tracking-widest text-electro-blue mb-2">
                    {{ $product->category->name ?? 'Electronics' }}
                </div>

                <h1 class="font-heading font-bold text-3xl md:text-4xl text-electro-dark mb-4 leading-tight">{{ $product->name }}</h1>

                {{-- Rating --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="flex text-yellow-400 text-sm">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="text-sm text-gray-500 font-medium">4.8 (124 Reviews)</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-sm text-green-600 font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        In Stock
                    </span>
                </div>

                {{-- Tech Highlights --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-8 border border-gray-100">
                    <h4 class="font-heading font-bold text-sm mb-3 uppercase">Key Highlights</h4>
                    <ul class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-electro-blue rounded-full"></div> High Performance Chipset</li>
                         <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-electro-blue rounded-full"></div> 2 Year Official Warranty</li>
                         <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-electro-blue rounded-full"></div> Fast Charging Support</li>
                         <li class="flex items-center gap-2"><div class="w-1.5 h-1.5 bg-electro-blue rounded-full"></div> Premium Build Quality</li>
                    </ul>
                </div>

                {{-- Pricing --}}
                <div class="mb-8">
                    @if($product->compare_price > $product->price)
                         <div class="text-lg text-gray-400 line-through font-bold mb-1">{{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format($product->compare_price, 2) }}</div>
                    @endif
                    <div class="text-4xl font-heading font-bold text-electro-blue flex items-center gap-2">
                        {{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format($product->price, 2) }}
                        <span class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded">+Tax included</span>
                    </div>
                </div>

                {{-- Add to Cart --}}
                <form action="{{ route('storefront.cart.add', $product) }}" method="POST" class="flex items-center gap-4 mb-8">
                    @csrf
                    <div class="w-24">
                        <input type="number" name="quantity" value="1" min="1" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue font-bold text-center py-3">
                    </div>
                    <button type="submit" class="flex-1 bg-electro-blue text-white font-heading font-bold uppercase text-lg py-3 px-8 rounded shadow-lg hover:bg-blue-600 hover:shadow-xl transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        Add to Cart
                    </button>
                    <button type="button" class="p-3 border border-gray-300 rounded hover:bg-gray-50 text-gray-500 hover:text-red-500 transition">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </button>
                </form>
                
                {{-- Secure Badges --}}
                <div class="flex items-center gap-6 text-xs text-gray-500 border-t border-gray-100 pt-6">
                    <span class="flex items-center gap-1"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Secure Payment</span>
                    <span class="flex items-center gap-1"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Official Vendor</span>
                    <span class="flex items-center gap-1"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Fast Dispatch</span>
                </div>
            </div>
        </div>
        
        {{-- Tabs --}}
        <div class="mt-16" x-data="{ activeTab: 'description' }">
            <div class="flex border-b border-gray-200 mb-8 overflow-x-auto">
                <button @click="activeTab = 'description'" class="px-8 py-4 font-heading font-bold text-lg uppercase tracking-wide border-b-2 transition whitespace-nowrap" :class="activeTab === 'description' ? 'border-electro-blue text-electro-blue' : 'border-transparent text-gray-500 hover:text-gray-800'">Description</button>
                <button @click="activeTab = 'specs'" class="px-8 py-4 font-heading font-bold text-lg uppercase tracking-wide border-b-2 transition whitespace-nowrap" :class="activeTab === 'specs' ? 'border-electro-blue text-electro-blue' : 'border-transparent text-gray-500 hover:text-gray-800'">Specifications</button>
                <button @click="activeTab = 'reviews'" class="px-8 py-4 font-heading font-bold text-lg uppercase tracking-wide border-b-2 transition whitespace-nowrap" :class="activeTab === 'reviews' ? 'border-electro-blue text-electro-blue' : 'border-transparent text-gray-500 hover:text-gray-800'">Reviews (24)</button>
            </div>
            
            <div x-show="activeTab === 'description'" class="prose max-w-none text-gray-600">
                <p class="text-lg leading-relaxed mb-6">
                    Elevate your tech experience with the {{ $product->name }}. Designed for professionals and enthusiasts alike, this device combines cutting-edge performance with premium aesthetics. Whether you are gaming, working, or creating, the {{ $product->name }} delivers reliability and speed.
                </p>
                <h3 class="text-electro-dark font-heading font-bold text-xl mb-4">Why Choose This Product?</h3>
                <ul class="list-disc pl-5 space-y-2 mb-6">
                    <li><strong>Unmatched Speed:</strong> Powered by the latest architecture for seamless multitasking.</li>
                    <li><strong>Stunning Visuals:</strong> High-resolution display with vibrant color accuracy.</li>
                    <li><strong>All-Day Battery:</strong> Optimized power consumption to keep you going longer.</li>
                </ul>
            </div>
            
            <div x-show="activeTab === 'specs'" class="hidden" :class="{ 'hidden': activeTab !== 'specs' }">
                <table class="w-full max-w-2xl text-sm text-left">
                    <tbody class="divide-y divide-gray-100">
                        <tr class="bg-gray-50">
                            <th class="py-3 px-4 font-bold text-gray-800 w-1/3">Brand</th>
                            <td class="py-3 px-4 text-gray-600">Premium Tech</td>
                        </tr>
                        <tr>
                            <th class="py-3 px-4 font-bold text-gray-800 w-1/3">Model</th>
                            <td class="py-3 px-4 text-gray-600">X-Series Pro 2025</td>
                        </tr>
                         <tr class="bg-gray-50">
                            <th class="py-3 px-4 font-bold text-gray-800 w-1/3">Warranty</th>
                            <td class="py-3 px-4 text-gray-600">2 Years Limited</td>
                        </tr>
                        <tr>
                            <th class="py-3 px-4 font-bold text-gray-800 w-1/3">Box Contents</th>
                            <td class="py-3 px-4 text-gray-600">Device, Charger, Manual, Warranty Card</td>
                        </tr>
                    </tbody>
                </table>
            </div>

             <div x-show="activeTab === 'reviews'" class="hidden" :class="{ 'hidden': activeTab !== 'reviews' }">
                <div class="bg-gray-50 rounded-xl p-8 text-center border border-dashed border-gray-300">
                    <h3 class="font-heading font-bold text-xl text-gray-800 mb-2">Customer Reviews</h3>
                    <p class="text-gray-500 mb-6">Be the first to review this product!</p>
                    <button class="bg-electro-dark text-white px-6 py-2 rounded font-bold uppercase text-sm">Write Review</button>
                </div>
            </div>
        </div>
        
    </div>
@endsection
