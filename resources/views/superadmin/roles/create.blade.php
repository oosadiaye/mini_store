@extends('layouts.superadmin')

@section('header', 'Create Role')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.roles.index') }}" class="text-blue-600 hover:underline">← Back to Roles</a>
</div>

<div class="bg-white shadow rounded-lg p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Create New Role</h1>

    <form action="{{ route('superadmin.roles.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Role Name</label>
            <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Assign Permissions</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 border p-4 rounded bg-gray-50">
                @foreach($permissions as $permission)
                    <div class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" class="mr-2">
                        <label for="perm_{{ $permission->id }}" class="text-sm text-gray-600 cursor-pointer select-none">
                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create Role
            </button>
        </div>
    </form>
</div>
@endsection
