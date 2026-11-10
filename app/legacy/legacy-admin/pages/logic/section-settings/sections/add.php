<?php

/*
* Page configuration
*/
// $pageConfig['confirm'] = $pageConfig['confirm'] ?? null;

/*
* Query configuration
*/
$queryConfig['table'] = 'admin_sections';
$queryConfig['index-field'] = 'id';
$queryConfig['title-field'] = 'name';

/*
* Form fields
*/
$inputs = [
    [
        ['type' => 'lbl', 'text' => 'Titolo'],
        ['name' => 'name', 'text' => 'Titolo'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Slug'],
        ['name' => 'slug', 'text' => 'Slug'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Categoria'],
        [
            'type' => 'select',
            'name' => 'category',
            'text' => 'Categoria',
            'query' => '* FROM admin_categories ORDER BY manager ASC, name ASC',
            'field-name' => 'slug',
            'field-show' => 'name',
        ],
    ],
    [
        ['type' => 'lbl', 'text' => 'Url'],
        ['name' => 'url', 'text' => 'URL'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Template'],
        ['name' => 'template', 'text' => 'Template'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Ordina'],
        ['name' => 'sort', 'text' => 'Ordine', 'parse' => 'int'],
    ],
    [
        ['type' => 'cbx', 'name' => 'hidden', 'text' => 'non apparirà nel menu laterale'],
    ],
    [
        ['type' => 'cbx', 'name' => 'manager', 'text' => 'solo per utenti avanzati'],
    ],
];
