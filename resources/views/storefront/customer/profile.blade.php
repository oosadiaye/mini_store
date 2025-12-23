@extends('storefront.layout')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <div class="w-full md:w-1/4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="mb-6 text-center">
                        <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4 text-primary text-2xl font-bold">
                            {{ substr(Auth::guard('customer')->user()->name, 0, 1) }}
                        </div>
                        <h2 class="font-bold text-gray-900">{{ Auth::guard('customer')->user()->name }}</h2>
                        <p class="text-sm text-gray-500">{{ Auth::guard('customer')->user()->email }}</p>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="{{ route('storefront.customer.profile') }}" class="block px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-medium">My Orders</a>
                        <form action="{{ route('storefront.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">Logout</button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="w-full md:w-3/4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-900">Order History</h2>
                    </div>
                    
                    @if($orders->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($orders as $order)
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex flex-col md:flex-row justify-between md:items-center">
                                    <div class="mb-4 md:mb-0">
                                        <div class="flex items-center gap-3">
                                            <span class="font-mono font-bold text-indigo-600">#{{ $order->order_number }}</span>
                                            <span class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-600">
                                            {{ $order->items->count() }} items • Total: ${{ number_format($order->total, 2) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                               'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        <a href="{{ route('storefront.customer.order', $order->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="p-4 border-t border-gray-100">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="text-5xl mb-4">🛍️</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                            <p class="text-gray-500 mb-6">Looks like you haven't placed any orders yet.</p>
                            <a href="{{ route('storefront.products') }}" class="inline-block bg-primary text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
