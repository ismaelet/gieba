<?php

use Legacy\System\Classes\DB;

$customerId = $_GET['customerId'] ?? false;

if ($customerId) {
    $row = DB::row('info, phone FROM ecommerce_customers WHERE id=?', [$customerId]);
}

echo json_encode(
    [
        'info' => $row['info'],
        'phone' => $row['phone'],
    ]
);
