<?php

/*
* Page configuration
*/
// $pageConfig['confirm'] = $pageConfig['confirm'] ?? null;

/*
* Query configuration
*/
$queryConfig['table'] = 'ecommerce_customers';
$queryConfig['index-field'] = 'id';
$queryConfig['title-field'] = 'name';

// form
$inputs = [
    [
        ['type' => 'lbl', 'text' => 'Nome'],
        ['name' => 'name', 'text' => 'Nome'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Telefono'],
        ['name' => 'phone', 'text' => 'Telefono'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Info sul cliente'],
        ['type' => 'textarea', 'name' => 'info', 'text' => 'Info sul cliente'],
    ],
];
