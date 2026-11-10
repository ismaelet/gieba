<?php

namespace Ecommerce;

use Legacy\System\Classes\DB;
use Structure\DatabaseObject;

/**
 * Cart class
 *
 * @property float downPayment
 * @property float formattedDownPayment
 * @property float discount
 * @property float formattedDiscount
 * @property float fixedDiscount
 * @property float formattedFixedDiscount
 * @property int percentageDiscount
 */
class Cart
{
    use DatabaseObject;

    public const TABLE = 'ecommerce_carts';

    public const INDEX = 'id';

    public const STATUS_INDEX = 'status';

    public $id;

    public $customerId;

    public $status;

    public $billed;

    public $date;

    public $time;

    /*
    * Get methods
    */
    private function getIdCode()
    {
        return '#'.str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    private function getDateString()
    {
        return date('Y-m-d', strtotime($this->time));
    }

    private function getStatusId()
    {
        return $this->{self::STATUS_INDEX};
    }

    private function getStatusCode()
    {
        return ecommerce_config('cart-statuses')[$this->statusId][0];
    }

    private function getStatusString()
    {
        return ecommerce_config('cart-statuses')[$this->statusId][1] ?? 'Non definito';
    }

    private function getStatusColor()
    {
        return ecommerce_config('cart-statuses')[$this->statusId][2];
    }

    private function getStatusIcon()
    {
        return ecommerce_config('cart-statuses')[$this->statusId][3];
    }

    private function getFullPrice()
    {
        $price = 0;

        foreach ($this->orders as $order) {
            $price += $order->price;
        }

        return $price;
    }

    private function getPrice()
    {
        return $this->fullPrice - $this->fullDownPayment - $this->fullDiscount;
    }

    private function getFullDownPayment()
    {
        return $this->downPaymentWithVat ? $this->downPayment : $this->downPayment * 1.22;
    }

    private function getFormattedFullDownPayment()
    {
        return format($this->fullDownPayment);
    }

    private function getFullDiscount()
    {
        return $this->discountWithVat ? $this->discount : $this->discount * 1.22;
    }

    private function getFormattedFullDiscount()
    {
        return format($this->fullDiscount);
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
        $price = 0;

        foreach ($this->orders as $order) {
            $price += $order->netPrice;
        }

        return $price;
    }

    private function getFormattedNetPrice()
    {
        return format($this->netPrice);
    }

    private function getnetPriceString()
    {
        return format_price($this->netPrice);
    }

    private function getVat()
    {
        $vat = 0;

        foreach ($this->orders as $order) {
            $vat += $order->vat;
        }

        return $vat;
    }

    private function getFormattedVat()
    {
        return format($this->vat);
    }

    private function getVatString()
    {
        return format_price($this->vat);
    }

    private function getFormattedDownPayment()
    {
        return format($this->downPayment);
    }

    private function getDownPaymentString()
    {
        return format_price($this->downPayment);
    }

    private function getDiscount()
    {
        return round($this->fixedDiscount + $this->fullPrice * ($this->percentageDiscount / 100), 2);
    }

    private function getFormattedDiscount()
    {
        return format($this->discount);
    }

    private function getDiscountString()
    {
        return format_price($this->discount);
    }

    private function getFormattedFixedDiscount()
    {
        return format($this->fixedDiscount);
    }

    private function getOrders()
    {
        return rows_to_objects(DB::select('ecommerce_orders.id AS id FROM ecommerce_orders LEFT JOIN ecommerce_products ON ecommerce_orders.productId = ecommerce_products.id WHERE cartId = ? ORDER BY status DESC, name ASC, ecommerce_orders.id ASC', [$this->id]), Order::class);
    }

    private function getOrdersSortedById()
    {
        return rows_to_objects(DB::select('ecommerce_orders.id AS id FROM ecommerce_orders JOIN ecommerce_products ON ecommerce_orders.productId = ecommerce_products.id WHERE cartId = ? ORDER BY ecommerce_products.id ASC, status DESC, ecommerce_orders.id ASC, name ASC', [$this->id]), Order::class);
    }

    private function getQuantity()
    {
        return (int) DB::sum('quantity FROM ecommerce_orders WHERE cartId = ?', [$this->id]);
    }

    private function getCustomer()
    {
        return new Customer($this->customerId);
    }

    /*
    * Update methods
    */
    public function updateBilled($billed = false)
    {
        $billed = $billed ? 1 : 0;

        DB::update(self::TABLE.' SET billed = ? WHERE '.self::INDEX.' = ?', [$billed, $this->id]);

        $this->billed = $billed;

        return $this;
    }

    /*
    * Istance methods
    */
    public function duplicate()
    {
        DB::execute('CREATE TEMPORARY TABLE temp_table SELECT * FROM ecommerce_carts WHERE id = ?', [$this->id]);
        DB::update('temp_table SET id = NULL, date = NOW(), billed = 0');
        $newCartId = DB::add('ecommerce_carts SELECT * FROM temp_table');
        DB::execute('DROP TEMPORARY TABLE IF EXISTS temp_table');

        foreach ($this->orders as $order) {
            DB::execute('CREATE TEMPORARY TABLE temp_table SELECT * FROM ecommerce_orders WHERE id = ?', [$order->id]);
            DB::update('temp_table SET id = NULL, cartId = ?', [$newCartId]);
            DB::add('ecommerce_orders SELECT * FROM temp_table');
            DB::execute('DROP TEMPORARY TABLE IF EXISTS temp_table');
        }

        return new self($newCartId);
    }
}
