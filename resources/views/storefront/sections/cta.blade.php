@php
    $settings = $section->settings ?? [];
    $layout = $settings['layout'] ?? 'centered'; // centered, left, right
    $backgroundImage = $settings['background_image'] ?? 'https://images.unsplash.com/photo-1557821552-17105176677c?w=1920';
    $buttonText = $settings['button_text'] ?? 'Get Started';
    $buttonLink = $settings['button_link'] ?? '#';
    $secondaryButtonText = $settings['secondary_button_text'] ?? '';
    $secondaryButtonLink = $settings['secondary_button_link'] ?? '#';
    
    $alignClass = match($layout) {
        'left' => 'text-left items-start',
        'right' => 'text-right items-end',
        default => 'text-center items-center'
    };
@endphp

<section class="relative py-24 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 bg-cover bg-center" 
         style="background-image: url('{{ $backgroundImage }}')">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900/90 to-purple-900/90"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 container mx-auto px-4">
        <div class="max-w-4xl mx-auto flex flex-col {{ $alignClass }}">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                {{ $section->title ?? 'Ready to Get Started?' }}
            </h2>
            <p class="text-xl text-gray-200 mb-8 max-w-2xl">
                {{ $section->content ?? 'Join thousands of satisfied customers and experience the difference today.' }}
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-wrap gap-4 {{ $layout === 'centered' ? 'justify-center' : '' }}">
                <a href="{{ $buttonLink }}" 
                   class="inline-block bg-white text-indigo-600 font-bold px-8 py-4 rounded-full hover:bg-gray-100 transition shadow-lg transform hover:-translate-y-1">
                    {{ $buttonText }}
                    <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                
                @if($secondaryButtonText)
                <a href="{{ $secondaryButtonLink }}" 
                   class="inline-block bg-transparent border-2 border-white text-white font-bold px-8 py-4 rounded-full hover:bg-white hover:text-indigo-600 transition shadow-lg">
                    {{ $secondaryButtonText }}
                </a>
                @endif
            </div>
        </div>
    </div>
</section>
