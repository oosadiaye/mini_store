<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Created Successfully!</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-12 text-center">
            <!-- Success Icon -->
            <div class="mb-6">
                <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <!-- Success Message -->
            <h1 class="text-4xl font-bold text-gray-900 mb-4">🎉 Your Store is Ready!</h1>
            <p class="text-lg text-gray-600 mb-8">
                Congratulations! Your online store has been created successfully.<br>
                You can now login and start customizing your storefront.
            </p>

            <!-- Store Info -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Next Steps -->
            <div class="bg-indigo-50 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Next Steps:</h2>
                <ol class="space-y-3 text-gray-700">
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm mr-3">1</span>
                        <span>Login to your admin panel using the credentials you created</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm mr-3">2</span>
                        <span>Choose a premium storefront template</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm mr-3">3</span>
                        <span>Add your products and customize your store</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm mr-3">4</span>
                        <span>Start selling! Your store is live immediately</span>
                    </li>
                </ol>
            </div>

            <!-- Trial Info -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-yellow-800 text-sm">
                    ⏰ Your 14-day free trial has started. No credit card required!
                </p>
            </div>

            <!-- Action Button -->
            <a href="/" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 transform hover:scale-105">
                Go to Homepage
            </a>
        </div>
    </div>
</body>
</html>
