<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ tenant('id') }} | {{ $pageTitle ?? 'Home' }}</title>

    <!-- Dynamic Fonts and Styles -->
    @php
        $headingFont = $themeSettings->fonts['heading'] ?? 'Inter';
        $bodyFont = $themeSettings->fonts['body'] ?? 'Inter';
        $primaryColor = $themeSettings->colors['primary'] ?? '#4f46e5';
        $secondaryColor = $themeSettings->colors['secondary'] ?? '#1f2937';
        $accentColor = $themeSettings->colors['accent'] ?? '#fbbf24';
        
        $footerSettings = $themeSettings->layout_settings['footer'] ?? [];
        $aboutText = $footerSettings['about'] ?? 'Curated essentials for the modern lifestyle.';
        $copyrightText = $footerSettings['copyright'] ?? 'All rights reserved.';
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($headingFont) }}:wght@400;600;700&family={{ urlencode($bodyFont) }}:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    {{-- Favicon (custom or auto-generated) --}}
    @php
        $faviconUrl = \App\Helpers\LogoHelper::getFavicon();
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --font-heading: '{{ $headingFont }}', serif;
            --font-body: '{{ $bodyFont }}', sans-serif;
            --color-primary: {{ $primaryColor }};
            --color-secondary: {{ $secondaryColor }};
            --color-accent: {{ $accentColor }};
        }
        body { font-family: var(--font-body); color: var(--color-secondary); }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); }
        
        /* Dynamic Components */
        .btn-primary { background-color: var(--color-primary); color: white; }
        .text-primary { color: var(--color-primary); }
        .bg-primary { background-color: var(--color-primary); }
        
        [x-cloak] { display: none !important; }
        
        /* Custom CSS */
        {!! $themeSettings->custom_css ?? '' !!}
    </style>
</head>
<body class="antialiased bg-white flex flex-col min-h-screen">

    {{-- Navigation --}}
    <nav x-data="{ mobileMenuOpen: false, searchOpen: false }" class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 transition-all duration-300">
        <div class="container mx-auto px-4 md:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ \App\Helpers\LogoHelper::getLogo(64) }}" alt="{{ tenant('name') }}" class="h-12 w-auto">
                        <span class="text-2xl font-semibold tracking-tight" style="color: var(--color-primary);">{{ tenant('name') }}</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('storefront.home') }}" class="text-sm font-medium hover:text-gray-900 uppercase tracking-wide">Home</a>
                    <a href="{{ route('storefront.products.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 uppercase tracking-wide">Shop</a>
                    <a href="{{ route('storefront.page', 'about') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 uppercase tracking-wide">About</a>
                    <a href="{{ route('storefront.page', 'contact') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 uppercase tracking-wide">Contact</a>
                </div>

                <!-- Icons -->
                <div class="flex items-center space-x-4">
                    <button @click="searchOpen = !searchOpen" class="p-2 text-gray-500 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    <a href="{{ route('storefront.cart.index') }}" class="p-2 text-gray-500 hover:text-gray-900 relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="absolute top-1 right-0 text-[10px] text-white rounded-full w-4 h-4 flex items-center justify-center" style="background-color: var(--color-primary);">
                            {{ \App\Models\Cart::where('session_id', session()->getId())->first()?->total_items ?? 0 }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition x-cloak class="md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-4 space-y-3">
                <a href="{{ route('storefront.home') }}" class="block text-base font-medium text-gray-900">Home</a>
                <a href="{{ route('storefront.products.index') }}" class="block text-base font-medium text-gray-600">Shop</a>
                <a href="{{ route('storefront.page', 'about') }}" class="block text-base font-medium text-gray-600">About</a>
                <a href="{{ route('storefront.page', 'contact') }}" class="block text-base font-medium text-gray-600">Contact</a>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-50 pt-16 pb-8 border-t border-gray-200">
        <div class="container mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="space-y-4">
                    <h4 class="font-serif text-lg font-medium">{{ tenant('id') }}</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $aboutText }}</p>
                    
                    <div class="flex space-x-4 mt-4">
                        @if(!empty($footerSettings['social']['facebook']))
                            <a href="{{ $footerSettings['social']['facebook'] }}" class="text-gray-400 hover:text-gray-600"><span class="sr-only">Facebook</span>FB</a>
                        @endif
                        @if(!empty($footerSettings['social']['instagram']))
                            <a href="{{ $footerSettings['social']['instagram'] }}" class="text-gray-400 hover:text-gray-600"><span class="sr-only">Instagram</span>IG</a>
                        @endif
                         @if(!empty($footerSettings['social']['twitter']))
                            <a href="{{ $footerSettings['social']['twitter'] }}" class="text-gray-400 hover:text-gray-600"><span class="sr-only">Twitter</span>TW</a>
                        @endif
                    </div>
                </div>
                <div>
                    <h5 class="font-bold text-xs uppercase tracking-wider mb-4">Shop</h5>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-gray-900 transition">New Arrivals</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition">Best Sellers</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition">Accessories</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold text-xs uppercase tracking-wider mb-4">Support</h5>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-gray-900 transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition">Shipping & Returns</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold text-xs uppercase tracking-wider mb-4">Newsletter</h5>
                    <form class="flex border-b border-gray-300 pb-1">
                        <input type="email" placeholder="Email address" class="w-full bg-transparent border-none text-sm focus:ring-0 px-0">
                        <button type="submit" class="text-xs font-bold uppercase hover:text-gray-600">Join</button>
                    </form>
                </div>
            </div>
            <div class="text-center text-xs text-gray-400 pt-8 border-t border-gray-200">
                &copy; {{ date('Y') }} {{ tenant('id') }}. {{ $copyrightText }}
            </div>
        </div>
    </footer>

    {{-- Mobile Sticky Footer (Modern Minimal) --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 py-3 md:hidden z-50 flex justify-around items-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <a href="{{ route('storefront.home') }}" class="flex flex-col items-center text-gray-400 hover:text-gray-900 {{ request()->routeIs('storefront.home') ? 'text-gray-900' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] uppercase font-medium tracking-wide">Home</span>
        </a>
        <a href="{{ route('storefront.products.index') }}" class="flex flex-col items-center text-gray-400 hover:text-gray-900 {{ request()->routeIs('storefront.products.*') ? 'text-gray-900' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2H4V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2h-6V6zM4 16c0-1.1.9-2 2-2h12a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM4 12h16"></path></svg>
            <span class="text-[10px] uppercase font-medium tracking-wide">Shop</span>
        </a>
        <a href="{{ route('storefront.cart.index') }}" class="flex flex-col items-center text-gray-400 hover:text-gray-900 {{ request()->routeIs('storefront.cart.*') ? 'text-gray-900' : '' }} relative">
            <div class="relative">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                @if(\App\Facades\Cart::count() > 0)
                    <span class="absolute -top-1 -right-1 bg-black text-white text-[9px] font-bold h-3.5 w-3.5 flex items-center justify-center rounded-full">
                         {{ \App\Facades\Cart::count() }}
                    </span>
                 @endif
            </div>
            <span class="text-[10px] uppercase font-medium tracking-wide">Cart</span>
        </a>
        <a href="#" class="flex flex-col items-center text-gray-400 hover:text-gray-900">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] uppercase font-medium tracking-wide">Account</span>
        </a>
    </div>

</body>
</html>
