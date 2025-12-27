@extends('install.layout')

@section('content')
<div class="text-center">
    <div class="mb-6">
        <svg class="mx-auto h-16 w-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
        </svg>
    </div>
    
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Welcome to the Installer</h2>
    <p class="text-gray-600 mb-8">
        Thank you for choosing <strong>{{ config('app.name') }}</strong>. This wizard will guide you through the installation process.
        We check your server requirements, verify file permissions, and set up the database.
    </p>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8 text-left">
        <h3 class="font-semibold text-blue-800 mb-2">What you need:</h3>
        <ul class="list-disc list-inside text-blue-700 space-y-1">
            <li>Database Host, Name, Username & Password</li>
            <li>PHP 8.1+</li>
            <li>Server Permissions</li>
        </ul>
    </div>

    <a href="{{ route('install.requirements') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full md:w-auto">
        Start Installation &rarr;
    </a>
</div>
@endsection
