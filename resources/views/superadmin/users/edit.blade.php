@extends('layouts.superadmin')

@section('header', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Users
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('superadmin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="border-t border-gray-100 my-4"></div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password (Optional)</label>
                    <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password.</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                 <div class="pt-4 border-t border-gray-100 flex justify-end">
                     <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition shadow-sm font-medium">Update User</button>
                 </div>
            </div>
        </form>
    </div>
</div>
@endsection
