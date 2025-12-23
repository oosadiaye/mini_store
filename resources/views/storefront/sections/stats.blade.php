@php
    $settings = $section->settings ?? [];
    
    // Sample stats - in real implementation, these would be configurable
    $stats = $settings['stats'] ?? [
        [
            'icon' => '👥',
            'number' => '50000',
            'suffix' => '+',
            'label' => 'Happy Customers',
            'color' => 'from-blue-500 to-cyan-500'
        ],
        [
            'icon' => '📦',
            'number' => '100000',
            'suffix' => '+',
            'label' => 'Products Sold',
            'color' => 'from-purple-500 to-pink-500'
        ],
        [
            'icon' => '⭐',
            'number' => '4.9',
            'suffix' => '/5',
            'label' => 'Average Rating',
            'color' => 'from-yellow-500 to-orange-500'
        ],
        [
            'icon' => '🌍',
            'number' => '50',
            'suffix' => '+',
            'label' => 'Countries Served',
            'color' => 'from-green-500 to-emerald-500'
        ],
    ];
@endphp

<section class="py-16 bg-gradient-to-br from-indigo-600 to-purple-600 text-white">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-2">{{ $section->title ?? 'Our Achievements' }}</h2>
            <p class="text-indigo-100">{{ $section->content ?? 'Numbers that speak for themselves' }}</p>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
            @foreach($stats as $index => $stat)
            <div class="text-center" 
                 x-data="{ count: 0, target: {{ is_numeric($stat['number']) ? $stat['number'] : 0 }} }"
                 x-init="
                     let duration = 2000;
                     let steps = 60;
                     let increment = target / steps;
                     let stepDuration = duration / steps;
                     let interval = setInterval(() => {
                         if (count < target) {
                             count = Math.min(count + increment, target);
                         } else {
                             clearInterval(interval);
                         }
                     }, stepDuration);
                 ">
                <!-- Icon -->
                <div class="bg-gradient-to-br {{ $stat['color'] }} w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl shadow-lg">
                    {{ $stat['icon'] }}
                </div>
                
                <!-- Number -->
                <div class="text-5xl font-bold mb-2">
                    <span x-text="Math.floor(count).toLocaleString()">0</span>{{ $stat['suffix'] }}
                </div>
                
                <!-- Label -->
                <div class="text-indigo-100 font-medium">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
