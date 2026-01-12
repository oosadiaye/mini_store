@extends('admin.layout')

@section('content')
@php
    $routes = [
        "settingsIndex" => route("admin.settings.index"),
        "domainRequest" => route("admin.settings.domain.request"),
        "domainCancel" => route("admin.settings.domain.cancel", ":id"),
    ];
@endphp

<domain-settings
    :initial-data='@json($domainData)'
    :routes='@json($routes)'
></domain-settings>
@endsection
