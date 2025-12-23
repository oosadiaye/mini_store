@php
    $settings = $section->settings ?? [];
    
    // Default alignment
    $alignment = $settings['alignment'] ?? 'center';
    $alignClass = match($alignment) {
        'left' => 'text-left items-start',
        'right' => 'text-right items-end',
        default => 'text-center items-center',
    };

    // Advanced Style Settings (excluding padding - handled by Page Builder wrapper)
    $styles = [];
    if (!empty($settings['margin_top'])) $styles[] = "margin-top: {$settings['margin_top']}";
    if (!empty($settings['margin_bottom'])) $styles[] = "margin-bottom: {$settings['margin_bottom']}";
    if (!empty($settings['background_color'])) $styles[] = "background-color: {$settings['background_color']}";
    if (!empty($settings['background_image'])) {
        $styles[] = "background-image: url('{$settings['background_image']}')";
        $styles[] = "background-size: cover";
        $styles[] = "background-position: center";
    }
    
    // Min Height logic
    $minHeight = $settings['min_height'] ?? '450';
    $styles[] = "min-height: {$minHeight}px";

    // External Dynamic Styles (e.g. from Page Builder wrapper)
    if(isset($inlineStyles)) {
        $styles[] = $inlineStyles;
    }
    
    // Defaults if no advanced styling is present
    $classes = "relative w-full flex flex-col justify-center {$alignClass} bg-cover bg-center overflow-hidden";
    
    // Container
    $containerWidth = $settings['container_width'] ?? 'container'; // 'container' or 'full'
    $containerClass = $containerWidth === 'full' ? 'w-full px-4' : 'container mx-auto px-6';
@endphp

<section id="{{ $section_id ?? '' }}" class="{{ $classes }}" style="{{ implode('; ', $styles) }}">
    
    {{-- Background Image (Fallback or Overlay Target) --}}
    @if(empty($settings['background_image']) && empty($settings['background_color']))
         <div class="absolute inset-0 bg-cover bg-center opacity-50" style="background-image: url('https://images.unsplash.com/photo-1522204523234-8729aa6e3d5f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');"></div>
    @endif
    
    {{-- Overlay: Always render if color/opacity is defined, or default to standard overlay --}}
    @php
        $overlayColor = $settings['overlay_color'] ?? '#000000';
        $overlayOpacity = ($settings['overlay_opacity'] ?? 50) / 100;
    @endphp
    <div class="absolute inset-0 transition-all duration-300" style="background-color: {{ $overlayColor }}; opacity: {{ $overlayOpacity }}; pointer-events: none;"></div>
    
    <div class="relative z-10 {{ $containerClass }} text-white h-full flex flex-col justify-center">
        @php
            // Determine Column Layout
            // Default to 1 column. If 'foreground_image' exists, default to 2.
            // If user explicitly sets 'grid_cols_desktop', use that.
            
            $colsDesktop = $settings['grid_cols_desktop'] ?? (empty($settings['foreground_image']) ? 1 : 2);
            $colsMobile = $settings['grid_cols_mobile'] ?? 1;
            
            $gridClass = "grid grid-cols-{$colsMobile} md:grid-cols-{$colsDesktop} items-center gap-8 md:gap-16";
        @endphp

        <div class="{{ $gridClass }}">
            {{-- Content Column --}}
            <div class="{{ $alignClass == 'items-center' && $colsDesktop == 1 ? 'text-center mx-auto max-w-4xl' : ($alignClass == 'items-center' ? 'text-center md:text-left' : ($alignClass == 'items-end' ? 'text-right' : 'text-left')) }}">
                 <h2 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                    {{ $section->title }}
                </h2>
                @if($section->content)
                <p class="text-lg md:text-xl mb-8 text-white/90">
                    {{ $section->content }}
                </p>
                @endif
                
                @if(!empty($settings['button_text']))
                <a href="{{ $settings['button_link'] ?? '#' }}" class="inline-block bg-white text-black font-semibold px-8 py-3 rounded-full hover:bg-gray-100 transition shadow-lg transform hover:-translate-y-1">
                    {{ $settings['button_text'] }}
                </a>
                @endif
            </div>
            
            {{-- Second Column (Foreground Image OR Placeholder if 2 cols forced) --}}
            @if($colsDesktop >= 2)
                <div class="relative h-64 md:h-auto w-full flex justify-center {{ $alignClass == 'items-end' ? 'md:order-first' : '' }}">
                    @if(!empty($settings['foreground_image']))
                        <img src="{{ $settings['foreground_image'] }}" alt="Hero Image" class="max-h-[400px] w-auto object-contain drop-shadow-2xl rounded-lg transform hover:scale-105 transition duration-500">
                    @elseif($colsDesktop == 2)
                        {{-- Placeholder for 2nd column if no image but 2 cols requested --}}
                        <div class="border-2 border-dashed border-white/20 rounded-lg w-full h-64 flex items-center justify-center text-white/40">
                            <span>2nd Column Area</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
