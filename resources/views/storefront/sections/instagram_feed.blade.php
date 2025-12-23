@php
    $settings = $section->settings ?? [];
    $title = $settings['title'] ?? 'From Instagram';
    $images = $instagramImages ?? collect();
@endphp

<section class="py-12 bg-gray-50 from-gray-50 to-white" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-8">{{ $title }}</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($images as $image)
                <a href="{{ $image->link ?? '#' }}" target="_blank" class="relative overflow-hidden group rounded-lg aspect-square cursor-pointer block">
                    <img src="{{ $image->image_url ?? $image }}" alt="Instagram" class="w-full h-full object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center text-white">
                        <i class="fab fa-instagram text-3xl"></i>
                    </div>
                </a>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="#" class="inline-flex items-center gap-2 text-gray-600 hover:text-primary font-bold transition">
                <i class="fab fa-instagram"></i> Follow us on Instagram
            </a>
        </div>
    </div>
</section>
