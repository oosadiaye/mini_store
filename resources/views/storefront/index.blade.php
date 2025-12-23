@extends('storefront.layout')

@section('content')
<!-- Hero Section -->
<!-- Hero Section -->
<div class="relative bg-white overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-secondary/10"></div>
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-primary/20 blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-secondary/20 blur-3xl opacity-50"></div>
    </div>
    
    <div class="container mx-auto px-4 py-20 md:py-32 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <!-- Text Content -->
            <div class="text-center md:text-left space-y-6">
                <span class="inline-block py-1 px-3 rounded-full bg-primary/10 text-primary text-sm font-bold tracking-wide uppercase mb-2">
                    New Collection
                </span>
                <h1 class="text-5xl md:text-7xl font-bold text-gray-900 leading-tight">
                    Discover <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Excellence</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-lg mx-auto md:mx-0 leading-relaxed">
                    Explore our curated selection of premium products designed to elevate your lifestyle. Unbeatable quality, delivered to your door.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start pt-4">
                    <a href="{{ route('storefront.products') }}" class="inline-flex justify-center items-center px-8 py-4 bg-primary text-white rounded-xl font-semibold hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-1 transition duration-300">
                        Shop Now
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                    <a href="#featured" class="inline-flex justify-center items-center px-8 py-4 bg-white text-gray-800 border border-gray-200 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-300 transition duration-300">
                        View Featured
                    </a>
                </div>
            </div>
            
            <!-- Hero Image/Banners -->
            <div class="hidden md:block relative h-[500px] w-full">
                @if(isset($banners['home_hero']) && $banners['home_hero']->count() > 0)
                    <!-- Alpine.js Carousel -->
                    <div x-data="{ activeSlide: 0, slides: {{ $banners['home_hero']->count() }}, interval: null }" 
                         x-init="interval = setInterval(() => { activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1 }, 5000)"
                         class="relative h-full w-full rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                        
                        @foreach($banners['home_hero'] as $index => $banner)
                            <div x-show="activeSlide === {{ $index }}" 
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 transform scale-105"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-300"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute inset-0 w-full h-full">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/20"></div>
                                
                                <div class="absolute bottom-10 left-10 text-white max-w-lg">
                                    <h3 class="text-3xl font-bold mb-2">{{ $banner->title }}</h3>
                                    @if($banner->description)
                                        <p class="text-lg mb-4 opacity-90">{{ $banner->description }}</p>
                                    @endif
                                    @if($banner->link)
                                        <a href="{{ $banner->link }}" class="inline-block bg-white text-gray-900 px-6 py-2 rounded-lg font-bold hover:bg-primary hover:text-white transition">
                                            {{ $banner->button_text ?? 'Shop Now' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Dots -->
                         <div class="absolute bottom-4 right-4 flex space-x-2">
                            <template x-for="i in slides">
                                <button @click="activeSlide = i - 1; clearInterval(interval)" 
                                        :class="activeSlide === i - 1 ? 'bg-white w-8' : 'bg-white/50 w-2'"
                                        class="h-2 rounded-full transition-all duration-300"></button>
                            </template>
                        </div>
                    </div>
                @else
                    <!-- Fallback to Settings Hero or Default -->
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white transform rotate-3 hover:rotate-0 transition duration-500 h-full">
                        @if(isset(tenant()->data['hero_banner']))
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center bg-cover bg-center" style="background-image: url('{{ route('tenant.media', ['path' => tenant()->data['hero_banner']]) }}')">
                                <div class="absolute inset-0 bg-black/10"></div>
                            </div>
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center bg-[url('https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&q=80&w=800')] bg-cover bg-center">
                                <div class="absolute inset-0 bg-black/10"></div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Floating Card (Only on default/static hero) -->
                    <div class="absolute -bottom-10 -left-10 bg-white/90 backdrop-blur-xl p-6 rounded-2xl shadow-xl border border-white/50 max-w-xs animate-bounce delay-1000 duration-[3000ms]">
                        <div class="flex items-center gap-4">
                            <div class="bg-green-100 p-3 rounded-full text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Premium Quality</p>
                                <p class="text-sm text-gray-500">Dozens of 5-star reviews</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(isset($banners['home_top']) && $banners['home_top']->count() > 0)
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-{{ min($banners['home_top']->count(), 3) }} gap-6">
            @foreach($banners['home_top'] as $banner)
                <a href="{{ $banner->link ?? '#' }}" class="group relative overflow-hidden rounded-xl shadow-sm hover:shadow-lg transition">
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
                </a>
            @endforeach
        </div>
    </div>
@endif

<!-- Featured Categories -->
@if($categories->count() > 0)
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Shop by Category</h2>
        <a href="{{ route('storefront.search') }}" class="text-primary text-sm font-semibold hover:underline">View All Categories →</a>
    </div>
    
    <!-- Carousel / Slider -->
    <div class="flex overflow-x-auto pb-4 gap-4 snap-x hide-scrollbar">
        @foreach($categories as $category)
            <a href="{{ route('storefront.category', $category) }}" class="group block flex-shrink-0 snap-start">
                <div class="w-32 md:w-40 flex flex-col items-center">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full overflow-hidden shadow-sm border border-gray-200 group-hover:shadow-md group-hover:border-primary transition duration-300">
                        @if($category->image)
                            <img src="{{ route('tenant.media', ['path' => $category->image]) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-300 text-2xl group-hover:bg-primary/5 transition">
                                <span class="group-hover:scale-110 transition duration-300">📦</span>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-medium text-sm text-gray-900 text-center mt-3 group-hover:text-primary transition line-clamp-1">{{ $category->name }}</h3>
                    <!-- <p class="text-xs text-gray-400 text-center">{{ $category->products_count }} items</p> -->
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- Middle Banners -->
@if(isset($banners['home_middle']) && $banners['home_middle']->count() > 0)
    <div class="container mx-auto px-4 py-8">
        <div class="rounded-2xl overflow-hidden shadow-lg relative h-64 md:h-80 group">
             @php $middle = $banners['home_middle']->first(); @endphp
             <img src="{{ $middle->image_url }}" alt="{{ $middle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
             <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center p-12">
                 <div class="text-white max-w-md">
                     <h3 class="text-3xl md:text-4xl font-bold mb-4">{{ $middle->title }}</h3>
                     <p class="text-lg mb-6 text-gray-200">{{ $middle->description }}</p>
                     @if($middle->link)
                         <a href="{{ $middle->link }}" class="inline-block bg-white text-gray-900 px-8 py-3 rounded-full font-bold hover:bg-primary hover:text-white transition">
                             {{ $middle->button_text ?? 'Discover More' }}
                         </a>
                     @endif
                 </div>
             </div>
        </div>
    </div>
@endif

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<div class="bg-white py-16">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Featured Products</h2>
            <a href="{{ route('storefront.products') }}" class="text-primary hover:underline font-medium">View All →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredProducts as $product)
                <div class="bg-white rounded-xl border border-gray-300 overflow-hidden hover:shadow-2xl transition duration-300 group flex flex-col h-full relative">
                    <a href="{{ route('storefront.product.show', $product) }}" class="relative block overflow-hidden aspect-[4/5]">
                        @if($product->primaryImage())
                            <img src="{{ $product->primaryImage()->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-gray-50 flex items-center justify-center text-gray-300 text-5xl">
                                📷
                            </div>
                        @endif
                         @if($product->compare_at_price && $product->compare_at_price > $product->price)
                            <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">SALE</span>
                        @endif
                        
                        <!-- Quick Add Overlay -->
                        <div class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition duration-300">
                             <button onclick="addToCart({{ $product->id }})" class="w-full bg-white text-gray-900 border border-gray-300 py-3 rounded-lg font-bold hover:bg-gray-900 hover:text-white transition shadow-lg">
                                Quick Add
                            </button>
                        </div>
                    </a>
                    
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex-1">
                             <p class="text-xs text-gray-500 mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            <a href="{{ route('storefront.product.show', $product) }}">
                                <h3 class="font-bold text-lg text-gray-900 mb-2 group-hover:text-primary transition line-clamp-2">{{ $product->name }}</h3>
                            </a>
                        </div>
                        
                        <div class="flex items-end justify-between mt-4">
                            <div>
                                <span class="text-xl font-bold text-primary">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($product->price, 2) }}</span>
                                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                    <span class="text-sm text-gray-400 line-through ml-2">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($product->compare_at_price, 2) }}</span>
                                @endif
                            </div>
                            <div class="text-yellow-400 text-sm">
                                ★★★★★ <span class="text-gray-300">(0)</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- New Arrivals -->
