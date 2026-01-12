<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        $logoUrl = ($tenant && !empty($tenant->data['logo'])) 
            ? '/storage/' . $tenant->data['logo']
            : null;
            
        $routes = [
            'login' => Route::currentRouteName() === 'tenant.login' && $tenant ? route('tenant.login.store', ['tenant' => $tenant->slug]) : route('login'),
            'passwordRequest' => Route::has('password.request') ? route('password.request') : null,
            'supportInfo' => ($tenant) ? route('tenant.support.guest', ['tenant' => $tenant->slug]) : null,
        ];
    @endphp
    <title>{{ config('app.name', 'Laravel') }} - {{ $tenant ? $tenant->name : 'Login' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-950 text-white min-h-screen">
    <div id="app">
        <tenant-login 
            :tenant='@json($tenant)' 
            :tenant-logo-url='@json($logoUrl)'
            :routes='@json($routes)'
            :old-email='@json(old("email"))'
            :errors='@json($errors->toArray())'
            :status='@json(session("status"))'
            csrf-token="{{ csrf_token() }}"
        ></tenant-login>
    </div>
</body>
</html>
