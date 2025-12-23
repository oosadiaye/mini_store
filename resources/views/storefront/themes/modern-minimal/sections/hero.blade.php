@php
    $settings = $themeSettings->settings['hero'] ?? [];
    $title = $settings['title'] ?? 'The New Standard';
    $subtitle = $settings['subtitle'] ?? 'Elevate your everyday with our curated collection of essentials.';
    $buttonText = $settings['button_text'] ?? 'Shop Now';
    $buttonUrl = $settings['button_url'] ?? '/shop';
    $bgImage = $settings['image'] ?? 'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80';
@endphp

<section class="relative h-[80vh] flex items-center justify-center bg-gray-100 overflow-hidden">
    {{-- Background Image --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ $bgImage }}" alt="Hero Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/20"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto text-white">
        <h1 class="text-4xl md:text-6xl font-serif font-medium mb-6 animate-fade-in-up">
            {{ $title }}
        </h1>
        <p class="text-lg md:text-xl font-light mb-8 max-w-2xl mx-auto opacity-90 animate-fade-in-up animation-delay-200">
            {{ $subtitle }}
        </p>
        <a href="{{ $buttonUrl }}" class="inline-block bg-white text-black px-8 py-3 text-sm font-bold uppercase tracking-widest hover:bg-gray-100 transition duration-300 animate-fade-in-up animation-delay-400">
            {{ $buttonText }}
        </a>
    </div>
</section>
