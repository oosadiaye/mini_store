@props(['heroData', 'featuredProducts', 'categorySections', 'schema' => []])

{{-- DYNAMIC SECTIONS RENDERER --}}
@if(isset($schema['sections']) && count($schema['sections']) > 0)
    @foreach($schema['sections'] as $index => $section)
        @switch($section['type'])
            @case('hero_banner')
                {{-- Re-using existing Hero Logic (simplified for brevity, realistically would pull out to component or use data) --}}
                @php $hData = $section['data']; @endphp
                <div class="relative bg-gray-900 overflow-hidden">
                    @if(isset($hData['image']) && $hData['image'])
                        <div class="absolute inset-0">
                            <img src="{{ Str::startsWith($hData['image'], 'http') ? $hData['image'] : route('tenant.media', ['path' => $hData['image']]) }}" 
                                 alt="Hero Banner" 
                                 class="w-full h-full object-cover opacity-60">
                        </div>
                    @endif
                    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8 text-center">
                        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl mb-6"
                            x-data="editable('sections.{{ $index }}.data.title', 'text')"
                            x-bind:class="{ 'border-2 border-blue-500 border-dashed p-1': isEditMode }"
                            @click="startEditing()"
                            @click.away="cancel()"
                        >
                            {{-- View Mode --}}
                            <span x-show="!isEditing" x-text="content">{{ $hData['title'] }}</span>
                            
                            {{-- Edit Mode --}}
                            <input x-ref="input" 
                                   x-show="isEditing" 
                                   :value="content" 
                                   @blur="save()" 
                                   @keydown.enter="save()"
                                   @keydown.escape="cancel()"
                                   class="text-black px-2 py-1 rounded w-full text-center"
                            >
                        </h1>
                        <p class="mt-4 text-xl text-gray-300 max-w-3xl mx-auto">{{ $hData['subtitle'] }}</p>
                        @if(isset($hData['cta_text']))
                            <div class="mt-8">
                                <a href="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" class="inline-block bg-[color:var(--brand-color)] border border-transparent rounded-md py-3 px-8 font-medium text-white hover:bg-indigo-700">{{ $hData['cta_text'] }}</a>
                            </div>
                        @endif
                    </div>
                </div>
                @break

            {{-- Disable product_grid in legacy loop to prevent duplication/empty shells. Relies on home-sections API. --}}
            {{-- 
            @case('product_grid')
                <x-storefront.dynamic-grid 
                    :mode="$section['mode']" 
                    :title="$section['title']" 
                    :layout="$section['layout'] ?? 'grid'"
                />
                @break
            --}}

            {{-- Disable split_banner in legacy loop to prevent duplication. Relies on home-sections API. --}}
            {{-- 
            @case('split_banner')
                <x-storefront.split-banner :data="$section['data']" />
                @break
            --}}

        @endswitch
    @endforeach

@else
    {{-- FALLBACK / LEGACY MODE (Keep existing code) --}}
    
    {{-- Hero Banner --}}
    @if(!empty($heroData))
        <div class="relative bg-gray-900 overflow-hidden">
            {{-- Background Image --}}
            @if(isset($heroData['banner_image']) && $heroData['banner_image'])
                <div class="absolute inset-0">
                    <img src="{{ Str::startsWith($heroData['banner_image'], 'http') ? $heroData['banner_image'] : route('tenant.media', ['path' => $heroData['banner_image']]) }}" 
                         alt="Hero Banner" 
                         class="w-full h-full object-cover opacity-60">
                </div>
            @endif
            
            <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl mb-6 drop-shadow-md">
                    {{ $heroData['title'] }}
                </h1>
                <p class="mt-4 text-xl text-gray-300 max-w-3xl mx-auto">
                    {{ $heroData['subtitle'] }}
                </p>
            </div>
        </div>
    @else
        {{-- Fallback Gradient Hero --}}
        <div class="bg-gradient-to-r from-[color:var(--brand-color)] to-[color:var(--brand-color)]/80 text-white py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $heroData['title'] ?? 'Welcome' }}</h1>
                <p class="text-xl opacity-90">{{ $heroData['subtitle'] ?? 'Browse our collection' }}</p>
            </div>
        </div>
    @endif

    {{-- Featured Collection (Visual Focus) - Disabled to use API Slider --}}
    {{--
    @if($featuredProducts->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Featured Collection</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-6 xl:gap-x-8">
                @foreach($featuredProducts as $product)
                    <div class="group relative">
                        <div class="w-full min-h-80 bg-gray-200 aspect-w-1 aspect-h-1 rounded-md overflow-hidden group-hover:opacity-75 lg:h-80 lg:aspect-none">
                            <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-center object-cover lg:w-full lg:h-full">
                        </div>
                        <div class="mt-4 flex justify-between">
                            <div>
                                <h3 class="text-sm text-gray-700">
                                    <a href="#">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        {{ $product['name'] }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $product['category'] }}</p>
                            </div>
                            <p class="text-sm font-medium text-gray-900">${{ number_format($product['price'], 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    --}}

    {{-- Category Sections with Story Blocks (Legacy - Disabled to prevent duplication with API sections) --}}
    {{-- 
    @foreach($categorySections as $section)
        <div class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="md:flex md:items-center md:justify-between mb-8">
                    <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">{{ $section['category_name'] }}</h2>
                    <a href="#" class="hidden text-sm font-medium text-[color:var(--brand-color)] hover:text-indigo-500 md:block">
                        Shop the collection<span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach(collect($section['products'])->take(4) as $product)
                            <x-storefront.product-card :product="$product" />
                    @endforeach
                </div>
                    <div class="mt-8 md:hidden">
                    <a href="#" class="text-sm font-medium text-[color:var(--brand-color)] hover:text-indigo-500">
                        Shop the collection<span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
    --}}

@endif

{{-- Dynamic Category Sections (API Driven) --}}
<x-storefront.home-sections />
