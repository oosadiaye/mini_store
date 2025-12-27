@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Inventory Reports</h2>
            <p class="text-sm text-gray-600 mt-1">Stock levels, valuations, and movement tracking</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Reports
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse</label>
                <select name="warehouse_id" class="px-3 py-2 border border-gray-300 rounded-md min-w-[200px]">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md transition">
                Apply Filters
            </button>
             <a href="{{ route('admin.reports.export', ['type' => 'inventory', 'start_date' => $startDate, 'end_date' => $endDate, 'warehouse_id' => request('warehouse_id')]) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md transition ml-auto">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Inventory Value</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($totalInventoryValue, 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Units in Stock</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($totalInventoryUnits) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $lowStock->count() }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock by Warehouse -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Stock Distribution by Warehouse</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Units</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockByWarehouse as $stock)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stock->warehouse }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($stock->product_count) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($stock->total_units) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                            {{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($stock->total_value, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No warehouse data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Inventory Activity -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Inventory Activity Report</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Opening</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Purchased</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Closing (Current)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inventoryReport as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                            <div class="text-xs text-gray-500">{{ $item->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-500">{{ number_format($item->opening_stock) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-green-600">+{{ number_format($item->purchased_qty) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-red-600">-{{ number_format($item->sold_qty) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">{{ number_format($item->current_stock) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $inventoryReport->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Low Stock Alert -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h3 class="text-lg font-bold text-red-800">⚠️ Low Stock Alert</h3>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Threshold</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($lowStock as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <div class="font-medium">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 text-right">{{ $product->low_stock_threshold }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-green-600">
                                <i class="fas fa-check-circle text-2xl mb-2"></i>
                                <div>All products are well stocked!</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Fast Moving Products -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                <h3 class="text-lg font-bold text-blue-800">🚀 Fast Moving Products</h3>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sold Qty</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($fastMoving as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <div class="font-medium">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-blue-600 text-right">{{ number_format($product->sold_qty) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                <div>No sales data found!</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Recent Stock Movements (Last 30 Days)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From → To</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockMovements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $movement->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $movement->fromWarehouse->name }} → {{ $movement->toWarehouse->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            {{ $movement->quantity }} units
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ ucfirst($movement->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No stock movements in the last 30 days</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
