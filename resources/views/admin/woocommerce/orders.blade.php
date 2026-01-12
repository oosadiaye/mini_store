<x-app-layout :title="'WooCommerce Orders'">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">WooCommerce Orders</h2>
            <a href="{{ route('admin.woocommerce.index', ['tenant' => tenant()->slug]) }}" class="text-gray-500 hover:text-gray-700">Back to Settings</a>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 border-t-4 border-indigo-500">
                <div class="text-xs font-medium text-gray-500 uppercase">Received</div>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 border-t-4 border-yellow-500">
                <div class="text-xs font-medium text-gray-500 uppercase">Processing</div>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['processing'] }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 border-t-4 border-purple-500">
                <div class="text-xs font-medium text-gray-500 uppercase">Shipped</div>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['shipped'] }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 border-t-4 border-blue-500">
                <div class="text-xs font-medium text-gray-500 uppercase">Pending</div>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 border-t-4 border-green-500">
                <div class="text-xs font-medium text-gray-500 uppercase">Completed</div>
                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status (Local / WC)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('admin.orders.show', ['tenant' => tenant()->slug, 'order' => $order->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $order->order_number }}
                                        </a>
                                        <div class="text-xs text-gray-500">WC ID: {{ $order->woocommerce_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->customer->name ?? 'Guest' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($order->woocommerce_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->currency_symbol }}{{ number_format($order->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('tenant.admin.orders.show', ['tenant' => tenant()->slug, 'order' => $order->id]) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No synced orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
