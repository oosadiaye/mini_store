@php
    $navClass = 'sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm transition-all duration-300';
@endphp

<nav class="{{ $navClass }}" x-data="{ drawerOpen: false, searchOpen: false }">
    <!-- Top Bar -->
    <div class="bg-gray-100 py-1.5 px-4 text-xs text-gray-600 border-b border-gray-200">
        <div class="container mx-auto max-w-7xl flex justify-between items-center">
            <div class="truncate">Welcome to {{ tenant('name') }}!</div>
            <div class="flex items-center gap-3">
                @if(Auth::guard('customer')->check())
                    <a href="#" class="hover:text-primary">My Account</a>
                @else
                    <a href="{{ route('storefront.login') }}" class="hover:text-primary">Sign In</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('storefront.register') }}" class="hover:text-primary">Register</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex justify-between items-center h-16">
            
            <!-- Left: Drawer Toggle & Logo -->
            <div class="flex items-center gap-4">
                <!-- Hamburger Button -->
                <button @click="drawerOpen = true" class="p-2 -ml-2 text-gray-700 hover:text-primary transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Logo -->
                <a href="{{ route('storefront.home') }}" class="group flex items-center gap-2">
                    <x-logo size="sm" class="" />
                    <h1 class="text-xl md:text-2xl font-bold font-serif text-blue-900 tracking-tight">{{ tenant('name') }}</h1>
                </a>
            </div>

            <!-- Right: Search, Account, Cart -->
            <div class="flex items-center space-x-4">
                <!-- Search Icon -->
                <button @click="searchOpen = !searchOpen" class="text-gray-700 hover:text-primary">
                    <i class="fas fa-search text-xl"></i>
                </button>

                <!-- Account Icon -->
                <a href="{{ Auth::guard('customer')->check() ? '#' : route('storefront.login') }}" class="text-gray-700 hover:text-primary hidden md:block">
                    <i class="far fa-user text-xl"></i>
                </a>

                <!-- Cart Icon -->
                <a href="{{ route('storefront.cart.index') }}" class="relative group text-gray-700 hover:text-primary transition">
                    <i class="fas fa-shopping-bag text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-primary text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full {{ (\App\Models\Cart::where('session_id', session()->getId())->first()?->items->sum('quantity') ?? 0) > 0 ? '' : 'hidden' }}">
                        {{ \App\Models\Cart::where('session_id', session()->getId())->first()?->items->sum('quantity') ?? 0 }}
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search Bar (Collapsible) -->
    <div x-show="searchOpen" x-collapse class="border-t border-gray-100 bg-gray-50 p-4">
        <form action="{{ route('storefront.search') }}" method="GET" class="container mx-auto max-w-2xl relative">
            <input type="text" name="search" placeholder="Search our catalog..." class="w-full bg-white border border-gray-300 rounded-lg py-3 pl-4 pr-12 text-sm focus:ring-2 focus:ring-primary focus:border-transparent shadow-sm">
            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-primary p-1">
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

    <!-- Drawer Component -->
    <div x-show="drawerOpen" class="fixed inset-0 z-[60] flex" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="drawerOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/50 backdrop-blur-sm" 
             @click="drawerOpen = false"></div>

        <!-- Panel -->
        <div x-show="drawerOpen" 
             x-transition:enter="transition ease-in-out duration-300 transform" 
             x-transition:enter-start="-translate-x-full" 
             x-transition:enter-end="translate-x-0" 
             x-transition:leave="transition ease-in-out duration-300 transform" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="-translate-x-full" 
             class="relative bg-white w-80 max-w-[85vw] h-full shadow-2xl flex flex-col">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <span class="font-bold text-lg text-gray-800">Menu</span>
                <button @click="drawerOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto py-4 px-6 space-y-6">
                <!-- Navigation Links -->
                <nav class="flex flex-col space-y-4">
                    <a href="{{ route('storefront.home') }}" class="text-lg font-medium text-gray-800 hover:text-primary transition flex items-center justify-between group">
                        Home <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-opacity text-gray-400"></i>
                    </a>
                    <a href="{{ route('storefront.products') }}" class="text-lg font-medium text-gray-800 hover:text-primary transition flex items-center justify-between group">
                        Shop <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-opacity text-gray-400"></i>
                    </a>
                    <a href="{{ route('storefront.page', 'about-us') }}" class="text-lg font-medium text-gray-800 hover:text-primary transition flex items-center justify-between group">
                        About <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-opacity text-gray-400"></i>
                    </a>
                    <a href="{{ route('storefront.page', 'contact') }}" class="text-lg font-medium text-gray-800 hover:text-primary transition flex items-center justify-between group">
                        Contact <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-opacity text-gray-400"></i>
                    </a>
                </nav>

                <hr class="border-gray-100">

                <!-- Account Links -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Account</h3>
                    @if(Auth::guard('customer')->check())
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                {{ substr(Auth::guard('customer')->user()->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ Auth::guard('customer')->user()->name }}</p>
                                <a href="#" class="text-xs text-primary hover:underline">View Profile</a>
                            </div>
                        </div>
                        <a href="#" class="block text-gray-600 hover:text-red-600 text-sm">Logout</a>
                    @else
                        <a href="{{ route('storefront.login') }}" class="block text-gray-600 hover:text-primary text-sm font-medium">Sign In</a>
                        <a href="{{ route('storefront.register') }}" class="block text-gray-600 hover:text-primary text-sm font-medium">Register</a>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-gray-100 bg-gray-50 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ tenant('name') }}
            </div>
        </div>
    </div>
</nav>
