@php
    $settings = $section->settings ?? [];
    $bgColor = $settings['bg_color'] ?? 'white';
@endphp

<section id="{{ $section_id ?? '' }}" class="py-16 {{ $bgColor === 'gray' ? 'bg-gray-50' : 'bg-white' }}">
    <div class="container mx-auto px-6 max-w-4xl">
        <div class="prose prose-lg mx-auto text-gray-700">
            @if($section->title)
            <h2 class="text-3xl font-bold mb-6 text-gray-900">{{ $section->title }}</h2>
            @endif
            
            <div class="whitespace-pre-wrap">{{ $section->content }}</div>
        </div>
    </div>
</section>
