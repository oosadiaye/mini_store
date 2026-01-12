<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Pricing</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50">
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
        <style>body { padding-top: 40px; }</style>
    @endif
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-100 py-12 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-16">
                @php
                    $branding = \App\Models\GlobalSetting::where('group', 'branding')->pluck('value', 'key');
                    $brandLogo = $branding['brand_logo'] ?? null;
                    $brandName = $branding['brand_name'] ?? config('app.name', 'SuperAdmin');
                @endphp
                <!-- SuperAdmin Logo -->
                <a href="/" class="inline-block mb-8">
                    @if($brandLogo)
                        <img src="{{ Storage::disk('public')->url($brandLogo) }}?v={{ time() }}" alt="{{ $brandName }}" class="h-16 w-auto mx-auto">
                    @else
                        <div class="text-4xl font-bold text-indigo-600">{{ $brandName }}</div>
                    @endif
                </a>

                <div class="absolute top-8 left-8">
                    <a href="{{ route('admin.dashboard', ['tenant' => app('tenant')->slug]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Dashboard
                    </a>
                </div>

                <h2 class="text-indigo-600 font-semibold tracking-wide uppercase text-sm sm:text-base mb-2">Pricing Plans</h2>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 tight-leading">
                    Choose the Perfect Plan<br class="hidden sm:inline"> for Your Store
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg sm:text-xl text-gray-600">
                    Transparent pricing. No hidden fees. Start with a free trial today.
                </p>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-8 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm max-w-4xl mx-auto">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r shadow-sm max-w-4xl mx-auto">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-yellow-700 font-medium">{{ session('warning') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm max-w-4xl mx-auto">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Plans Flex Layout (Responsive & Centered) -->
            <div class="flex flex-wrap justify-center gap-8 mx-auto">
                @foreach($plans as $index => $plan)
                @php
                    $isActive = $plan->id === app('tenant')->plan_id;
                    $isPopular = $index === 1 && !$isActive; // Only highlight popular if not current
                @endphp
                <div class="relative w-full max-w-sm flex-shrink-0 bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col {{ $isActive ? 'border-2 border-indigo-600 ring-4 ring-indigo-50' : ($isPopular ? 'border-2 border-indigo-400 ring-2 ring-indigo-50/50' : 'border border-gray-100') }}">
                    
                    @if($isActive)
                    <div class="absolute top-0 right-0 left-0 bg-indigo-600 text-white text-xs font-bold px-3 py-1 uppercase tracking-widest text-center rounded-t-xl">
                        Current Plan
                    </div>
                    @elseif($isPopular)
                    <div class="absolute top-0 right-0 left-0 bg-indigo-500 text-white text-xs font-bold px-3 py-1 uppercase tracking-widest text-center rounded-t-xl">
                        Most Popular
                    </div>
                    @endif

                    <div class="p-8 {{ ($isActive || $isPopular) ? 'pt-10' : '' }}">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-500 text-sm mb-6">{{ $plan->description ?? 'Perfect for growing businesses.' }}</p>
                        
                        <div class="flex items-baseline mb-8">
                            <span class="text-5xl font-extrabold text-gray-900">₦{{ number_format($plan->price, 0) }}</span>
                            <span class="text-gray-500 ml-2 font-medium">/ {{ $plan->duration_days }} days</span>
                        </div>

                        @if(isset($prorations[$plan->id]) && $prorations[$plan->id])
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm font-semibold text-green-800 mb-1">Upgrade Pricing</p>
                                <p class="text-xs text-green-700 mb-2">Credit from current plan: ₦{{ number_format($prorations[$plan->id]['credit'], 2) }}</p>
                                <div class="flex items-baseline">
                                    <span class="text-2xl font-bold text-green-600">₦{{ number_format($prorations[$plan->id]['amount_due'], 2) }}</span>
                                    <span class="text-green-600 ml-2 text-sm">to pay</span>
                                </div>
                            </div>
                        @endif

                        <!-- Features -->
                        <ul class="space-y-4 mb-8">
                            @if($plan->trial_days > 0)
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="ml-3 text-gray-700 font-medium">{{ $plan->trial_days }} Days Free Trial</span>
                            </li>
                            @endif

                             @if($plan->features)
                                @foreach($plan->features as $feature)
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        </div>
                                        <span class="ml-3 text-gray-600">{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                                    </li>
                                @endforeach
                            @endif
                            
                            @if($plan->caps)
                                @foreach($plan->caps as $key => $limit)
                                    @if($limit)
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-50 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                        </div>
                                        <span class="ml-3 text-gray-600">
                                            <strong class="text-gray-900">{{ $limit }}</strong> {{ ucfirst(str_replace(['max_', '_'], ['', ' '], $key)) }}
                                        </span>
                                    </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <!-- Action Button -->
                    <div class="p-8 pt-0 mt-auto">
                        @if($isActive)
                            <button type="button" disabled class="w-full py-4 px-6 rounded-xl shadow-none text-lg font-bold bg-green-100 text-green-700 cursor-default opacity-75">
                                Current Plan
                            </button>
                        @elseif($hasPending)
                            <button type="button" disabled class="w-full py-4 px-6 rounded-xl shadow-none text-lg font-bold bg-yellow-100 text-yellow-700 cursor-not-allowed opacity-75">
                                Payment Pending
                            </button>
                        @else
                        <form action="{{ route('tenant.subscription.store', ['tenant' => app('tenant')->slug]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="w-full py-4 px-6 rounded-xl shadow-lg text-lg font-bold transition duration-300 transform hover:-translate-y-1 block text-center {{ $isPopular ? 'bg-indigo-600 text-white hover:bg-indigo-700 hover:shadow-indigo-500/30' : 'bg-white text-indigo-600 border-2 border-indigo-100 hover:border-indigo-600 hover:bg-indigo-50' }}">
                                {{ $plan->price > 0 ? 'Subscribe Now' : 'Downgrade to Free' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Trust Badges / Footer Info -->
            <div class="mt-16 text-center">
                <p class="text-gray-500 text-sm">Secure payments provided by Paystack. Cancel anytime.</p>
                <div class="flex justify-center space-x-6 mt-4 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                     <span class="flex items-center text-gray-400 font-medium"><svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> SSL Secure</span>
                     <span class="flex items-center text-gray-400 font-medium"><svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg> 24/7 Support</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
