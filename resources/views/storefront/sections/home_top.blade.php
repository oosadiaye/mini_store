@if($banners['home_top'] && $banners['home_top']->count() > 0)
    @php $topBanner = $banners['home_top']->first(); @endphp
    <div class="bg-indigo-600 text-white text-sm py-2 px-4 text-center font-medium relative z-50">
        <div class="container mx-auto flex justify-center items-center">
            <span>{{ $topBanner->description ?? $topBanner->title }}</span>
            @if($topBanner->link)
                <a href="{{ $topBanner->link }}" class="ml-4 underline hover:text-indigo-100">{{ $topBanner->button_text ?? 'Learn More' }}</a>
            @endif
        </div>
    </div>
@endif
