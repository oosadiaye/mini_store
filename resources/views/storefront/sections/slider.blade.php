@php
    $settings = $section->settings ?? [];
    $autoplay = $settings['autoplay'] ?? true;
    $speed = $settings['speed'] ?? 3000;
    $slides = $settings['slides'] ?? [
        [
            'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920',
            'title' => 'Welcome to Our Store',
            'subtitle' => 'Discover amazing products',
            'button_text' => 'Shop Now',
            'button_link' => '/shop'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1920',
            'title' => 'New Collection',
            'subtitle' => 'Check out our latest arrivals',
            'button_text' => 'View Collection',
            'button_link' => '/shop'
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1607082349566-187342175e2f?w=1920',
            'title' => 'Special Offers',
            'subtitle' => 'Limited time deals',
            'button_text' => 'Get Deals',
            'button_link' => '/shop'
        ]
    ];
@endphp

<section class="relative w-full overflow-hidden bg-gray-900" x-data="imageSlider({{ json_encode($slides) }}, {{ $autoplay ? 'true' : 'false' }}, {{ $speed }})">
    <!-- Slides Container -->
    <div class="relative h-[500px] md:h-[600px]">
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="currentSlide === index"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform -translate-x-full"
                 class="absolute inset-0 w-full h-full">
                
                <!-- Background Image -->
                <div class="absolute inset-0 bg-cover bg-center" 
                     :style="'background-image: url(' + slide.image + ')'">
                    <div class="absolute inset-0 bg-black opacity-40"></div>
                </div>
                
                <!-- Content -->
                <div class="relative z-10 h-full flex items-center justify-center text-center px-6">
                    <div class="max-w-4xl">
                        <h2 class="text-4xl md:text-6xl font-bold text-white mb-4" x-text="slide.title"></h2>
                        <p class="text-xl md:text-2xl text-gray-200 mb-8" x-text="slide.subtitle"></p>
                        <a :href="slide.button_link" 
                           class="inline-block bg-white text-gray-900 font-semibold px-8 py-4 rounded-full hover:bg-gray-100 transition shadow-lg transform hover:-translate-y-1"
                           x-text="slide.button_text"></a>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Navigation Arrows -->
    <button @click="prevSlide()" 
            class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/80 hover:bg-white text-gray-900 p-3 rounded-full shadow-lg transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button @click="nextSlide()" 
            class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/80 hover:bg-white text-gray-900 p-3 rounded-full shadow-lg transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    
    <!-- Pagination Dots -->
    <div class="absolute bottom-6 left-0 right-0 z-20 flex justify-center gap-2">
        <template x-for="(slide, index) in slides" :key="index">
            <button @click="goToSlide(index)"
                    :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50 w-3'"
                    class="h-3 rounded-full transition-all duration-300"></button>
        </template>
    </div>
</section>

<script>
function imageSlider(slides, autoplay, speed) {
    return {
        slides: slides,
        currentSlide: 0,
        autoplay: autoplay,
        speed: speed,
        interval: null,
        
        init() {
            if (this.autoplay) {
                this.startAutoplay();
            }
        },
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
            this.resetAutoplay();
        },
        
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
            this.resetAutoplay();
        },
        
        goToSlide(index) {
            this.currentSlide = index;
            this.resetAutoplay();
        },
        
        startAutoplay() {
            this.interval = setInterval(() => {
                this.nextSlide();
            }, this.speed);
        },
        
        resetAutoplay() {
            if (this.autoplay) {
                clearInterval(this.interval);
                this.startAutoplay();
            }
        }
    }
}
</script>
