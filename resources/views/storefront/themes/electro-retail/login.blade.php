@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-6xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid md:grid-cols-2 gap-0">
                
                {{-- Left Side - Welcome Section --}}
                <div class="hidden md:flex bg-gradient-to-br from-electro-dark to-gray-900 p-12 flex-col justify-between relative overflow-hidden">
                    {{-- Decorative Elements --}}
                    <div class="absolute top-0 right-0 w-64 h-64 bg-electro-blue opacity-10 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-electro-neon opacity-10 rounded-full -ml-24 -mb-24"></div>
                    
                    <div class="relative z-10">
                        <h2 class="text-4xl font-heading text-white mb-4">WELCOME BACK!</h2>
                        <p class="text-gray-300 text-lg">Sign in to access your account and continue shopping for the latest tech.</p>
                    </div>
                    
                    <div class="relative z-10 space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-electro-blue rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Fast Checkout</h3>
                                <p class="text-gray-400 text-sm">Save your details for quicker purchases</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-electro-blue rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Order Tracking</h3>
                                <p class="text-gray-400 text-sm">Monitor your orders in real-time</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-electro-blue rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Wishlist & Favorites</h3>
                                <p class="text-gray-400 text-sm">Save items for later</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative z-10 text-gray-400 text-sm">
                        © {{ date('Y') }} {{ tenant('name') }}. All rights reserved.
                    </div>
                </div>
                
                {{-- Right Side - Login Form --}}
                <div class="p-8 sm:p-12">
                    <div class="mb-8">
                        <h1 class="text-3xl font-heading text-gray-900 mb-2">LOGIN</h1>
                        <p class="text-gray-600">Enter your credentials to access your account</p>
                    </div>
                    
                    @if (session('status'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <p class="text-green-700 text-sm">{{ session('status') }}</p>
                        </div>
                    @endif
                    
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                            <p class="text-red-700 text-sm font-semibold mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('storefront.login') }}" class="space-y-6">
                        @csrf
                        
                        {{-- Email Field --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="email"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-electro-neon focus:border-electro-blue transition-colors @error('email') border-red-500 @enderror"
                                placeholder="you@example.com"
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Password Field --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative" x-data="{ showPassword: false }">
                                <input 
                                    id="password" 
                                    :type="showPassword ? 'text' : 'password'" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-electro-neon focus:border-electro-blue transition-colors @error('password') border-red-500 @enderror"
                                    placeholder="••••••••"
                                >
                                <button 
                                    type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                >
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Remember Me & Forgot Password --}}
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="remember" 
                                    {{ old('remember') ? 'checked' : '' }}
                                    class="w-4 h-4 text-electro-blue border-gray-300 rounded focus:ring-electro-neon"
                                >
                                <span class="ml-2 text-sm text-gray-700">Remember me</span>
                            </label>
                            
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-electro-blue hover:text-electro-neon transition-colors">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        
                        {{-- Submit Button --}}
                        <button 
                            type="submit" 
                            class="w-full bg-electro-blue hover:bg-blue-700 text-white font-heading py-3.5 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                        >
                            LOGIN
                        </button>
                    </form>
                    
                    {{-- Divider --}}
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>
                    
                    {{-- Social Login Buttons --}}
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Google</span>
                        </button>
                        
                        <button type="button" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Facebook</span>
                        </button>
                    </div>
                    
                    {{-- Sign Up Link --}}
                    <div class="mt-8 text-center">
                        <p class="text-gray-600">
                            Don't have an account? 
                            <a href="{{ route('storefront.register') }}" class="text-electro-blue hover:text-electro-neon font-semibold transition-colors">
                                Sign up now
                            </a>
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
