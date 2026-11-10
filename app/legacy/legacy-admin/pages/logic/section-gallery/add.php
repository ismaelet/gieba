<?php

$id = $_GET['id'] ?? null;

/*
* Page configuration
*/
// $pageConfig['confirm'] = $pageConfig['confirm'] ?? null;

/*
* Query configuration
*/
$queryConfig['table'] = 'images';
$queryConfig['index-field'] = 'id';
$queryConfig['title-field'] = 'id';

// form
$inputs = [
    [
        ['type' => 'lbl', 'text' => 'Collezione'],
        [
            'type' => 'select',
            'name' => 'collection',
            'lbl' => 'Collezione',
            'query' => '* FROM collections ORDER BY sort ASC, id ASC',
            'field-name' => 'id',
            'field-show' => 'name',
        ],
    ],
    [
        ['type' => 'lbl', 'text' => $id ? 'Nuova foto (opzionale)' : 'Foto'],
        [
            'name' => 'file',
            'type' => 'file',
            'lbl' => 'Immagine',
            'required' => $id ? false : true,
        ],
    ],
];

function _expand_add($tbl, $inputs)
{
    $id = form_db_add($tbl, $inputs);

    _upload_imgs($id);
}

function _expand_edit($tbl, $inputs, $index, $id)
{
    form_db_update($tbl, $inputs, $index, $id);

    if (! empty($_FILES['file']['name'])) {
        _upload_imgs($id);
    }
}

function _upload_imgs($id)
{
    // upload HD
    $fileData = [
        'url' => public_server_path('/uploads/images/'.$id),
        'size' => 1920,
        'mode' => 'max',
    ];

    upload_img($fileData);

    $fileData = [
        'url' => public_server_path('/uploads/images/'.$id.'_m'),
        'size' => 480,
        'mode' => 'max',
        'quality' => 70,

    ];

    upload_img($fileData);
}
