<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Online Store - Storefront SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Create Your Online Store</h1>
                <p class="text-lg text-gray-600">Get your store up and running in minutes with a 14-day free trial</p>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8" x-data="{ loading: false }">
                <form method="POST" action="{{ route('tenant.store') }}" class="space-y-6" @submit="loading = true">
                    @csrf

                    <!-- Store Information -->
                    <div class="border-b pb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Store Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Store Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone (Optional)</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-1">Choose Your Subdomain</label>
                                <div class="flex items-center">
                                    <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" required
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="mystore">
                                    <span class="px-4 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600">.{{ config('app.domain', 'localhost') }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">This will be your store's URL</p>
                                @error('subdomain')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="custom_domain" class="block text-sm font-medium text-gray-700 mb-1">Custom Domain (Optional)</label>
                                <input type="text" id="custom_domain" name="custom_domain" value="{{ old('custom_domain') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="example.com">
                                <p class="mt-1 text-xs text-gray-500">You can use your own domain name (requires simple DNS setup)</p>
                                @error('custom_domain')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Admin Account -->
                    <div class="border-b pb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Admin Account</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                                <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @error('admin_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-1">Your Email</label>
                                <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @error('admin_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" :disabled="loading" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center">
                            <span x-show="!loading">Create My Store - Start 14-Day Free Trial</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating your store...
                            </span>
                        </button>
                        <p class="mt-3 text-center text-sm text-gray-500">No credit card required • Cancel anytime</p>
                    </div>
                </form>
            </div>

            <!-- Features -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-3xl mb-2">🚀</div>
                    <h3 class="font-semibold text-gray-800">Quick Setup</h3>
                    <p class="text-sm text-gray-600">Store ready in minutes</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-3xl mb-2">🎨</div>
                    <h3 class="font-semibold text-gray-800">Premium Templates</h3>
                    <p class="text-sm text-gray-600">Beautiful designs included</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-3xl mb-2">📊</div>
                    <h3 class="font-semibold text-gray-800">Full Inventory</h3>
                    <p class="text-sm text-gray-600">Complete management system</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
