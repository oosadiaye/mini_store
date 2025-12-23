<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'sku' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'attributes' => 'nullable|array',
            'attributes.*.key' => 'required_with:attributes|string',
            'attributes.*.value' => 'required_with:attributes|string',
        ]);

        // Transform attributes array to key-value pair for storage
        $storedAttributes = [];
        if (!empty($validated['attributes'])) {
            foreach ($validated['attributes'] as $attr) {
                if (!empty($attr['key']) && !empty($attr['value'])) {
                    $storedAttributes[$attr['key']] = $attr['value'];
                }
            }
        }
        
        $validated['attributes'] = $storedAttributes;
        $validated['product_id'] = $product->id;

        ProductVariant::create($validated);

        return back()->with('success', 'Variant added successfully!');
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'attributes' => 'nullable|array',
            'attributes.*.key' => 'required_with:attributes|string',
            'attributes.*.value' => 'required_with:attributes|string',
        ]);

         // Transform attributes array to key-value pair for storage
         $storedAttributes = [];
         if (!empty($validated['attributes'])) {
             foreach ($validated['attributes'] as $attr) {
                 if (!empty($attr['key']) && !empty($attr['value'])) {
                     $storedAttributes[$attr['key']] = $attr['value'];
                 }
             }
         }
         
         $validated['attributes'] = $storedAttributes;
 
         $variant->update($validated);
 
         return back()->with('success', 'Variant updated successfully!');
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Variant deleted successfully!');
    }
}
