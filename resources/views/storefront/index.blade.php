<x-storefront.layout :config="$config" :menuCategories="$menuCategories">
    
    <x-storefront.layout-resolver 
        :layoutMode="$layoutMode ?? $data['layout_mode'] ?? 'brand_showcase'"
        :heroData="$heroData"
        :featuredProducts="$featuredProducts"
        :categorySections="$categorySections"
        :schema="$schema"
    />

</x-storefront.layout>
