@php
    $settings = $themeSettings->settings['promo_banners'] ?? [];
    
    // Default Fallbacks
    $banner1 = $settings['banner_1'] ?? [
        'title' => 'New Season',
        'subtitle' => 'Explore the latest trends.',
        'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        'cta_text' => 'Shop Now',
        'cta_url' => '/shop'
    ];
    
    $banner2 = $settings['banner_2'] ?? [
        'title' => 'The Collection',
        'subtitle' => 'Timeless pieces for every day.',
        'image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        'cta_text' => 'View Lookbook',
        'cta_url' => '/shop'
    ];
@endphp

<section class="py-16 md:py-24">
    <div class="container mx-auto px-4 md:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Banner 1 --}}
            <div class="relative aspect-[4/3] group overflow-hidden">
                <img src="{{ $banner1['image'] }}" alt="{{ $banner1['title'] }}" class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition duration-500"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-8 md:p-12 text-white">
                    <h3 class="text-3xl font-serif mb-2">{{ $banner1['title'] }}</h3>
                    <p class="mb-6 opacity-90">{{ $banner1['subtitle'] }}</p>
                    <a href="{{ $banner1['cta_url'] }}" class="inline-block border-b border-white pb-1 font-bold uppercase tracking-widest text-xs hover:text-gray-200">
                        {{ $banner1['cta_text'] }}
                    </a>
                </div>
            </div>

            {{-- Banner 2 --}}
            <div class="relative aspect-[4/3] group overflow-hidden">
                <img src="{{ $banner2['image'] }}" alt="{{ $banner2['title'] }}" class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition duration-500"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-8 md:p-12 text-white">
                    <h3 class="text-3xl font-serif mb-2">{{ $banner2['title'] }}</h3>
                    <p class="mb-6 opacity-90">{{ $banner2['subtitle'] }}</p>
                    <a href="{{ $banner2['cta_url'] }}" class="inline-block border-b border-white pb-1 font-bold uppercase tracking-widest text-xs hover:text-gray-200">
                        {{ $banner2['cta_text'] }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
