@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Blog Posts</h2>
    <a href="{{ route('admin.posts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
        + Create Post
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published At</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($posts as $post)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($post->image_url)
                        <img src="{{ $post->image_url }}" alt="" class="h-10 w-10 rounded object-cover">
                    @else
                        <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center text-gray-400">
                            <span class="text-xs">No Img</span>
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                    <div class="text-xs text-gray-500">{{ Str::limit($post->excerpt, 50) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($post->is_published)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Published
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Draft
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $post->published_at ? $post->published_at->format('M d, Y') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    No posts found. Start by creating one!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $posts->links() }}
    </div>
</div>
@endsection
