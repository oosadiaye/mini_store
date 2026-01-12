@props(['data'])

@if(!empty($data['image_left']) || !empty($data['image_right']))
<div class="relative w-full flex flex-col md:flex-row h-[500px] md:h-[600px] bg-white overflow-hidden my-12 mb-[100px]">
    {{-- Left Image --}}
    <div class="w-full md:w-1/2 h-1/2 md:h-full relative group">
        @if(!empty($data['image_left']))
            <img src="{{ $data['image_left'] }}" alt="Collection" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
        @endif
         <div class="absolute inset-0 bg-black/10 transition-colors group-hover:bg-black/20"></div>
    </div>

    {{-- Right Image --}}
    <div class="w-full md:w-1/2 h-1/2 md:h-full relative group">
        @if(!empty($data['image_right']))
            <img src="{{ $data['image_right'] }}" alt="Collection" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
        @endif
        <div class="absolute inset-0 bg-black/10 transition-colors group-hover:bg-black/20"></div>
    </div>

    {{-- Center Floating Card --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20 w-[90%] md:w-auto min-w-[320px] max-w-lg">
        <div class="bg-white/95 backdrop-blur-md p-8 md:p-12 text-center shadow-2xl rounded-sm transition-transform duration-300 hover:scale-105 border border-white/50">
            @if(isset($data['center_text']['subtitle']))
                <p class="text-sm md:text-base uppercase tracking-[0.2em] text-gray-500 mb-3 font-medium">
                    {{ $data['center_text']['subtitle'] }}
                </p>
            @endif
            
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-6">
                {{ $data['center_text']['title'] ?? 'Collection' }}
            </h2>

            @if(isset($data['center_text']['cta']))
                <a href="#" class="inline-block border-b-2 border-black pb-1 text-sm font-bold uppercase tracking-widest hover:text-gray-600 hover:border-gray-600 transition-colors">
                    {{ $data['center_text']['cta'] }}
                </a>
            @endif
        </div>
    </div>
</div>
@endif
