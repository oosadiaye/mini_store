@extends('install.layout')

@section('content')
<div class="text-center mb-8">
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
        </svg>
    </div>
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Connect to Database</h2>
    <p class="text-gray-600">Your configuration was saved. We are ready to migrate the database schemas and seed default data.</p>
</div>

<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 text-left">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700">
                This process might take a few seconds. Do not close this tab.
                Existing tables in the configured database will be <strong>dropped</strong>.
            </p>
        </div>
    </div>
</div>

<form action="{{ route('install.database.migrate') }}" method="POST" class="text-center">
    @csrf
    <button type="submit" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg transform transition hover:scale-105">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        Run Migrations & Seeders
    </button>
</form>
@endsection
