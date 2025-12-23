@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Roles & Permissions</h2>
    <a href="{{ route('admin.roles.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">
        + Add New Role
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users Assigned</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($roles as $role)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $role->guard_name }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500">
                            @if($role->name === 'Store Admin')
                                <span class="text-green-600 font-semibold">All Permissions</span>
                            @else
                                {{ $role->permissions->count() }} permissions
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $role->users()->count() }} users
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        
                        @if(!in_array($role->name, ['Store Admin', 'Sales', 'Inventory', 'Purchase Manager', 'Accountant', 'Cashier']))
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        No roles found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $roles->links() }}
</div>
@endsection
