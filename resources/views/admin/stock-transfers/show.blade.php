@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Stock Transfer Details</h2>
            <p class="text-sm text-gray-600 mt-1">Transaction #{{ $stockTransfer->id }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.stock-transfers.index') }}" class="bg-white border-2 border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition text-sm">
                Back to List
            </a>
            @if($stockTransfer->status === 'pending')
                <form action="{{ route('admin.stock-transfers.approve', $stockTransfer) }}" method="POST" onsubmit="return confirm('Approve this transfer?')">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm">
                        Approve Transfer
                    </button>
                </form>
                <form action="{{ route('admin.stock-transfers.reject', $stockTransfer) }}" method="POST" onsubmit="return confirm('Reject this transfer?')">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition text-sm">
                        Reject Transfer
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Transfer Information</h3>
                
                <div class="grid grid-cols-2 gap-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Status</p>
                        <p class="mt-1">
                            @if($stockTransfer->status === 'pending')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending Approval
                                </span>
                            @elseif($stockTransfer->status === 'completed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @elseif($stockTransfer->status === 'rejected')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Quantity</p>
                        <p class="mt-1 text-gray-900 font-semibold">{{ $stockTransfer->quantity }} Units</p>
                    </div>
                    
                    <div class="col-span-2 py-4 border-t border-gray-100">
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
                            <div class="text-center flex-1">
                                <p class="text-xs text-gray-500 uppercase mb-1">Source Warehouse</p>
                                <p class="font-bold text-gray-800">{{ $stockTransfer->fromWarehouse->name }}</p>
                                <p class="text-xs text-gray-400">({{ $stockTransfer->fromWarehouse->code }})</p>
                            </div>
                            <div class="px-4">
                                <i class="fas fa-arrow-right text-indigo-400 text-xl"></i>
                            </div>
                            <div class="text-center flex-1">
                                <p class="text-xs text-gray-500 uppercase mb-1">Destination Warehouse</p>
                                <p class="font-bold text-gray-800">{{ $stockTransfer->toWarehouse->name }}</p>
                                <p class="text-xs text-gray-400">({{ $stockTransfer->toWarehouse->code }})</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Notes</p>
                        <p class="mt-1 text-gray-700 italic">
                            {{ $stockTransfer->notes ?? 'No notes provided.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Product Card -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Product Details</h3>
                    <div class="flex items-center">
                        <div class="h-16 w-16 flex-shrink-0">
                            <img class="h-16 w-16 rounded object-cover border" src="{{ $stockTransfer->product->image_url }}" alt="">
                        </div>
                        <div class="ml-4">
                            <div class="text-lg font-bold text-indigo-600">{{ $stockTransfer->product->name }}</div>
                            <div class="text-sm text-gray-500">SKU: {{ $stockTransfer->product->sku }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Timeline -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Transfer Timeline</h4>
                <div class="space-y-4">
                    <div class="flex">
                        <div class="flex flex-col items-center mr-3">
                            <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                            <div class="w-0.5 h-full bg-gray-200"></div>
                        </div>
                        <div class="pb-4">
                            <p class="text-sm font-bold text-gray-800">Requested</p>
                            <p class="text-xs text-gray-500">{{ $stockTransfer->created_at->format('M d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400 mt-1">By: {{ $stockTransfer->requestedBy->name ?? 'System' }}</p>
                        </div>
                    </div>
                    
                    @if($stockTransfer->status !== 'pending')
                    <div class="flex">
                        <div class="flex flex-col items-center mr-3">
                            <div class="w-3 h-3 {{ $stockTransfer->status === 'completed' ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></div>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">
                                {{ ucfirst($stockTransfer->status) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $stockTransfer->approved_at ? $stockTransfer->approved_at->format('M d, Y h:i A') : '-' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Action By: {{ $stockTransfer->approvedBy->name ?? 'System' }}
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="flex">
                        <div class="flex flex-col items-center mr-3">
                            <div class="w-3 h-3 bg-gray-300 rounded-full border-2 border-white"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-400 italic">Awaiting Action</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($stockTransfer->status === 'completed')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                    <div>
                        <h4 class="text-sm font-bold text-green-800">Inventory Updated</h4>
                        <p class="text-xs text-green-700 mt-1">Stock has been successfully moved and warehouse balances are updated.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
