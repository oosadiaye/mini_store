@extends('admin.layout')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Pages</h2>
    <a href="{{ route('admin.pages.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + Add Page
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">Title</th>
                <th class="p-4 border-b border-gray-100">URL Slug</th>
                <th class="p-4 border-b border-gray-100">Status</th>
                <th class="p-4 border-b border-gray-100 text-center">Last Updated</th>
                <th class="p-4 border-b border-gray-100 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($pages as $page)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 text-sm font-medium text-gray-900">{{ $page->title }}</td>
                <td class="p-4 text-sm text-gray-600">/pages/{{ $page->slug }}</td>
                <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $page->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $page->is_published ? 'Published' : 'Draft' }}
                    </span>
                </td>
                <td class="p-4 text-center text-sm text-gray-500">{{ $page->updated_at->diffForHumans() }}</td>
                <td class="p-4 text-center">
                    <div class="flex items-center justify-center space-x-3">
                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit Content</a>
                        <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Delete this page?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">No pages yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
