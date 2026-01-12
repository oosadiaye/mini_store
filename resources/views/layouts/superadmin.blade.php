<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $branding = \App\Models\GlobalSetting::where('group', 'branding')->pluck('value', 'key');
        $brandLogo = $branding['brand_logo'] ?? null;
        $brandFavicon = $branding['brand_favicon'] ?? null;
        $brandName = $branding['brand_name'] ?? config('app.name', 'SuperAdmin');
    @endphp

    <title>{{ $brandName }}</title>
    
    @if($brandFavicon)
        <link rel="icon" href="{{ Storage::disk('public')->url($brandFavicon) }}?v={{ time() }}">
    @elseif($brandLogo)
         <link rel="icon" href="{{ Storage::disk('public')->url($brandLogo) }}?v={{ time() }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    

    <!-- Custom Style for Inputs -->
    <style type="text/tailwindcss">
        @layer base {
            input[type='text'], input[type='email'], input[type='password'], 
            input[type='number'], input[type='date'], input[type='datetime-local'], 
            input[type='month'], input[type='search'], input[type='tel'], 
            input[type='time'], input[type='url'], input[type='week'], 
            select, textarea {
                @apply border-[2px] border-slate-300 rounded-lg shadow-sm transition-all duration-200;
            }
            
            input:focus, select:focus, textarea:focus {
                @apply ring-2 ring-blue-500 border-blue-500;
            }
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 bg-slate-50">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside class="bg-slate-900 text-white flex flex-col shrink-0 transition-all duration-300 relative z-20" 
               :class="sidebarOpen ? 'w-64' : 'w-20'"
        >
            <!-- Logo/Brand -->
            <div class="h-16 flex items-center justify-center border-b border-slate-800 bg-slate-950 px-4">
                <a href="{{ route('superadmin.dashboard') }}" x-show="sidebarOpen" class="flex items-center gap-3 group">
                    @if($brandLogo)
                        <img src="{{ Storage::disk('public')->url($brandLogo) }}?v={{ time() }}" alt="{{ $brandName }}" class="h-8 w-auto object-contain transition-transform group-hover:scale-105">
                    @endif
                    <span class="text-lg font-bold tracking-wide bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-300 drop-shadow-sm">
                        {{ $brandName }}
                    </span>
                </a>
                <span x-show="!sidebarOpen" class="text-xl font-bold text-blue-500 cursor-pointer" @click="sidebarOpen = true">
                    @if($brandLogo)
                         <img src="{{ Storage::disk('public')->url($brandLogo) }}?v={{ time() }}" alt="{{ $brandName }}" class="h-8 w-8 object-contain">
                    @else
                        {{ substr($brandName, 0, 2) }}
                    @endif
                </span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
                <a href="{{ route('superadmin.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>

                <a href="{{ route('superadmin.tenants.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.tenants.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span x-show="sidebarOpen">Tenants</span>
                </a>

                <a href="{{ route('superadmin.plans.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.plans.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-show="sidebarOpen">Plans & Billing</span>
                </a>

                <a href="{{ route('superadmin.payment-gateways.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.payment-gateways.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span x-show="sidebarOpen">Payment Gateways</span>
                </a>

                <a href="{{ route('superadmin.subscription-requests.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.subscription-requests.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-show="sidebarOpen">Subscription Requests</span>
                </a>

                <div x-show="sidebarOpen" class="px-4 mt-8 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Reports
                </div>

                <a href="{{ route('superadmin.reports.subscriptions') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.reports.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span x-show="sidebarOpen">Subscription Report</span>
                </a>

                <div x-show="sidebarOpen" class="px-4 mt-8 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    System
                </div>

                <a href="{{ route('superadmin.announcements.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.announcements.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    <span x-show="sidebarOpen">Announcements</span>
                </a>
                
                <a href="{{ route('superadmin.custom-domains.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.custom-domains.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    <span x-show="sidebarOpen">Custom Domains</span>
                </a>

                <a href="{{ route('superadmin.settings.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.settings.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span x-show="sidebarOpen">Global Settings</span>
                </a>

                <a href="{{ route('superadmin.ticket-categories.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.ticket-categories.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span x-show="sidebarOpen">Ticket Categories</span>
                </a>

                <a href="{{ route('superadmin.tickets.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.tickets.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span x-show="sidebarOpen">Support Tickets</span>
                </a>
                
                <a href="{{ route('superadmin.staff.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.staff.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span x-show="sidebarOpen">Administrators</span>
                </a>

                <a href="{{ route('superadmin.audit-logs.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.audit-logs.index') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span x-show="sidebarOpen">Audit Logs</span>
                </a>

                <a href="{{ route('superadmin.roles.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.roles.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span x-show="sidebarOpen">Roles</span>
                </a>
            
            
            <!-- User Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center text-slate-400 hover:text-white transition group w-full">
                        <svg class="w-6 h-6 flex-shrink-0 group-hover:text-red-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span x-show="sidebarOpen" class="ml-3 font-medium">Logout</span>
                        <div x-show="!sidebarOpen" class="sr-only">Logout</div>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-slate-200 shadow-sm flex items-center justify-between px-6 z-10">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 focus:outline-none mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">@yield('header')</h1>
                </div>
                
                <div class="flex items-center space-x-6">
                     <div class="flex items-center text-sm border-l pl-6 border-slate-200">
                        <div class="text-right mr-3 hidden sm:block">
                            <p class="font-semibold text-slate-700">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500">Super Administrator</p>
                        </div>
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                     </div>
                </div>
            </header>

            <!-- Content Scroll Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Notifications -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex justify-between items-start">
                            <div class="flex text-green-700">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-green-500 hover:text-green-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex justify-between items-start">
                             <div class="flex text-red-700">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-red-500 hover:text-red-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    <x-idle-logout :logoutRoute="route('logout')" :timeout="900" />
</body>
</html>
