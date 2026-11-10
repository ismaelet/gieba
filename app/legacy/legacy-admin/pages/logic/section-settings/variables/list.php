<?php

// select table
$index = 'id';
$tbl = 'variables';
$query['index'] = 'name';
$query['order'] = 'asc';

// handle search
$query['filter'] = "name LIKE CONCAT('%',?,'%')";

// select fields
$fields = [
    ['title' => 'Nome', 'name' => 'name', 'size' => 'l'],
    ['title' => 'Valore', 'name' => 'value', 'size' => 'm'],

    ['type' => 'btn'],
];

// buttons
$buttons = [
    ['type' => 'edit'],
    ['type' => 'delete'],
];
