<?php

/*
* Page config
*/
$pageConfig['row-action'] = 'add?id={{id}}';

/*
* Query config
*/
$queryConfig['table'] = 'ecommerce_customers';
$queryConfig['index'] = 'id';

// handle search
$queryConfig['where-search'] = "
	LOWER(name) LIKE CONCAT('%',?,'%') OR
	phone LIKE CONCAT('%',?,'%') OR
	LOWER(info) LIKE CONCAT('%',?,'%')
";

// select fields
$fields = [
    ['title' => 'ID', 'name' => 'id'],
    ['title' => 'Nome', 'name' => 'name', 'size' => 1],
    ['title' => 'Info', 'name' => 'info', 'size' => 1],
    ['title' => 'Telefono', 'name' => 'phone'],

    ['type' => 'btn'],
];

// buttons
$buttons = [
    ['type' => 'edit'],
    ['type' => 'delete'],
    ['url' => 'merge?id={{id}}', 'title' => 'Unifica', 'color' => 'green'],
];
