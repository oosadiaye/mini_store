@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Create New Role</h2>
    <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-800">
        &larr; Back to Roles
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
            <input type="text" name="name" id="name" class="w-full max-w-md border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Supervisor" value="{{ old('name') }}" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($permissions as $module => $modulePermissions)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-3 border-b pb-2">{{ $module }}</h3>
                        <div class="space-y-2">
                            @foreach($modulePermissions as $permission)
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="perm_{{ $permission->id }}" class="font-medium text-gray-700">{{ $permission->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <a href="{{ route('admin.roles.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition mr-3">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition shadow-lg">Create Role</button>
        </div>
    </form>
</div>
@endsection
