@php
    $settings = $section->settings ?? [];
    
    // Sample pricing plans - in real implementation, these would be configurable
    $plans = $settings['plans'] ?? [
        [
            'name' => 'Basic',
            'price' => (tenant('data')['currency_symbol'] ?? '₦') . '29',
            'period' => '/month',
            'description' => 'Perfect for getting started',
            'featured' => false,
            'features' => [
                'Up to 10 products',
                'Basic analytics',
                'Email support',
                '1 GB storage',
                'Mobile app access'
            ],
            'button_text' => 'Get Started',
            'button_link' => '#'
        ],
        [
            'name' => 'Professional',
            'price' => (tenant('data')['currency_symbol'] ?? '₦') . '79',
            'period' => '/month',
            'description' => 'Best for growing businesses',
            'featured' => true,
            'features' => [
                'Unlimited products',
                'Advanced analytics',
                'Priority support',
                '10 GB storage',
                'Mobile app access',
                'Custom domain',
                'API access'
            ],
            'button_text' => 'Start Free Trial',
            'button_link' => '#'
        ],
        [
            'name' => 'Enterprise',
            'price' => (tenant('data')['currency_symbol'] ?? '₦') . '199',
            'period' => '/month',
            'description' => 'For large organizations',
            'featured' => false,
            'features' => [
                'Unlimited everything',
                'Custom analytics',
                '24/7 phone support',
                'Unlimited storage',
                'Mobile app access',
                'Custom domain',
                'API access',
                'Dedicated account manager'
            ],
            'button_text' => 'Contact Sales',
            'button_link' => '#'
        ],
    ];
@endphp

<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $section->title ?? 'Choose Your Plan' }}</h2>
            <p class="text-gray-600">{{ $section->content ?? 'Select the perfect plan for your needs' }}</p>
        </div>
        
        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @foreach($plans as $plan)
            <div class="relative bg-white rounded-2xl shadow-lg overflow-hidden {{ $plan['featured'] ? 'ring-2 ring-indigo-600 transform scale-105' : 'border border-gray-200' }}">
                <!-- Featured Badge -->
                @if($plan['featured'])
                <div class="absolute top-0 right-0 bg-indigo-600 text-white px-4 py-1 text-sm font-semibold rounded-bl-lg">
                    Most Popular
                </div>
                @endif
                
                <div class="p-8">
                    <!-- Plan Name -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan['name'] }}</h3>
                    <p class="text-gray-600 mb-6">{{ $plan['description'] }}</p>
                    
                    <!-- Price -->
                    <div class="mb-6">
                        <span class="text-5xl font-bold text-gray-900">{{ $plan['price'] }}</span>
                        <span class="text-gray-600">{{ $plan['period'] }}</span>
                    </div>
                    
                    <!-- Features List -->
                    <ul class="space-y-3 mb-8">
                        @foreach($plan['features'] as $feature)
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    
                    <!-- CTA Button -->
                    <a href="{{ $plan['button_link'] }}" 
                       class="block w-full text-center py-3 px-6 rounded-lg font-semibold transition {{ $plan['featured'] ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }}">
                        {{ $plan['button_text'] }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
