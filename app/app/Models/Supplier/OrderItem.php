<?php

namespace App\Models\Supplier;

use App\Models\Product;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /**
     * Property: The table associated with the model
     */
    protected $table = 'supplier_order_items';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'supplier_order_id',
        'product_id',
        'quantity',
        'status',
        'info',
    ];

    /**
     * Attribute: Name of the order item (product name)
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->product ? $this->product->name : 'N/A';
            },
        );
    }

    /**
     * Relationship: The order that owns the order item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'supplier_order_id');
    }

    /**
     * Relationship: The product that belongs to the order item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the status name of the order
     */
    public function statusName(): string
    {
        return config('ecommerce.supplier_order_statuses')[$this->status]['label'] ?? 'Unknown';
    }
}
