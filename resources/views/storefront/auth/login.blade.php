@extends('storefront.layout')

@section('content')
<div class="bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 min-h-screen flex items-center justify-center p-4 pb-32 md:pb-0">
    <div class="bg-white/80 backdrop-blur-md p-8 md:p-10 rounded-2xl shadow-2xl max-w-md w-full border border-white/50 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 via-pink-500 to-orange-500"></div>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Welcome Back</h1>
            <p class="text-gray-500 mt-2 text-sm">Sign in to continue to your account</p>
        </div>

        @if(session('status'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 text-sm font-medium text-center border border-green-100">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('storefront.login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1 ml-1">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="far fa-envelope"></i>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary transition-all bg-gray-50 focus:bg-white" 
                        placeholder="you@example.com">
                </div>
                @error('email') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1 ml-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password" type="password" name="password" required autocomplete="current-password" 
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary transition-all bg-gray-50 focus:bg-white" 
                        placeholder="••••••••">
                </div>
                @error('password') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary h-4 w-4">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a class="text-sm text-primary hover:text-indigo-700 font-medium transition-colors" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-primary to-indigo-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg hover:shadow-xl hover:opacity-90 transition-all transform hover:-translate-y-0.5 active:scale-95">
                Sign In
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('storefront.register') }}" class="font-bold text-primary hover:text-indigo-700 transition-colors ml-1">Create Account</a>
            </p>
        </div>
    </div>
</div>
@endsection
