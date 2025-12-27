<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50">
    <!-- Main Button -->
    <button 
        @click="open = !open"
        class="flex items-center justify-center w-14 h-14 rounded-full shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        :class="open ? 'bg-red-500 hover:bg-red-600 rotate-45' : 'bg-blue-600 hover:bg-blue-700'"
    >
        <svg x-show="!open" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <svg x-show="open" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>

    <!-- Chat Panel -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95"
        class="absolute bottom-20 right-0 w-80 sm:w-96 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
        style="display: none;"
    >
        <div class="bg-blue-600 p-4 border-b border-blue-700">
            <h3 class="text-white font-medium text-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Support Center
            </h3>
            <p class="text-blue-100 text-xs mt-1">Need help? We are here for you.</p>
        </div>

        <div class="p-4 bg-gray-50 h-80 overflow-y-auto">
            <!-- Quick Actions -->
            <div class="space-y-3">
                <a href="{{ route('admin.support.index') }}" class="block w-full text-left bg-white p-3 rounded-lg shadow-sm hover:shadow-md transition border border-gray-100 flex items-center justify-between group">
                    <span class="font-medium text-gray-700 group-hover:text-blue-600">View My Tickets</span>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Quick Contact</p>
                    <form action="{{ route('admin.support.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="priority" value="medium">
                        
                        <div class="space-y-3">
                            <select name="category_id" required class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @php
                                    $widgetCategories = \App\Models\TicketCategory::all();
                                @endphp
                                <option value="">Select Topic</option>
                                @foreach($widgetCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>

                            <input type="text" name="subject" required placeholder="Subject" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                            <textarea name="message" rows="3" required placeholder="How can we help?" class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-sm text-sm transition">
                                Start Chat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
