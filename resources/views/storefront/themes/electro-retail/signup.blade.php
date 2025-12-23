@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', 'Sign Up')

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
                        <h2 class="text-4xl font-heading text-white mb-4">JOIN US TODAY!</h2>
                        <p class="text-gray-300 text-lg">Create an account and unlock exclusive benefits and offers.</p>
                    </div>
                    
                    <div class="relative z-10 space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-electro-neon rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Exclusive Deals</h3>
                                <p class="text-gray-400 text-sm">Get access to member-only discounts</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-electro-neon rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Lightning Fast Checkout</h3>
                                <p class="text-gray-400 text-sm">Save time with one-click purchasing</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-electro-neon rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold mb-1">Order Updates</h3>
                                <p class="text-gray-400 text-sm">Get notified about your purchases</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative z-10 text-gray-400 text-sm">
                        © {{ date('Y') }} {{ tenant('name') }}. All rights reserved.
                    </div>
                </div>
                
                {{-- Right Side - Signup Form --}}
                <div class="p-8 sm:p-12">
                    <div class="mb-8">
                        <h1 class="text-3xl font-heading text-gray-900 mb-2">CREATE ACCOUNT</h1>
                        <p class="text-gray-600">Fill in your details to get started</p>
                    </div>
                    
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
                    
                    <form method="POST" action="{{ route('storefront.register') }}" class="space-y-5" x-data="{ 
                        password: '', 
                        confirmPassword: '',
                        get passwordStrength() {
                            if (this.password.length === 0) return 0;
                            let strength = 0;
                            if (this.password.length >= 8) strength++;
                            if (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password)) strength++;
                            if (/\d/.test(this.password)) strength++;
                            if (/[^a-zA-Z0-9]/.test(this.password)) strength++;
                            return strength;
                        },
                        get passwordMatch() {
                            return this.confirmPassword.length > 0 && this.password === this.confirmPassword;
                        }
                    }">
                        @csrf
                        
                        {{-- Name Field --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name
                            </label>
                            <input 
                                id="name" 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                autofocus 
                                autocomplete="name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-electro-neon focus:border-electro-blue transition-colors @error('name') border-red-500 @enderror"
                                placeholder="John Doe"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
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
                                    autocomplete="new-password"
                                    x-model="password"
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
                            
                            {{-- Password Strength Indicator --}}
                            <div x-show="password.length > 0" class="mt-2">
                                <div class="flex gap-1 mb-1">
                                    <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-200'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-gray-200'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 3 ? 'bg-blue-500' : 'bg-gray-200'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors" :class="passwordStrength >= 4 ? 'bg-green-500' : 'bg-gray-200'"></div>
                                </div>
                                <p class="text-xs text-gray-600">
                                    <span x-show="passwordStrength === 1">Weak password</span>
                                    <span x-show="passwordStrength === 2">Fair password</span>
                                    <span x-show="passwordStrength === 3">Good password</span>
                                    <span x-show="passwordStrength === 4">Strong password</span>
                                </p>
                            </div>
                            
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Confirm Password Field --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password"
                                    x-model="confirmPassword"
                                    class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-electro-neon focus:border-electro-blue transition-colors"
                                    placeholder="••••••••"
                                >
                                <div x-show="confirmPassword.length > 0" class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <svg x-show="passwordMatch" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg x-show="!passwordMatch" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Terms & Conditions --}}
                        <div class="flex items-start">
                            <input 
                                type="checkbox" 
                                name="terms" 
                                id="terms"
                                required
                                class="w-4 h-4 mt-1 text-electro-blue border-gray-300 rounded focus:ring-electro-neon"
                            >
                            <label for="terms" class="ml-2 text-sm text-gray-700">
                                I agree to the 
                                <a href="#" class="text-electro-blue hover:text-electro-neon transition-colors">Terms of Service</a> 
                                and 
                                <a href="#" class="text-electro-blue hover:text-electro-neon transition-colors">Privacy Policy</a>
                            </label>
                        </div>
                        
                        {{-- Newsletter Opt-in --}}
                        <div class="flex items-start">
                            <input 
                                type="checkbox" 
                                name="newsletter" 
                                id="newsletter"
                                {{ old('newsletter') ? 'checked' : '' }}
                                class="w-4 h-4 mt-1 text-electro-blue border-gray-300 rounded focus:ring-electro-neon"
                            >
                            <label for="newsletter" class="ml-2 text-sm text-gray-700">
                                Send me exclusive offers and updates via email
                            </label>
                        </div>
                        
                        {{-- Submit Button --}}
                        <button 
                            type="submit" 
                            class="w-full bg-electro-blue hover:bg-blue-700 text-white font-heading py-3.5 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                        >
                            CREATE ACCOUNT
                        </button>
                    </form>
                    
                    {{-- Divider --}}
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or sign up with</span>
                        </div>
                    </div>
                    
                    {{-- Social Signup Buttons --}}
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
                    
                    {{-- Login Link --}}
                    <div class="mt-6 text-center">
                        <p class="text-gray-600">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-electro-blue hover:text-electro-neon font-semibold transition-colors">
                                Log in
                            </a>
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
