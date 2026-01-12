<x-storefront.layout>
    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Account</h1>
                    <p class="mt-1 text-gray-500">Welcome back, {{ $customer->name }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <form method="POST" action="{{ route('storefront.logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border-2 border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Profile Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $customer->phone ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Order History</h3>
                        </div>
                        
                        @if($orders->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <li>
                                        <div class="px-4 py-4 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex flex-col">
                                                    <p class="text-sm font-medium text-indigo-600 truncate">
                                                        {{ $order->order_number }}
                                                    </p>
                                                    <p class="flex items-center text-sm text-gray-500 mt-1">
                                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        Placed on {{ $order->created_at->format('M d, Y') }}
                                                    </p>
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                                        @if($order->status === 'completed') bg-green-100 text-green-800
                                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                                        @else bg-yellow-100 text-yellow-800 @endif">
                                                        {{ $order->status }}
                                                    </span>
                                                    <p class="mt-1 text-sm font-bold text-gray-900">
                                                        {{ $config->currency_symbol ?? '$' }}{{ number_format($order->total, 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-between items-center">
                                                <div class="text-sm text-gray-500">
                                                    {{ $order->items->count() }} Item(s)
                                                </div>
                                                <div>
                                                    <a href="{{ route('storefront.orders.track', ['order_number' => $order->order_number, 'email' => $customer->email]) }}" 
                                                       onclick="event.preventDefault(); document.getElementById('track-form-{{ $order->id }}').submit();"
                                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                        View Details &rarr;
                                                    </a>
                                                    <!-- Hidden form to POST to track endpoint to view details immediately -->
                                                    <form id="track-form-{{ $order->id }}" action="{{ route('storefront.orders.track.store') }}" method="POST" style="display: none;">
                                                        @csrf
                                                        <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                                                        <input type="hidden" name="email" value="{{ $customer->email }}">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                                {{ $orders->links() }}
                            </div>
                        @else
                            <div class="p-6 text-center text-gray-500">
                                You haven't placed any orders yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-storefront.layout>
