<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Sales Analytics Dashboard
     */
    /**
     * Sales Analytics Dashboard
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $channel = $request->get('channel'); // storefront, pos, admin

        // Base Query
        $platformQuery = Order::whereBetween('created_at', [$startDate, $endDate]);
        if ($channel) {
            $platformQuery->where('order_source', $channel);
        }

        // Sales Overview
        $salesData = (clone $platformQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top Selling Products
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->when($channel, function($q) use ($channel) {
                return $q->where('orders.order_source', $channel);
            })
            ->select('products.name', 'products.sku', 
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Sales by Category
        $categoryRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->when($channel, function($q) use ($channel) {
                return $q->where('orders.order_source', $channel);
            })
            ->select('categories.name', 
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        // Sales by Payment Method
        $paymentMethods = (clone $platformQuery)
            ->select('payment_method as name', 
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        // Summary Stats
        $totalOrders = (clone $platformQuery)->count();
        $totalRevenue = (clone $platformQuery)->sum('total');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Sales Channel Breakdown (New)
        $salesByChannel = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('order_source', 
                DB::raw('COUNT(*) as count'), 
                DB::raw('SUM(total) as revenue'))
            ->groupBy('order_source')
            ->get();

        return view('admin.reports.sales', compact(
            'salesData', 'topProducts', 'categoryRevenue', 'paymentMethods',
            'totalOrders', 'totalRevenue', 'averageOrderValue', 'newCustomers',
            'salesByChannel', 'startDate', 'endDate', 'channel'
        ));
    }

    /**
     * Inventory Reports
     */
    public function inventory(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $warehouseId = $request->get('warehouse_id');

        // Stock Levels by Warehouse
        $stockByWarehouse = DB::table('product_warehouse')
            ->join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->join('warehouses', 'product_warehouse.warehouse_id', '=', 'warehouses.id')
            ->when($warehouseId, function($q) use ($warehouseId) {
                return $q->where('product_warehouse.warehouse_id', $warehouseId);
            })
            ->select('warehouses.name as warehouse', 
                DB::raw('COUNT(DISTINCT products.id) as product_count'),
                DB::raw('SUM(product_warehouse.quantity) as total_units'),
                DB::raw('SUM(product_warehouse.quantity * products.cost_price) as total_value'))
            ->groupBy('warehouses.id', 'warehouses.name')
            ->get();

        // Comprehensive Inventory Table: Opening, Purchase, Sold, Closing
        // This simulates a historical report.
        // Current Stock = From Product (Global) or ProductWarehouse (Specific)
        // Adjustments = Reverse movements from Now to EndDate? 
        // For simplicity in this iteration, we treat "Closing" as "Current" if EndDate is Today.
        // If not, we should use movements. 
        // Let's implement basic "Activities in Range" logic.
        
        $inventoryReport = Product::where('track_inventory', true)
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->whereHas('warehouses', function($w) use ($warehouseId) {
                    $w->where('warehouses.id', $warehouseId);
                });
            })
            ->select('products.id', 'products.name', 'products.sku', 'products.stock_quantity as current_stock')
            ->withCount(['orderItems as sold_qty' => function ($query) use ($startDate, $endDate) {
                $query->join('orders', 'order_items.order_id', '=', 'orders.id')
                      ->whereBetween('orders.created_at', [$startDate, $endDate]);
            }])
            ->paginate(50);
            
        // Note: For "Purchased" qty, we need PurchaseOrderItem relation (assumed hasMany through or similar)
        // Since Eloquent relation might not exist on Product model yet, we can attach it manually or use subquery.
        // Adding simplified subquery for Purchased:
        $inventoryReport->getCollection()->transform(function ($product) use ($startDate, $endDate) {
            $purchased = DB::table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->where('purchase_order_items.product_id', $product->id)
                ->where('purchase_orders.status', 'received')
                ->whereBetween('purchase_orders.received_date', [$startDate, $endDate])
                ->sum('purchase_order_items.quantity_received');

            $product->purchased_qty = $purchased;
            
            // Reverse Calc Opening: Opening = (Current + Sold - Purchased) [Assuming Current is End Date]
            // This is an approximation. Ideally we use stock movements.
            $product->opening_stock = $product->current_stock + $product->sold_qty - $product->purchased_qty;
            
            return $product;
        });

        // Fast Moving Products (Velocity) across all warehouses or specific
        $fastMoving = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
             ->when($warehouseId, function($q) {
                // If warehouse filter is on, we'd need to link order to warehouse (if possible).
                // Usually Orders are linked to location, but for 'basic' stores they might not be.
                // Ignoring warehouse filter for Sales Velocity as sales might not be strictly warehouse-bound unless split.
            })
            ->select('products.name', 'products.sku', 
                DB::raw('SUM(order_items.quantity) as sold_qty'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('sold_qty')
            ->limit(10)
            ->get();

        // Low Stock Products
        $lowStock = Product::where('track_inventory', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->when($warehouseId, function($q) use ($warehouseId) {
                 $q->whereHas('warehouses', function($w) use ($warehouseId) {
                    $w->where('warehouses.id', $warehouseId);
                });
            })
            ->with('warehouses')
            ->orderBy('stock_quantity')
            ->limit(20)
            ->get();

        // Stock Movement (Last 30 Days or Range)
        $stockMovements = StockTransfer::with(['product', 'fromWarehouse', 'toWarehouse'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(50)
            ->get();

        // Aggregate Stats (Filtered)
        $totalInventoryValue = DB::table('products')
             ->when($warehouseId, function($q) use ($warehouseId) {
                // Approximate value per warehouse strictly requires ProductWarehouse table sum
                // But global products table holds total stock.
                // Let's use product_warehouse table if filter is applied.
                 $q->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
                   ->where('product_warehouse.warehouse_id', $warehouseId);
            })
            ->where('track_inventory', true)
            ->selectRaw('SUM(' . ($warehouseId ? 'product_warehouse.quantity' : 'stock_quantity') . ' * cost_price) as total_value')
            ->value('total_value') ?? 0;

        $totalInventoryUnits = $warehouseId 
            ? DB::table('product_warehouse')->where('warehouse_id', $warehouseId)->sum('quantity')
            : Product::where('track_inventory', true)->sum('stock_quantity');
            
        // Warehouses list for filter
        $warehouses = \App\Models\Warehouse::all();

        return view('admin.reports.inventory', compact(
            'stockByWarehouse', 'lowStock', 'stockMovements',
            'totalInventoryValue', 'totalInventoryUnits', 
            'inventoryReport', 'fastMoving', 'warehouses',
            'startDate', 'endDate', 'warehouseId'
        ));
    }

    /**
     * Customer Analytics
     */
    public function customers(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(90)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Top Customers by Revenue
        $topCustomers = Customer::join('orders', 'customers.id', '=', 'orders.customer_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('customers.id', 'customers.name', 'customers.email',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('AVG(orders.total) as avg_order_value'))
            ->groupBy('customers.id', 'customers.name', 'customers.email')
            ->orderByDesc('total_spent')
            ->limit(20)
            ->get();

        // Customer Acquisition Trend
        $customerGrowth = Customer::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as new_customers')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Customer Segments
        $segments = [
            'new' => Customer::whereBetween('created_at', [now()->subDays(30), now()])->count(),
            'active' => Customer::whereHas('orders', function($q) {
                $q->whereBetween('created_at', [now()->subDays(90), now()]);
            })->count(),
            'inactive' => Customer::whereDoesntHave('orders', function($q) {
                $q->where('created_at', '>=', now()->subDays(90));
            })->whereHas('orders')->count(),
        ];

        // Purchase Frequency Distribution
        $frequencyDistribution = DB::table(DB::raw('(
            SELECT 
                customers.id,
                COUNT(orders.id) as order_count
            FROM customers
            LEFT JOIN orders ON customers.id = orders.customer_id
            GROUP BY customers.id
        ) as customer_orders'))
            ->select(DB::raw('
                CASE 
                    WHEN order_count = 0 THEN "0 orders"
                    WHEN order_count = 1 THEN "1 order"
                    WHEN order_count BETWEEN 2 AND 5 THEN "2-5 orders"
                    WHEN order_count BETWEEN 6 AND 10 THEN "6-10 orders"
                    ELSE "10+ orders"
                END as frequency_range,
                COUNT(*) as customer_count
            '))
            ->groupBy('frequency_range')
            ->get();

        return view('admin.reports.customers', compact(
            'topCustomers', 'customerGrowth', 'segments', 'frequencyDistribution',
            'startDate', 'endDate'
        ));
    }

    /**
     * Financial Summary
     */
    public function financial(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Revenue Breakdown
        $revenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total');

        // Cost of Goods Sold
        $cogs = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.payment_status', 'paid')
            ->selectRaw('SUM(order_items.quantity * products.cost_price) as total_cogs')
            ->value('total_cogs') ?? 0;

        // Gross Profit
        $grossProfit = $revenue - $cogs;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        // Payment Status Summary
        $paymentStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_status', 
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as amount'))
            ->groupBy('payment_status')
            ->get();

        // Profit by Product
        $productProfits = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.payment_status', 'paid')
            ->select('products.name', 'products.sku',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue'),
                DB::raw('SUM(order_items.quantity * products.cost_price) as cost'),
                DB::raw('SUM(order_items.quantity * (order_items.price - products.cost_price)) as profit'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('profit')
            ->limit(20)
            ->get();

        return view('admin.reports.financial', compact(
            'revenue', 'cogs', 'grossProfit', 'grossMargin',
            'paymentStatus', 'productProfits', 'startDate', 'endDate'
        ));
    }

    /**
     * Export Report to CSV
     */
    public function export(Request $request)
    {
        $type = $request->get('type');
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $filename = "{$type}_report_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'sales':
                    fputcsv($file, ['Date', 'Orders', 'Revenue', 'Avg Order Value']);
                    $data = Order::whereBetween('created_at', [$startDate, $endDate])
                        ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue, AVG(total) as avg')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();
                    foreach ($data as $row) {
                        fputcsv($file, [$row->date, $row->orders, $row->revenue, $row->avg]);
                    }
                    break;

                case 'inventory':
                    fputcsv($file, ['Product', 'SKU', 'Stock', 'Cost Price', 'Total Value']);
                    $data = Product::where('track_inventory', true)
                        ->select('name', 'sku', 'stock_quantity', 'cost_price',
                            DB::raw('stock_quantity * cost_price as value'))
                        ->get();
                    foreach ($data as $row) {
                        fputcsv($file, [$row->name, $row->sku, $row->stock_quantity, $row->cost_price, $row->value]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
