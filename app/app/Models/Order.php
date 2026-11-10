<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    private const VAT = 0.22;

    /**
     * Property: The attributes that are mass assignable
     */
    protected $fillable = [
        'customer_id',
        'price_net',
        'price_gross',
        'vat',
        'total_discount',
        'quantity',
        'date',
        'down_payment',
        'down_payment_with_vat',
        'fixed_discount',
        'discount_with_vat',
        'percentage_discount',
        'billed',
        'print_descriptions',
        'print_status',
        'print_total',
        'print_notes',
        'always_print_prices',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Relationship: The order items for the order
     */
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class)
            ->orderBy('status');
    }

    /**
     * Relationship: The order items for the order, ordered by status and product name
     */
    public function ordered_order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class)
            ->select('order_items.*')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->orderBy('order_items.status')
            ->orderBy('products.name')
            ->orderBy('order_items.quantity')
            ->orderBy('order_items.created_at');
    }

    /**
     * Relationship: Get the customer that owns the order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the ID code of the product
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
        return config('ecommerce.order_statuses')[$this->status]['label'] ?? 'Unknown';
    }

    /**
     * Get the total quantity of the order's items
     */
    public function calcQuantity(): int
    {
        return $this->order_items()->sum('quantity');
    }

    /**
     * Calculate the total price of the order after discounts
     */
    public function calcPriceGross()
    {
        return $this->calcPriceNet() + $this->calcVat() - $this->calcTotalDiscount() - $this->calcTotalDownPayment();
    }

    /**
     * Calculate the total price of the order
     */
    public function calcPriceNet()
    {
        return $this->order_items()->sum('total_price_net');
    }

    /**
     * Calculate the total discount applied to the order
     */
    public function calcTotalDiscount()
    {
        return $this->fixed_discount + $this->calcPercentageDiscount();
    }

    /**
     * Calculate the percentage discount amount
     */
    public function calcPercentageDiscount(): float
    {
        return round($this->percentage_discount * $this->calcPriceNet() / 100, 2);
    }

    /**
     * Calculate the VAT amount for the order
     */
    public function calcVat(): float
    {
        return $this->order_items()->sum('total_vat');
    }

    /**
     * Calculate the down payment amount with VAT if applicable
     */
    public function calcTotalDownPayment()
    {
        return $this->down_payment_with_vat ? $this->down_payment : $this->down_payment * (1 + self::VAT);
    }

    /**
     * Recalculate and update the order's total quantity and price
     */
    public function recalculateTotals(): void
    {
        $this->quantity = $this->calcQuantity();
        $this->price_net = $this->calcPriceNet();

        $this->price_gross = $this->calcPriceGross();
        $this->vat = $this->calcVat();
        $this->total_discount = $this->calcTotalDiscount();
        $this->save();
    }
}
