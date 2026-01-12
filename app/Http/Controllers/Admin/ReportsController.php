<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\StockTransfer;
use App\Models\StockMovement;
use App\Models\Category;
use App\Models\Warehouse;
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
        $warehouseId = $request->get('warehouse_id');
        $categoryId = $request->get('category_id');
        $customerId = $request->get('customer_id');

        // Base Query
        $orderQuery = Order::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($channel) {
            $orderQuery->where('order_source', $channel);
        }
        
        if ($warehouseId) {
            $orderQuery->where('warehouse_id', $warehouseId);
        }
        
        if ($customerId) {
            $orderQuery->where('customer_id', $customerId);
        }
        
        if ($categoryId) {
            $orderQuery->whereHas('items.product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Sales Overview
        $salesData = (clone $orderQuery)
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
            ->when($warehouseId, function($q) use ($warehouseId) {
                return $q->where('orders.warehouse_id', $warehouseId);
            })
            ->when($customerId, function($q) use ($customerId) {
                return $q->where('orders.customer_id', $customerId);
            })
            ->when($categoryId, function($q) use ($categoryId) {
                return $q->where('products.category_id', $categoryId);
            })
            ->select('products.name', 'products.sku', 
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as total_revenue'))
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
            ->when($warehouseId, function($q) use ($warehouseId) {
                return $q->where('orders.warehouse_id', $warehouseId);
            })
            ->when($customerId, function($q) use ($customerId) {
                return $q->where('orders.customer_id', $customerId);
            })
            ->select('categories.name', 
                DB::raw('SUM(order_items.total) as revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        // Sales by Payment Method
        $paymentMethods = (clone $orderQuery)
            ->select('payment_method as name', 
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        // Summary Stats
        $totalOrders = (clone $orderQuery)->count();
        $totalRevenue = (clone $orderQuery)->sum('total');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Sales Channel Breakdown
        $salesByChannel = (clone $orderQuery)
            ->select('order_source', 
                DB::raw('COUNT(*) as count'), 
                DB::raw('SUM(total) as revenue'))
            ->groupBy('order_source')
            ->get();
            
        $warehouses = Warehouse::where('is_active', true)->get();
        $categories = Category::active()->get();
        $customers = Customer::orderBy('name')->get();
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
            
        $data = compact(
            'salesData', 'topProducts', 'categoryRevenue', 'paymentMethods',
            'totalOrders', 'totalRevenue', 'averageOrderValue',
            'salesByChannel', 'startDate', 'endDate', 'channel',
            'warehouses', 'categories', 'customers',
            'warehouseId', 'categoryId', 'customerId', 'newCustomers'
        );

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('admin.reports.sales', $data);
    }

    /**
     * Inventory Movement Dashboard
     */
    public function movement(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $warehouseId = $request->get('warehouse_id');
        $categoryId = $request->get('category_id');
        $productId = $request->get('product_id');
        $type = $request->get('type');
        $customerId = $request->get('customer_id');

        $query = StockMovement::with(['product', 'warehouse', 'user'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($categoryId) {
            $query->whereHas('product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($customerId) {
            $query->where(function($q) use ($customerId) {
                $q->where(function($sq) use ($customerId) {
                    $sq->where('reference_type', 'order')
                        ->whereIn('reference_id', function($sub) use ($customerId) {
                            $sub->select('id')->from('orders')->where('customer_id', $customerId);
                        });
                })->orWhere(function($sq) use ($customerId) {
                     $sq->where('reference_type', 'order_return')
                        ->whereIn('reference_id', function($sub) use ($customerId) {
                            $sub->select('id')->from('order_returns')
                                ->whereIn('order_id', function($o) use ($customerId) {
                                    $o->select('id')->from('orders')->where('customer_id', $customerId);
                                });
                        });
                });
            });
        }

        $movements = $query->latest()->paginate(50);

        // Stats for Dashboard
        $stats = [
            'total_in' => (clone $query)->where('quantity', '>', 0)->sum('quantity'),
            'total_out' => abs((clone $query)->where('quantity', '<', 0)->sum('quantity')),
            'by_type' => (clone $query)->select('type', DB::raw('SUM(ABS(quantity)) as total'))
                ->groupBy('type')->get()
        ];

        $warehouses = Warehouse::where('is_active', true)->get();
        $categories = Category::active()->get();
        $products = Product::active()->orderBy('name')->get();
        $customers = \App\Models\Customer::orderBy('name')->get();

        $data = compact(
            'movements', 'stats', 'warehouses', 'categories', 'products', 'customers',
            'startDate', 'endDate', 'warehouseId', 'categoryId', 'productId', 'type', 'customerId'
        );

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('admin.reports.movement', $data);
    }

    /**
     * Inventory Reports
     */
    public function inventory(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $warehouseId = $request->get('warehouse_id');
        $categoryId = $request->get('category_id');

        // Stock Levels by Warehouse (Valuation)
        $stockByWarehouse = DB::table('product_warehouse')
            ->join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->join('warehouses', 'product_warehouse.warehouse_id', '=', 'warehouses.id')
            ->when($warehouseId, function($q) use ($warehouseId) {
                return $q->where('product_warehouse.warehouse_id', $warehouseId);
            })
            ->when($categoryId, function($q) use ($categoryId) {
                return $q->where('products.category_id', $categoryId);
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
            ->when($categoryId, function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->select('products.id', 'products.name', 'products.sku', 'products.stock_quantity as current_stock')
            ->paginate(50);
            
        $inventoryReport->getCollection()->transform(function ($product) use ($startDate, $endDate, $warehouseId) {
            $movementsQuery = StockMovement::where('product_id', $product->id);
            if ($warehouseId) {
                $movementsQuery->where('warehouse_id', $warehouseId);
            }

            $movementsAfterStart = (clone $movementsQuery)
                ->where('created_at', '>=', $startDate . ' 00:00:00')
                ->sum('quantity');

            $rangeQuery = (clone $movementsQuery)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
            $product->purchased_qty = (clone $rangeQuery)->where('type', 'purchase')->sum('quantity');
            $product->sold_qty = abs((clone $rangeQuery)->where('type', 'sale')->sum('quantity'));
            $product->adjustment_qty = (clone $rangeQuery)->where('type', 'adjustment')->sum('quantity');
            $product->transfer_qty = (clone $rangeQuery)->where('type', 'transfer')->sum('quantity');
            $product->return_qty = (clone $rangeQuery)->where('type', 'return')->sum('quantity');

            $product_current_stock = $product->current_stock;
            if ($warehouseId) {
                $product_current_stock = DB::table('product_warehouse')
                    ->where('product_id', $product->id)
                    ->where('warehouse_id', $warehouseId)
                    ->value('quantity') ?? 0;
            }

            $product->opening_stock = $product_current_stock - (int)$movementsAfterStart;
            $product->closing_stock = $product->opening_stock + (int)(clone $rangeQuery)->sum('quantity');
            
            return $product;
        });

        // Fast Moving Products (Velocity)
        $fastMoving = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                return $q->where('orders.warehouse_id', $warehouseId);
            })
            ->when($categoryId, function($q) use ($categoryId) {
                return $q->where('products.category_id', $categoryId);
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
            ->when($categoryId, function($q) use ($categoryId) {
                return $q->where('category_id', $categoryId);
            })
            ->when($warehouseId, function($q) use ($warehouseId) {
                 $q->whereHas('warehouses', function($w) use ($warehouseId) {
                    $w->where('warehouses.id', $warehouseId);
                });
            })
            ->orderBy('stock_quantity')
            ->limit(20)
            ->get();

        // Recent Stock Movements (Universal)
        $stockMovements = StockMovement::with(['product', 'warehouse'])
            ->when($warehouseId, function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            })
            ->latest()
            ->limit(10)
            ->get();

        // Aggregate Stats (Universal)
        $totalInventoryValue = $stockByWarehouse->sum('total_value');
        $totalInventoryUnits = $stockByWarehouse->sum('total_units');
            
        $warehouses = Warehouse::where('is_active', true)->get();
        $categories = Category::active()->get();

        $data = compact(
            'inventoryReport', 'stockByWarehouse', 'fastMoving', 'lowStock', 'stockMovements',
            'startDate', 'endDate', 'warehouseId', 'categoryId',
            'totalInventoryValue', 'totalInventoryUnits', 'warehouses', 'categories'
        );

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('admin.reports.inventory', $data);
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

        $data = compact(
            'topCustomers', 'customerGrowth', 'segments', 'frequencyDistribution',
            'startDate', 'endDate'
        );

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('admin.reports.customers', $data);
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

        $data = compact(
            'revenue', 'cogs', 'grossProfit', 'grossMargin',
            'paymentStatus', 'productProfits', 'startDate', 'endDate'
        );

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('admin.reports.financial', $data);
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

        $callback = function() use ($type, $startDate, $endDate, $request) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'sales':
                    fputcsv($file, ['Date', 'Orders', 'Revenue', 'Avg Order Value']);
                    $query = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    
                    if ($request->filled('warehouse_id')) $query->where('warehouse_id', $request->warehouse_id);
                    if ($request->filled('channel')) $query->where('order_source', $request->channel);
                    if ($request->filled('customer_id')) $query->where('customer_id', $request->customer_id);
                    if ($request->filled('category_id')) {
                        $query->whereHas('items.product', function($q) use ($request) {
                            $q->where('category_id', $request->category_id);
                        });
                    }
                    
                    $data = $query->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue, AVG(total) as avg')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();
                    foreach ($data as $row) {
                        fputcsv($file, [$row->date, $row->orders, $row->revenue, $row->avg]);
                    }
                    break;

                case 'inventory':
                    fputcsv($file, ['Product', 'SKU', 'Opening', 'Purchased', 'Sold', 'Adjustments', 'Closing']);
                    $warehouseId = $request->get('warehouse_id');
                    $categoryId = $request->get('category_id');

                    $products = Product::where('track_inventory', true)
                        ->when($warehouseId, function($q) use ($warehouseId) {
                            $q->whereHas('warehouses', function($w) use ($warehouseId) {
                                $w->where('warehouses.id', $warehouseId);
                            });
                        })
                        ->when($categoryId, function($q) use ($categoryId) {
                            $q->where('category_id', $categoryId);
                        })
                        ->get();

                    foreach ($products as $product) {
                        $movementsQuery = StockMovement::where('product_id', $product->id);
                        if ($warehouseId) { $movementsQuery->where('warehouse_id', $warehouseId); }

                        $movementsAfterStart = (clone $movementsQuery)->where('created_at', '>=', $startDate . ' 00:00:00')->sum('quantity');
                        $rangeQuery = (clone $movementsQuery)->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                        
                        $purchased = (clone $rangeQuery)->where('type', 'purchase')->sum('quantity');
                        $sold = abs((clone $rangeQuery)->where('type', 'sale')->sum('quantity'));
                        $others = (clone $rangeQuery)->whereIn('type', ['adjustment', 'transfer', 'return'])->sum('quantity');

                        $current = $product->stock_quantity;
                        if ($warehouseId) {
                            $current = DB::table('product_warehouse')->where('product_id', $product->id)->where('warehouse_id', $warehouseId)->value('quantity') ?? 0;
                        }

                        $opening = $current - $movementsAfterStart;
                        $closing = $opening + (clone $rangeQuery)->sum('quantity');

                        fputcsv($file, [$product->name, $product->sku, $opening, $purchased, $sold, $others, $closing]);
                    }
                    break;

                case 'movement':
                    fputcsv($file, ['Date', 'Product', 'Warehouse', 'Type', 'Quantity', 'Balance After', 'Reference']);
                    $query = \App\Models\StockMovement::with(['product', 'warehouse'])
                        ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->latest();

                    if ($request->filled('warehouse_id')) $query->where('warehouse_id', $request->warehouse_id);
                    if ($request->filled('type')) $query->where('type', $request->type);
                    if ($request->filled('category_id')) {
                        $query->whereHas('product', function($q) use ($request) {
                            $q->where('category_id', $request->category_id);
                        });
                    }
                    if ($request->filled('product_id')) $query->where('product_id', $request->product_id);
                if ($request->filled('customer_id')) {
                    $customerId = $request->customer_id;
                    $query->where(function($q) use ($customerId) {
                        $q->where(function($sq) use ($customerId) {
                            $sq->where('reference_type', 'order')
                                ->whereIn('reference_id', function($sub) use ($customerId) {
                                    $sub->select('id')->from('orders')->where('customer_id', $customerId);
                                });
                        })->orWhere(function($sq) use ($customerId) {
                            $sq->where('reference_type', 'order_return')
                                ->whereIn('reference_id', function($sub) use ($customerId) {
                                    $sub->select('id')->from('order_returns')
                                        ->whereIn('order_id', function($o) use ($customerId) {
                                            $o->select('id')->from('orders')->where('customer_id', $customerId);
                                        });
                                });
                        });
                    });
                }

                    foreach ($query->cursor() as $row) {
                        fputcsv($file, [
                            $row->created_at->format('Y-m-d H:i'),
                            $row->product->name ?? 'N/A',
                            $row->warehouse->name ?? 'N/A',
                            ucfirst($row->type),
                            $row->quantity,
                            $row->balance_after,
                            $row->reference_type ? ucfirst($row->reference_type) . ' #' . $row->reference_id : 'Adjustment'
                        ]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
