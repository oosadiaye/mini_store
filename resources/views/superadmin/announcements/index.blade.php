@extends('layouts.superadmin')

@section('header', 'Announcements & Onboarding')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Announcements</h2>
        <p class="text-gray-500 text-sm">Manage onboarding messages and system updates.</p>
    </div>
    <a href="{{ route('superadmin.announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Create New
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">Title</th>
                <th class="p-4 border-b border-gray-100">Type</th>
                <th class="p-4 border-b border-gray-100">Target</th>
                <th class="p-4 border-b border-gray-100">Starts</th>
                <th class="p-4 border-b border-gray-100">Ends</th>
                <th class="p-4 border-b border-gray-100 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($announcements as $announcement)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 font-medium text-gray-900">
                    {{ $announcement->title }}
                    @if($announcement->attachment_type !== 'none')
                        <span class="ml-2 text-gray-400">
                            <i class="fas fa-paperclip"></i> 
                            ({{ ucfirst($announcement->attachment_type) }})
                        </span>
                    @endif
                </td>
                <td class="p-4">
                    <span class="px-2 py-1 text-xs rounded-full {{ $announcement->type === 'onboarding' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($announcement->type) }}
                    </span>
                </td>
                <td class="p-4 text-sm text-gray-600">
                    @if($announcement->target_type === 'all')
                        All Tenants
                    @else
                        {{ $announcement->tenants_count }} Selected
                    @endif
                </td>
                <td class="p-4 text-sm text-gray-600">
                    {{ $announcement->start_at ? $announcement->start_at->format('M d, Y H:i') : '-' }}
                </td>
                <td class="p-4 text-sm text-gray-600">
                    {{ $announcement->end_at ? $announcement->end_at->format('M d, Y H:i') : '-' }}
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('superadmin.announcements.edit', $announcement) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Edit</a>
                    <form action="{{ route('superadmin.announcements.destroy', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this announcement?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-8 text-center text-gray-500">
                    No announcements found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
