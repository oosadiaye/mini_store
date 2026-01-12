@props(['config', 'settings' => [], 'content' => []])
@php
    $heightClass = match($settings['hero_height'] ?? 'large') {
        'full' => 'min-h-screen',
        'large' => 'h-[500px]',
        'small' => 'h-[300px]',
        default => 'h-[500px]'
    };
    
    // Fallback to config if content not injected
    $title = $content['hero']['title'] ?? "Welcome to " . ($config->store_name ?? 'Our Store');
    $subtitle = $content['hero']['subtitle'] ?? "Discover our premium selection of curated products designed just for you.";
    $btnText = $content['hero']['button_text'] ?? "Shop Collection";
@endphp
<div class="bg-gray-900 relative {{ $heightClass }} flex items-center">
    <!-- Overlay/Image Placeholder -->
    <div class="absolute inset-0 bg-gray-800 opacity-50 z-10"></div>
    <!-- Dynamic Placeholder BG if no real image -->
    <div class="absolute inset-0 bg-gradient-to-r from-[color:var(--brand-color)] to-gray-900 opacity-90 z-0"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 text-white text-center sm:text-left">
        <h1 class="text-5xl font-bold tracking-tight mb-4">
            <x-storefront.editable path="content.hero.title">
                {{ $title }}
            </x-storefront.editable>
        </h1>
        <p class="text-xl text-gray-200 mb-8 max-w-2xl">
            <x-storefront.editable path="content.hero.subtitle" type="textarea">
                {{ $subtitle }}
            </x-storefront.editable>
        </p>
        <a href="#featured" class="inline-block bg-white text-gray-900 px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition">
            <x-storefront.editable path="content.hero.button_text">
                {{ $btnText }}
            </x-storefront.editable>
        </a>
    </div>
</div>
