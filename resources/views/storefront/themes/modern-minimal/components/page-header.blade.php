@props(['title', 'subtitle' => null, 'breadcrumbs' => []])

<div class="bg-gray-50 py-12 md:py-20 text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl md:text-5xl font-serif font-medium text-gray-900 mb-4">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-gray-500 max-w-2xl mx-auto text-lg">{{ $subtitle }}</p>
        @endif
        
        @if(!empty($breadcrumbs))
        <div class="flex justify-center items-center gap-2 text-xs uppercase tracking-widest text-gray-400 mt-6">
            <a href="/" class="hover:text-black transition">Home</a>
            @foreach($breadcrumbs as $label => $url)
                <span>/</span>
                @if($loop->last)
                    <span class="text-black">{{ $label }}</span>
                @else
                    <a href="{{ $url }}" class="hover:text-black transition">{{ $label }}</a>
                @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
