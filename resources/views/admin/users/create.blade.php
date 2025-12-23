@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Create New User</h2>
    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
        &larr; Back to Users
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('name') }}" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('email') }}" required>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role" id="role" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                <option value="">Select a Role</option>
                @foreach($roles as $roleName)
                    <option value="{{ $roleName }}" {{ old('role') == $roleName ? 'selected' : '' }}>{{ $roleName }}</option>
                @endforeach
            </select>
             @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <a href="{{ route('admin.users.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition mr-3">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition shadow-lg">Create User</button>
        </div>
    </form>
</div>
@endsection
