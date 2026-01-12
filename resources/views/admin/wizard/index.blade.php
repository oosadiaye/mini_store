<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store Setup Wizard') }}
        </h2>
    </x-slot>

    <!-- Premium Design Foundation -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --professional-navy: #0A2540;
            --navy-soft: #1a3a5a;
            --navy-glow: rgba(10, 37, 64, 0.1);
            --gray-subtle: #F6F9FC;
            --premium-border: #E6E9EF;
        }
        body { font-family: 'Inter', sans-serif; }
    </style>

@php
    $storeConfigArray = [
        "store_name" => $storeConfig->store_name,
        "brand_color" => $storeConfig->brand_color ?? "#3b82f6",
        "industry" => $storeConfig->industry,
        "selected_categories" => $storeConfig->selected_categories ?? [],
        "layout_preference" => $storeConfig->layout_preference ?? "minimal"
    ];
    
    $routes = [
        "update" => route("admin.wizard.update"),
        "finish" => route("admin.wizard.finish"),
    ];
@endphp

    <store-wizard
        :store-config='@json($storeConfigArray)'
        :categories='@json($tree)'
        :routes='@json($routes)'
        csrf-token="{{ csrf_token() }}"
    ></store-wizard>

</x-app-layout>
