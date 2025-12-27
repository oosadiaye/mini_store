@extends('install.layout')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Create Super Admin</h2>
    <p class="text-gray-600 mb-6">Create the primary administrator account to manage your tenants and plans.</p>
</div>

<form action="{{ route('install.admin.store') }}" method="POST">
    @csrf
    
    <div class="space-y-6 mb-8">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required placeholder="John Doe">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required placeholder="admin@example.com">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border" required>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Create Account & Finish &rarr;
        </button>
    </div>
</form>
@endsection
