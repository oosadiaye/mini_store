<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support Ticket Submitted - {{ $tenant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-8 rounded-xl border border-gray-700 shadow-2xl text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-500/20">
            <svg class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        
        <h2 class="mt-4 text-3xl font-bold tracking-tight text-white">Ticket Submitted!</h2>
        
        <p class="mt-2 text-gray-400">
            {{ session('success') ?? 'Your support ticket has been received.' }}
        </p>
        
        <div class="mt-8">
             <a href="{{ route('tenant.login', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition">
                Return to Login
             </a>
        </div>
    </div>
</body>
</html>
