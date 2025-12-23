@extends('layouts.superadmin')

@section('header', 'User Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Superadmins</h2>
        <p class="text-gray-500 text-sm">Manage administrators who can access this dashboard.</p>
    </div>
    <a href="{{ route('superadmin.users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
        New User
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">Name</th>
                <th class="p-4 border-b border-gray-100">Email</th>
                <th class="p-4 border-b border-gray-100">Created At</th>
                <th class="p-4 border-b border-gray-100 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 font-medium text-gray-900">
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">You</span>
                    @endif
                </td>
                <td class="p-4 text-gray-700">
                    {{ $user->email }}
                </td>
                <td class="p-4 text-gray-500 text-sm">
                    {{ $user->created_at->format('M d, Y') }}
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('superadmin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="p-8 text-center text-gray-500">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
</div>
@endsection
