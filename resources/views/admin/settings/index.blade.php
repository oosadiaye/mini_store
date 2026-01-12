@extends('admin.layout')

@section('content')
@php
    $routes = [
        "update" => route("admin.settings.update"),
        "media" => route("tenant.media"),
        "paymentTypesStore" => route("admin.payment-types.store"),
        "paymentTypesDestroy" => route("admin.payment-types.destroy", ":id"),
        "paymentTypesToggle" => route("admin.payment-types.toggle", ":id"),
        "paymentTypesToggleStorefront" => route("admin.payment-types.toggle-storefront", ":id"),
        "testEmail" => route("admin.settings.test-email"),
        "seoSuggest" => route("admin.settings.seo-suggest"),
        "storefrontSitemap" => route("storefront.sitemap"),
        "storefrontRobots" => route("storefront.robots"),
        "storefrontHome" => route("storefront.home"),
        "wizardIndex" => route("admin.wizard.index"),
        "taxCodesIndex" => route("admin.tax-codes.index"),
        "taxCodesCreate" => route("admin.tax-codes.create"),
        "pagesIndex" => route("admin.pages.index"),
        "pagesCreate" => route("admin.pages.create"),
        "postsIndex" => route("admin.posts.index"),
    ];
@endphp

    <general-settings
        :initial-settings='@json($settings)'
        tenant-name="{{ $tenant->name }}"
        :payment-types='@json($paymentTypes)'
        :initial-storefront-status="{{ $tenant->is_storefront_enabled ? 'true' : 'false' }}"
        :has-feature="{{ $tenant->hasFeature('online_store') ? 'true' : 'false' }}"
        tenant-slug="{{ $tenant->slug }}"
        default-tab="{{ request('tab', 'general') }}"
        :routes='@json($routes)'
        csrf-token="{{ csrf_token() }}"
        current-user-email="{{ auth()->user()->email }}"
    ></general-settings>
@endsection
