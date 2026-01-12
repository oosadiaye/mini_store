@extends('admin.layout')

@php
    $routes = [
        "store" => route("admin.pos.store"),
        "receipt" => route("admin.pos.receipt", ":id"),
        "display" => route("admin.pos.display")
    ];
@endphp

@section('content')
    <pos-system
        :initial-categories='@json($categories)'
        :initial-products='@json($allProducts)'
        :initial-payment-types='@json($paymentTypes)'
        :initial-customers='@json($customers)'
        :tax-codes='@json($taxCodes)'
        :tax-rate='@json($taxRate)'
        :enable-tax='@json($enableTax)'
        currency-symbol='{{ $currencySymbol }}'
        tenant-id='{{ app("tenant")->id }}'
        tenant-name='{{ app("tenant")->name ?? "Store" }}'
        :routes='@json($routes)'
        csrf-token="{{ csrf_token() }}"
    ></pos-system>
@endsection

@push('styles')
<style>
    /* POS Fullscreen overrides */
    body.pos-fullscreen header,
    body.pos-fullscreen mobile-sidebar,
    body.pos-fullscreen mobile-bottom-nav,
    body.pos-fullscreen .hidden.md\:flex.md\:flex-shrink-0.z-20 {
        display: none !important;
    }
    
    body.pos-fullscreen .flex-1.flex.flex-col {
        height: 100vh !important;
        padding-bottom: 0 !important;
    }
    
    body.pos-fullscreen main {
        padding: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
@endpush
