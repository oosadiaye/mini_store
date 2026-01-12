<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $tenant = app('tenant');
        $seoTitle = $tenant->data['meta_title'] ?? $config->store_name ?? 'Our Store';
        $seoDesc = $tenant->data['meta_description'] ?? '';
        $seoKeywords = $tenant->data['meta_keywords'] ?? '';
        $ogImage = isset($tenant->data['og_image']) ? asset('storage/' . $tenant->data['og_image']) : null;
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <meta name="keywords" content="{{ $seoKeywords }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    @if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $seoTitle }}">
    <meta property="twitter:description" content="{{ $seoDesc }}">
    @if($ogImage)
    <meta property="twitter:image" content="{{ $ogImage }}">
    @endif

    <!-- Fonts: Dynamic based on settings -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Basic Defaults -->
    <link href="https://fonts.googleapis.com/css2?family=Internal:wght@100..900&family=Inter:wght@300;400;500;600;700&family=Lato:wght@300;400;700&family=Oswald:wght@400;500;700&family=Open+Sans:wght@300;400;600&family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;600&family=Roboto:wght@300;400;500;700&family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs" defer></script>

    @php
        // Helper to safely get theme settings
        $theme = json_decode(Storage::disk('tenant')->get('theme_settings.json') ?? '{}', true);
        $fontHeading = 'Playfair Display'; // Premium Default
        $fontBody = 'Lato'; // Premium Default
        $radius = '12px'; // Softer, more modern radius
        
        // Settings Fallback (Tenant Data > Store Config > Default)
        $tenantSettings = app('tenant')->data ?? [];
        $logo = $tenantSettings['logo'] ?? $config->logo_path ?? null;
        $favicon = $tenantSettings['favicon'] ?? null;
        $storeName = $tenantSettings['store_name'] ?? $config->store_name ?? 'Our Store';
    @endphp

    <style>
        :root {
            --brand-color: {{ $config->brand_color ?? '#0A2540' }};
            --professional-navy: #0A2540;
            --brand-color-rgb: {{ hexdec(substr($config->brand_color ?? '#0A2540', 1, 2)) }}, {{ hexdec(substr($config->brand_color ?? '#0A2540', 3, 2)) }}, {{ hexdec(substr($config->brand_color ?? '#0A2540', 5, 2)) }};
            --radius-default: {{ $radius }};
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Lato', sans-serif;
            --header-shadow: 0 4px 20px -5px rgba(10, 37, 64, 0.05);
        }
        
        /* Apply dynamic fonts & Radius */
        body {
            font-family: var(--font-body);
            color: #1A1A1A;
            line-height: 1.6;
            letter-spacing: -0.01em;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--professional-navy);
            letter-spacing: -0.02em;
        }
        
        .rounded-premium {
            border-radius: var(--radius-default) !important;
        }
        
        .btn-premium {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            border-radius: 50px !important;
        }
        
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(10, 37, 64, 0.1);
        }
        
        [x-cloak] { display: none !important; }

        /* Global Input Styling Overrides */
        input[type='text'], input[type='email'], input[type='password'], 
        input[type='number'], input[type='date'], input[type='datetime-local'], 
        input[type='month'], input[type='search'], input[type='tel'], 
        input[type='time'], input[type='url'], input[type='week'], 
        select, textarea {
            border-width: 2px !important;
            border-radius: 0.5rem !important; /* rounded-lg */
            border-color: #d1d5db !important; /* gray-300 */
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: var(--brand-color) !important;
            ring-width: 2px !important;
            --tw-ring-color: var(--brand-color) !important;
        }
    </style>
    
    @if($favicon)
        <link rel="icon" type="image/png" href="{{ '/storage/' . $favicon }}">
    @endif
