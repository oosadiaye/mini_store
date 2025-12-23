<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShipping extends Model
{
    use HasFactory;

    protected $table = 'order_shipping';

    protected $fillable = [
        'order_id',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'tracking_number',
        'carrier',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
