@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Report</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Detailed analysis of inventory levels, value, and movements.
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:flex-none">
             <a href="{{ route('admin.products.export', ['tenant' => $tenant->slug]) }}" 
                class="inline-flex items-center justify-center rounded-md border-2 border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                <svg class="mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <form action="{{ route('admin.reports.inventory', $tenant->slug) }}" method="GET" class="grid grid-cols-1 gap-y-6 sm:grid-cols-4 sm:gap-x-4">
            <div>
                <label for="warehouse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Warehouse</label>
                <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-md border-2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ $warehouseId == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
             <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                       class="mt-1 block w-full rounded-md border-2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                       class="mt-1 block w-full rounded-md border-2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pt-5 pb-12 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-indigo-500 p-3">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Units</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-1 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalInventoryUnits) }}</p>
            </dd>
        </div>

        <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pt-5 pb-12 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-green-500 p-3">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Value</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-1 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $tenant->currency_symbol }}{{ number_format($totalInventoryValue, 2) }}</p>
            </dd>
        </div>

        <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pt-5 pb-12 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-red-500 p-3">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock Items</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-1 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $lowStock->count() }}</p>
            </dd>
        </div>
        
         <div class="relative overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pt-5 pb-12 shadow sm:px-6 sm:pt-6">
            <dt>
                <div class="absolute rounded-md bg-blue-500 p-3">
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Products</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-1 sm:pb-7">
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($inventoryReport->total()) }}</p>
            </dd>
        </div>
    </dl>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Stock Levels by Warehouse Chart/List -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Stock Distribution by Warehouse</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Warehouse</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unique Products</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Units</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($stockByWarehouse as $record)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $record->warehouse }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($record->product_count) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">{{ number_format($record->total_units) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-semibold text-right">{{ $tenant->currency_symbol }}{{ number_format($record->total_value, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Fast Moving Products -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Fastest Moving Item (Sold Qty)</h3>
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($fastMoving as $item)
                <li class="py-4 flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $item->sku }}</p>
                    </div>
                    <div class="inline-flex items-center text-sm font-semibold text-gray-900 dark:text-white">
                        {{ number_format($item->sold_qty) }} sold
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Detailed Inventory Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Inventory Valuation Report</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Opening</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Purchased</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wider">Sold</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-900 dark:text-white uppercase tracking-wider">Closing (Current)</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($inventoryReport as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">{{ number_format($product->opening_stock) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400 font-medium text-right">+{{ number_format($product->purchased_qty) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400 font-medium text-right">-{{ number_format($product->sold_qty) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-bold text-right">{{ number_format($product->current_stock) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No inventory data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            {{ $inventoryReport->links() }}
        </div>
    </div>
</div>
@endsection
