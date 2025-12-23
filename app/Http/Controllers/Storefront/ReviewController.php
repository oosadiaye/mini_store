<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Optional: Check if user already reviewed? 
        // For now, allow multiple reviews or handle via unique constraint logic if needed.

        // Default approval? Maybe manual approval is safer.
        // Assuming 'is_approved' defaults to false in DB migration.
        
        $review = Review::create([
            'product_id' => $product->id,
            'customer_id' => Auth::guard('customer')->id(),
            'name' => Auth::guard('customer')->user()->name, // Store name for snapshot
            'rating' => $validated['rating'],
            'body' => $validated['comment'], // Map comment to body
            'status' => 'pending', // Default to pending
        ]);

        return back()->with('success', 'Thank you for your review! It has been submitted for approval.');
    }
}
