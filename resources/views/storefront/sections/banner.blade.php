@php
    $settings = $section->settings ?? [];
@endphp

<section id="{{ $section_id ?? '' }}" class="py-12 bg-indigo-600 text-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-4">{{ $section->title }}</h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">{{ $section->content }}</p>
        
        @if(!empty($settings['button_text']))
        <a href="{{ $settings['button_link'] ?? '#' }}" class="bg-white text-indigo-600 font-bold px-8 py-3 rounded-lg shadow hover:bg-indigo-50 transition">
            {{ $settings['button_text'] }}
        </a>
        @endif
    </div>
</section>
