<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductComboController extends Controller
{
    /**
     * Add a product to a combo (attach child to parent)
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'child_sku' => 'required|exists:products,sku',
            'quantity' => 'required|integer|min:1',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $childProduct = Product::where('sku', $request->child_sku)->first();

        if ($childProduct->id === $product->id) {
            return back()->withErrors(['child_sku' => 'Cannot add a product as its own combo item.']);
        }

        // Check if already exists
        if ($product->combos()->where('child_product_id', $childProduct->id)->exists()) {
             return back()->withErrors(['child_sku' => 'This product is already in the combo list.']);
        }

        $product->combos()->attach($childProduct->id, [
            'quantity' => $request->quantity,
            'discount_amount' => $request->discount_amount
        ]);

        return back()->with('success', 'Product added to combo successfully!');
    }

    /**
     * Remove a product from a combo
     */
    public function destroy(Product $product, Product $childProduct)
    {
        $product->combos()->detach($childProduct->id);

        return back()->with('success', 'Product removed from combo successfully!');
    }
}
