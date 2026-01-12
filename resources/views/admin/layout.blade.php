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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        /* Global Input Styling Overrides */
        input[type='text'], input[type='email'], input[type='password'], 
        input[type='number'], input[type='date'], input[type='datetime-local'], 
        input[type='month'], input[type='search'], input[type='tel'], 
        input[type='time'], input[type='url'], input[type='week'], 
        select, textarea {
            border-width: 2px !important;
            border-radius: 0.5rem !important; /* rounded-lg */
        }
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
<body class="font-sans antialiased text-slate-900 bg-slate-50 flex h-screen overflow-hidden">
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

    <div id="app" class="flex w-full h-screen overflow-hidden">
        <!-- Mobile Sidebar -->
        <mobile-sidebar>
            <div class="md:hidden">
                @include('layouts.sidebar', ['settings' => $settings, 'mobile' => true])
            </div>
        </mobile-sidebar>

        <!-- Desktop Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0 z-20">
            @include('layouts.sidebar', ['settings' => $settings, 'mobile' => false])
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Topbar -->
            <header class="bg-white border-b border-slate-200 h-14 md:h-16 flex items-center justify-between px-3 md:px-6 z-10 shadow-sm">
                <div class="flex items-center flex-1 min-w-0">
                    <sidebar-toggle></sidebar-toggle>
                    <img src="{{ \App\Helpers\LogoHelper::getLogo(32) }}" alt="{{ $tenant->name }}" class="h-6 md:h-8 w-auto mr-2 md:mr-3 flex-shrink-0">
                    <h1 class="text-base md:text-xl font-bold text-slate-800 truncate">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                
                <div class="flex items-center ml-auto md:mr-4">
                    @php
                        // Get tenant's first domain or fallback to slug-based URL
                        $primaryDomain = $tenant->domains()->first();
                        $storefrontUrl = $primaryDomain 
                            ? 'https://' . $primaryDomain->domain 
                            : url('/' . $tenant->slug);
                    @endphp
                    <a href="{{ $storefrontUrl }}" target="_blank" class="flex items-center gap-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border border-indigo-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        <span class="hidden sm:inline">View Storefront</span>
                    </a>
                </div>

                @if(session()->has('tenancy_impersonation_token'))
                    <div class="mr-2 md:mr-4">
                        <a href="{{ route('impersonate.leave') }}" class="bg-red-600 text-white px-2 md:px-4 py-1 md:py-2 rounded-md font-semibold text-xs md:text-sm hover:bg-red-700 transition">
                            <i class="fas fa-sign-out-alt mr-1 md:mr-2"></i><span class="hidden sm:inline">Exit Impersonation</span>
                        </a>
                    </div>
                @endif

                <!-- Notifications -->
                <notification-dropdown 
                    :notifications='@json(auth()->user()->unreadNotifications->map(function($n) { 
                        return array_merge($n->toArray(), ["created_time_ago" => $n->created_at->diffForHumans()]); 
                    }))'
                    mark-all-read-url="{{ route('admin.notifications.read-all', ['tenant' => $tenant->slug]) }}"
                    read-url-prefix="{{ url($tenant->slug . '/admin/notifications/read/') }}/"
                ></notification-dropdown>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-6">
                        <user-dropdown
                            user-name="{{ auth()->user()->name }}"
                            user-email="{{ auth()->user()->email }}"
                            user-role="{{ ucfirst(auth()->user()->role ?? 'Admin') }}"
                            user-initial="{{ substr(auth()->user()->name, 0, 1) }}"
                            profile-url="{{ route('admin.users.edit', ['tenant' => $tenant->slug, 'user' => auth()->user()->id]) }}"
                            logout-url="{{ route('tenant.logout', ['tenant' => $tenant->slug]) }}"
                            csrf-token="{{ csrf_token() }}"
                        ></user-dropdown>
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

        <mobile-bottom-nav
            products-url="{{ route('admin.products.index', ['tenant' => $tenant->slug]) }}"
            pos-url="{{ route('admin.pos.index', ['tenant' => $tenant->slug]) }}"
            orders-url="{{ route('admin.orders.index', ['tenant' => $tenant->slug]) }}"
            dashboard-url="{{ route('admin.dashboard', ['tenant' => $tenant->slug]) }}"
            active-route="{{ request()->route()->getName() }}"
        ></mobile-bottom-nav>

        <offline-indicator></offline-indicator>
    </div>

    <!-- Global Form Interceptor for Offline Mode -->
    <script>
        document.addEventListener('submit', async (event) => {
            if (!navigator.onLine) {
                const form = event.target;
                const method = form.getAttribute('method') || 'GET';
                if (method.toUpperCase() !== 'GET') {
                    event.preventDefault();
                    const formData = new FormData(form);
                    const url = form.getAttribute('action') || window.location.href;
                    try {
                        await window.OfflineManager.queueRequest(url, {
                            method: method.toUpperCase(),
                            body: formData
                        });
                        const btn = form.querySelector('button[type="submit"]');
                        if (btn) {
                            const originalText = btn.innerText;
                            btn.innerText = '✅ Saved Locally (Offline)';
                            btn.disabled = true;
                            btn.classList.add('opacity-50', 'bg-green-600');
                            setTimeout(() => {
                                btn.innerText = originalText;
                                btn.disabled = false;
                                btn.classList.remove('opacity-50', 'bg-green-600');
                            }, 3000);
                        }
                    } catch (err) {
                        console.error('Failed to queue form:', err);
                    }
                }
            }
        });
    </script>
    @stack('scripts')
    <x-idle-logout :logoutRoute="route('tenant.logout', ['tenant' => $tenant->slug])" :timeout="900" />
</body>
</html>
