<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The list of order statuses
    |--------------------------------------------------------------------------
    |
    | This array contains all the possible order statuses used.
    |
    */

    'order_statuses' => [
        '1' => ['tag' => 'to-order', 'label' => 'Da ordinare'],
        '2' => ['tag' => 'added', 'label' => 'Da fare'],
        '3' => ['tag' => 'in-cart', 'label' => 'Nel carrello'],
        '4' => ['tag' => 'ordered', 'label' => 'Ordinato'],
        '5' => ['tag' => 'prepared', 'label' => 'Preparato'],
        '6' => ['tag' => 'delivered', 'label' => 'Ritirato'],
        '7' => ['tag' => 'completed', 'label' => 'Pagato'],
        '8' => ['tag' => 'billed', 'label' => 'Preventivato'],
        '9' => ['tag' => 'billed', 'label' => 'Archiviato'],
        '99' => ['tag' => 'canceled', 'label' => 'Cancellato'],
    ],

    'supplier_order_statuses' => [
        '1' => ['tag' => 'to-order', 'label' => 'Da ordinare'],
        '2' => ['tag' => 'in-cart', 'label' => 'Nel carrello'],
        '3' => ['tag' => 'ordered', 'label' => 'Ordinato'],
    ],
];
