@extends('layouts.superadmin')

@section('header')
<div class="flex items-center justify-between">
    <div class="flex items-center space-x-4">
        <a href="{{ route('superadmin.tickets.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $supportTicket->subject }} <span class="text-sm font-normal text-gray-500">#{{ $supportTicket->id }}</span>
            </h2>
            <p class="text-xs text-gray-500">
                Tenant: <strong>{{ $supportTicket->tenant->name }}</strong> • 
                Category: {{ $supportTicket->category->name }}
            </p>
        </div>
    </div>
    
    <!-- Status & Priority Controls -->
    <form action="{{ route('superadmin.tickets.update', $supportTicket) }}" method="POST" class="flex items-center space-x-2">
        @csrf
        @method('PUT')
        
        <select name="priority" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="low" {{ $supportTicket->priority === 'low' ? 'selected' : '' }}>Low Priority</option>
            <option value="medium" {{ $supportTicket->priority === 'medium' ? 'selected' : '' }}>Medium Priority</option>
            <option value="high" {{ $supportTicket->priority === 'high' ? 'selected' : '' }}>High Priority</option>
        </select>

        <select name="status" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-bold 
            {{ $supportTicket->status === 'open' ? 'text-green-600' : '' }}
            {{ $supportTicket->status === 'in_progress' ? 'text-blue-600' : '' }}
            {{ $supportTicket->status === 'resolved' ? 'text-gray-600' : '' }}
            {{ $supportTicket->status === 'closed' ? 'text-red-600' : '' }}
        ">
            <option value="open" {{ $supportTicket->status === 'open' ? 'selected' : '' }}>Open</option>
            <option value="in_progress" {{ $supportTicket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="resolved" {{ $supportTicket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
            <option value="closed" {{ $supportTicket->status === 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
    </form>
</div>
@endsection

@section('content')
<div class="flex flex-col h-[calc(100vh-200px)]">
    <!-- Messages Area -->
    <div class="flex-1 overflow-y-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6 mb-4" id="messagesContainer">
        @foreach($supportTicket->messages as $message)
            <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-3/4 {{ $message->user_id === auth()->id() ? 'bg-blue-50' : 'bg-gray-50' }} rounded-lg p-4 border {{ $message->user_id === auth()->id() ? 'border-blue-100' : 'border-gray-200' }}">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-medium text-sm {{ $message->user_id === auth()->id() ? 'text-blue-900' : 'text-gray-900' }}">
                            {{ $message->user->name }}
                            @if($message->user->role === 'superadmin') <span class="text-xs bg-blue-200 text-blue-800 px-1 rounded">Staff</span> @endif
                        </span>
                        <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, H:i') }}</span>
                    </div>
                    <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $message->message }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Reply Area -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form action="{{ route('superadmin.tickets.reply', $supportTicket) }}" method="POST">
            @csrf
            <div class="flex gap-4">
                <textarea name="message" rows="2" required class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Type your reply..."></textarea>
                <button type="submit" class="self-end px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex-shrink-0">
                    Send Reply
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const container = document.getElementById('messagesContainer');
    if(container) {
        container.scrollTop = container.scrollHeight;
    }
</script>
@endsection
