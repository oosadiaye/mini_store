@php
    $settings = $section->settings ?? [];
    $title = $settings['title'] ?? 'Latest Posts';
    $posts = $data['posts'] ?? collect(); // Note: $data usually merged? Or accessible? 
    // In StorefrontController, typically $data is extracted?
    // Let's check: return view(..., $data). So $posts is available directly.
    $posts = $posts ?? collect();
@endphp

<section class="py-12 bg-white" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 border-b pb-4">{{ $title }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group border border-gray-100">
                    <div class="relative overflow-hidden h-48">
                        <img src="{{ $post->image_url ?? 'https://via.placeholder.com/400x300' }}" alt="{{ $post->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded text-xs font-bold text-gray-600 shadow-sm">
                            {{ $post->published_at ? $post->published_at->format('d M, Y') : $post->created_at->format('d M, Y') }}
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2 text-gray-800 group-hover:text-primary transition-colors line-clamp-2">
                             <a href="{{ route('storefront.blog.show', $post->slug) }}">{{ $post->title }}</a>
                        </h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-3">{{ $post->excerpt }}</p>
                        <a href="{{ route('storefront.blog.show', $post->slug) }}" class="inline-flex items-center text-primary font-bold text-sm hover:underline">
                            Read More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
