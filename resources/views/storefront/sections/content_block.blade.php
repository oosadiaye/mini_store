@php
    $settings = $section->settings ?? [];
    $layout = $settings['layout'] ?? 'image-left'; // image-left, image-right, full-width
    $image = $settings['image'] ?? 'https://images.unsplash.com/photo-1557821552-17105176677c?w=800';
    
    $gridClass = match($layout) {
        'image-right' => 'md:grid-cols-2',
        'image-left' => 'md:grid-cols-2',
        default => 'md:grid-cols-1'
    };
    
    $imageOrder = $layout === 'image-right' ? 'md:order-2' : '';
    $contentOrder = $layout === 'image-right' ? 'md:order-1' : '';
@endphp

<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 {{ $gridClass }} gap-12 max-w-6xl mx-auto items-center">
            <!-- Image Column -->
            @if($layout !== 'full-width')
            <div class="{{ $imageOrder }}">
                <img src="{{ $image }}" 
                     alt="{{ $section->title ?? 'Content' }}"
                     class="w-full h-auto rounded-2xl shadow-lg">
            </div>
            @endif
            
            <!-- Content Column -->
            <div class="{{ $contentOrder }} prose max-w-none">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">
                    {{ $section->title ?? 'Our Story' }}
                </h2>
                
                <div class="text-gray-700 leading-relaxed space-y-4">
                    {!! $section->content ?? '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p><p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>' !!}
                </div>
                
                @if(!empty($settings['button_text']))
                <div class="mt-8">
                    <a href="{{ $settings['button_link'] ?? '#' }}" 
                       class="inline-block bg-indigo-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-indigo-700 transition shadow-md">
                        {{ $settings['button_text'] }}
                    </a>
                </div>
                @endif
            </div>
            
            <!-- Full Width Image -->
            @if($layout === 'full-width')
            <div class="mt-8">
                <img src="{{ $image }}" 
                     alt="{{ $section->title ?? 'Content' }}"
                     class="w-full h-auto rounded-2xl shadow-lg">
            </div>
            @endif
        </div>
    </div>
</section>
