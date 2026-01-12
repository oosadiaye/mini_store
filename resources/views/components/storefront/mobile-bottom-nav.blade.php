@props(['tenant' => app('tenant')])

<nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 lg:hidden pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
    <div class="flex justify-around items-center h-16 px-2">
        <!-- Home -->
        <a href="{{ route('storefront.home', ['tenant' => $tenant->slug]) }}" 
           class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('storefront.home') ? 'text-[color:var(--brand-color)]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-6 h-6" fill="{{ request()->routeIs('storefront.home') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-[10px] font-medium leading-none">Home</span>
        </a>

        <!-- Shop (Categories) -->
        <a href="{{ route('storefront.products.index', ['tenant' => $tenant->slug]) }}" 
           class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('storefront.products.*') ? 'text-[color:var(--brand-color)]' : 'text-gray-400 hover:text-gray-600' }}">
            <svg class="w-6 h-6" fill="{{ request()->routeIs('storefront.products.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            <span class="text-[10px] font-medium leading-none">Shop</span>
        </a>

        <!-- Cart -->
        <a href="{{ route('storefront.cart.index', ['tenant' => $tenant->slug]) }}" 
           class="relative flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('storefront.cart.*') ? 'text-[color:var(--brand-color)]' : 'text-gray-400 hover:text-gray-600' }}">
            <div class="relative">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('storefront.cart.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                @if(\App\Models\Cart::where('session_id', session()->getId())->first()?->items->sum('quantity') > 0)
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-[color:var(--brand-color)]"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-[color:var(--brand-color)]"></span>
                    </span>
                @endif
            </div>
            <span class="text-[10px] font-medium leading-none">Cart</span>
        </a>

        <!-- Account -->
        @auth('web')
            <a href="{{ route('admin.dashboard', ['tenant' => $tenant->slug]) }}" 
               class="flex flex-col items-center justify-center w-full h-full space-y-1 text-[color:var(--brand-color)]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">Admin</span>
            </a>
        @elseauth('customer')
            <a href="{{ route('storefront.account.index') }}" 
               class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('storefront.account.*') ? 'text-[color:var(--brand-color)]' : 'text-gray-400 hover:text-gray-600' }}">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('storefront.account.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-[10px] font-medium leading-none">Account</span>
            </a>
        @else
            <a href="{{ route('storefront.login') }}" 
               class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('storefront.login') ? 'text-[color:var(--brand-color)]' : 'text-gray-400 hover:text-gray-600' }}">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('storefront.login') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span class="text-[10px] font-medium leading-none">Log In</span>
            </a>
        @endauth
    </div>
</nav>
