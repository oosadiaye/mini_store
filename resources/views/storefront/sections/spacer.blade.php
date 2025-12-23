@php
    $settings = $section->settings ?? [];
    $height = $settings['height'] ?? 'medium'; // small, medium, large
    $showDivider = $settings['show_divider'] ?? false;
    $backgroundColor = $settings['background_color'] ?? 'transparent';
    
    $heightClass = match($height) {
        'small' => 'h-12',
        'large' => 'h-32',
        default => 'h-24'
    };
@endphp

<section class="{{ $heightClass }} flex items-center justify-center" 
         style="background-color: {{ $backgroundColor }}">
    @if($showDivider)
    <div class="container mx-auto px-4">
        <hr class="border-t-2 border-gray-200">
    </div>
    @endif
</section>
