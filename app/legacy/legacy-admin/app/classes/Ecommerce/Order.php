<?php

namespace Ecommerce;

use Structure\DatabaseObject;

class Order
{
    use DatabaseObject;

    public const TABLE = 'ecommerce_orders';

    public const INDEX = 'id';

    public const PRODUCT_INDEX = 'productId';

    public $id;

    public $productId;

    public $cartId;

    public $info;

    public $quantity;

    public $netProductPrice;

    public $discount;

    public $status;

    public $hasVat;

    /*
    * Get methods
    */
    private function getIdCode()
    {
        return '#'.str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    private function getName()
    {
        return $this->quantity > 1 ? $this->quantity.' x '.$this->product->name : $this->product->name;
    }

    private function getDescription()
    {
        return $this->info;
    }

    private function getStatusString()
    {
        return ecommerce_config('cart-statuses')[$this->status][1];
    }

    private function getProductPrice()
    {
        return $this->hasVat ? $this->netProductPrice * 1.22 : $this->netProductPrice;
    }

    private function getFormattedProductPrice()
    {
        return format($this->productPrice);
    }

    private function getProductPriceString()
    {
        return format_price($this->productPrice);
    }

    private function getFormattedNetProductPrice()
    {
        return format($this->netProductPrice);
    }

    private function getNetProductPriceString()
    {
        return format_price($this->netProductPrice);
    }

    private function getPrice()
    {
        return $this->productPrice * $this->quantity;
    }

    private function getFormattedPrice()
    {
        return format($this->price);
    }

    private function getPriceString()
    {
        return format_price($this->price);
    }

    private function getNetPrice()
    {
        return $this->netProductPrice * $this->quantity;
    }

    private function getNetPriceString()
    {
        return format_price($this->netPrice);
    }

    private function getProductVat()
    {
        return $this->hasVat ? $this->netProductPrice * .22 : 0;
    }

    private function getProductVatString()
    {
        return format_price($this->productVat);
    }

    private function getVat()
    {
        return $this->hasVat ? $this->netPrice * .22 : 0;
    }

    private function getProduct()
    {
        return new Product($this->productId);
    }

    private function getCart()
    {
        return new Cart($this->cartId);
    }
}
