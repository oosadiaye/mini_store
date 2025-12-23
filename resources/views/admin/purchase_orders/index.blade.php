@extends('admin.layout')

@section('content')
@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
        <h3 class="text-lg font-bold text-gray-800">Purchase Orders</h3>
        <div class="flex items-center gap-2">
            <form method="GET">
                <select name="warehouse_id" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.purchase-orders.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
                + New Order
            </a>
        </div>
    </div>
    
    <form action="{{ route('admin.purchase-orders.bulk') }}" method="POST" id="bulk-form">
        @csrf
        
        <!-- Bulk Actions Toolbar -->
        <div class="px-6 py-2 bg-gray-50 border-b border-gray-200 flex items-center space-x-3 text-sm">
            <span class="text-gray-500">With Selected:</span>
            <select name="action" class="rounded border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="mark_ordered">Mark as Ordered</option>
                <option value="delete">Delete (Drafts Only)</option>
            </select>
            <button type="submit" class="px-3 py-1 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50" onclick="return confirm('Are you sure?')">Apply</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 w-4">
                            <input type="checkbox" onclick="toggleAll(this)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3">PO #</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Supplier</th>
                        <th class="px-6 py-3">Warehouse</th>
                        <th class="px-6 py-3 text-right">Items</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $po)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">
                            <input type="checkbox" name="ids[]" value="{{ $po->id }}" class="bulk-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-900">
                            <a href="{{ route('admin.purchase-orders.show', $po->id) }}" class="hover:underline">
                                {{ $po->po_number }}    
                            </a>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $po->order_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $po->supplier->name }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $po->warehouse->name }}</td>
                        <td class="px-6 py-3 text-right font-mono text-gray-600">{{ $po->total_quantity ?? 0 }}</td>
                        <td class="px-6 py-3 text-right font-bold text-gray-900">${{ number_format($po->total, 2) }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($po->status === 'draft')
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold">Draft</span>
                            @elseif($po->status === 'ordered')
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">Ordered</span>
                            @elseif($po->status === 'received')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Received</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold">{{ ucfirst($po->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('admin.purchase-orders.show', $po->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            No purchase orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
    
    <div class="p-4 border-t border-gray-200">
        {{ $orders->links() }}
    </div>
</div>

<script>
function toggleAll(source) {
    checkboxes = document.querySelectorAll('.bulk-check');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>
@endsection
