@if($banners['home_hero'] && $banners['home_hero']->count() > 0)
    @php $hero = $banners['home_hero']->first(); @endphp
    <div class="relative w-full h-[85vh] bg-gray-900 overflow-hidden flex items-center justify-center text-center group">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/80 via-purple-500/60 to-pink-500/40 mix-blend-overlay z-10"></div>
        <img src="{{ $hero->image_url }}" alt="{{ $hero->title }}" class="absolute inset-0 w-full h-full object-cover transition duration-1000 group-hover:scale-105 animate-ken-burns">
        <div class="absolute inset-0 bg-black/20 z-0"></div>
        <div class="relative z-20 px-4 max-w-6xl mx-auto text-white">
            <span class="inline-block py-1 px-3 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-sm font-bold tracking-widest uppercase mb-6 animate-fade-in-down">New Collection</span>
            <h1 class="text-6xl md:text-8xl font-serif font-black mb-6 leading-tight drop-shadow-lg animate-slide-up-delay-1">{{ $hero->title }}</h1>
            <p class="text-xl md:text-2xl text-white/90 mb-10 max-w-3xl mx-auto font-medium drop-shadow-md animate-slide-up-delay-2">{{ $hero->description }}</p>
            @if($hero->link)
                <a href="{{ $hero->link }}" class="inline-block bg-white text-indigo-900 px-12 py-5 font-bold text-lg tracking-wide uppercase hover:bg-indigo-50 hover:scale-105 hover:shadow-2xl transition duration-300 rounded-full animate-slide-up-delay-3 shadow-xl h-btn">{{ $hero->button_text ?? 'Shop Now' }}</a>
            @endif
        </div>
    </div>
@else
    <!-- Fallback Hero -->
     <div class="relative w-full h-[80vh] bg-gradient-to-br from-violet-600 via-indigo-600 to-purple-700 flex items-center justify-center text-center overflow-hidden">
         <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
            <div class="absolute top-0 -right-4 w-96 h-96 bg-yellow-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
        </div>
        <div class="relative z-10 px-4">
            <h1 class="text-7xl md:text-9xl font-serif font-black text-white mb-6 drop-shadow-2xl tracking-tight">Be Bold.</h1>
            <p class="text-2xl text-white/90 font-medium mb-12 tracking-wide">Express your style with our vibrant new collection.</p>
            <a href="{{ route('storefront.products') }}" class="inline-block bg-[#F59E0B] text-white px-12 py-5 font-black text-lg tracking-widest uppercase hover:bg-yellow-400 hover:-translate-y-1 transition duration-300 rounded-full shadow-lg transform h-btn">Shop The Look</a>
        </div>
    </div>
@endif
