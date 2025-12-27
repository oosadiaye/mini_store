@extends('install.layout')

@section('content')
<div class="text-center py-8">
    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6 animate-bounce">
        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    
    <h2 class="text-3xl font-bold text-gray-900 mb-4">Installation Complete!</h2>
    <p class="text-gray-600 mb-8 max-w-md mx-auto">
        Your application has been successfully installed. You can now log in to the administration panel.
    </p>

    <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left max-w-md mx-auto">
        <h3 class="font-semibold text-gray-800 mb-2">Next Steps:</h3>
        <ul class="list-disc list-inside text-gray-600 space-y-2">
            <li>Log in to the Super Admin Dashboard</li>
            <li>Configure your Subscription Plans</li>
            <li>Onboard your first Tenant</li>
        </ul>
    </div>

    <a href="{{ url('/') }}" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg transition transform hover:-translate-y-1">
        Go to Homepage
    </a>
</div>
@endsection
