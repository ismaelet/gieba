<?php

/*
* Page configuration
*/
// $pageConfig['confirm'] = $pageConfig['confirm'] ?? null;

/*
* Query configuration
*/
$queryConfig['table'] = 'ecommerce_products';
$queryConfig['index-field'] = 'id';
$queryConfig['title-field'] = 'name';

// form
$inputs = [
    [
        ['type' => 'lbl', 'text' => 'Nome'],
        ['name' => 'name', 'text' => 'Nome'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Prezzo di default'],
        ['name' => 'price', 'text' => 'Prezzo di default', 'parse' => 'float'],
    ],
];
