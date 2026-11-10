<?php

// setup page
$tbl = 'user_groups';
$index = 'id';
$index_show = 'name';

// form
$inputs = [
    [
        ['type' => 'lbl', 'text' => 'Nome del gruppo'],
        ['name' => 'name', 'text' => 'Nome del gruppo', 'required' => true],
    ],
    [
        ['type' => 'lbl', 'text' => 'Percentuale di sconto'],
        ['type' => 'number', 'name' => 'discount', 'text' => 'Percentuale di sconto', 'min' => 0, 'max' => 100, 'required' => true],
    ],
];
