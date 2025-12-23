@props(['size' => 'lg'])

@php
$settings = tenant()->data ?? [];
$hasCustomLogo = !empty($settings['logo']);

$sizes = [
    'sm' => 32,
    'md' => 48,
    'lg' => 64,
    'xl' => 128,
];

$dimension = $sizes[$size] ?? 64;
$storeName = tenant('name') ?? 'Store';
@endphp

@if($hasCustomLogo)
    <img src="{{ route('tenant.media', ['path' => $settings['logo']]) }}" 
         alt="{{ $storeName }}" 
         {{ $attributes->merge(['class' => 'object-contain']) }}
         style="height: {{ $dimension }}px; width: auto; max-width: {{ $dimension * 3 }}px;">
@else
    @php
        $initials = \App\Helpers\LogoHelper::getInitials($storeName);
        $logoSvg = \App\Helpers\LogoHelper::generateSvg($initials, $dimension);
    @endphp
    <img src="{{ $logoSvg }}" 
         alt="{{ $storeName }}" 
         {{ $attributes->merge(['class' => '']) }}
         style="width: {{ $dimension }}px; height: {{ $dimension }}px;">
@endif
