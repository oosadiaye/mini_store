@props(['category', 'brandColor' => '#0A2540'])

@php
    // Generate gradient from brand color
    $gradientStart = $brandColor;
    $gradientEnd = '#1a3a5a'; // Darker shade
@endphp

<div class="relative overflow-hidden bg-gradient-to-br from-[{{ $gradientStart }}] to-[{{ $gradientEnd }}] text-white" style="background: linear-gradient(135deg, {{ $gradientStart }} 0%, {{ $gradientEnd }} 100%);">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <circle cx="20" cy="20" r="1" fill="white"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
        </svg>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            
            <!-- Left: Title & Description -->
            <div class="space-y-4">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-[0.2em] font-bold text-white/60">Collection</p>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold leading-tight">
                        {{ $category->name }}
                    </h1>
                </div>
                
                @if($category->description)
                    <p class="text-base text-white/80 leading-relaxed max-w-xl">
                        {{ $category->description }}
                    </p>
                @else
                    <p class="text-base text-white/80 leading-relaxed max-w-xl">
                        Discover our curated selection of premium {{ strtolower($category->name) }}.
                    </p>
                @endif

                <!-- Stats or CTA -->
                <div class="flex items-center gap-6 pt-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-sm text-white/60">Premium Quality</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm text-white/60">Curated Selection</span>
                    </div>
                </div>
            </div>

            <!-- Right: Abstract Pattern / Featured Image -->
            <div class="hidden lg:block">
                <div class="relative flex justify-end">
                    <!-- Abstract Geometric Pattern -->
                    <div class="aspect-square w-48 h-48 rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 p-8 flex items-center justify-center">
                        <svg class="w-full h-full opacity-20" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <path fill="white" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.6,81.4,29.2,73.1,42.8C64.8,56.4,53.8,69,40.4,76.8C27,84.6,11.2,87.6,-4.8,85.9C-20.8,84.2,-41.6,77.8,-56.2,66.8C-70.8,55.8,-79.2,40.2,-83.4,23.8C-87.6,7.4,-87.6,-9.8,-82.8,-25.4C-78,-41,-68.4,-55,-56.2,-66.2C-44,-77.4,-29.2,-85.8,-13.8,-87.4C1.6,-89,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
                        </svg>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -right-4 w-16 h-16 bg-white/10 rounded-full backdrop-blur-sm"></div>
                    <div class="absolute -bottom-4 left-auto right-32 w-24 h-24 bg-white/5 rounded-full backdrop-blur-sm"></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg class="w-full h-12" preserveAspectRatio="none" viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="#f9fafb"></path>
            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="#f9fafb"></path>
            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="#f9fafb"></path>
        </svg>
    </div>
</div>
