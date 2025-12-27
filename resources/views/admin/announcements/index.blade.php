@extends('admin.layout')

@section('header', 'System Announcements')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Latest Updates & Onboarding</h2>
            
            <div class="space-y-8">
                @forelse($announcements as $announcement)
                    <div class="border-b border-gray-100 pb-8 last:border-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                    {{ $announcement->title }}
                                    @if(!$announcement->is_read)
                                        <span class="ml-3 bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">New</span>
                                    @endif
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $announcement->type === 'onboarding' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($announcement->type) }}
                                    </span>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1 mb-4">{{ $announcement->created_at->format('F d, Y') }}</p>
                                
                                <div class="prose max-w-none text-gray-700">
                                    {!! nl2br(e($announcement->content)) !!}
                                </div>
                                
                                @if($announcement->attachment_type !== 'none' && $announcement->attachment_path)
                                    <div class="mt-4">
                                        @if($announcement->attachment_type === 'video')
                                            <video controls class="w-full max-w-2xl rounded-lg shadow-sm">
                                                <source src="{{ Storage::url($announcement->attachment_path) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <img src="{{ Storage::url($announcement->attachment_path) }}" alt="Attachment" class="max-w-xl w-full rounded-lg shadow-sm">
                                        @endif
                                    </div>
                                @endif
                                
                                @if($announcement->action_url)
                                    <div class="mt-4">
                                        <a href="{{ $announcement->action_url }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                            Visit Link &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        No announcements found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    // Mark as read when viewing this page (optional, or rely on clicks/modal)
</script>
@endsection
