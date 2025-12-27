<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use App\Traits\BelongsToTenant;

class StockTransfer extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'quantity',
        'status',
        'notes',
        'requested_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'quantity' => 'integer',
    ];

    /**
     * Get the product being transferred
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the source warehouse
     */
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * Get the destination warehouse
     */
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * Get the user who requested the transfer
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved the transfer
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Approve the transfer and update stock
     */
    public function approve($userId)
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending transfers can be approved');
        }

        \DB::transaction(function () use ($userId) {
            // Get current stock in source warehouse
            $fromStock = \DB::table('product_warehouse')
                ->where('product_id', $this->product_id)
                ->where('warehouse_id', $this->from_warehouse_id)
                ->first();

            if (!$fromStock || $fromStock->quantity < $this->quantity) {
                throw new \Exception('Insufficient stock in source warehouse');
            }

            // Decrease stock in source warehouse
            \DB::table('product_warehouse')
                ->where('product_id', $this->product_id)
                ->where('warehouse_id', $this->from_warehouse_id)
                ->decrement('quantity', $this->quantity);

            // Increase stock in destination warehouse (or create record)
            \DB::table('product_warehouse')
                ->updateOrInsert(
                    [
                        'product_id' => $this->product_id,
                        'warehouse_id' => $this->to_warehouse_id,
                    ],
                    [
                        'quantity' => \DB::raw("quantity + {$this->quantity}"),
                        'updated_at' => now(),
                    ]
                );

            // Update transfer status
            $this->update([
                'status' => 'completed',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * Reject the transfer
     */
    public function reject($userId)
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending transfers can be rejected');
        }

        $this->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Scope for pending transfers
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed transfers
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
