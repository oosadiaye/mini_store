@php
    $settings = $section->settings ?? [];
    $limit = $settings['limit'] ?? 6;
    
    // Sample testimonials - in real implementation, these would come from database
    $testimonials = $settings['testimonials'] ?? [
        [
            'name' => 'Sarah Johnson',
            'role' => 'Verified Customer',
            'image' => 'https://i.pravatar.cc/150?img=1',
            'rating' => 5,
            'content' => 'Absolutely love my purchase! The quality exceeded my expectations and shipping was super fast. Will definitely be ordering again!'
        ],
        [
            'name' => 'Michael Chen',
            'role' => 'Verified Customer',
            'image' => 'https://i.pravatar.cc/150?img=2',
            'rating' => 5,
            'content' => 'Outstanding customer service and top-notch products. This is now my go-to store for all my shopping needs.'
        ],
        [
            'name' => 'Emily Rodriguez',
            'role' => 'Verified Customer',
            'image' => 'https://i.pravatar.cc/150?img=3',
            'rating' => 4,
            'content' => 'Great experience overall! The product matches the description perfectly. Highly recommend to anyone looking for quality items.'
        ],
        [
            'name' => 'David Thompson',
            'role' => 'Verified Customer',
            'image' => 'https://i.pravatar.cc/150?img=4',
            'rating' => 5,
            'content' => 'Impressed with the attention to detail and packaging. Everything arrived in perfect condition. Five stars!'
        ],
        [
            'name' => 'Lisa Anderson',
            'role' => 'Verified Customer',
            'image' => 'https://i.pravatar.cc/150?img=5',
            'rating' => 5,
            'content' => 'Best online shopping experience I\'ve had in years. The quality is exceptional and prices are very reasonable.'
        ],
        [
            'name' => 'James Wilson',
            'role' => 'Verified Customer',
            'image' => 'https://i.pravatar.cc/150?img=6',
            'rating' => 4,
            'content' => 'Very satisfied with my purchase. The product quality is excellent and delivery was prompt. Will shop here again!'
        ],
    ];
    
    $testimonials = array_slice($testimonials, 0, $limit);
@endphp

<section class="py-16 bg-gradient-to-br from-indigo-50 to-purple-50">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $section->title ?? 'What Our Customers Say' }}</h2>
            <p class="text-gray-600">{{ $section->content ?? 'Real reviews from real customers' }}</p>
        </div>
        
        <!-- Testimonials Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
            @foreach($testimonials as $testimonial)
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition">
                <!-- Rating Stars -->
                <div class="flex gap-1 mb-4">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $testimonial['rating'])
                            <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
                
                <!-- Testimonial Content -->
                <p class="text-gray-700 mb-6 leading-relaxed">"{{ $testimonial['content'] }}"</p>
                
                <!-- Customer Info -->
                <div class="flex items-center gap-3 border-t pt-4">
                    <img src="{{ $testimonial['image'] }}" 
                         alt="{{ $testimonial['name'] }}"
                         class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <div class="font-semibold text-gray-900">{{ $testimonial['name'] }}</div>
                        <div class="text-sm text-gray-500">{{ $testimonial['role'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
