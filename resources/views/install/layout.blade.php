<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installation Wizard - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-xl w-full bg-white rounded-xl shadow-2xl overflow-hidden">
        <div class="bg-indigo-600 p-6 text-center">
            <h1 class="text-2xl font-bold text-white">Installation Wizard</h1>
            <p class="text-indigo-100 mt-2">Set up your application in a few steps</p>
        </div>
        
        <div class="p-8">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @yield('content')
        </div>

        <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-100">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
