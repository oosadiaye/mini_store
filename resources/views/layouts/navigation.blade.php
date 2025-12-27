<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center md:hidden">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
            </div>

            <!-- Notification Bell -->
            <div class="flex items-center sm:ms-6">
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none transition duration-150 ease-in-out">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6 {{ ($sharedUnreadAnnouncementsCount ?? 0) > 0 ? 'animate-pulse text-red-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if(($sharedUnreadAnnouncementsCount ?? 0) > 0)
                                <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-red-600"></span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                            Announcements
                        </div>
                        
                        <div class="max-h-64 overflow-y-auto">
                            @forelse($sharedAnnouncements ?? [] as $announcement)
                                <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition cursor-pointer"
                                     onclick="markAnnouncementRead({{ $announcement->id }}); window.location.href='{{ $announcement->action_url ?? '#' }}'">
                                    <div class="flex items-start">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 {{ !$announcement->is_read ? 'font-bold' : '' }}">
                                                @if(!$announcement->is_read)
                                                    <span class="bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.5 rounded mr-1">NEW</span>
                                                @endif
                                                {{ $announcement->title }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($announcement->content, 60) }}</p>
                                            <p class="text-[10px] text-gray-400 mt-1">{{ $announcement->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                    No announcements.
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="border-t border-gray-100">
                             <a href="{{ route('admin.announcements.index') }}" class="block px-4 py-2 text-xs text-center text-gray-500 hover:bg-gray-100 transition">
                                View All
                            </a>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Subscription Badge -->
            @if(($tenant = app('tenant')) && $tenant->currentPlan)
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <a href="{{ route('tenant.subscription.index', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ $tenant->currentPlan->name }}
                </a>
            </div>
            @endif

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Inventory -->
            <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Inventory</div>
                <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                    {{ __('Products') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                    {{ __('Categories') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.purchase-orders.index')" :active="request()->routeIs('admin.purchase-orders.*')">
                    {{ __('Purchases') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.warehouses.index')" :active="request()->routeIs('admin.warehouses.*')">
                    {{ __('Warehouses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.suppliers.index')" :active="request()->routeIs('admin.suppliers.*')">
                    {{ __('Suppliers') }}
                </x-responsive-nav-link>
            </div>

            <!-- Sales -->
            <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Sales</div>
                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">
                    {{ __('Customers') }}
                </x-responsive-nav-link>
            </div>

             <!-- Website -->
            <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Website</div>
                 <x-responsive-nav-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.*')">
                    {{ __('Pages') }}
                </x-responsive-nav-link>
                 <x-responsive-nav-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')">
                    {{ __('Banners') }}
                </x-responsive-nav-link>

            </div>

             <!-- Accounting -->
            <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Accounting</div>
                <x-responsive-nav-link :href="route('admin.accounts.index')" :active="request()->routeIs('admin.accounts.*')">
                    {{ __('Accounts') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.incomes.index')" :active="request()->routeIs('admin.incomes.*')">
                    {{ __('Income') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.expenses.index')" :active="request()->routeIs('admin.expenses.*')">
                    {{ __('Expenses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.accounting.profit-loss')" :active="request()->routeIs('admin.accounting.profit-loss')">
                    {{ __('Profit & Loss') }}
                </x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
