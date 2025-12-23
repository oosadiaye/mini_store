@php
    $settings = $section->settings ?? [];
    $columns = $settings['columns'] ?? 3;
    
    // Sample gallery images - in real implementation, these would come from database
    $images = $settings['images'] ?? [
        ['url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800', 'title' => 'Product 1', 'description' => 'Premium quality product'],
        ['url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800', 'title' => 'Product 2', 'description' => 'Stylish design'],
        ['url' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=800', 'title' => 'Product 3', 'description' => 'Modern aesthetic'],
        ['url' => 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=800', 'title' => 'Product 4', 'description' => 'Elegant finish'],
        ['url' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=800', 'title' => 'Product 5', 'description' => 'Comfortable fit'],
        ['url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800', 'title' => 'Product 6', 'description' => 'Durable construction'],
        ['url' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=800', 'title' => 'Product 7', 'description' => 'Versatile use'],
        ['url' => 'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=800', 'title' => 'Product 8', 'description' => 'Premium materials'],
        ['url' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800', 'title' => 'Product 9', 'description' => 'Sleek design'],
    ];
    
    $gridClass = match($columns) {
        2 => 'md:grid-cols-2',
        3 => 'md:grid-cols-3',
        4 => 'md:grid-cols-4',
        default => 'md:grid-cols-3'
    };
@endphp

<section class="py-16 bg-white" x-data="{ lightboxOpen: false, currentImage: null }">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $section->title ?? 'Gallery' }}</h2>
            <p class="text-gray-600">{{ $section->content ?? 'Explore our collection' }}</p>
        </div>
        
        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 {{ $gridClass }} gap-4 max-w-7xl mx-auto">
            @foreach($images as $image)
            <div class="group relative aspect-square overflow-hidden rounded-lg cursor-pointer"
                 @click="lightboxOpen = true; currentImage = {{ json_encode($image) }}">
                <img src="{{ $image['url'] }}" 
                     alt="{{ $image['title'] }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                     loading="lazy">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition flex items-center justify-center">
                    <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Lightbox Modal -->
    <div x-show="lightboxOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="lightboxOpen = false"
         @keydown.escape.window="lightboxOpen = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4"
         style="display: none;">
        <button @click="lightboxOpen = false" 
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div @click.stop class="max-w-4xl w-full">
            <img :src="currentImage?.url" 
                 :alt="currentImage?.title"
                 class="w-full h-auto rounded-lg shadow-2xl">
            <div class="text-white text-center mt-4">
                <h3 class="text-2xl font-bold" x-text="currentImage?.title"></h3>
                <p class="text-gray-300" x-text="currentImage?.description"></p>
            </div>
        </div>
    </div>
</section>
