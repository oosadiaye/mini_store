<x-storefront.layout :config="$config" :menuCategories="$menuCategories" :schema="$schema">
    <x-slot name="title">About Us - {{ $config->store_name }}</x-slot>

    <!-- 1. HERO SECTION (Redesigned: Header Style, 300px Max Height) -->
    <div class="relative h-[300px] flex items-center justify-center overflow-hidden">
        <!-- Background Image with Overlay -->
        @if(!empty($aboutData['hero_image']))
            <img src="{{ Str::startsWith($aboutData['hero_image'], 'http') ? $aboutData['hero_image'] : route('tenant.media', ['tenant' => app('tenant')->slug, 'path' => $aboutData['hero_image']]) }}" 
                 alt="About Us" 
                 class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 w-full h-full bg-[#0A2540]"></div>
        @endif
        
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>
        
        <!-- Text: Centered, Bold Serif, Text Shadow -->
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-bold text-white font-serif tracking-tight drop-shadow-lg" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                {{ $aboutData['title'] ?? 'Our Story' }}
            </h1>
        </div>
    </div>

    <!-- 2. STORY SECTION (Redesigned: 2-Column Grid) -->
    <div class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                
                <!-- Left: Text & Stats -->
                <div>
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Who We Are</h2>
                    
                    <!-- Main Text (Larger, Relaxed Leading) -->
                    <div class="prose prose-lg text-gray-700 leading-relaxed font-body mb-10 text-lg">
                         {!! nl2br(e($aboutData['content'] ?? 'Wait for us to tell our story.')) !!}
                    </div>

                    <!-- Integrated Stats (Horizontal Row) -->
                    @if(!empty($aboutData['stats']))
                        <div class="flex flex-wrap gap-12 border-t border-gray-100 pt-8">
                            @foreach($aboutData['stats'] as $stat)
                                <div>
                                    <div class="text-4xl font-serif font-bold text-[#0A2540] mb-1">
                                        {{ $stat['value'] }}
                                    </div>
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                        {{ $stat['label'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right: Brand Image / Collage Placeholder -->
                <div class="relative h-full min-h-[400px]">
                    <!-- Placeholder Pattern/Graphic if no explicit secondary image is provided in schema (using hero as fallback or pattern) -->
                    <div class="absolute inset-0 bg-gray-100 rounded-2xl overflow-hidden shadow-2xl skew-y-3 transform transition-transform hover:skew-y-0 duration-500">
                        @if(!empty($aboutData['hero_image']))
                             <img src="{{ Str::startsWith($aboutData['hero_image'], 'http') ? $aboutData['hero_image'] : route('tenant.media', ['tenant' => app('tenant')->slug, 'path' => $aboutData['hero_image']]) }}" 
                                  class="w-full h-full object-cover opacity-80 scale-110" alt="Brand Aesthetics">
                        @else
                            <!-- Abstract Pattern -->
                            <div class="w-full h-full bg-[#0A2540] opacity-10" style="background-image: radial-gradient(#0A2540 1px, transparent 1px); background-size: 20px 20px;"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="font-serif text-4xl text-[#0A2540] italic opacity-20">Est. {{ date('Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- 3. MISSION SECTION (Redesigned: Dark Background, Centered, Pull Quote) -->
    <div class="relative py-24 bg-[#0A2540] overflow-hidden">
        <!-- Watermark Icon (Opacity 5%) -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
             <svg class="w-96 h-96 text-white opacity-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
             </svg>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 text-center text-white">
            
            <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-blue-200 mb-8">Our Mission</h2>
            
            <!-- Pull Quote Style -->
            <blockquote class="font-serif text-3xl md:text-5xl font-medium italic leading-tight mb-8 text-white/90">
                &ldquo;{{ $aboutData['mission_text'] ?? 'To provide the best products and service to our customers.' }}&rdquo;
            </blockquote>

            <div class="text-xl font-light text-blue-100 font-sans">
                &mdash; {{ $aboutData['mission_title'] ?? 'The Team' }}
            </div>

        </div>
    </div>

</x-storefront.layout>
