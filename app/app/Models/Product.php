<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * Property: The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'price',
    ];

    /**
     * Relationship: The orders items for the product
     */
    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the ID code of the product
     */
    public function idCode()
    {
        return '#'.str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
