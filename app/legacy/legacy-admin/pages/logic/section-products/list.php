<?php

/*
* Page configuration
*/

use function Ecommerce\format_price;

$pageConfig['add'] = false;
$pageConfig['row-action'] = 'add?id={{id}}';

/*
* Query configuration
*/
$queryConfig['table'] = 'ecommerce_products';
$queryConfig['index'] = 'id';
$queryConfig['select'] = ['id', 'name', 'price', 'saleCount'];
$queryConfig['from'] = '
	ecommerce_products
	LEFT JOIN (SELECT productId, SUM(quantity) AS saleCount FROM ecommerce_orders WHERE status NOT IN (8, 99) GROUP BY productId) orderCount
	ON orderCount.productId = ecommerce_products.id';
$queryConfig['where-search'] = "LOWER(name) LIKE CONCAT('%',?,'%')";

/*
* List fields
*/
$fields = [
    ['title' => 'ID', 'name' => 'id'],
    ['title' => 'Nome', 'name' => 'name', 'size' => 2],
    ['title' => 'Prezzo di default', 'name' => 'price', 'size' => 1],
    ['title' => 'Vendite', 'name' => 'saleCount', 'size' => 1],

    ['type' => 'btn'],
];

$buttons = [
    ['type' => 'edit'],
    ['type' => 'delete'],
    ['url' => 'merge?id={{id}}', 'title' => 'Unifica', 'color' => 'green'],
];

function _expand_results(&$entry, $field, $key, $row)
{
    if ($field['name'] == 'price') {
        $entry['value'] = format_price($entry['value']);
    }
}
