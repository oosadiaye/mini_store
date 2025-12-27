@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Support Tickets</h2>
        <p class="text-sm text-gray-500 mt-1">Manage your support requests and inquiries.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Ticket List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Your Tickets</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $tickets->total() }} Total
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Subject</th>
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Last Update</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $ticket->subject }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">#{{ $ticket->id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center space-x-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                        <span>{{ $ticket->category->name }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                        @if($ticket->status === 'open') bg-green-50 text-green-700 border-green-100
                                        @elseif($ticket->status === 'in_progress') bg-indigo-50 text-indigo-700 border-indigo-100
                                        @elseif($ticket->status === 'closed') bg-gray-100 text-gray-700 border-gray-200
                                        @else bg-yellow-50 text-yellow-700 border-yellow-100 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400">
                                    {{ $ticket->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.support.show', $ticket) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        </div>
                                        <p>No tickets found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $tickets->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Create Ticket Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50/50">
                    <h3 class="font-semibold text-indigo-900 text-sm uppercase tracking-wide">Open New Ticket</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.support.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                                <div class="relative">
                                    <select name="category_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                        <option value="">Select a Category...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Priority</label>
                                <select name="priority" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                    <option value="low">Low - General Inquiry</option>
                                    <option value="medium" selected>Medium - Issue impeding work</option>
                                    <option value="high">High - Critical Issue</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject</label>
                                <input type="text" name="subject" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5" placeholder="Brief summary...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
                                <textarea name="message" rows="5" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none" placeholder="Describe your issue in detail..."></textarea>
                            </div>

                            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform active:scale-95">
                                Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
