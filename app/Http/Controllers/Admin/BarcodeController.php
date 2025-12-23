<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index()
    {
        $products = Product::active()->latest()->paginate(20);
        return view('admin.barcodes.index', compact('products'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        // Filter out empty quantities if any
        $items = collect($request->items)->filter(fn($item) => isset($item['quantity']) && $item['quantity'] > 0);

        if ($items->isEmpty()) {
            return back()->with('error', 'No products selected.');
        }

        // Fetch product details
        $products = Product::whereIn('id', $items->pluck('id'))->get()->keyBy('id');

        $printQueue = [];
        foreach ($items as $item) {
            if ($product = $products->get($item['id'])) {
                $printQueue[] = [
                    'product' => $product,
                    'quantity' => $item['quantity']
                ];
            }
        }

        return view('admin.barcodes.print', compact('printQueue'));
    }
}
