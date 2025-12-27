@extends('admin.layout')

@section('title', 'Create Role')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.roles.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Roles</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Create New Role</h1>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required 
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                   placeholder="e.g., Manager, Staff, Accountant">
            @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Permissions</h3>
            
            @foreach($permissions as $module => $perms)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-700 mb-2 capitalize">{{ $module }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($perms as $permission)
                            <label class="flex items-center">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">{{ str_replace('_', ' ', $permission->name) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.roles.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Create Role</button>
        </div>
    </form>
</div>
@endsection
