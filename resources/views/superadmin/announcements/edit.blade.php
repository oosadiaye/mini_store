@extends('layouts.superadmin')

@section('header', 'Edit Announcement')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.announcements.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Announcements
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-3xl mx-auto">
    <form action="{{ route('superadmin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Content -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message Note</label>
                <textarea name="content" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $announcement->content) }}</textarea>
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="announcement" {{ old('type', $announcement->type) == 'announcement' ? 'selected' : '' }}>General Announcement (Notification Bell)</option>
                    <option value="onboarding" {{ old('type', $announcement->type) == 'onboarding' ? 'selected' : '' }}>Onboarding (Popup Modal)</option>
                </select>
            </div>

            <!-- Attachment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment (Image or Video)</label>
                @if($announcement->attachment_path)
                    <div class="mb-2 text-sm text-gray-600">
                        Current: <a href="{{ Storage::url($announcement->attachment_path) }}" target="_blank" class="text-blue-600 underline">View File</a> ({{ $announcement->attachment_type }})
                    </div>
                @endif
                <input type="file" name="attachment" accept="image/*,video/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">Upload to replace current attachment.</p>
                @error('attachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Action URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Action URL (Optional)</label>
                <input type="url" name="action_url" value="{{ old('action_url', $announcement->action_url) }}" placeholder="https://" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Duration Note -->
             <div class="bg-yellow-50 p-3 rounded text-sm text-yellow-800">
                <strong>Note:</strong> Updating this will not change the already set Expiration Date: {{ $announcement->end_at->format('M d, Y H:i') }}.
            </div>

            <!-- Target -->
            <div x-data="{ target: '{{ old('target_type', $announcement->target_type) }}' }">
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
                            <option value="{{ $tenant->id }}" {{ in_array($tenant->id, $selectedTenants) ? 'selected' : '' }}>
                                {{ $tenant->name }} ({{ $tenant->id }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition">
                    Update Announcement
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
