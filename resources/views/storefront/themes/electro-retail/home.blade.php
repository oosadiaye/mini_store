@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', 'Home')

@section('content')
    @php
        $sectionOrder = $themeSettings->layout_settings['section_order'] ?? [
            'hero', 'deal_strip', 'categories', 'featured_products', 'trust_badges'
        ];
        
        $sectionMap = [
            'hero' => 'storefront.themes.electro-retail.sections.hero',
            'deal_strip' => 'storefront.themes.electro-retail.sections.deal_strip',
            'categories' => 'storefront.themes.electro-retail.sections.categories',
            'featured_products' => 'storefront.themes.electro-retail.sections.featured_products',
            'trust_badges' => 'storefront.themes.electro-retail.sections.trust_badges',
        ];

        $sectionVisibility = $themeSettings->layout_settings['sections'] ?? [];
    @endphp

    @foreach($sectionOrder as $sectionKey)
        @if(isset($sectionMap[$sectionKey]) && ($sectionVisibility[$sectionKey] ?? true))
            @include($sectionMap[$sectionKey])
        @endif
    @endforeach
@endsection
