@props(['layoutMode' => 'brand_showcase', 'heroData', 'featuredProducts', 'categorySections', 'schema' => []])

@php
    // Normalize layout mode (legacy support)
    $layout = $layoutMode ?? 'brand_showcase';
@endphp

@switch($layout)
    @case('high_volume')
        <x-storefront.layouts.mart 
            :heroData="$heroData"
            :featuredProducts="$featuredProducts"
            :categorySections="$categorySections"
        />
        @break

    @case('quick_order')
        <x-storefront.layouts.b2b 
            :heroData="$heroData"
            :featuredProducts="$featuredProducts"
            :categorySections="$categorySections"
        />
        @break

    @case('brand_showcase')
    @default
        <x-storefront.layouts.brand-showcase 
            :heroData="$heroData"
            :featuredProducts="$featuredProducts"
            :categorySections="$categorySections"
            :schema="$schema"
        />
@endswitch
