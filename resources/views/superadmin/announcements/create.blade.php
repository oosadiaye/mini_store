@extends('layouts.superadmin')

@section('header', 'Create Announcement')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.announcements.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Announcements
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl mx-auto">
    <form action="{{ route('superadmin.announcements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Content -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message Note</label>
                <textarea name="content" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content') }}</textarea>
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="announcement" {{ old('type') == 'announcement' ? 'selected' : '' }}>General Announcement (Notification Bell)</option>
                    <option value="onboarding" {{ old('type') == 'onboarding' ? 'selected' : '' }}>Onboarding (Popup Modal)</option>
                </select>
            </div>

            <!-- Attachment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment (Image or Video)</label>
                <input type="file" name="attachment" accept="image/*,video/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">Supports Image (JPG, PNG) or Video (MP4). Max 10MB.</p>
                @error('attachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Action URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Action URL (Optional)</label>
                <input type="url" name="action_url" value="{{ old('action_url') }}" placeholder="https://" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Duration -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration Value</label>
                    <input type="number" name="duration_val" value="{{ old('duration_val', 7) }}" min="1" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <select name="duration_unit" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="hours">Hours</option>
                        <option value="days" selected>Days</option>
                        <option value="weeks">Weeks</option>
                        <option value="months">Months</option>
                    </select>
                </div>
            </div>

            <!-- Target -->
            <div x-data="{ target: '{{ old('target_type', 'all') }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                <div class="flex items-center space-x-6 mb-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="target_type" value="all" x-model="target" class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">All Tenants</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="target_type" value="selected" x-model="target" class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">Selected Tenants</span>
                    </label>
                </div>

                <div x-show="target === 'selected'" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Tenants</label>
                    <select name="tenants[]" multiple class="w-full h-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->id }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</p>
                </div>
            </div>

            <div class="pt-4 border-t">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition">
                    Publish Announcement
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
