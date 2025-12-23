<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SuperAdmin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .sidebar-nav-item {
            @apply flex items-center px-4 py-3 rounded-lg transition-all duration-200;
        }
        .sidebar-nav-item-active {
            @apply bg-blue-600 text-white shadow-lg;
        }
        .sidebar-nav-item-inactive {
            @apply text-slate-300 hover:bg-slate-800 hover:text-white;
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
            <div class="h-16 flex items-center justify-center border-b border-slate-800 bg-slate-950">
                <span x-show="sidebarOpen" class="text-lg font-bold tracking-wider uppercase text-blue-400">Super<span class="text-white">Admin</span></span>
                <span x-show="!sidebarOpen" class="text-xl font-bold text-blue-500">SA</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
                <a href="{{ route('superadmin.dashboard') }}" 
                   class="sidebar-nav-item {{ request()->routeIs('superadmin.dashboard') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>

                <a href="{{ route('superadmin.tenants.index') }}" 
                   class="sidebar-nav-item {{ request()->routeIs('superadmin.tenants.*') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span x-show="sidebarOpen">Tenants</span>
                </a>

                <a href="{{ route('superadmin.plans.index') }}" 
                   class="sidebar-nav-item {{ request()->routeIs('superadmin.plans.*') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-show="sidebarOpen">Plans & Billing</span>
                </a>

                <div x-show="sidebarOpen" class="px-4 mt-8 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    System
                </div>

                <a href="{{ route('superadmin.settings.index') }}" 
                   class="sidebar-nav-item {{ request()->routeIs('superadmin.settings.*') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span x-show="sidebarOpen">Global Settings</span>
                </a>

                <a href="{{ route('superadmin.users.index') }}" 
                   class="sidebar-nav-item {{ request()->routeIs('superadmin.users.*') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span x-show="sidebarOpen">Administrators</span>
                </a>

                <a href="{{ route('superadmin.audit_logs.index') }}" 
                   class="sidebar-nav-item {{ request()->routeIs('superadmin.audit_logs.*') ? 'sidebar-nav-item-active' : 'sidebar-nav-item-inactive' }}">
                    <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span x-show="sidebarOpen">Audit Logs</span>
                </a>
            </nav>
            
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
                     @php
                        $branding = \App\Models\GlobalSetting::where('group', 'branding')->pluck('value', 'key');
                        $brandLogo = $branding['brand_logo'] ?? null;
                        $brandName = $branding['brand_name'] ?? config('app.name');
                     @endphp

                     @if($brandLogo)
                        <img src="{{ Storage::disk('public')->url($brandLogo) }}" alt="{{ $brandName }}" class="h-8 w-auto">
                     @else
                        <span class="font-bold text-slate-700">{{ $brandName }}</span>
                     @endif
                     
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
</body>
</html>
