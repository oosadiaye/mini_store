@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto px-3 md:px-0">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 md:mb-6 gap-3">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Stock Transfers</h2>
            <p class="text-xs md:text-sm text-gray-600 mt-1">Manage inventory transfers between warehouses</p>
        </div>
        <a href="{{ route('admin.stock-transfers.create') }}" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition text-center text-sm">
            <i class="fas fa-plus mr-2"></i>New Transfer
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-3 md:p-4 mb-4 md:mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Warehouse</label>
                <select name="warehouse_id" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col sm:flex-row items-end gap-2">
                <button type="submit" class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition text-sm">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.stock-transfers.index') }}" class="w-full sm:w-auto text-center text-gray-600 hover:text-gray-900 px-4 py-2 text-sm">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Transfers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($transfers->count() > 0)
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From → To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transfers as $transfer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $transfer->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $transfer->product->name }}</div>
                            <div class="text-xs text-gray-500">SKU: {{ $transfer->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center text-sm">
                                <span class="text-gray-900">{{ $transfer->fromWarehouse->name }}</span>
                                <i class="fas fa-arrow-right mx-2 text-gray-400"></i>
                                <span class="text-gray-900">{{ $transfer->toWarehouse->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transfer->quantity }} units
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transfer->status === 'pending')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($transfer->status === 'completed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @elseif($transfer->status === 'rejected')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transfer->requestedBy->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transfer->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.stock-transfers.show', $transfer) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                View
                            </a>
                            @if($transfer->status === 'pending')
                                <form action="{{ route('admin.stock-transfers.approve', $transfer) }}" method="POST" class="inline" onsubmit="return confirm('Approve this transfer?')">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.stock-transfers.reject', $transfer) }}" method="POST" class="inline" onsubmit="return confirm('Reject this transfer?')">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Reject
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transfers->links() }}
            </div>
            </div>
            
            <!-- Mobile Card View -->
            <div class="lg:hidden divide-y divide-gray-100">
                @foreach($transfers as $transfer)
                <div class="p-3">
                    <!-- Card Header -->
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="text-sm font-semibold text-gray-900">#{{ $transfer->id }}</span>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $transfer->created_at->format('M d, Y') }}</div>
                        </div>
                        @if($transfer->status === 'pending')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @elseif($transfer->status === 'completed')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                            </span>
                        @elseif($transfer->status === 'rejected')
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Rejected
                            </span>
                        @endif
                    </div>
                    
                    <!-- Product Info -->
                    <div class="mb-2">
                        <div class="text-sm font-medium text-gray-900">{{ $transfer->product->name }}</div>
                        <div class="text-xs text-gray-500">SKU: {{ $transfer->product->sku }}</div>
                    </div>
                    
                    <!-- Transfer Details -->
                    <div class="space-y-1.5 py-2 border-t border-gray-100 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">From → To:</span>
                            <span class="text-gray-900 font-medium">{{ $transfer->fromWarehouse->name }} → {{ $transfer->toWarehouse->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Quantity:</span>
                            <span class="text-gray-900 font-medium">{{ $transfer->quantity }} units</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Requested By:</span>
                            <span class="text-gray-900 font-medium">{{ $transfer->requestedBy->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                        <a href="{{ route('admin.stock-transfers.show', $transfer) }}" class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-3 py-2 rounded text-xs font-medium transition">
                            <i class="fas fa-eye mr-1"></i> View
                        </a>
                        @if($transfer->status === 'pending')
                            <form action="{{ route('admin.stock-transfers.approve', $transfer) }}" method="POST" class="flex-1" onsubmit="return confirm('Approve this transfer?')">
                                @csrf
                                <button type="submit" class="w-full bg-green-50 hover:bg-green-100 text-green-600 px-3 py-2 rounded text-xs font-medium transition">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.stock-transfers.reject', $transfer) }}" method="POST" class="flex-1" onsubmit="return confirm('Reject this transfer?')">
                                @csrf
                                <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded text-xs font-medium transition">
                                    <i class="fas fa-times mr-1"></i> Reject
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
                
                <div class="p-4">
                    {{ $transfers->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-exchange-alt text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">No stock transfers found</p>
                <a href="{{ route('admin.stock-transfers.create') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">
                    Create First Transfer
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
