<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $branding['brand_name'] ?? config('app.name') }}</title>
    @if(isset($branding['brand_favicon']))
        <link rel="icon" href="{{ Storage::disk('public')->url($branding['brand_favicon']) }}?v={{ time() }}">
    @elseif(isset($branding['brand_logo']))
        <link rel="icon" href="{{ Storage::disk('public')->url($branding['brand_logo']) }}?v={{ time() }}">
    @endif
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-950 text-white min-h-screen flex flex-col relative overflow-hidden">

    <!-- Background Decoration -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/20 rounded-full blur-[120px]"></div>
    </div>

    <!-- Header -->
    <header class="relative z-10 container mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-3">
            @if(isset($branding['brand_logo']))
                <img src="{{ Storage::disk('public')->url($branding['brand_logo']) }}?v={{ time() }}" alt="Logo" class="h-10 w-auto">
            @endif
            <span class="text-xl font-bold tracking-tight text-white">{{ $branding['brand_name'] ?? config('app.name') }}</span>
        </div>
        <nav class="hidden md:flex items-center gap-6">
            @auth
                <a href="{{ url('/superadmin') }}" class="text-sm font-medium text-gray-300 hover:text-white transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition">Log in</a>
                <a href="{{ route('tenant.register') }}" class="px-5 py-2.5 text-sm font-medium text-white bg-white/10 border border-white/20 rounded-full hover:bg-white/20 transition">Get Started</a>
            @endauth
        </nav>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 flex-grow flex items-center justify-center text-center px-4">
        <div class="max-w-4xl mx-auto space-y-8">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight">
                <span class="block text-white mb-2">Welcome to</span>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-500">
                    {{ $branding['brand_name'] ?? config('app.name') }}
                </span>
            </h1>
            
            <p class="text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Empower your business with our all-in-one platform settings.
                Experience the next generation of management.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-8">
                <a href="{{ route('tenant.register') }}" class="w-full sm:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-full font-semibold text-lg transition shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50">
                    Get Started Now
                </a>
                
                @auth
                    <a href="{{ url('/superadmin') }}" class="w-full sm:w-auto px-8 py-4 bg-gray-800 hover:bg-gray-700 text-white rounded-full font-semibold text-lg transition border border-gray-700">
                        Go to Dashboard
                    </a>
                @endauth
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 container mx-auto px-6 py-8 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} {{ $branding['brand_name'] ?? config('app.name') }}. All rights reserved.
    </footer>

</body>
</html>
