<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('pageTitle', 'Retail Shop') - {{ tenant('id') }}</title>

    <!-- Dynamic Settings -->
    @php
        $headingFont = $themeSettings->fonts['heading'] ?? 'Playfair Display';
        $bodyFont = $themeSettings->fonts['body'] ?? 'Inter';
        $primaryColor = $themeSettings->colors['primary'] ?? '#0d9488';
        $secondaryColor = $themeSettings->colors['secondary'] ?? '#581c87';
        $accentColor = $themeSettings->colors['accent'] ?? '#f59e0b';
        
        $footerSettings = $themeSettings->layout_settings['footer'] ?? [];
        $aboutText = $footerSettings['about'] ?? 'Defining modern retail with a touch of elegance. Browse our curated collection today.';
        $copyrightText = $footerSettings['copyright'] ?? 'All rights reserved.';
    @endphp

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($headingFont) }}:wght@400;600;700&family={{ urlencode($bodyFont) }}:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --font-heading: '{{ $headingFont }}', serif;
            --font-body: '{{ $bodyFont }}', sans-serif;
            --color-primary: {{ $primaryColor }};
            --color-secondary: {{ $secondaryColor }};
            --color-accent: {{ $accentColor }};
        }
    
        [x-cloak] { display: none !important; }
        body { font-family: var(--font-body); }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); }
        
        /* Dynamic Utilities */
        .text-primary { color: var(--color-primary) !important; }
        .text-secondary { color: var(--color-secondary) !important; }
        .text-accent { color: var(--color-accent) !important; }
        
        .bg-primary { background-color: var(--color-primary) !important; }
        .bg-secondary { background-color: var(--color-secondary) !important; }
        
        .hover\:text-primary:hover { color: var(--color-primary) !important; }
        .hover\:bg-primary:hover { background-color: var(--color-primary) !important; }

        .border-primary { border-color: var(--color-primary) !important; }
        .focus\:ring-primary:focus { --tw-ring-color: var(--color-primary) !important; }

        /* Premium Gradient Background */
        .bg-gradient-premium {
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
        }
        
        .bg-gradient-brand {
             background-image: linear-gradient(to right, var(--color-primary), var(--color-secondary));
        }
        
        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        
        /* Scrollbar hiding */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Custom CSS */
        {!! $themeSettings->custom_css ?? '' !!}
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased selection:bg-gray-300 selection:text-gray-900 flex flex-col min-h-screen">
    
    <!-- Header -->
    <header x-data="{ mobileMenuOpen: false, searchOpen: false }" class="sticky top-0 z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="container mx-auto px-4 md:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('storefront.home') }}" class="text-2xl font-bold tracking-tight font-serif bg-clip-text text-transparent bg-gradient-brand">
                        {{ tenant('id') }}<span class="text-gray-900">.</span>
                    </a>
                </div>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('storefront.home') }}" class="text-sm font-medium hover:text-primary transition uppercase tracking-wider text-gray-700">Home</a>
                    <a href="{{ route('storefront.products.index') }}" class="text-sm font-medium hover:text-primary transition uppercase tracking-wider text-gray-700">Shop</a>
                    <a href="{{ route('storefront.page', 'about') }}" class="text-sm font-medium hover:text-primary transition uppercase tracking-wider text-gray-700">About Us</a>
                    <a href="{{ route('storefront.page', 'contact') }}" class="text-sm font-medium hover:text-primary transition uppercase tracking-wider text-gray-700">Contact Us</a>
                </nav>

                <!-- Icons -->
                <div class="flex items-center space-x-6">
                    <button @click="searchOpen = !searchOpen" class="text-gray-500 hover:text-primary transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('storefront.cart.index') }}" class="group relative text-gray-500 hover:text-primary transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span class="absolute -top-1 -right-2 bg-gradient-brand text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm group-hover:scale-110 transition">
                             {{ \App\Models\Cart::where('session_id', session()->getId())->first()?->total_items ?? 0 }}
                        </span>
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div x-show="searchOpen" x-transition x-cloak class="absolute top-full left-0 w-full bg-white border-b border-gray-100 p-4 shadow-lg">
            <form action="{{ route('storefront.products.index') }}" method="GET" class="container mx-auto max-w-2xl relative">
                <input type="text" name="search" placeholder="Search products..." class="w-full pl-4 pr-12 py-3 bg-gray-50 border-none rounded-lg focus:ring-2 focus:ring-primary">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition x-cloak class="fixed inset-0 z-50 bg-white">
            <div class="flex justify-between items-center p-4 border-b border-gray-100">
                <span class="font-bold text-xl font-serif">Menu</span>
                <button @click="mobileMenuOpen = false" class="p-2 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-8 space-y-6 flex flex-col items-center justify-center h-screen pb-32">
                <a href="{{ route('storefront.home') }}" class="text-2xl font-serif font-medium text-gray-900">Home</a>
                <a href="{{ route('storefront.products.index') }}" class="text-2xl font-serif font-medium text-gray-900">Shop</a>
                <a href="{{ route('storefront.page', 'about') }}" class="text-2xl font-serif font-medium text-gray-900">About Us</a>
                <a href="{{ route('storefront.page', 'contact') }}" class="text-2xl font-serif font-medium text-gray-900">Contact Us</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-auto pt-16 pb-8">
        <div class="container mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div>
                    <h3 class="text-2xl font-serif font-bold mb-6 bg-clip-text text-transparent bg-gradient-brand">{{ tenant('id') }}</h3>
                    <p class="text-gray-400 leading-relaxed">{{ $aboutText }}</p>
                </div>
                <div>
                    <h4 class="font-bold uppercase tracking-widest mb-6 text-sm text-gray-300">Shop</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="{{ route('storefront.products.index') }}" class="hover:text-white transition">All Products</a></li>
                        <li><a href="#" class="hover:text-white transition">New Arrivals</a></li>
                        <li><a href="#" class="hover:text-white transition">Best Sellers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold uppercase tracking-widest mb-6 text-sm text-gray-300">Company</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="{{ route('storefront.page', 'about') }}" class="hover:text-white transition">About Us</a></li>
                        <li><a href="{{ route('storefront.page', 'contact') }}" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold uppercase tracking-widest mb-6 text-sm text-gray-300">Connect</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-teal-600 transition">
                            <span class="sr-only">Facebook</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-teal-600 transition">
                            <span class="sr-only">Instagram</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.073-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} {{ tenant('id') }}. {{ $copyrightText }} 
            </div>
        </div>
    </footer>

    {{-- Mobile Sticky Footer (Retail Shop) --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-gray-200 py-3 md:hidden z-50 flex justify-around items-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <a href="{{ route('storefront.home') }}" class="group flex flex-col items-center text-gray-400 hover:text-primary {{ request()->routeIs('storefront.home') ? 'text-primary' : '' }}">
            <svg class="w-6 h-6 mb-1 transition-transform group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-bold tracking-wide">Home</span>
        </a>
        <a href="{{ route('storefront.products.index') }}" class="group flex flex-col items-center text-gray-400 hover:text-primary {{ request()->routeIs('storefront.products.*') ? 'text-primary' : '' }}">
            <svg class="w-6 h-6 mb-1 transition-transform group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <span class="text-[10px] font-bold tracking-wide">Shop</span>
        </a>
        <a href="{{ route('storefront.cart.index') }}" class="group flex flex-col items-center text-gray-400 hover:text-primary {{ request()->routeIs('storefront.cart.*') ? 'text-primary' : '' }} relative">
            <div class="relative">
                <svg class="w-6 h-6 mb-1 transition-transform group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                @if(\App\Facades\Cart::count() > 0)
                    <span class="absolute -top-2 -right-2 bg-gradient-brand text-white text-[9px] font-bold h-4 w-4 flex items-center justify-center rounded-full shadow-sm">
                         {{ \App\Facades\Cart::count() }}
                    </span>
                 @endif
            </div>
            <span class="text-[10px] font-bold tracking-wide">Cart</span>
        </a>
        <a href="#" class="group flex flex-col items-center text-gray-400 hover:text-primary">
            <svg class="w-6 h-6 mb-1 transition-transform group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-bold tracking-wide">Account</span>
        </a>
    </div>
</body>
</html>
