@php
    $settings = $section->settings ?? [];
@endphp

<section class="py-8 bg-white" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-8 border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300">
            @for ($i = 1; $i <= 4; $i++)
                @if(!empty($settings["item_{$i}_title"]))
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                            <i class="fas fa-{{ $settings["item_{$i}_icon"] ?? 'star' }} text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide group-hover:text-primary transition-colors">{{ $settings["item_{$i}_title"] }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $settings["item_{$i}_text"] ?? '' }}</p>
                        </div>
                    </div>
                @endif
            @endfor
            
            @if(empty($settings['item_1_title']) && empty($settings['item_2_title']))
                <!-- Placeholder for new layout -->
                <div class="col-span-4 text-center text-gray-400 py-4 italic">
                    Configure Info Bar items in Page Builder
                </div>
            @endif
        </div>
    </div>
</section>
