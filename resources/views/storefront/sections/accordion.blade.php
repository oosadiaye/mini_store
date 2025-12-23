@php
    $settings = $section->settings ?? [];
    $allowMultiple = $settings['allow_multiple'] ?? false;
    
    // Sample accordion items - in real implementation, these would be configurable
    $items = $settings['items'] ?? [
        [
            'title' => 'What is your return policy?',
            'content' => 'We offer a 30-day return policy on all items. Products must be unused and in their original packaging. Simply contact our customer service team to initiate a return.'
        ],
        [
            'title' => 'How long does shipping take?',
            'content' => 'Standard shipping typically takes 5-7 business days. Express shipping options are available at checkout for delivery within 2-3 business days.'
        ],
        [
            'title' => 'Do you ship internationally?',
            'content' => 'Yes! We ship to over 50 countries worldwide. International shipping times vary by destination but typically range from 10-21 business days.'
        ],
        [
            'title' => 'How can I track my order?',
            'content' => 'Once your order ships, you\'ll receive a tracking number via email. You can use this number to track your package on our website or the carrier\'s website.'
        ],
        [
            'title' => 'What payment methods do you accept?',
            'content' => 'We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and various digital payment methods including Apple Pay and Google Pay.'
        ],
    ];
@endphp

<section class="py-16 bg-white" x-data="accordion({{ $allowMultiple ? 'true' : 'false' }})">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $section->title ?? 'Frequently Asked Questions' }}</h2>
            <p class="text-gray-600">{{ $section->content ?? 'Find answers to common questions' }}</p>
        </div>
        
        <!-- Accordion Items -->
        <div class="space-y-4">
            @foreach($items as $index => $item)
            <div class="border border-gray-200 rounded-lg overflow-hidden hover:border-indigo-300 transition">
                <button @click="toggle({{ $index }})"
                        class="w-full flex items-center justify-between p-5 text-left bg-white hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900 text-lg">{{ $item['title'] }}</span>
                    <svg class="w-6 h-6 text-indigo-600 transition-transform duration-300"
                         :class="isOpen({{ $index }}) ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="isOpen({{ $index }})"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-96"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 max-h-96"
                     x-transition:leave-end="opacity-0 max-h-0"
                     class="overflow-hidden">
                    <div class="p-5 pt-0 text-gray-600 leading-relaxed">
                        {{ $item['content'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
function accordion(allowMultiple) {
    return {
        openItems: [],
        allowMultiple: allowMultiple,
        
        toggle(index) {
            if (this.allowMultiple) {
                // Allow multiple items open
                const position = this.openItems.indexOf(index);
                if (position !== -1) {
                    this.openItems.splice(position, 1);
                } else {
                    this.openItems.push(index);
                }
            } else {
                // Only one item open at a time
                if (this.openItems.includes(index)) {
                    this.openItems = [];
                } else {
                    this.openItems = [index];
                }
            }
        },
        
        isOpen(index) {
            return this.openItems.includes(index);
        }
    }
}
</script>
