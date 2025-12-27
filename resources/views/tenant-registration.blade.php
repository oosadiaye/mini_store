<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Start Your Business - {{ $branding['brand_name'] ?? config('app.name') }}</title>
    @if(isset($branding['brand_favicon']))
        <link rel="icon" href="{{ Storage::disk('public')->url($branding['brand_favicon']) }}?v={{ time() }}">
    @elseif(isset($branding['brand_logo']))
        <link rel="icon" href="{{ Storage::disk('public')->url($branding['brand_logo']) }}?v={{ time() }}">
    @endif
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col justify-center py-8 sm:py-12 sm:px-6 lg:px-8 relative overflow-hidden">
    
    <!-- Background Decoration -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute top-[-20%] right-[-10%] w-[50%] h-[50%] bg-blue-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[50%] h-[50%] bg-purple-600/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative z-10 mx-auto w-full max-w-[340px] sm:max-w-md">
        @if(isset($branding['brand_logo']))
            <img class="mx-auto h-10 sm:h-12 w-auto mb-4 sm:mb-6 drop-shadow-lg" src="{{ Storage::disk('public')->url($branding['brand_logo']) }}?v={{ time() }}" alt="{{ $branding['brand_name'] ?? config('app.name') }}">
        @endif
        
        <h2 class="text-center text-2xl sm:text-3xl font-bold tracking-tight text-white">
            Automate your business
        </h2>
        <p class="mt-2 text-center text-xs sm:text-sm text-gray-300">
            Join {{ $branding['brand_name'] ?? config('app.name') }} and start your 14-day free trial.
        </p>
    </div>

    <div class="relative z-10 mt-6 sm:mt-8 mx-auto w-full max-w-[340px] sm:max-w-md">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 py-6 px-4 sm:py-8 sm:px-10 shadow-2xl rounded-xl sm:rounded-2xl">
            
            @if(session('error'))
                <div class="rounded-lg bg-red-500/10 border border-red-500/20 p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-xs sm:text-sm font-medium text-red-400">
                                {{ session('error') }}
                            </h3>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-4 sm:space-y-6" action="{{ route('tenant.store') }}" method="POST">
                @csrf
                
                <div>
                    <label for="store_name" class="block text-xs sm:text-sm font-medium text-gray-200">Business Name</label>
                    <div class="mt-1">
                        <input id="store_name" name="store_name" type="text" required value="{{ old('store_name') }}"
                            class="appearance-none block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-900 transition-colors"
                            placeholder="e.g. Acme Corp">
                        @error('store_name')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="subdomain" class="block text-xs sm:text-sm font-medium text-gray-200">Business Subdomain</label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                        <input type="text" name="subdomain" id="subdomain" required value="{{ old('subdomain') }}"
                            class="flex-1 min-w-0 block w-full px-3 py-2 bg-white border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-900 border-r-0 placeholder-gray-400 transition-colors" 
                            placeholder="my-business">
                        <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-100 text-gray-500 text-xs sm:text-sm">
                            .{{ config('app.url_base', 'mini.tryquot.com') }}
                        </span>
                    </div>
                    @error('subdomain')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="relative py-1 sm:py-2">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-2 bg-transparent text-xs sm:text-sm text-gray-300 bg-black/20 rounded">
                            Admin Account Details
                        </span>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-xs sm:text-sm font-medium text-gray-200">Full Name</label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="appearance-none block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-900 transition-colors">
                        @error('name')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs sm:text-sm font-medium text-gray-200">Email Address</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-900 transition-colors">
                        @error('email')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-xs sm:text-sm font-medium text-gray-200">Phone Number</label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                            class="appearance-none block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-900 transition-colors">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs sm:text-sm font-medium text-gray-200">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-900 transition-colors">
                        @error('password')
                            <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-xs sm:text-sm font-medium text-gray-200">Confirm Password</label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm text-gray-900 transition-colors">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 sm:py-3 px-4 border border-transparent rounded-full shadow-lg shadow-blue-600/20 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:scale-[1.02]">
                        Create Business
                    </button>
                </div>
            </form>
        </div>
        
        <p class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ $branding['brand_name'] ?? config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
