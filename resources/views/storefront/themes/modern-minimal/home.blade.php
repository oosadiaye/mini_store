@extends('storefront.themes.modern-minimal.layout')

@section('content')
    @php
        $sectionOrder = $themeSettings->layout_settings['section_order'] ?? [
            'hero', 'category_slider', 'featured_products', 'dual_banners', 'best_sellers', 'testimonials', 'newsletter'
        ];
        
        $sectionMap = [
            'hero' => 'storefront.themes.modern-minimal.sections.hero',
            'category_slider' => 'storefront.themes.modern-minimal.sections.category_slider',
            'featured_products' => 'storefront.themes.modern-minimal.sections.featured_products',
            'dual_banners' => 'storefront.themes.modern-minimal.sections.dual_banners',
            'best_sellers' => 'storefront.themes.modern-minimal.sections.best_sellers',
            'testimonials' => 'storefront.themes.modern-minimal.sections.testimonials',
            'newsletter' => 'storefront.themes.modern-minimal.sections.newsletter',
        ];

        $sectionVisibility = $themeSettings->layout_settings['sections'] ?? [];
    @endphp

    @foreach($sectionOrder as $sectionKey)
        @if(isset($sectionMap[$sectionKey]) && ($sectionVisibility[$sectionKey] ?? true))
            @include($sectionMap[$sectionKey])
        @endif
    @endforeach
@endsection
