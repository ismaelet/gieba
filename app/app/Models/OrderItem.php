<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    private const VAT = 0.22;

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price_net',
        'unit_price_gross',
        'total_price_net',
        'total_price_gross',
        'total_vat',
        'status',
        'has_vat',
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
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: The product that belongs to the order item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the unit net price of the order item from total price and quantity
     */
    public function calcUnitPriceNet(): float
    {
        if ($this->has_vat) {
            return $this->total_price_gross / (1 + self::VAT) / $this->quantity;
        } else {
            return $this->total_price_gross / $this->quantity;
        }
    }

    /**
     * Get the unit gross price of the order item from total price and quantity
     */
    public function calcUnitPriceGross(): float
    {
        return $this->total_price_gross / $this->quantity;
    }

    /**
     * Get the VAT amount for the order item
     */
    public function calcTotalVat(): float
    {
        return $this->has_vat ? round($this->total_price_gross - $this->total_price_net, 2) : 0;
    }

    /**
     * Get the total net price of the order item
     */
    public function calcTotalPriceNet(): float
    {
        if ($this->has_vat) {
            return $this->unit_price_net * $this->quantity;
        } else {
            return $this->total_price_gross;
        }
    }

    /**
     * Recalculate and update the unit prices based on total price and quantity
     */
    public function recalculatePrices(): void
    {
        $this->unit_price_net = $this->calcUnitPriceNet();
        $this->unit_price_gross = $this->calcUnitPriceGross();
        $this->total_price_net = $this->calcTotalPriceNet();
        $this->total_vat = $this->calcTotalVat();
        $this->save();
    }

    /**
     * Get the status name of the order
     */
    public function statusName(): string
    {
        return config('ecommerce.order_statuses')[$this->status]['label'] ?? 'Unknown';
    }
}
