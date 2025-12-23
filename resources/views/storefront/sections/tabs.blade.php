@php
    $settings = $section->settings ?? [];
    
    // Sample tab data - in real implementation, these would be configurable
    $tabs = $settings['tabs'] ?? [
        [
            'id' => 'description',
            'title' => 'Description',
            'icon' => '📝',
            'content' => 'Our products are carefully crafted with premium materials and attention to detail. Each item undergoes rigorous quality control to ensure it meets our high standards. We believe in creating products that not only look great but also stand the test of time.'
        ],
        [
            'id' => 'features',
            'title' => 'Features',
            'icon' => '⭐',
            'content' => '<ul class="list-disc list-inside space-y-2"><li>Premium quality materials</li><li>Durable construction</li><li>Modern design</li><li>Easy to use</li><li>Eco-friendly packaging</li><li>1-year warranty included</li></ul>'
        ],
        [
            'id' => 'shipping',
            'title' => 'Shipping',
            'icon' => '🚚',
            'content' => 'We offer free standard shipping on orders over ' . (tenant('data')['currency_symbol'] ?? '₦') . '50. Express shipping is available for an additional fee. All orders are processed within 1-2 business days. You will receive a tracking number once your order ships.'
        ],
        [
            'id' => 'reviews',
            'title' => 'Reviews',
            'icon' => '💬',
            'content' => 'Our customers love our products! With an average rating of 4.8 out of 5 stars, we\'re proud to deliver quality and satisfaction. Read what our customers have to say about their experience with our products.'
        ],
    ];
@endphp

<section class="py-16 bg-gray-50" x-data="{ activeTab: '{{ $tabs[0]['id'] ?? 'description' }}' }">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $section->title ?? 'Product Information' }}</h2>
            <p class="text-gray-600">{{ $section->content ?? 'Everything you need to know' }}</p>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="bg-white rounded-t-lg border-b border-gray-200 overflow-x-auto">
            <div class="flex">
                @foreach($tabs as $tab)
                <button @click="activeTab = '{{ $tab['id'] }}'"
                        :class="activeTab === '{{ $tab['id'] }}' ? 'border-indigo-600 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                        class="flex-1 min-w-[120px] px-6 py-4 text-center font-semibold border-b-2 transition whitespace-nowrap">
                    <span class="mr-2">{{ $tab['icon'] }}</span>
                    <span>{{ $tab['title'] }}</span>
                </button>
                @endforeach
            </div>
        </div>
        
        <!-- Tab Content -->
        <div class="bg-white rounded-b-lg shadow-md p-8">
            @foreach($tabs as $tab)
            <div x-show="activeTab === '{{ $tab['id'] }}'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="prose max-w-none">
                <div class="text-gray-700 leading-relaxed">
                    {!! $tab['content'] !!}
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Optional: Tab Indicators -->
        <div class="flex justify-center gap-2 mt-6">
            @foreach($tabs as $index => $tab)
            <button @click="activeTab = '{{ $tab['id'] }}'"
                    :class="activeTab === '{{ $tab['id'] }}' ? 'bg-indigo-600 w-8' : 'bg-gray-300 w-3'"
                    class="h-3 rounded-full transition-all duration-300"
                    title="{{ $tab['title'] }}"></button>
            @endforeach
        </div>
    </div>
</section>
