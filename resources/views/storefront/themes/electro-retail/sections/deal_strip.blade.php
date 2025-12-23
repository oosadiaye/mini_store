    {{-- Deal Strip --}}
    @php
        $deal = $themeSettings->settings['deals'] ?? [];
        // Default to enabled if not set, or check explicit setting
        $dealEnabled = isset($deal['enabled']) ? $deal['enabled'] : true; 
        $dealBadge = $deal['badge_text'] ?? 'Deal of Day';
        $dealTitle = $deal['title'] ?? 'Flash Sale: 50% Off Smartwatches';
        $dealUrl = $deal['url'] ?? '#';
        // Parse end time or default to 4 hours from now
        $dealEndTime = isset($deal['end_time']) && $deal['end_time'] 
            ? \Carbon\Carbon::parse($deal['end_time'])->timestamp * 1000 
            : (now()->timestamp * 1000 + 14400000);
    @endphp

    @if($dealEnabled)
    <section class="bg-electro-dark text-white py-4 mb-12 border-y border-gray-800">
        <div class="container-custom flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="bg-electro-neon text-electro-dark font-heading font-bold uppercase px-3 py-1 rounded skew-x-[-10deg]">{{ $dealBadge }}</span>
                <span class="font-bold text-lg hidden md:inline">{{ $dealTitle }}</span>
            </div>
            
            <div class="flex items-center gap-6 text-sm" x-data="{ 
                endTime: {{ $dealEndTime }},
                hours: '00', minutes: '00', seconds: '00',
                updateTimer() {
                    const now = new Date().getTime();
                    const distance = this.endTime - now;
                    if (distance < 0) {
                        this.hours = '00'; this.minutes = '00'; this.seconds = '00';
                        return;
                    }
                    this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2,'0');
                    this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2,'0');
                    this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2,'0');
                }
            }" x-init="updateTimer(); setInterval(() => updateTimer(), 1000)">
                <span class="uppercase text-gray-400 text-xs font-bold tracking-widest mr-2">Ends In:</span>
                <div class="flex gap-2 font-heading font-bold text-xl">
                    <span class="bg-gray-800 px-2 rounded text-electro-neon" x-text="hours">00</span> :
                    <span class="bg-gray-800 px-2 rounded text-electro-neon" x-text="minutes">00</span> :
                    <span class="bg-gray-800 px-2 rounded text-electro-neon" x-text="seconds">00</span>
                </div>
            </div>

            <a href="{{ $dealUrl }}" class="text-sm font-bold text-electro-blue hover:text-white transition">Shop All Deals &rarr;</a>
        </div>
    </section>
    @endif