@if($newProducts->count() > 0)
<div class="container mx-auto px-4 py-16">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">New Arrivals</h2>
        <a href="{{ route('storefront.products') }}" class="text-primary hover:underline">View All →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($newProducts as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                <a href="{{ route('storefront.product.show', $product) }}" class="relative">
                    <span class="absolute top-2 left-2 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold z-10">New</span>
                    @if($product->primaryImage())
                        <img src="{{ $product->primaryImage()->url }}" alt="{{ $product->name }}" class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center text-gray-400 text-6xl">
                            📦
                        </div>
                    @endif
                </a>
                <div class="p-4">
                    <a href="{{ route('storefront.product.show', $product) }}">
                        <h3 class="font-semibold text-gray-800 mb-2 group-hover:text-primary transition">{{ $product->name }}</h3>
                    </a>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-primary">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($product->price, 2) }}</span>
                    </div>
                    <button onclick="addToCart({{ $product->id }})" class="mt-4 w-full bg-primary text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                        Add to Cart
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Footer Banners -->
@if(isset($banners['footer']) && $banners['footer']->count() > 0)
    <div class="container mx-auto px-4 pb-16">
        @foreach($banners['footer'] as $banner)
             <a href="{{ $banner->link ?? '#' }}" class="block relative rounded-xl overflow-hidden h-40 md:h-56 group mb-6 last:mb-0">
                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                     <span class="border-2 border-white text-white px-6 py-2 rounded-full font-bold tracking-widest uppercase transform scale-90 group-hover:scale-100 transition">
                         {{ $banner->button_text ?? 'Visit' }}
                     </span>
                </div>
            </a>
        @endforeach
    </div>
@endif

<!-- Features -->
<div class="bg-gray-100 py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-5xl mb-4">🚚</div>
                <h3 class="font-semibold text-gray-800 mb-2">Free Shipping</h3>
                <p class="text-gray-600">On orders over {{ tenant('data')['currency_symbol'] ?? '₦' }}50</p>
            </div>
            <div class="text-center">
                <div class="text-5xl mb-4">🔒</div>
                <h3 class="font-semibold text-gray-800 mb-2">Secure Payment</h3>
                <p class="text-gray-600">100% secure transactions</p>
            </div>
            <div class="text-center">
                <div class="text-5xl mb-4">↩️</div>
                <h3 class="font-semibold text-gray-800 mb-2">Easy Returns</h3>
                <p class="text-gray-600">30-day return policy</p>
            </div>
            <div class="text-center">
                <div class="text-5xl mb-4">💬</div>
                <h3 class="font-semibold text-gray-800 mb-2">24/7 Support</h3>
                <p class="text-gray-600">Always here to help</p>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection
