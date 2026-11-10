<?php

$thisUser = new User($_GET['id']);

// setup page
$tbl = 'users';
$index = 'id';
$index_show = 'username';
$title = 'Modifica: '.($thisUser->username ?: $thisUser->email);

// form
$inputs = [
    [
        ['type' => 'lbl', 'text' => 'Username / Email'],
        ['text' => 'Username / Email', 'name' => 'username', 'value' => $thisUser->username ?: $thisUser->email, 'readonly' => true],
    ],
    [
        ['type' => 'lbl', 'text' => 'Nome e cognome'],
        ['text' => 'Nome', 'name' => 'firstname', 'value' => $thisUser->firstname, 'size' => 2, 'readonly' => true],
        ['text' => 'Cognome', 'name' => 'lastname', 'value' => $thisUser->lastname, 'size' => 2, 'readonly' => true],
    ],
    [
        ['type' => 'lbl', 'text' => 'Gruppo di appartenenza'],
        [
            'type' => 'select',
            'name' => 'groupId',
            'query' => '* FROM user_groups ORDER BY name ASC',
            'field-name' => 'id',
            'field-show' => 'name',
            'empty' => true,
        ],
    ],
];
