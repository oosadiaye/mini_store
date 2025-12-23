<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Superadmin Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN for immediate fix -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-6">
            <a href="/">
                <div class="flex items-center justify-center">
                    <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 text-center">Superadmin Portal</h1>
            </a>
        </div>

        <!-- Login Card -->
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl rounded-2xl overflow-hidden">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Sign in to your account</h2>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="flex items-center">
                        <input id="remember_me" 
                               type="checkbox" 
                               name="remember"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        Sign In
                    </button>
                </div>
            </form>

            <!-- Additional Info -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Superadmin access only
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
