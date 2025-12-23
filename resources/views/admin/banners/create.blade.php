@extends('admin.layout')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center space-x-4">
        <a href="{{ route('admin.banners.index') }}" class="text-gray-500 hover:text-gray-700">
            &larr; Back
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Add New Banner</h2>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Position -->
                <div x-data="{ position: 'home_hero' }">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                        <select name="position" x-model="position" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="home_hero">Home Hero (Top Large)</option>
                            <option value="home_top">Home Top (Below Hero)</option>
                            <option value="home_middle">Home Middle</option>
                            <option value="sidebar">Sidebar</option>
                            <option value="footer">Footer</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    </div>

                    <!-- Link & Button -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Link URL (Optional)</label>
                            <input type="text" name="link" value="{{ old('link') }}" placeholder="/products" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Button Text (Optional)</label>
                            <input type="text" name="button_text" value="{{ old('button_text') }}" placeholder="Shop Now" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Banner Image</label>
                        <input type="file" name="image" accept="image/*" required class="w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100">
                        <p class="text-xs text-indigo-600 font-medium mt-1">
                            Recommended Size: 
                            <span x-show="position === 'home_hero'">1920x600px</span>
                            <span x-show="position === 'home_top'">1200x300px</span>
                            <span x-show="position === 'home_middle'">1200x400px</span>
                            <span x-show="position === 'sidebar'">300x600px</span>
                            <span x-show="position === 'footer'">1200x200px</span>
                        </p>
                    </div>
                </div>

                <!-- Toggles -->
                <div class="flex items-center space-x-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Create Banner
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
