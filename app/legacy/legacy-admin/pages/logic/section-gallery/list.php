<?php

use Legacy\System\Classes\DB;

/*
* Page configuration
*/
// $pageConfig['add'] = false;
$pageConfig['row-action'] = 'add?id={{id}}';

/*
* Query configuration
*/
$queryConfig['table'] = 'images';
$queryConfig['index'] = 'id';
$queryConfig['where-search'] = "id = ? OR collection IN (SELECT id FROM collections WHERE LOWER(name) LIKE CONCAT('%',?,'%'))";

// select fields
$fields = [
    ['title' => 'ID', 'name' => 'id'],
    ['title' => 'Foto', 'type' => 'image', 'size' => 1],
    ['title' => 'Collezione', 'name' => 'collection', 'type' => 'find', 'find' => 'name FROM collections WHERE id = ?', 'size' => 3],
    ['title' => 'Data', 'name' => 'time', 'parse' => 'date-day', 'size' => 2],

    ['type' => 'btn'],
];

// buttons
$buttons = [
    ['type' => 'edit'],
    ['name' => 'delete-img', 'title' => 'Elimina', 'color' => 'red', 'warning' => 'Sei sicuro di voler eliminare la foto?'],
];

// delete
if (isset($_POST['delete-img'])) {
    $id = $_POST['delete-img'];

    DB::delete('images WHERE id = ?', [$id]);

    unlink(public_server_path('/uploads/images/'.$id));
    unlink(public_server_path('/uploads/images/'.$id.'_m'));

    legacy_redirect();
}

function _expand_results(&$entry, $field, $key, $row)
{
    if ($field['type'] == 'image') {
        $image = '/uploads/images/'.$row['id'].'_m';

        $entry['src'] = file_exists(public_server_path($image)) ? public_client_path($image) : public_client_path($image.'.jpg');
    }
}
