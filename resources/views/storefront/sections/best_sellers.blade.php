@php
    $settings = $section->settings ?? [];
    $products = $section->data['best_sellers'] ?? []; // Data Loaded by StorefrontController
    $limit = $settings['limit'] ?? 8;
    $colsDesktop = $settings['grid_cols_desktop'] ?? 4;
    $colsMobile = $settings['grid_cols_mobile'] ?? 2;
@endphp

<section id="{{ $section_id ?? '' }}" class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $section->title ?? 'Best Sellers' }}</h2>
            @if(!empty($section->content))
                <p class="text-gray-600 max-w-2xl mx-auto">{{ $section->content }}</p>
            @endif
        </div>

        @if($products->isEmpty())
             <div class="text-center text-gray-500 py-10">No best selling products found.</div>
        @else
            <div class="grid grid-cols-{{ $colsMobile }} md:grid-cols-{{ $colsDesktop }} gap-6">
                @foreach($products as $product)
                    <div class="group relative">
                        {{-- Product Card --}}
                        <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                            <img src="{{ $product->images->first()->url ?? asset('assets/img/placeholder.png') }}" 
                                 alt="{{ $product->name }}" 
                                 class="h-full w-full object-cover object-center lg:h-full lg:w-full">
                        </div>
                        <div class="mt-4 flex justify-between">
                            <div>
                                <h3 class="text-sm text-gray-700">
                                    <a href="{{ route('storefront.product', $product->id) }}">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $product->category->name ?? '' }}</p>
                            </div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ number_format($product->price, 2) }} {{ tenant('currency') ?? 'USD' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
