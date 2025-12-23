@php
    $settings = $section->settings ?? [];
    $paddingTop = $settings['padding_top'] ?? '2rem';
    $paddingBottom = $settings['padding_bottom'] ?? '2rem';
    $bgColor = $settings['background_color'] ?? 'transparent';
@endphp

<section id="{{ $section_id ?? '' }}" class="custom-html-section" style="background-color: {{ $bgColor }};">
    <div class="{{ ($settings['container_width'] ?? 'container') === 'full' ? 'w-full' : 'container mx-auto px-4' }}">
        @if(!empty($section->content))
            {!! $section->content !!}
        @else
            <div class="p-8 text-center text-gray-400 border-2 border-dashed border-gray-300 rounded">
                Custom HTML Section (Edit content to add HTML)
            </div>
        @endif
    </div>
</section>
