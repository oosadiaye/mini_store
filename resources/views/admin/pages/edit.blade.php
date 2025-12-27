@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.pages.index') }}" class="text-gray-500 hover:text-gray-700">
                &larr; Back
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Edit Page: {{ $page->title }}</h2>
        </div>
        <a href="#" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
            View Live <i class="fas fa-external-link-alt ml-1"></i>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Title & Slug -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Page Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                /pages/
                            </span>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" required class="flex-1 rounded-r-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Content</label>
                    <div class="text-xs text-gray-500 mb-2">You can use HTML here.</div>
                    <textarea name="content" rows="15" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('content', $page->content ?? '') }}</textarea>
                </div>

                <!-- Publishing -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" value="1" {{ $page->is_published ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_published" class="ml-2 block text-sm text-gray-900">
                        Publish this page
                    </label>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Update Page
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('title').addEventListener('input', function() {
        if (!this.dataset.slugEdited) {
            const slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            document.getElementById('slug').value = slug;
        }
    });

    document.getElementById('slug').addEventListener('input', function() {
        document.getElementById('title').dataset.slugEdited = true;
    });
</script>
@endsection
