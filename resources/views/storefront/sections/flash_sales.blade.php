@php
    $settings = $section->settings ?? [];
    $title = $settings['title'] ?? 'Details';
    $products = $flashSaleProducts ?? collect();
    $endDate = $settings['end_date'] ?? date('Y-m-d', strtotime('+3 days'));
    
    // Get active theme slug using helper method
    $themeSlug = \App\Models\ThemeSetting::getActiveThemeSlug();
@endphp

<section id="{{ $section_id ?? '' }}" class="py-12 bg-gray-50 from-gray-50 to-white" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
            
            <div class="flex items-center gap-4 mt-4 md:mt-0" 
                 x-data="{
                     endDate: new Date('{{ $endDate }}').getTime(),
                     now: new Date().getTime(),
                     timeLeft: 0,
                     days: 0, hours: 0, minutes: 0, seconds: 0,
                     
                     init() {
                         this.updateTimer();
                         setInterval(() => this.updateTimer(), 1000);
                     },
                     
                     updateTimer() {
                         this.now = new Date().getTime();
                         this.timeLeft = this.endDate - this.now;
                         
                         if (this.timeLeft < 0) {
                             this.days = 0; this.hours = 0; this.minutes = 0; this.seconds = 0;
                         } else {
                             this.days = Math.floor(this.timeLeft / (1000 * 60 * 60 * 24));
                             this.hours = Math.floor((this.timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                             this.minutes = Math.floor((this.timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                             this.seconds = Math.floor((this.timeLeft % (1000 * 60)) / 1000);
                         }
                     }
                 }">
                <span class="text-sm font-medium text-gray-500 mr-2 uppercase tracking-wider">Offer ends in:</span>
                
                <div class="flex gap-2 text-center">
                    <div class="bg-gray-800 text-white rounded px-3 py-1 min-w-[50px]">
                        <div class="font-bold text-xl leading-none" x-text="days.toString().padStart(2, '0')">00</div>
                        <div class="text-[10px] uppercase opacity-75">Days</div>
                    </div>
                    <div class="font-bold text-2xl text-gray-400">:</div>
                    <div class="bg-gray-800 text-white rounded px-3 py-1 min-w-[50px]">
                        <div class="font-bold text-xl leading-none" x-text="hours.toString().padStart(2, '0')">00</div>
                        <div class="text-[10px] uppercase opacity-75">Hrs</div>
                    </div>
                    <div class="font-bold text-2xl text-gray-400">:</div>
                    <div class="bg-gray-800 text-white rounded px-3 py-1 min-w-[50px]">
                        <div class="font-bold text-xl leading-none" x-text="minutes.toString().padStart(2, '0')">00</div>
                        <div class="text-[10px] uppercase opacity-75">Min</div>
                    </div>
                    <div class="font-bold text-2xl text-gray-400">:</div>
                    <div class="bg-gray-800 text-white rounded px-3 py-1 min-w-[50px]">
                        <div class="font-bold text-xl leading-none" x-text="seconds.toString().padStart(2, '0')">00</div>
                        <div class="text-[10px] uppercase opacity-75">Sec</div>
                    </div>
                </div>
            </div>
        </div>

        @if($products->isEmpty())
             <div class="text-center py-10 text-gray-400 bg-white rounded-lg border border-dashed">
                 No flash sale products found. Configure settings or add products.
             </div>
        @else
            <!-- Product Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($products as $product)
                     @include("storefront.themes.{$themeSlug}.components.product-card", ['product' => $product])
                @endforeach
            </div>
        @endif
    </div>
</section>
