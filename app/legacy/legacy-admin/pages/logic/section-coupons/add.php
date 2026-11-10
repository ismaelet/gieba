<?php

use Legacy\System\Classes\DB;

// setup page
$id = $_GET['id'] ?? false;
$tbl = 'ecommerce_coupons';
$index = 'id';
$index_show = 'code';

// form
$inputs = [
    'code-row' => [
        ['type' => 'lbl', 'text' => 'Codice sconto<br><span class="txt-reg">Il codice sconto è generato automaticamente, ma può essere modificato.</span>'],
        'code' => [
            'name' => 'code',
            'text' => 'Codice sconto',
            'maxlenght' => 11,
            'size' => 4,
        ],
    ],
    [
        ['type' => 'lbl', 'text' => 'Prezzo minimo (opzionale)'],
        [
            'name' => 'minPrice',
            'text' => 'Prezzo minimo',
            'max' => 11,
            'size' => 4,
            'parse' => 'float',
        ],
    ],
    'time-row' => [
        ['type' => 'lbl', 'text' => 'Durata<br><span class="txt-reg">La durata di default è di un anno.</span>'],
        'time-start' => [
            'type' => 'date',
            'name' => 'timeStart',
            'size' => 2,
        ],
        'time-end' => [
            'type' => 'date',
            'name' => 'timeEnd',
            'size' => 2,
        ],
    ],
    'type-row' => [
        ['type' => 'lbl', 'text' => 'È applicato solo a questi tipi di prodotto (opzionale)'],
        ['type' => 'hidden', 'name' => 'types'],
    ],
    [
        ['type' => 'lbl', 'text' => 'Entità dello sconto.<br><span class="txt-reg">Inserire una percentuale o un valore fisso, ma non entrambi.</span>'],
        [
            'name' => 'percentage',
            'text' => '% di sconto per 1 prodotto',
            'size' => 2,
            'maxlenght' => 3,
            'parse' => 'int',
        ],
        [
            'name' => 'flat',
            'text' => '- € di sconto per 1 prodotto',
            'size' => 2,
            'parse' => 'int',
        ],
        [
            'name' => 'percentage2',
            'text' => '% di sconto per 2 prodotti',
            'size' => 2,
            'maxlenght' => 3,
            'parse' => 'int',
        ],
        [
            'name' => 'flat2',
            'text' => '- € di sconto per 2 prodotti',
            'size' => 2,
            'parse' => 'int',
        ],
        [
            'name' => 'percentageMore',
            'text' => '% di sconto per 3 o più prodotti',
            'size' => 2,
            'maxlenght' => 3,
            'parse' => 'int',
        ],
        [
            'name' => 'flatMore',
            'text' => '- € di sconto per 3 o più prodotti',
            'size' => 2,
            'parse' => 'int',
        ],
    ],
    [
        [
            'type' => 'cbx',
            'name' => 'reusable',
            'text' => 'Il codice sconto <strong>può essere usato per più acquisti</strong>',
        ],
        [
            'type' => 'cbx',
            'name' => 'onePerUser',
            'text' => 'Il codice può essere usato <strong>solo una volta per cliente</strong>',
            'custom-class' => 'hidden',
        ],
        [
            'type' => 'cbx',
            'name' => 'freeShipping',
            'text' => 'Usando questo codice le <strong>spese di spedizione sono gratuite</strong>',
        ],
    ],
];

//  type list
$currentTypes = $id ? explode(',', DB::find('types FROM ecommerce_coupons WHERE id=?', [$id])) : [];
$typesRes = DB::select('id, name FROM item_types ORDER BY name ASC');

while ($type = $typesRes->fetch_assoc()) {
    $inputs['type-row'][] = [
        'type' => 'cbx',
        'name' => 'type[]',
        'value' => $type['id'],
        'text' => $type['name'],
        'ignore' => true,
        'checked' => in_array($type['id'], $currentTypes),
        'size' => 1,
    ];
}

// setup code
if (! $id) {
    $new = false;

    while (! $new) {
        $code = str_pad(rand(100, 99999999), 8, '0', STR_PAD_LEFT);
        if (! DB::exists('ecommerce_coupons WHERE code=?', [$code])) {
            $new = true;
        }
    }

    $inputs['code-row']['code']['value'] = $code;
    $inputs['time-row']['time-start']['value'] = date('Y-m-d');
    $inputs['time-row']['time-end']['value'] = date('Y-m-d', strtotime('+1 year'));
}

// add
function _expand_add($tbl, $inputs)
{
    $_POST['code'] = strtoupper($_POST['code']);
    $_POST['code'] = str_replace(' ', '', $_POST['code']);
    $_POST['types'] = isset($_POST['type']) ? implode(',', $_POST['type']) : '';

    form_db_add($tbl, $inputs);
}

// edit
function _expand_edit($tbl, $inputs, $index, $id)
{
    $_POST['code'] = strtoupper($_POST['code']);
    $_POST['code'] = str_replace(' ', '', $_POST['code']);
    $_POST['types'] = isset($_POST['type']) ? implode(',', $_POST['type']) : '';

    form_db_update($tbl, $inputs, $index, $id);
}
