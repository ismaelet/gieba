<?php

namespace Ecommerce;

/*
* 1. Basic functions
    1.1 Access configuration
    1.2 Formatting
* 2. Customer
* 3. Cart
* 4. Downloads
*/

/*
* 1. Basic functions
*/
// 1.1 Access configuration
function ecommerce_config($paramenter)
{
    global $ecommerceConfig;

    if (empty($ecommerceConfig)) {
        include_once __DIR__.'../../../app/config/ecommerce-config.php';
    }

    return $ecommerceConfig[$paramenter];
}

// 1.2 Formatting
function format_price($price, $alwaysNumber = true)
{
    if ($price || $alwaysNumber) {
        switch (ecommerce_config('currency')) {
            case 'BRL':
                $priceString = 'R$ '.formatc($price);
                break;
            case 'EUR':
            default:
                $priceString = formatc($price).' €';
        }
    } else {
        $priceString = legacy__('Free');
    }

    return $priceString;
}

/*
* 2. Customer
*/
function customer()
{
    global $customer;

    return $customer;
}

function customerId()
{
    return customer()->id;
}

function isActiveCustomer()
{
    return (bool) customerId();
}

function requireActiveCustomer()
{
    global $customer;

    if (! isActiveCustomer()) {
        $customer = Customer::new();

        $_SESSION['customerId'] = $customer->id;
    }

    return $customer;
}

/*
* 3. Cart
*/
function cart()
{
    return customer()->cart;
}

function cartId()
{
    return cart()->id;
}

function associate_cart($permanentCustomerId, $guestCustomerId = false)
{
    global $customer;

    if (! empty($customer->id)) {
        $permanentCustomer = new Customer($permanentCustomerId);
        $guestCustomer = $guestCustomerId ? new Customer($guestCustomerId) : $customer;

        if (! $permanentCustomer->id) {
            \Legacy\System\Classes\DB::add('ecommerce_customers (userId)', [$permanentCustomerId]);
            $permanentCustomer = new Customer($permanentCustomerId);
        }

        if ($guestCustomer->hasCart) {
            if ($permanentCustomer->hasCart) {
                // if the user has a cart transfer the guest orders to it
                \Legacy\System\Classes\DB::update('ecommerce_orders SET cartId=? WHERE cartId=?', [$permanentCustomer->cart->id, $guestCustomer->cart->id]);
                \Legacy\System\Classes\DB::delete('ecommerce_carts WHERE id=?', [$guestCustomer->cart->id]);
            } else {
                // otherwise convert the session cart to the user cart
                \Legacy\System\Classes\DB::update('ecommerce_carts SET customerId=? WHERE id=?', [$permanentCustomer->id, $guestCustomer->cart->id]);
                \Legacy\System\Classes\DB::update('ecommerce_orders SET customerId=? WHERE cartId=?', [$permanentCustomer->id, $guestCustomer->cart->id]);

                // add an address
                if (! $guestCustomer->cart->addressId) {
                    $addressId = \Legacy\System\Classes\DB::find('id FROM ecommerce_addresses WHERE customerId=? ORDER BY id DESC', [$permanentCustomer->id]);
                    \Legacy\System\Classes\DB::update('ecommerce_carts SET addressId=? WHERE id=?', [$addressId, $guestCustomer->cart->id]);
                }
            }
        }

        $_SESSION['customerId'] = $permanentCustomerId;
    }
}

function order_count()
{
    return cart()->quantity;
}
