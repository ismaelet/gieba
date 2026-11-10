<?php

use Legacy\System\Classes\DB;

$price = null;
$productId = $_GET['productId'] ?? null;
$customerId = $_GET['customerId'] ?? null;

if ($productId) {
    if ($customerId) {
        $row = DB::row(
            '
		price
		FROM ecommerce_orders
		INNER JOIN ecommerce_carts
			ON ecommerce_orders.cartId = ecommerce_carts.id
		WHERE productId=? AND customerId=?
		ORDER BY time DESC',
            [$productId, $customerId]
        );

        if ($row) {
            $price = $row['price'];
        }
    }

    if (! $price) {
        $row = DB::row('price FROM ecommerce_products WHERE id=?', [$productId]);
        $price = $row['price'];
    }
}

echo json_encode(
    [
        'priceFormatted' => format($price),
    ]
);
