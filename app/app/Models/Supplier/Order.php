<?php

namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * Property: The table associated with the model
     */
    protected $table = 'supplier_orders';

    /**
     * Property: The attributes that are mass assignable
     */
    protected $fillable = [
        'supplier_id',
        'status',
    ];

    /**
     * Relationship: The order items for the order
     */
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'supplier_order_id')
            ->select('supplier_order_items.*')
            ->join('products', 'supplier_order_items.product_id', '=', 'products.id')
            ->orderBy('status')
            ->orderBy('products.name');
    }

    /**
     * Relationship: Get the supplier that owns the order
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the ID code of the order
     */
    public function idCode()
    {
        return '#'.str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the status name of the order
     */
    public function statusName(): string
    {
        return config('ecommerce.supplier_order_statuses')[$this->status]['label'] ?? 'Unknown';
    }

    /**
     * Get the total quantity of the order's items
     */
    public function calcQuantity(): int
    {
        return $this->order_items()->sum('quantity');
    }

    /**
     * Recalculate and update the order's total quantity and price
     */
    public function recalculateTotals(): void
    {
        $this->quantity = $this->calcQuantity();
        $this->save();
    }
}
