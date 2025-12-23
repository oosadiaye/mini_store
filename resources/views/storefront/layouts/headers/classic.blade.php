    @php
        $navClass = 'sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm transition-all duration-300';
    @endphp

    <nav class="{{ $navClass }}" x-data="{ mobileMenuOpen: false, searchOpen: false }">
        <!-- Top Bar (Mobile & Desktop) -->
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

        <div class="container mx-auto px-4 max-w-7xl">
            <div class="flex justify-between items-center h-16">
                
                <!-- Logo (Center on Mobile, Left on Desktop) -->
                <div class="flex-1 md:flex-none flex justify-start md:justify-start">
                    <a href="{{ route('storefront.home') }}" class="group flex items-center gap-2">
                        <x-logo size="sm" class="" />
                        <h1 class="text-xl md:text-2xl font-bold font-serif text-blue-900 tracking-tight">{{ tenant('name') }}</h1>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('storefront.home') }}" class="text-sm font-bold text-gray-800 hover:text-primary uppercase">Home</a>
                    <a href="{{ route('storefront.products') }}" class="text-sm font-bold text-gray-800 hover:text-primary uppercase">Shop</a>
                    <a href="{{ route('storefront.page', 'about-us') }}" class="text-sm font-bold text-gray-800 hover:text-primary uppercase">About</a>
                    <a href="{{ route('storefront.page', 'contact') }}" class="text-sm font-bold text-gray-800 hover:text-primary uppercase">Contact</a>
                </div>

                <!-- Right Icons (Cart, Account) -->
                <div class="flex items-center space-x-4">
                     <!-- Desktop Search -->
                    <div class="hidden md:block relative">
                        <form action="{{ route('storefront.search') }}" method="GET">
                            <input type="text" name="search" placeholder="Search..." 
                                class="w-48 bg-gray-100 text-sm border-gray-200 rounded-full py-2 pl-4 pr-10 focus:ring-1 focus:ring-primary focus:border-primary transition-all duration-300">
                            <button type="submit" class="absolute right-0 top-0 h-full px-3 text-gray-500 hover:text-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Account Icon -->
                    <a href="{{ Auth::guard('customer')->check() ? '#' : route('storefront.login') }}" class="text-gray-700 hover:text-primary">
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

            <!-- Mobile Search Bar (Always Visible below header) -->
            <div class="md:hidden pb-4">
                <form action="{{ route('storefront.search') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Search..." class="w-full bg-white border border-gray-300 rounded-lg py-2.5 pl-4 pr-10 text-sm focus:ring-1 focus:ring-primary focus:border-primary shadow-sm">
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-primary p-1">
                        <i class="fas fa-search text-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>
