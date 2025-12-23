<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed getCart()
 * @method static int count()
 * @method static mixed items()
 * @method static string total()
 * @method static mixed add($product, $quantity = 1)
 * @method static void clear()
 * 
 * @see \App\Services\CartService
 */
class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
