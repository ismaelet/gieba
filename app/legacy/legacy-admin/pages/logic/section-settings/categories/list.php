<?php

/*
* Page configuration
*/
// $pageConfig['add'] = false;
$pageConfig['row-action'] = 'add?id={{id}}';

/*
* Query configuration
*/
$queryConfig['table'] = 'admin_categories';
$queryConfig['index'] = 'id';
$queryConfig['where-search'] = "name LIKE CONCAT('%',?,'%') OR slug LIKE CONCAT('%',?,'%')";
$queryConfig['order-index'] = 'name';
$queryConfig['order-direction'] = 'ASC';

/*
* List fields
*/
$fields = [
    ['title' => 'Nome', 'name' => 'name', 'size' => 3],
    ['title' => 'Slug', 'name' => 'slug', 'size' => 3],

    ['type' => 'btn'],
];

$buttons = [
    ['type' => 'edit'],
    ['type' => 'delete'],
];
