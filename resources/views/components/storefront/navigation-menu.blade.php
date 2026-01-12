@props(['menuItems'])

<div x-data="{ mobileMenuOpen: false }">
    <!-- Desktop Navigation -->
    <nav class="hidden md:flex items-center gap-8">
        @foreach($menuItems as $item)
            <a href="{{ route('storefront.category', ['slug' => $item['slug']]) }}" 
               class="text-sm font-medium text-gray-700 hover:text-[#0A2540] transition-colors uppercase tracking-wide relative group">
                {{ $item['label'] }}
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#0A2540] transition-all group-hover:w-full"></span>
            </a>
        @endforeach
        <a href="{{ route('storefront.about') }}" 
           class="text-sm font-medium text-gray-700 hover:text-[#0A2540] transition-colors uppercase tracking-wide relative group">
            About Us
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#0A2540] transition-all group-hover:w-full"></span>
        </a>
    </nav>

    <!-- Mobile Menu Trigger -->
    <div class="md:hidden flex items-center">
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 -mr-2 text-gray-600 hover:text-[#0A2540] transition-colors">
            <span class="sr-only">Open menu</span>
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Menu Drawer -->
    <div x-show="mobileMenuOpen" 
         class="fixed inset-0 z-[100] flex justify-end" 
         style="display: none;" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/20 backdrop-blur-sm" 
             @click="mobileMenuOpen = false"></div>

        <!-- Drawer Panel -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="relative w-full max-w-xs bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            
            <!-- Close Button -->
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                <span class="text-lg font-bold font-serif text-[#0A2540]">Menu</span>
                <button @click="mobileMenuOpen = false" class="p-2 -mr-2 text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Links -->
            <div class="px-6 py-4 space-y-6">
                @foreach($menuItems as $item)
                    <a href="{{ route('storefront.category', ['slug' => $item['slug']]) }}" 
                       class="block text-lg font-medium text-gray-900 hover:text-[#0A2540] active:text-[#0A2540] active:bg-gray-50 px-2 py-1 rounded transition-colors">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                
                <hr class="border-gray-100 my-4">
                
                <a href="{{ route('storefront.home') }}" class="block text-base font-medium text-gray-500 hover:text-[#0A2540]">
                    Home
                </a>
                <a href="{{ route('storefront.products.index') }}" class="block text-base font-medium text-gray-500 hover:text-[#0A2540]">
                    Shop All
                </a>
                <a href="{{ route('storefront.about') }}" class="block text-base font-medium text-gray-500 hover:text-[#0A2540]">
                    About Us
                </a>
            </div>
            
            <!-- Mobile Footer Info -->
            <div class="mt-auto px-6 py-8 bg-gray-50">
                <p class="text-xs text-center text-gray-400">&copy; {{ date('Y') }} All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
