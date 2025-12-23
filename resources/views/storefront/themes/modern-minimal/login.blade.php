@extends('storefront.themes.modern-minimal.layout')

@section('pageTitle', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Welcome back
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sign in to your account
            </p>
        </div>

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('storefront.login') }}">
            @csrf
            
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email address
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        value="{{ old('email') }}"
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-0 focus:z-10 sm:text-sm transition-all"
                        style="focus:ring-color: var(--color-primary);"
                        placeholder="you@example.com"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="current-password" 
                        required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-0 focus:z-10 sm:text-sm transition-all"
                        style="focus:ring-color: var(--color-primary);"
                        placeholder="••••••••"
                    >
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox" 
                        {{ old('remember') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 focus:ring-2 focus:ring-offset-0"
                        style="color: var(--color-primary);"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium hover:opacity-80 transition-opacity" style="color: var(--color-primary);">
                            Forgot password?
                        </a>
                    </div>
                @endif
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all hover:opacity-90"
                    style="background-color: var(--color-primary); focus:ring-color: var(--color-primary);"
                >
                    Sign in
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('storefront.register') }}" class="font-medium hover:opacity-80 transition-opacity" style="color: var(--color-primary);">
                        Sign up
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
