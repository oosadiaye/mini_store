<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class StoreCollection extends Category
{
    use \App\Traits\BelongsToTenant;

    protected $table = 'categories';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('visible_online', function (Builder $builder) {
            $builder->where('is_visible_online', true)
                    ->where('is_active', true);
        });
    }

    /**
     * Get products available for the storefront in this collection.
     */
    public function storefrontProducts()
    {
        return $this->hasMany(StorefrontProduct::class, 'category_id');
    }
}
