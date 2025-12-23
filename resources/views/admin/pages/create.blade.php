@extends('admin.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center space-x-4">
        <a href="{{ route('admin.pages.index') }}" class="text-gray-500 hover:text-gray-700">
            &larr; Back
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Create New Page</h2>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Title</label>
                    <input type="text" name="title" id="title" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                            /pages/
                        </span>
                        <input type="text" name="slug" id="slug" required class="flex-1 rounded-r-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Create & Open Builder
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('title').addEventListener('input', function() {
        const slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
