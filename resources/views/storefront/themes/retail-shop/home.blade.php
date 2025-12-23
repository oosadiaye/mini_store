@extends('storefront.themes.retail-shop.layout')

@section('pageTitle', 'Welcome')

@section('content')
    @php
        $sectionOrder = $themeSettings->layout_settings['section_order'] ?? [
            'hero', 'categories', 'featured_products'
        ];
        
        $sectionMap = [
            'hero' => 'storefront.themes.retail-shop.sections.hero',
            'categories' => 'storefront.themes.retail-shop.sections.categories',
            'featured_products' => 'storefront.themes.retail-shop.sections.featured_products',
        ];

        $sectionVisibility = $themeSettings->layout_settings['sections'] ?? [];
    @endphp

    @foreach($sectionOrder as $sectionKey)
        @if(isset($sectionMap[$sectionKey]) && ($sectionVisibility[$sectionKey] ?? true))
            @include($sectionMap[$sectionKey])
        @endif
    @endforeach
@endsection
