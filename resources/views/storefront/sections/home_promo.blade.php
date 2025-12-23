@php $breakout = isset($banners['home_middle']) ? $banners['home_middle']->first() : null; @endphp
@if($breakout)
<section class="py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-purple-900 to-indigo-900"></div>
    <div class="absolute -right-20 -top-20 w-[500px] h-[500px] bg-white/5 rounded-full blur-3xl"></div>
    <div class="container mx-auto max-w-7xl px-4 relative z-10 flex flex-col md:flex-row items-center gap-16">
        <div class="md:w-1/2 order-2 md:order-1 text-center md:text-left text-white">
            <span class="bg-white/20 text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 inline-block">Editor's Pick</span>
            <h2 class="text-5xl lg:text-7xl font-serif font-black mb-6 leading-tight">{{ $breakout->title }}</h2>
            <p class="text-xl text-white/80 mb-10 font-medium leading-relaxed max-w-xl">{{ $breakout->description }}</p>
            @if($breakout->link)
                <a href="{{ $breakout->link }}" class="inline-block bg-white text-purple-900 px-10 py-4 font-bold rounded-full uppercase tracking-wide hover:shadow-2xl hover:scale-105 transition transform h-btn">{{ $breakout->button_text ?? 'Shop Collection' }}</a>
            @endif
        </div>
            <div class="md:w-1/2 order-1 md:order-2">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-tr from-pink-500 to-yellow-500 rounded-3xl transform rotate-3 scale-105 blur-lg opacity-70"></div>
                <img src="{{ $breakout->image_url }}" class="relative w-full h-auto rounded-3xl shadow-2xl transform hover:rotate-1 transition duration-500 border-4 border-white/20" alt="Curated Collection">
            </div>
        </div>
    </div>
</section>
@else
<!-- Fallback Breakout (Minimal) -->
<div class="bg-indigo-900 py-24 text-white text-center">
    <h2 class="text-4xl font-serif font-bold">Discover More</h2>
    <p class="text-xl mt-4 text-white/80">Explore our curated collections.</p>
</div>
@endif
