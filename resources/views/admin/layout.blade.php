<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tenant->name }} - Admin Dashboard</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @php
        $settings = $tenant->data ?? [];
    @endphp

    {{-- Favicon (custom or auto-generated) --}}
    @php
        $faviconUrl = \App\Helpers\LogoHelper::getFavicon();
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="manifest" href="{{ route('tenant.manifest', ['tenant' => $tenant->slug]) }}">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
    
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('Admin ServiceWorker registration successful');
                })
                .catch(err => {
                    console.log('Admin ServiceWorker registration failed: ', err);
                });
        }
    </script>
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-50 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
    @if(session('superadmin_impersonator_id'))
        <div class="fixed top-0 left-0 w-full z-50 bg-indigo-600 px-4 py-2 text-white shadow-md">
            <div class="flex items-center justify-between max-w-7xl mx-auto">
                <p class="text-sm font-medium">
                    <i class="fas fa-user-secret mr-2"></i> Impersonating <span class="font-bold underline">{{ Auth::user()->name }}</span>
                </p>
                <form action="{{ route('superadmin.stop-impersonation') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white text-indigo-700 hover:bg-gray-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wide transition shadow-sm border border-transparent">
                        Exit Impersonation
                    </button>
                </form>
            </div>
        </div>
        <style> 
            body { padding-top: 40px !important; }
            .h-screen { height: calc(100vh - 40px) !important; } 
            .fixed.inset-y-0 { top: 40px !important; } 
        </style>
    @endif

    
    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-slate-900/80 z-20 md:hidden"></div>

    <!-- Mobile Sidebar -->
    <div x-show="sidebarOpen" x-cloak 
         x-transition:enter="transition ease-in-out duration-300 transform" 
         x-transition:enter-start="-translate-x-full" 
         x-transition:enter-end="translate-x-0" 
         x-transition:leave="transition ease-in-out duration-300 transform" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="-translate-x-full" 
         class="fixed inset-y-0 left-0 w-64 bg-slate-900 z-30 md:hidden overflow-y-auto">
         @include('layouts.sidebar', ['settings' => $settings, 'mobile' => true])
    </div>

    <!-- Desktop Sidebar -->
    <div class="hidden md:flex md:flex-shrink-0 z-20">
        @include('layouts.sidebar', ['settings' => $settings, 'mobile' => false])
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Topbar -->
        <!-- Topbar -->
        <header class="bg-white border-b border-slate-200 h-14 md:h-16 flex items-center justify-between px-3 md:px-6 z-10 shadow-sm">
            <div class="flex items-center flex-1 min-w-0">
                 <button @click="sidebarOpen = true" class="text-slate-500 focus:outline-none md:hidden mr-2 md:mr-4 hover:text-slate-700 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                 </button>
                 <img src="{{ \App\Helpers\LogoHelper::getLogo(32) }}" alt="{{ $tenant->name }}" class="h-6 md:h-8 w-auto mr-2 md:mr-3 flex-shrink-0">
                 <h1 class="text-base md:text-xl font-bold text-slate-800 truncate">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            
            
            @if(session()->has('tenancy_impersonation_token'))
                <div class="mr-2 md:mr-4">
                    <a href="{{ route('impersonate.leave') }}" class="bg-red-600 text-white px-2 md:px-4 py-1 md:py-2 rounded-md font-semibold text-xs md:text-sm hover:bg-red-700 transition">
                        <i class="fas fa-sign-out-alt mr-1 md:mr-2"></i><span class="hidden sm:inline">Exit Impersonation</span>
                    </a>
                </div>
            @endif

            <!-- Notifications -->
            <div x-data="{ open: false }" class="relative mr-2 md:mr-4">
                <button @click="open = !open" class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">View notifications</span>
                    <svg class="h-5 md:h-6 w-5 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                    <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <a href="{{ route('admin.notifications.read.all', ['tenant' => $tenant->slug]) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Mark all read</a>
                        @endif
                    </div>
                    <div class="max-h-60 overflow-y-auto">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <div class="px-4 py-3 hover:bg-gray-50 transition border-b border-gray-100 last:border-0">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                    <a href="{{ route('admin.notifications.read', ['tenant' => $tenant->slug, 'id' => $notification->id]) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Dismiss</a>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-500">
                                No new notifications
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-6">
                     <div class="flex items-center text-sm border-l pl-6 border-slate-200 h-8 relative" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen" class="flex items-center focus:outline-none">
                            <div class="text-right mr-3 hidden sm:block">
                                <p class="font-semibold text-slate-700">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ ucfirst(auth()->user()->role ?? 'Admin') }}</p>
                            </div>
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </button>
                        
                        <!-- User Dropdown -->
                        <div x-show="userOpen" @click.away="userOpen = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 top-10 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            
                            <div class="px-4 py-3 border-b border-gray-100 md:hidden">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('admin.users.edit', ['tenant' => $tenant->slug, 'user' => auth()->user()->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-circle mr-2 w-4"></i> My Profile
                            </a>

                            <form method="POST" action="{{ route('tenant.logout', ['tenant' => $tenant->slug]) }}" class="border-t border-gray-100">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-2 w-4"></i> Sign Out
                                </button>
                            </form>
                        </div>
                     </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-3 md:p-6 pb-20 lg:pb-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-3 md:px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 md:px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation (Tablets & Phones Only) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
        <div class="grid grid-cols-4 h-16">
            <!-- Products -->
            <a href="{{ route('admin.products.index', ['tenant' => $tenant->slug]) }}" 
               class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('admin.products.*') ? 'text-indigo-600' : 'text-gray-600' }} hover:text-indigo-600 transition">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="font-medium">Products</span>
            </a>
            
            <!-- POS -->
            <a href="{{ route('admin.pos.index', ['tenant' => $tenant->slug]) }}" 
               class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('admin.pos.*') ? 'text-indigo-600' : 'text-gray-600' }} hover:text-indigo-600 transition">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span class="font-medium">POS</span>
            </a>
            
            <!-- Orders -->
            <a href="{{ route('admin.orders.index', ['tenant' => $tenant->slug]) }}" 
               class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('admin.orders.*') ? 'text-indigo-600' : 'text-gray-600' }} hover:text-indigo-600 transition">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="font-medium">Orders</span>
            </a>
            
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard', ['tenant' => $tenant->slug]) }}" 
               class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-gray-600' }} hover:text-indigo-600 transition">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
        </div>
    </nav>

    <!-- Offline Indicator -->
    <div id="offline-indicator" class="fixed bottom-4 right-4 bg-slate-800 text-white px-4 py-3 rounded-lg shadow-lg z-50 hidden flex items-center space-x-3 transition-transform transform translate-y-0" x-data="{ online: navigator.onLine, queueCount: 0 }" x-init="
        window.addEventListener('online', () => { online = true; setTimeout(() => { if(online) $el.classList.add('hidden'); }, 3000); });
        window.addEventListener('offline', () => { online = false; $el.classList.remove('hidden'); });
        window.addEventListener('offline-queue-updated', async () => { queueCount = await window.OfflineManager.getQueueCount(); });
    ">
        <div x-show="!online" class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 011.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path></svg>
            <span class="font-medium">You are offline.</span>
        </div>
        <div x-show="online" class="flex items-center">
             <svg class="animate-spin w-5 h-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
             <span class="font-medium">Back online! Syncing...</span>
        </div>
        <div x-show="queueCount > 0" class="ml-2 bg-white text-slate-800 text-xs font-bold px-2 py-0.5 rounded-full" x-text="queueCount + ' pending'"></div>
    </div>

    @stack('scripts')
</body>
</html>
