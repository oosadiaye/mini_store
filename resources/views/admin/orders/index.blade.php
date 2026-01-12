@extends('admin.layout')

@php
    $routes = [
        "index" => route("admin.orders.index"),
        "create" => route("admin.orders.create"),
        "bulkAction" => route("admin.orders.bulk-action"),
        "base" => route("admin.orders.index"), // Base for appending /id
    ];
    $currencySymbol = isset($tenant->data['currency_symbol']) ? $tenant->data['currency_symbol'] : '₦';
@endphp

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Omni Channel Orders</h2>
        <p class="text-gray-600 text-sm">Real-time order management across all channels.</p>
    </div>

    <omni-channel-orders
        :initial-orders='@json($orders)'
        :routes='@json($routes)'
        currency-symbol="{{ $currencySymbol }}"
    ></omni-channel-orders>
@endsection
