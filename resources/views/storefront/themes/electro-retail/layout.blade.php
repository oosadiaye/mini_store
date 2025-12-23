<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('pageTitle', 'Home') - {{ tenant('store_name') }}</title>
    
    <!-- Dynamic Settings -->
    @php
        $headingFont = $themeSettings->fonts['heading'] ?? 'Oswald';
        $bodyFont = $themeSettings->fonts['body'] ?? 'Roboto';
        $primaryColor = $themeSettings->colors['primary'] ?? '#2563eb';
        $secondaryColor = $themeSettings->colors['secondary'] ?? '#0f172a';
        $accentColor = $themeSettings->colors['accent'] ?? '#facc15';
        
        $footerSettings = $themeSettings->layout_settings['footer'] ?? [];
        $aboutText = $footerSettings['about'] ?? 'Curated essentials for the modern lifestyle.';
        $copyrightText = $footerSettings['copyright'] ?? 'All rights reserved.';

        // Branding - Use LogoHelper for consistent behavior
        $storeName = tenant('name');
        $logoUrl = \App\Helpers\LogoHelper::getLogo(80); // 80px height for header
        $hasCustomLogo = !empty(tenant()->data['logo'] ?? null);
    @endphp

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($headingFont) }}:wght@300;400;500;600;700&family={{ urlencode($bodyFont) }}:wght@300;400;500;700&display=swap" rel="stylesheet">
 
    {{-- Favicon (custom or auto-generated) --}}
    @php
        $faviconUrl = \App\Helpers\LogoHelper::getFavicon();
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">
 
    <!-- Tailwind / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
 
    <!-- Theme Variables (Isolated) -->
    <style>
        :root {
            --electro-dark: {{ $secondaryColor }};
            --electro-blue: {{ $primaryColor }};
            --electro-neon: {{ $accentColor }};
            --electro-gray: #f1f5f9;
            --font-heading: '{{ $headingFont }}', sans-serif;
            --font-body: '{{ $bodyFont }}', sans-serif;
        }
        body {
            font-family: var(--font-body);
            background-color: #f8fafc;
            color: #334155;
        }
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: var(--font-heading);
            text-transform: uppercase; /* Tech feel */
            letter-spacing: 0.05em;
        }
        .bg-electro-dark { background-color: var(--electro-dark); }
        .bg-electro-blue { background-color: var(--electro-blue); }
        .bg-electro-neon { background-color: var(--electro-neon); }
        .text-electro-blue { color: var(--electro-blue); }
        .text-electro-neon { color: var(--electro-neon); }
        .border-electro-blue { border-color: var(--electro-blue); }

        .hover\:bg-electro-blue:hover { background-color: var(--electro-blue); }
        .hover\:text-electro-neon:hover { color: var(--electro-neon); }
        .hover\:border-electro-blue:hover { border-color: var(--electro-blue); }
        .group:hover .group-hover\:bg-electro-blue { background-color: var(--electro-blue); }
        .group:hover .group-hover\:text-electro-blue { color: var(--electro-blue); }
        .focus\:ring-electro-neon:focus { --tw-ring-color: var(--electro-neon); }
        
        .container-custom {
            max-width: 1400px; /* Widescreen for tech stores */
            margin: 0 auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        /* Custom CSS */
        {!! $themeSettings->custom_css ?? '' !!}
    </style>
</head>
<body class="antialiased flex flex-col min-h-screen">

    <!-- Top Bar -->
    <div class="bg-gray-100 text-xs text-gray-500 py-2 border-b border-gray-200 hidden md:block">
        <div class="container-custom flex justify-between items-center">
            <div>
                <span class="mr-4">Welcome to {{ $storeName }}!</span>
                <span class="text-electro-blue font-bold">Free Shipping on Orders Over $100</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#" class="hover:text-electro-blue">Track Order</a>
                <a href="#" class="hover:text-electro-blue">Support</a>
                <div class="border-l border-gray-300 pl-4">
                    {{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }} / EN
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-electro-dark text-white sticky top-0 z-50 shadow-lg" x-data="{ mobileMenuOpen: false, searchOpen: false }">
        <div class="container-custom py-4">
            <div class="flex items-center justify-between gap-8">
                
                <!-- Logo -->
                <a href="{{ route('storefront.home') }}" class="flex-shrink-0 flex items-center gap-3">
                    <img src="{{ $logoUrl }}" alt="{{ $storeName }}" class="h-10 w-auto">
                    <span class="font-heading text-2xl font-bold tracking-tighter text-white">{{ $storeName }}</span>
                </a>

                <!-- Search (Desktop) -->
                <div class="flex-1 max-w-2xl hidden md:block">
                    <form action="{{ route('storefront.products.index') }}" method="GET" class="relative">
                        <div class="flex">
                            <select class="bg-gray-800 text-gray-300 text-sm border-none focus:ring-0 rounded-l-md px-4 py-3 outline-none cursor-pointer hover:bg-gray-700 transition w-40">
                                <option value="">All Categories</option>
                                @foreach(\App\Models\Category::active()->take(8)->get() as $cat)
                                    <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="search" placeholder="Search for products..." class="w-full bg-white text-gray-900 border-none px-4 py-3 focus:ring-2 focus:ring-electro-neon outline-none">
                            <button type="submit" class="bg-electro-blue text-white px-6 py-3 font-heading font-bold uppercase hover:bg-blue-700 transition rounded-r-md">
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Icons -->
                <div class="flex items-center space-x-6">
                    <button @click="searchOpen = !searchOpen" class="md:hidden text-white hover:text-electro-neon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    
                    @auth('customer')
                        <a href="{{ route('storefront.customer.profile') }}" class="flex items-center gap-2 group">
                            <div class="relative">
                                <svg class="w-7 h-7 text-gray-300 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="hidden lg:block text-left leading-tight">
                                <div class="text-[10px] text-gray-400">Welcome</div>
                                <div class="text-sm font-bold">{{ Auth::guard('customer')->user()->name }}</div>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('storefront.login') }}" class="flex items-center gap-2 group">
                            <div class="relative">
                                <svg class="w-7 h-7 text-gray-300 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="hidden lg:block text-left leading-tight">
                                <div class="text-[10px] text-gray-400">Welcome</div>
                                <div class="text-sm font-bold">Sign In / Register</div>
                            </div>
                        </a>
                    @endauth

                    <a href="{{ route('storefront.cart.index') }}" class="flex items-center gap-2 group relative">
                        <div class="relative">
                            <svg class="w-7 h-7 text-gray-300 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="absolute -top-1 -right-1 bg-electro-neon text-electro-dark text-[10px] font-bold h-4 w-4 rounded-full flex items-center justify-center">
                                {{ \App\Facades\Cart::count() }}
                            </span>
                        </div>
                        <div class="hidden lg:block text-left leading-tight">
                            <div class="text-[10px] text-gray-400">My Cart</div>
                            <div class="text-sm font-bold">{{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}0.00</div>
                        </div>
                    </a>

                     <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search Dropdown -->
            <div x-show="searchOpen" class="mt-4 md:hidden">
                 <form action="{{ route('storefront.products.index') }}" method="GET">
                    <input type="text" name="search" placeholder="Search..." class="w-full bg-gray-800 text-white border-0 rounded p-3 focus:ring-1 focus:ring-electro-neon">
                 </form>
            </div>
        </div>

        <!-- Navigation Bar -->
        <nav class="border-t border-gray-800 bg-gray-900 hidden md:block">
            <div class="container-custom">
                <ul class="flex items-center space-x-8 text-sm font-bold uppercase tracking-wide h-12">
                    <li class="h-full flex items-center">
                        <a href="{{ route('storefront.products.index') }}" class="bg-electro-blue h-full flex items-center px-6 hover:bg-blue-600 transition gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                             Top Categories
                        </a>
                    </li>
                    
                    @if(isset($themeSettings->layout_settings['header_menu']))
                        @foreach($themeSettings->layout_settings['header_menu'] as $item)
                            <li><a href="{{ $item['url'] }}" class="text-white hover:text-electro-neon transition">{{ $item['label'] }}</a></li>
                        @endforeach
                    @else
                        <li><a href="{{ route('storefront.home') }}" class="text-white hover:text-electro-neon transition">Home</a></li>
                        <li><a href="{{ route('storefront.products.index') }}" class="text-white hover:text-electro-neon transition">Shop</a></li>
                    @endif
                    
                    <li class="ml-auto text-electro-neon">Special Offer: Save 20% on Laptops</li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Mobile Drawer -->
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 flex md:hidden bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white w-3/4 max-w-xs h-full shadow-2xl flex flex-col" @click.away="mobileMenuOpen = false">
            <div class="p-4 bg-electro-dark text-white flex justify-between items-center">
                <span class="font-heading font-bold text-xl">MENU</span>
                <button @click="mobileMenuOpen = false"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <a href="{{ route('storefront.home') }}" class="block text-gray-800 font-bold border-b border-gray-100 pb-2">Home</a>
                <a href="{{ route('storefront.products.index') }}" class="block text-gray-800 font-bold border-b border-gray-100 pb-2">Shop</a>
                <div class="pt-2">
                    <h4 class="text-xs uppercase text-gray-400 font-bold mb-2">Categories</h4>
                    @foreach(\App\Models\Category::active()->get() as $cat)
                        <a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="block py-2 text-gray-600 hover:text-electro-blue">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-electro-dark text-gray-400 pt-16 pb-8 border-t-4 border-electro-blue">
        <div class="container-custom grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            
            <!-- Col 1 -->
            <div>
                 <img src="{{ $logoUrl }}" alt="{{ $storeName }}" class="h-12 w-auto mb-6 brightness-0 invert">
                 <p class="text-sm mb-6 leading-relaxed">
                     {{ $aboutText }}
                 </p>
                 <div class="flex space-x-4">
                     <!-- Socials -->
                     @if(!empty($footerSettings['social']['facebook']))
                        <a href="{{ $footerSettings['social']['facebook'] }}" class="bg-gray-800 hover:bg-electro-blue text-white w-8 h-8 rounded flex items-center justify-center transition"><i class="fab fa-facebook-f"></i></a>
                     @endif
                     @if(!empty($footerSettings['social']['twitter']))
                        <a href="{{ $footerSettings['social']['twitter'] }}" class="bg-gray-800 hover:bg-electro-blue text-white w-8 h-8 rounded flex items-center justify-center transition"><i class="fab fa-twitter"></i></a>
                     @endif
                     @if(!empty($footerSettings['social']['instagram']))
                        <a href="{{ $footerSettings['social']['instagram'] }}" class="bg-gray-800 hover:bg-electro-blue text-white w-8 h-8 rounded flex items-center justify-center transition"><i class="fab fa-instagram"></i></a>
                     @endif
                 </div>
            </div>

            <!-- Col 2 -->
            <div>
                <h4 class="text-white font-heading font-bold text-lg mb-6">Customer Service</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-electro-neon transition">Help Center</a></li>
                    <li><a href="#" class="hover:text-electro-neon transition">Track My Order</a></li>
                    <li><a href="#" class="hover:text-electro-neon transition">Returns & Refunds</a></li>
                    <li><a href="#" class="hover:text-electro-neon transition">Warranty Info</a></li>
                </ul>
            </div>

            <!-- Col 3 -->
            <div>
                 <h4 class="text-white font-heading font-bold text-lg mb-6">Quick Links</h4>
                 <ul class="space-y-3 text-sm">
                     <li><a href="{{ route('storefront.products.index') }}" class="hover:text-electro-neon transition">Laptops & Computers</a></li>
                     <li><a href="{{ route('storefront.products.index') }}" class="hover:text-electro-neon transition">Smartphones</a></li>
                     <li><a href="{{ route('storefront.products.index') }}" class="hover:text-electro-neon transition">Cameras & Photography</a></li>
                     <li><a href="{{ route('storefront.products.index') }}" class="hover:text-electro-neon transition">Audio & Headphones</a></li>
                 </ul>
            </div>

            <!-- Col 4 -->
             <div>
                <h4 class="text-white font-heading font-bold text-lg mb-6">Newsletter</h4>
                <p class="text-sm mb-4">Subscribe to get info on new arrivals and special offers.</p>
                <form class="flex">
                    <input type="email" placeholder="Email Address" class="bg-gray-800 border-none text-white text-sm px-4 py-2 w-full focus:ring-1 focus:ring-electro-neon">
                    <button class="bg-electro-blue text-white px-4 font-bold uppercase text-xs hover:bg-blue-600">Sub</button>
                </form>
             </div>
        </div>
        <div class="container-custom border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-xs">
            <p>&copy; {{ date('Y') }} {{ tenant('store_name') }}. {{ $copyrightText }}</p>
            <div class="flex space-x-2 mt-4 md:mt-0 opacity-50">
                 <!-- Payment Icons -->
                 <div class="w-8 h-5 bg-gray-700 rounded"></div>
                 <div class="w-8 h-5 bg-gray-700 rounded"></div>
                 <div class="w-8 h-5 bg-gray-700 rounded"></div>
            </div>
        </div>
    </footer>

    {{-- Mobile Sticky Footer (Electro Retail) --}}
    <div class="fixed bottom-0 left-0 right-0 bg-electro-dark border-t border-gray-800 py-3 md:hidden z-50 flex justify-around items-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.3)]">
        <a href="{{ route('storefront.home') }}" class="flex flex-col items-center text-gray-400 hover:text-electro-neon {{ request()->routeIs('storefront.home') ? 'text-electro-neon' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[9px] font-heading font-bold uppercase tracking-wide">Home</span>
        </a>
        <a href="{{ route('storefront.products.index') }}" class="flex flex-col items-center text-gray-400 hover:text-electro-neon {{ request()->routeIs('storefront.products.*') ? 'text-electro-neon' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2H4V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2h-6V6zM4 16c0-1.1.9-2 2-2h12a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM4 12h16"></path></svg>
            <span class="text-[9px] font-heading font-bold uppercase tracking-wide">Shop</span>
        </a>
        <a href="#" class="flex flex-col items-center text-gray-400 hover:text-electro-neon relative">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[9px] font-heading font-bold uppercase tracking-wide">Deals</span>
            <span class="absolute top-0 right-2 w-2 h-2 bg-electro-blue rounded-full animate-pulse"></span>
        </a>
        <a href="{{ route('storefront.cart.index') }}" class="flex flex-col items-center text-gray-400 hover:text-electro-neon {{ request()->routeIs('storefront.cart.*') ? 'text-electro-neon' : '' }} relative">
            <div class="relative">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                @if(\App\Facades\Cart::count() > 0)
                    <span class="absolute -top-1 -right-1 bg-electro-neon text-electro-dark text-[10px] font-bold h-3.5 w-3.5 flex items-center justify-center rounded-full">
                         {{ \App\Facades\Cart::count() }}
                    </span>
                 @endif
            </div>
            <span class="text-[9px] font-heading font-bold uppercase tracking-wide">Cart</span>
        </a>
        <a href="#" class="flex flex-col items-center text-gray-400 hover:text-electro-neon">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[9px] font-heading font-bold uppercase tracking-wide">Account</span>
        </a>
    </div>

</body>
</html>