</head>
<body class="font-sans text-gray-900 antialiased min-h-screen flex flex-col bg-white pb-16 lg:pb-0">

    <!-- Header / Navigation -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-50 transition-all duration-300 border-b border-gray-100" style="box-shadow: var(--header-shadow)" 
            x-data="{ mobileMenuOpen: false, categoryMenuOpen: false, cartDrawerOpen: false }"
            @open-cart-drawer.window="cartDrawerOpen = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-8">
                
                <!-- Left Section: Logo & Category Dropdown -->
                <div class="flex items-center gap-8">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('storefront.home', ['tenant' => app('tenant')->slug]) }}" class="flex items-center gap-3 group">
                            @if($logo)
                                <img class="h-9 w-auto group-hover:scale-105 transition-transform" src="{{ '/storage/' . $logo }}" alt="{{ $storeName }}">
                            @else
                                <div class="h-10 w-10 rounded-xl bg-[color:var(--brand-color)] flex items-center justify-center text-white font-bold shadow-sm group-hover:rotate-3 transition-transform">
                                    {{ substr($storeName, 0, 1) }}
                                </div>
                            @endif
                            <span class="font-bold text-xl tracking-tighter text-[#0A2540] hidden lg:block">{{ $storeName }}</span>
                        </a>
                    </div>

                    <!-- Desktop Browse Categories (Dropdown) -->
                    <div class="hidden lg:block relative" @mouseleave="categoryMenuOpen = false">
                        <button @mouseover="categoryMenuOpen = true" 
                                class="flex items-center gap-2 px-4 py-2 rounded-full bg-gray-50 hover:bg-gray-100 text-sm font-bold text-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <span>Browse Categories</span>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="categoryMenuOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute top-full left-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-2 z-50">
                            @if(isset($menuCategories) && $menuCategories->count() > 0)
                                @foreach($menuCategories as $category)
                                    <a href="{{ route('storefront.category', ['tenant' => app('tenant')->slug, 'slug' => $category->slug]) }}" 
                                       class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#0A2540] hover:pl-6 transition-all border-b border-gray-50 last:border-0">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            @else
                                <div class="px-4 py-3 text-sm text-gray-400">No categories found</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Center Section: Static Links -->
                <!-- Center Section: Static Links (Desktop) & Search (Mobile) -->
                <div class="flex-1 px-4 lg:hidden flex justify-center">
                    <form action="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" method="GET" 
                          class="flex items-center bg-gray-50/50 backdrop-blur-sm rounded-full px-3 py-2 w-full max-w-[220px] border-2 transition-all"
                          style="border-color: var(--brand-color);"
                    >
                         <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" name="search" 
                               class="w-full bg-transparent border-none p-0 text-xs sm:text-sm focus:ring-0 placeholder-gray-500 text-gray-900" 
                               placeholder="Search...">
                    </form>
                </div>

                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('storefront.home', ['tenant' => app('tenant')->slug]) }}" 
                       class="text-sm font-medium text-gray-600 hover:text-[#0A2540] transition-colors {{ request()->routeIs('storefront.home') ? 'text-[#0A2540] font-bold' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" 
                       class="text-sm font-medium text-gray-600 hover:text-[#0A2540] transition-colors {{ request()->routeIs('storefront.products.index') ? 'text-[#0A2540] font-bold' : '' }}">
                        Shop
                    </a>
                    <a href="{{ route('storefront.about', ['tenant' => app('tenant')->slug]) }}" 
                       class="text-sm font-medium text-gray-600 hover:text-[#0A2540] transition-colors {{ request()->routeIs('storefront.about') ? 'text-[#0A2540] font-bold' : '' }}">
                        About Us
                    </a>
                    <a href="{{ route('storefront.contact', ['tenant' => app('tenant')->slug]) }}" 
                       class="text-sm font-medium text-gray-600 hover:text-[#0A2540] transition-colors {{ request()->routeIs('storefront.contact') ? 'text-[#0A2540] font-bold' : '' }}">
                        Contact
                    </a>
                </nav>

                <!-- Right Section: Actions -->
                <div class="flex items-center gap-4">
                    <!-- Search (Responsive) -->
                    <div x-data="{ expanded: false }" class="relative flex items-center">
                        <!-- Desktop: Expanding Search -->
                        <form action="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" method="GET" 
                              @submit.prevent="if(!expanded) { expanded = true; $nextTick(() => $refs.searchInput.focus()); } else { $el.submit(); }"
                              class="hidden lg:flex items-center transition-all duration-300 ease-out bg-white rounded-full border border-gray-200 shadow-sm hover:shadow-md h-10 overflow-hidden"
                              :class="expanded ? 'w-64 px-4 border-brand-200 ring-2 ring-brand-50' : 'w-10 justify-center border-transparent shadow-none bg-transparent hover:bg-gray-100 cursor-pointer'"
                              @click="if(!expanded) { expanded = true; $nextTick(() => $refs.searchInput.focus()); }"
                              @click.away="if($refs.searchInput.value === '') expanded = false"
                        >
                            <button type="submit" class="text-gray-500 hover:text-[#0A2540] transition-colors flex-shrink-0 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            
                            <input type="text" name="search" x-ref="searchInput" 
                                   class="ml-3 w-full bg-transparent border-none focus:ring-0 text-sm text-gray-800 placeholder-gray-400 p-0"
                                   placeholder="Search products..."
                                   x-show="expanded"
                                   x-transition:enter="transition ease-out duration-200 delay-100"
                                   x-transition:enter-start="opacity-0 translate-x-2"
                                   x-transition:enter-end="opacity-100 translate-x-0"
                                   autocomplete="off"
                            >
                        </form>
                    </div>

                    <!-- Cart -->
                    <a href="{{ route('storefront.cart.index', ['tenant' => app('tenant')->slug]) }}" class="relative p-2 text-gray-400 hover:text-[#0A2540] transition-colors group">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="absolute -top-1 -right-1 h-4 w-4 bg-[#0A2540] text-white text-[10px] font-bold flex items-center justify-center rounded-full ring-2 ring-white">
                            {{ \App\Models\Cart::where('session_id', session()->getId())->first()?->items->sum('quantity') ?? 0 }}
                        </span>
                    </a>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="hidden p-2 text-gray-600 hover:text-[#0A2540]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    @auth('customer')
                        <a href="{{ route('storefront.account.index') }}" class="relative p-2 text-gray-400 hover:text-[#0A2540] transition-colors group" title="My Account">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('storefront.login') }}" class="hidden md:block text-sm font-medium text-gray-600 hover:text-[#0A2540] transition-colors">
                            Sign In
                        </a>
                    @endauth

                    @auth
                        <!-- Admin Link -->
                        <a href="{{ route('admin.dashboard', ['tenant' => app('tenant')->slug]) }}" class="hidden md:block text-xs font-bold text-[#0A2540] bg-gray-100 px-3 py-1 rounded-full hover:bg-gray-200 transition-colors">
                            Admin
                        </a>
                    @endauth
                </div>

            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 w-80 bg-white shadow-2xl z-50 overflow-y-auto md:hidden">
            
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <span class="font-heading font-bold text-xl text-[#0A2540]">Menu</span>
                <button @click="mobileMenuOpen = false" class="p-2 text-gray-400 hover:text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="py-4">
                <!-- Main Links -->
                <div class="space-y-1 px-4 mb-8">
                    <a href="{{ route('storefront.home', ['tenant' => app('tenant')->slug]) }}" class="block px-4 py-3 text-base font-medium text-gray-900 rounded-xl hover:bg-gray-100">Home</a>
                    <a href="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" class="block px-4 py-3 text-base font-medium text-gray-900 rounded-xl hover:bg-gray-100">Shop All Products</a>
                    <a href="{{ route('storefront.about', ['tenant' => app('tenant')->slug]) }}" class="block px-4 py-3 text-base font-medium text-gray-900 rounded-xl hover:bg-gray-100">About Us</a>
                    <a href="{{ route('storefront.contact', ['tenant' => app('tenant')->slug]) }}" class="block px-4 py-3 text-base font-medium text-gray-900 rounded-xl hover:bg-gray-100">Contact & Support</a>
                </div>

                <!-- Categories Section -->
                <div x-data="{ categoriesOpen: true }" class="px-4">
                    <button @click="categoriesOpen = !categoriesOpen" class="flex items-center justify-between w-full px-4 py-3 text-sm font-bold text-gray-500 uppercase tracking-widest hover:text-[#0A2540]">
                        <span>Browse Categories</span>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-180': categoriesOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="categoriesOpen" class="space-y-1 mt-2 pl-2 border-l-2 border-gray-100 ml-4">
                        @if(isset($menuCategories) && $menuCategories->count() > 0)
                            @foreach($menuCategories as $category)
                                <a href="{{ route('storefront.category', ['tenant' => app('tenant')->slug, 'slug' => $category->slug]) }}" class="block px-4 py-2.5 text-sm text-gray-600 rounded-lg hover:text-[#0A2540] hover:bg-gray-50">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        @else
                             <span class="block px-4 py-2 text-sm text-gray-400">No categories</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm"></div>

        <!-- Cart Drawer -->
        <div x-show="cartDrawerOpen" 
             class="fixed inset-0 z-[100] flex justify-end" 
             style="display: none;" 
             role="dialog" 
             aria-modal="true">
            
            <!-- Backdrop -->
            <div x-show="cartDrawerOpen"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/20 backdrop-blur-sm" 
                 @click="cartDrawerOpen = false"></div>

            <!-- Drawer Panel -->
            <div x-show="cartDrawerOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="relative w-full max-w-sm bg-white shadow-2xl flex flex-col h-full bg-white">
                 
                 <!-- Header -->
                 <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
                     <h2 class="text-lg font-bold font-heading text-[#0A2540]">Shopping Cart</h2>
                     <button @click="cartDrawerOpen = false" class="text-gray-400 hover:text-gray-500">
                         <span class="sr-only">Close</span>
                         <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                         </svg>
                     </button>
                 </div>
                 
                 <!-- Content (Simple Success State for now) -->
                 <div class="flex-1 px-6 py-12 flex flex-col items-center justify-center text-center">
                      <div class="h-16 w-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6 animate-bounce">
                          <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                      </div>
                      <h3 class="text-xl font-bold text-gray-900 mb-2">Item Added!</h3>
                      <p class="text-gray-500 mb-8 px-4">The item has been successfully added to your cart.</p>
                      
                      <div class="space-y-3 w-full">
                          <a href="{{ route('storefront.cart.index', ['tenant' => app('tenant')->slug]) }}" class="block w-full bg-[#0A2540] text-white py-3.5 rounded-lg font-bold hover:bg-[#1a3a5a] transition-colors shadow-lg">
                              View Cart & Checkout
                          </a>
                          <button @click="cartDrawerOpen = false" class="block w-full bg-white border border-gray-200 text-gray-700 py-3.5 rounded-lg font-bold hover:bg-gray-50 transition-colors">
                              Continue Shopping
                          </button>
                      </div>
                 </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="hidden lg:block bg-gray-50 border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8 text-center md:text-left">
                <!-- Brand / Description -->
                <div class="col-span-1 md:col-span-1">
                    <span class="font-bold text-xl tracking-tighter text-[#0A2540]">{{ $storeName }}</span>
                    <p class="mt-4 text-sm text-gray-500">
                        Quality products, delivering excellence to your doorstep.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="col-span-1 md:col-span-1">
                   <h3 class="text-sm font-bold text-gray-900 tracking-wider uppercase mb-4">Quick Links</h3>
                   <ul class="space-y-3">
                        <li><a href="{{ route('storefront.home', ['tenant' => app('tenant')->slug]) }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">Home</a></li>
                        <li><a href="{{ route('storefront.products.index', ['tenant' => app('tenant')->slug]) }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">Shop</a></li>
                        <li><a href="{{ route('storefront.about', ['tenant' => app('tenant')->slug]) }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">About Us</a></li>
                        <li><a href="{{ route('storefront.contact', ['tenant' => app('tenant')->slug]) }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">Contact</a></li>
                   </ul>
                </div>
                
                 <!-- Customer Service -->
                 <div class="col-span-1 md:col-span-1">
                   <h3 class="text-sm font-bold text-gray-900 tracking-wider uppercase mb-4">Support</h3>
                   <ul class="space-y-3">
                        <li><a href="{{ route('storefront.orders.track', ['tenant' => app('tenant')->slug]) }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">Track Order</a></li>
                        <li><a href="{{ route('storefront.faq', ['tenant' => app('tenant')->slug]) }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">FAQ</a></li>
                        <li><a href="{{ route('storefront.shipping', ['tenant' => app('tenant')->slug]) }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">Shipping Policy</a></li>
                        <li><a href="{{ route('storefront.returns', ['tenant' => app('tenant')->slug]) }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">Returns</a></li>
                        @if(!empty($contact['email']))
                            <li><a href="mailto:{{ $contact['email'] }}" class="text-sm text-gray-500 hover:text-[#0A2540] transition-colors">{{ $contact['email'] }}</a></li>
                        @endif
                   </ul>
                </div>

                <!-- Newsletter (Optional / Placeholder) -->
                 <div class="col-span-1 md:col-span-1">
                   <h3 class="text-sm font-bold text-gray-900 tracking-wider uppercase mb-4">Stay Updated</h3>
                   <p class="text-sm text-gray-500 mb-4">Subscribe to our newsletter for the latest updates.</p>
                   <form class="flex flex-col gap-2" onsubmit="event.preventDefault(); alert('Subscribed!');">
                       <input type="email" placeholder="Enter your email" class="px-4 py-2 rounded-lg border border-gray-200 focus:border-[#0A2540] focus:ring-0 text-sm">
                       <button type="submit" class="bg-[#0A2540] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#1a3a5a] transition-colors">Subscribe</button>
                   </form>
                </div>
             </div>

            <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                
                {{-- Social Media Links (Dynamic) --}}
                <div class="flex space-x-6">
                    @php
                        $contact = $theme['injected_data']['contact_info'] ?? [];
                        $socials = $contact['social_links'] ?? [];
                    @endphp

                    @foreach($socials as $social)
                        <a href="{{ $social['url'] }}" target="_blank" class="text-gray-400 hover:text-[#0A2540] transition-colors">
                            <span class="sr-only">{{ ucfirst($social['platform']) }}</span>
                            @if($social['platform'] === 'facebook')
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                            @elseif($social['platform'] === 'instagram')
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.468 2.53c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                            @elseif($social['platform'] === 'twitter')
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                            @else
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" /></svg>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="text-center md:text-right">
                    <p class="text-base text-gray-400">
                        &copy; {{ date('Y') }} {{ $config->store_name }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Actions Script -->
    <script>
        function cartActions() {
            return {
                loading: null,
                async addToCart(productId) {
                    this.loading = productId;
                    try {
                        const res = await fetch('{{ route("storefront.cart.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ product_id: productId, quantity: 1 })
                        });
                        
                        const data = await res.json();
                        
                        if (res.ok) {
                            // Update cart count if exists
                            if (typeof updateCartCount === 'function') {
                                updateCartCount(data.count);
                            }
                            // Show success message (simple toast)
                            const toast = document.createElement('div');
                            toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl z-50 animate-bounce';
                            toast.innerHTML = 'Added to cart!';
                            document.body.appendChild(toast);
                            setTimeout(() => toast.remove(), 2000);
                        } else {
                            alert(data.error || 'Failed to add item');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Something went wrong');
                    } finally {
                        this.loading = null;
                    }
                }
            }
        }
    </script>

    <!-- Inject Theme Config for CMS Store -->
    <script>
        window.themeConfig = @json($theme);
    </script>

    {{-- <x-storefront.cms-script /> --}}

    @stack('scripts')
    <!-- Global Scroll Reset -->
    <script>
        // Force scroll to top on page load to emulate "fresh" navigation in case of browser history retention
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        window.scrollTo(0, 0);
    </script>
    <x-storefront.mobile-bottom-nav />
</body>
</html>
