@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Post</h2>
        <a href="{{ route('admin.posts.index') }}" class="text-gray-600 hover:text-gray-900">← Back to Posts</a>
    </div>

    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Slug -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Excerpt -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                <textarea name="excerpt" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            <!-- Content -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                <textarea name="content" rows="10"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('content', $post->content) }}</textarea>
            </div>

            <!-- Image -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                @if($post->image_url)
                    <div class="mb-2">
                        <img src="{{ $post->image_url }}" alt="" class="h-32 rounded object-cover">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Publishing -->
            <div class="md:col-span-2 border-t pt-6">
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Published</span>
                    </label>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Published At</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.posts.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Update Post
            </button>
        </div>
    </form>
</div>
@endsection
