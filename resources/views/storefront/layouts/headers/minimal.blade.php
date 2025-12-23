<nav class="bg-white border-b border-gray-100" x-data="{ mobileMenuOpen: false }">
    <div class="container mx-auto px-4 h-20 flex items-center justify-between">
        
        <!-- Left: Hamburger (All Screens) -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 -ml-2 text-gray-900 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <!-- Center: Logo -->
        <a href="{{ route('storefront.home') }}" class="absolute left-1/2 -translate-x-1/2 flex items-center gap-2">
            <x-logo size="sm" />
            <span class="font-bold text-xl tracking-tight">{{ tenant('name') }}</span>
        </a>

        <!-- Right: Cart Only -->
        <div class="flex items-center gap-4">
            <a href="{{ route('storefront.cart.index') }}" class="relative text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full {{ (\App\Models\Cart::where('session_id', session()->getId())->first()?->items->sum('quantity') ?? 0) > 0 ? '' : 'hidden' }}"></div>
            </a>
        </div>
    </div>

    <!-- Full Screen Menu Overlay -->
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 bg-white" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         x-cloak>
        
        <div class="container mx-auto px-4 py-8 relative h-full flex flex-col">
            <button @click="mobileMenuOpen = false" class="absolute top-8 left-4 p-2 -ml-2 text-gray-900">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="flex-1 flex flex-col justify-center items-center space-y-8">
                <a href="{{ route('storefront.home') }}" class="text-3xl font-bold hover:text-gray-500">Home</a>
                <a href="{{ route('storefront.products') }}" class="text-3xl font-bold hover:text-gray-500">Shop</a>
                <a href="{{ route('storefront.page', 'about-us') }}" class="text-3xl font-bold hover:text-gray-500">About</a>
                <a href="{{ route('storefront.page', 'contact') }}" class="text-3xl font-bold hover:text-gray-500">Contact</a>
            </div>
            
            <div class="text-center pb-8 border-t border-gray-100 pt-8">
                 @if(Auth::guard('customer')->check())
                     <p class="mb-4">Logged in as {{ Auth::guard('customer')->user()->name }}</p>
                      <form action="{{ route('storefront.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-500 underline">Sign Out</button>
                    </form>
                 @else
                    <a href="{{ route('storefront.login') }}" class="text-lg font-medium mr-4">Log in</a>
                    <a href="{{ route('storefront.register') }}" class="text-lg font-medium">Sign up</a>
                 @endif
            </div>
        </div>
    </div>
</nav>
