@extends('admin.layout')

@php
    $routes = [
        "index" => route("admin.orders.index", ["source" => "admin"]),
        "store" => route("admin.orders.store"),
        "customers" => route("admin.orders.resources.customers"),
        "products" => route("admin.orders.resources.products"),
    ];
@endphp

@section('content')
    <order-create
        :routes='@json($routes)'
        currency-symbol="{{ $currencySymbol ?? '$' }}"
    ></order-create>
@endsection
