<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransfer::with(['product', 'fromWarehouse', 'toWarehouse', 'requestedBy', 'approvedBy'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by warehouse
        if ($request->filled('warehouse_id')) {
            $query->where(function($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        $transfers = $query->paginate(20);
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('admin.stock-transfers.index', compact('transfers', 'warehouses'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('track_inventory', true)
            ->with('warehouses')
            ->orderBy('name')
            ->get();
            
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('admin.stock-transfers.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if source warehouse has enough stock
        $currentStock = \DB::table('product_warehouse')
            ->where('product_id', $validated['product_id'])
            ->where('warehouse_id', $validated['from_warehouse_id'])
            ->value('quantity');

        if (!$currentStock || $currentStock < $validated['quantity']) {
            return back()->withErrors([
                'quantity' => 'Insufficient stock in source warehouse. Available: ' . ($currentStock ?? 0)
            ])->withInput();
        }

        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'pending';

        StockTransfer::create($validated);

        return redirect()->route('admin.stock-transfers.index')
            ->with('success', 'Stock transfer request created successfully!');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['product', 'fromWarehouse', 'toWarehouse', 'requestedBy', 'approvedBy']);
        
        return view('admin.stock-transfers.show', compact('stockTransfer'));
    }

    public function approve(StockTransfer $stockTransfer)
    {
        try {
            $stockTransfer->approve(Auth::id());
            
            return back()->with('success', 'Transfer approved and stock updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(StockTransfer $stockTransfer)
    {
        try {
            $stockTransfer->reject(Auth::id());
            
            return back()->with('success', 'Transfer rejected successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status === 'completed') {
            return back()->with('error', 'Cannot delete completed transfers');
        }

        $stockTransfer->delete();

        return redirect()->route('admin.stock-transfers.index')
            ->with('success', 'Transfer deleted successfully!');
    }

    /**
     * Get available stock for a product in a warehouse (AJAX)
     */
    public function getStock(Request $request)
    {
        $stock = \DB::table('product_warehouse')
            ->where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->value('quantity');

        return response()->json(['stock' => $stock ?? 0]);
    }
}
