@php
    $settings = $section->settings ?? [];
    $videoUrl = $settings['video_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ';
    $thumbnail = $settings['thumbnail'] ?? 'https://images.unsplash.com/photo-1611162616475-46b635cb6868?w=1920';
    $autoplay = $settings['autoplay'] ?? false;
@endphp

<section class="py-16 bg-gray-900" x-data="{ playing: {{ $autoplay ? 'true' : 'false' }} }">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-white mb-2">{{ $section->title ?? 'Watch Our Story' }}</h2>
            <p class="text-gray-300">{{ $section->content ?? 'See what makes us special' }}</p>
        </div>
        
        <!-- Video Container -->
        <div class="max-w-5xl mx-auto">
            <div class="relative aspect-video rounded-2xl overflow-hidden shadow-2xl">
                <!-- Video Iframe -->
                <iframe x-show="playing"
                        src="{{ $videoUrl }}{{ $autoplay ? '?autoplay=1' : '' }}"
                        class="absolute inset-0 w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                </iframe>
                
                <!-- Thumbnail Overlay -->
                <div x-show="!playing" 
                     class="absolute inset-0 cursor-pointer group"
                     @click="playing = true">
                    <img src="{{ $thumbnail }}" 
                         alt="Video thumbnail"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-50 transition flex items-center justify-center">
                        <div class="bg-white rounded-full p-6 group-hover:scale-110 transition shadow-2xl">
                            <svg class="w-16 h-16 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
