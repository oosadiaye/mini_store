<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected $cart;

    public function __construct()
    {
        // defer loading until needed to allow session to start
    }

    public function getCart()
    {
        if ($this->cart) {
            return $this->cart;
        }

        $sessionId = Session::getId();
        $this->cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        
        return $this->cart;
    }

    public function count()
    {
        return $this->getCart()->total_items;
    }

    public function items()
    {
        return $this->getCart()->items()->with('product')->get();
    }

    public function total()
    {
        return number_format($this->getCart()->total, 2);
    }

    public function add($product, $quantity = 1)
    {
        $cart = $this->getCart();
        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price, // simplified price logic
            ]);
        }

        return $cart;
    }

    public function clear()
    {
        $this->getCart()->items()->delete();
    }
}
