<?php

use Legacy\System\Classes\DB;

/*
* Page configuration
*/
// $pageConfig['confirm'] = $pageConfig['confirm'] ?? null;

/*
* Query configuration
*/
$queryConfig['table'] = 'admin_categories';
$queryConfig['index-field'] = 'id';
$queryConfig['title-field'] = 'name';

/*
* Form fields
*/
$inputs = [
    [
        ['type' => 'lbl', 'text' => 'Nome'],
        ['name' => 'name', 'text' => 'Nome'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Slug'],
        ['name' => 'slug', 'text' => 'Slug'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Ordina'],
        ['name' => 'sort', 'text' => 'Ordine', 'parse' => 'int'],
    ],
    [
        ['type' => 'cbx', 'name' => 'manager', 'text' => 'solo per utenti avanzati'],
    ],
];

/*
* Form handling
*/
function _expand_edit($table, $inputs, $index, $id)
{
    $old = DB::find('slug FROM admin_categories WHERE id=?', $id);

    query_from_inputs($table, $inputs, $id);

    DB::update('admin_sections SET category=? WHERE category=?', [$_POST['slug'], $old]);

    if (file_exists($file = SERVER_ROOT.'/app/assets/icons/navigation/'.$old.'.svg')) {
        rename($file, SERVER_ROOT.'/app/assets/icons/navigation/'.$_POST['slug'].'.svg');
    }
}
