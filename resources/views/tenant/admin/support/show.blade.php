@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.support.index') }}" class="group flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-600 transition-all duration-200 shadow-sm">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                        {{ $support->subject }}
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border
                        @if($support->status === 'open') bg-green-50 text-green-700 border-green-100
                        @elseif($support->status === 'closed') bg-gray-100 text-gray-700 border-gray-200
                        @else bg-yellow-50 text-yellow-700 border-yellow-100 @endif">
                        {{ ucfirst($support->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Ticket #{{ $support->id }} &bull; Created {{ $support->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        
        @if(!$support->isClosed())
        <div class="flex items-center gap-2">
            <!-- Optional: Add actions like 'Close Ticket' here if needed later -->
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Chat Area (Left/Main) -->
        <div class="lg:col-span-2 flex flex-col h-[calc(100vh-220px)] min-h-[500px]">
            
            <!-- Messages Container -->
            <div class="flex-1 overflow-y-auto bg-white rounded-t-xl shadow-sm border border-gray-200 p-6 space-y-6 scroll-smooth" id="messagesContainer">
                @foreach($support->messages as $message)
                    @php
                        $isCurrentUser = $message->user_id === auth()->id();
                    @endphp
                    <div class="flex {{ $isCurrentUser ? 'justify-end' : 'justify-start' }} group">
                        <div class="flex flex-col {{ $isCurrentUser ? 'items-end' : 'items-start' }} max-w-[85%]">
                            
                            <!-- Header Info -->
                            <div class="flex items-center gap-2 mb-1 px-1">
                                <span class="text-xs font-semibold {{ $isCurrentUser ? 'text-indigo-600' : 'text-gray-900' }}">
                                    {{ $message->user->name }}
                                </span>
                                <span class="text-[10px] text-gray-400">
                                    {{ $message->created_at->format('M d, H:i') }}
                                </span>
                            </div>

                            <!-- Bubble -->
                            <div class="px-5 py-3.5 rounded-2xl text-sm leading-relaxed shadow-sm
                                {{ $isCurrentUser 
                                    ? 'bg-indigo-600 text-white rounded-tr-none' 
                                    : 'bg-gray-100 text-gray-800 rounded-tl-none border border-gray-200' 
                                }}">
                                {!! nl2br(e($message->message)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Reply Area -->
            <div class="bg-gray-50 rounded-b-xl shadow-sm border-x border-b border-gray-200 p-4">
                @if($support->isClosed())
                    <div class="flex items-center justify-center gap-2 text-gray-500 py-4 bg-white rounded-lg border border-gray-200 border-dashed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="font-medium">This ticket is closed. Replies are disabled.</span>
                    </div>
                @else
                    <form action="{{ route('admin.support.reply', $support) }}" method="POST" class="relative">
                        @csrf
                        <div class="relative">
                            <textarea name="message" rows="3" required 
                                class="w-full pl-4 pr-32 py-3 bg-white rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none text-sm placeholder-gray-400" 
                                placeholder="Type your reply here..."></textarea>
                            
                            <div class="absolute bottom-2 right-2 flex items-center">
                                <button type="submit" class="inline-flex items-center px-4 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Sidebar Info (Right) -->
        <div class="space-y-6">
            
            <!-- Ticket Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Ticket Details</h3>
                </div>
                <div class="p-5 space-y-4">
                    
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Category</span>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded bg-indigo-50 text-indigo-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            </span>
                            <span class="text-sm font-medium text-gray-900">{{ $support->category->name }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Priority</span>
                        <div class="flex items-center gap-2">
                            @if($support->priority === 'high' || $support->priority === 'urgent')
                                <span class="flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            @elseif($support->priority === 'medium')
                                <span class="flex h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                            @else
                                <span class="flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
                            @endif
                            <span class="text-sm font-medium text-gray-900 capitalize">{{ $support->priority }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase mb-1">Assigned Staff</span>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600 border border-gray-200">
                                {{ $support->assignedStaff ? substr($support->assignedStaff->name, 0, 1) : '?' }}
                            </div>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $support->assignedStaff->name ?? 'Unassigned' }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Customer Card (Optional, for context) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Customer Info</h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scroll to bottom script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    </script>
</div>
@endsection

