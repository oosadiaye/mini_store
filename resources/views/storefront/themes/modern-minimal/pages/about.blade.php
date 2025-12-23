@extends('storefront.themes.modern-minimal.layout')

@php
    $settings = \App\Models\ThemeSetting::getSettings();
    $about = $settings['about'] ?? [];
    $hero = $about['hero'] ?? [];
@endphp

@section('pageTitle', $hero['title'] ?? 'About Us')

@section('content')
    {{-- Hero --}}
    <div class="relative bg-gray-100 py-24 md:py-32 overflow-hidden">
        @if(!empty($hero['image']))
            <img src="{{ $hero['image'] }}" alt="About Hero" class="absolute inset-0 w-full h-full object-cover opacity-10"> <!-- Faded bg -->
        @endif
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-serif font-medium mb-6">{{ $hero['title'] ?? 'About Us' }}</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto font-light">{{ $hero['subtitle'] ?? 'Our story and mission.' }}</p>
        </div>
    </div>

    {{-- Content Sections (Split Layout) --}}
    @if(!empty($about['sections']))
        @foreach($about['sections'] as $index => $section)
        <div class="py-20">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center gap-12 md:gap-24 {{ $index % 2 != 0 ? 'md:flex-row-reverse' : '' }}">
                    <div class="w-full md:w-1/2">
                        @if(!empty($section['image']))
                            <img src="{{ $section['image'] }}" alt="{{ $section['title'] }}" class="w-full aspect-[4/3] object-cover shadow-xl rounded-sm">
                        @else
                           <div class="w-full aspect-[4/3] bg-gray-200 flex items-center justify-center text-gray-400">No Image</div>
                        @endif
                    </div>
                    <div class="w-full md:w-1/2">
                        <h2 class="text-3xl font-serif font-medium mb-6">{{ $section['title'] }}</h2>
                        <div class="prose prose-lg text-gray-600 font-light leading-relaxed">
                            <p>{{ $section['content'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    {{-- Stats --}}
    @include('storefront.themes.modern-minimal.components.stats-row', ['stats' => $about['stats'] ?? []])

    {{-- Team --}}
    @if(!empty($about['team']))
    <div class="py-24 bg-gray-50">
        <div class="container mx-auto px-4 text-center">
             <h2 class="text-3xl font-serif font-medium mb-16">Meet the Team</h2>
             <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
                 @foreach($about['team'] as $member)
                 <div class="group">
                     <div class="w-32 h-32 md:w-48 md:h-48 mx-auto rounded-full overflow-hidden mb-6 border-4 border-white shadow-lg relative">
                          <img src="{{ $member['image'] ?? 'https://via.placeholder.com/200' }}" alt="{{ $member['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                     </div>
                     <h3 class="text-lg font-bold">{{ $member['name'] }}</h3>
                     <p class="text-sm text-gray-500 uppercase tracking-widest mt-1">{{ $member['role'] }}</p>
                 </div>
                 @endforeach
             </div>
        </div>
    </div>
    @endif

@endsection
