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
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border-2 border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border-2 border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse</label>
                <select name="warehouse_id" class="px-3 py-2 border-2 border-gray-300 rounded-md min-w-[150px]">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ $warehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" class="px-3 py-2 border-2 border-gray-300 rounded-md min-w-[150px]">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($categoryId ?? 0) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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

    <!-- Vue Component -->
    <inventory-report
        :total-inventory-value="{{ json_encode($totalInventoryValue) }}"
        :total-inventory-units="{{ json_encode($totalInventoryUnits) }}"
        :low-stock="{{ json_encode($lowStock) }}"
        :stock-by-warehouse="{{ json_encode($stockByWarehouse) }}"
        :inventory-report-data="{{ json_encode($inventoryReport->items()) }}"
        :fast-moving="{{ json_encode($fastMoving) }}"
        :stock-movements="{{ json_encode($stockMovements) }}"
        currency-symbol="{{ $tenant->data['currency_symbol'] ?? '₦' }}"
        pagination-html="{{ (string) $inventoryReport->withQueryString()->links() }}"
    ></inventory-report>
</div>
@endsection
