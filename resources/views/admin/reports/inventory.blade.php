@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto" x-data="inventoryReport()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Inventory Reports</h2>
            <p class="text-sm text-gray-600 mt-1">Stock levels, valuations, and movement tracking <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse" x-show="loading">Updating...</span></p>
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

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Inventory Value</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        <span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(totalInventoryValue, 2)">{{ number_format($totalInventoryValue, 2) }}</span>
                    </p>
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
                    <p class="text-3xl font-bold text-blue-600 mt-2" x-text="formatNumber(totalInventoryUnits)">{{ number_format($totalInventoryUnits) }}</p>
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
                    <p class="text-3xl font-bold text-red-600 mt-2" x-text="lowStock.length">{{ count($lowStock) }}</p>
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
                    <template x-for="stock in stockByWarehouse" :key="stock.warehouse">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="stock.warehouse"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right" x-text="formatNumber(stock.product_count)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right" x-text="formatNumber(stock.total_units)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                                <span x-text="currencySymbol"></span><span x-text="formatNumber(stock.total_value, 2)"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="stockByWarehouse.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No warehouse data available</td>
                    </tr>
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
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Adj/Trans/Ret</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Closing</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in inventoryReportData" :key="item.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900" x-text="item.name"></div>
                                <div class="text-xs text-gray-500" x-text="item.sku"></div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-500" x-text="formatNumber(item.opening_stock)"></td>
                            <td class="px-6 py-4 text-right text-sm text-green-600" x-text="'+' + formatNumber(item.purchased_qty)"></td>
                            <td class="px-6 py-4 text-right text-sm text-red-600" x-text="'-' + formatNumber(item.sold_qty)"></td>
                            <td class="px-6 py-4 text-right text-sm text-blue-600" x-text="( (item.adjustment_qty + item.transfer_qty + item.return_qty) >= 0 ? '+' : '' ) + formatNumber(item.adjustment_qty + item.transfer_qty + item.return_qty)"></td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900" x-text="formatNumber(item.closing_stock)"></td>
                        </tr>
                    </template>
                    <tr x-show="inventoryReportData.length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No data found</td>
                    </tr>
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-200" x-show="!loading">
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
                        <template x-for="product in lowStock" :key="product.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium" x-text="product.name"></div>
                                    <div class="text-xs text-gray-500" x-text="product.sku"></div>
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800" x-text="product.stock_quantity"></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 text-right" x-text="product.low_stock_threshold"></td>
                            </tr>
                        </template>
                        <tr x-show="lowStock.length === 0">
                            <td colspan="3" class="px-4 py-8 text-center text-green-600">
                                <i class="fas fa-check-circle text-2xl mb-2"></i>
                                <div>All products are well stocked!</div>
                            </td>
                        </tr>
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
                        <template x-for="product in fastMoving" :key="product.sku">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium" x-text="product.name"></div>
                                    <div class="text-xs text-gray-500" x-text="product.sku"></div>
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-blue-600 text-right" x-text="formatNumber(product.sold_qty)"></td>
                            </tr>
                        </template>
                        <tr x-show="fastMoving.length === 0">
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                <div>No sales data found!</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Stock Movements -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Recent Movements</h3>
            <a href="{{ route('admin.reports.movement') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View All Dashboard →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="movement in stockMovements" :key="movement.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(movement.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium" x-text="movement.product ? movement.product.name : 'Deleted'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="movement.warehouse ? movement.warehouse.name : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize" 
                                    :class="{
                                        'bg-green-100 text-green-800': movement.type === 'purchase',
                                        'bg-blue-100 text-blue-800': movement.type === 'sale',
                                        'bg-yellow-100 text-yellow-800': movement.type === 'adjustment',
                                        'bg-gray-100 text-gray-800': !['purchase', 'sale', 'adjustment'].includes(movement.type)
                                    }" x-text="movement.type"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right" 
                                :class="movement.quantity > 0 ? 'text-green-600' : 'text-red-600'" 
                                x-text="(movement.quantity > 0 ? '+' : '') + movement.quantity"></td>
                        </tr>
                    </template>
                    <tr x-show="stockMovements.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No stock movements found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function inventoryReport() {
    return {
        loading: false,
        totalInventoryValue: {{ $totalInventoryValue }},
        totalInventoryUnits: {{ $totalInventoryUnits }},
        lowStock: {!! json_encode($lowStock) !!},
        stockByWarehouse: {!! json_encode($stockByWarehouse) !!},
        inventoryReportData: {!! json_encode($inventoryReport->items()) !!},
        fastMoving: {!! json_encode($fastMoving) !!},
        stockMovements: {!! json_encode($stockMovements) !!},
        currencySymbol: '{{ $tenant->data["currency_symbol"] ?? "₦" }}',

        init() {
            // Polling every 45 seconds to avoid too much load
            setInterval(() => this.fetchData(), 45000);
        },

        async fetchData() {
            if (this.loading) return;
            this.loading = true;
            try {
                const url = new URL(window.location.href);
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await response.json();
                
                this.totalInventoryValue = data.totalInventoryValue;
                this.totalInventoryUnits = data.totalInventoryUnits;
                this.lowStock = data.lowStock;
                this.stockByWarehouse = data.stockByWarehouse;
                // Only update the first page if we are on it, or if Laravel paginator works well with JSON
                // For now, let's keep the current page items
                if (data.inventoryReport && data.inventoryReport.data) {
                    this.inventoryReportData = data.inventoryReport.data;
                }
                this.fastMoving = data.fastMoving;
                this.stockMovements = data.stockMovements;

            } catch (error) {
                console.error('Failed to fetch real-time data:', error);
            } finally {
                this.loading = false;
            }
        },

        formatNumber(num, decimals = 0) {
            return Number(num).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        },

        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        }
    };
}
</script>

@endsection
