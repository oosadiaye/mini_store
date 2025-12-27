@extends('install.layout')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Database & Environment</h2>
    <p class="text-gray-600 mb-6">Configure your application settings and database connection.</p>
</div>

<form action="{{ route('install.environment.save') }}" method="POST">
    @csrf
    
    <div class="space-y-6 mb-8">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
            <input type="text" name="app_name" value="{{ config('app.name', 'Laravel') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Application URL</label>
            <input type="url" name="app_url" value="{{ config('app.url', 'http://localhost') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Database Connection</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                    <input type="text" name="db_host" value="127.0.0.1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Port</label>
                    <input type="text" name="db_port" value="3306" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
                <input type="text" name="db_database" value="laravel" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Username</label>
                    <input type="text" name="db_username" value="root" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Password</label>
                    <input type="password" name="db_password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border">
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Save & Continue &rarr;
        </button>
    </div>
</form>
@endsection
