<?php

// content
$footer['columns'] = [
    [
        'title' => 'Informazioni',
        'links' => [
            ['Home', client_url('/')],
            ['Spedizioni e ritiro', client_url('/')],
            ['Nota legale', client_url('/')],
            ['Chi siamo', client_url('/')],
            ['Pagamento sicuro', client_url('/')],
            ['Privacy policy', client_url('/')],
            ['FAQ', client_url('/')],
        ],
    ],
    [
        'title' => 'Il mio profilo',
        'links' => [
            ['Il mio profilo', client_url('/')],
            ['I miei buoni', client_url('/')],
            ['Wishlist', client_url('/')],
        ],
    ],
    [
        'title' => 'Riferimenti',
        'links' => [
            ['Loc. Giare 14/a<br>38050, Imèr (TN)', 'https://www.google.com/maps/place/Via+delle+Giare,+14,+38050+Mezzano+TN/data=!4m2!3m1!1s0x4778f7272e560bf1:0xec41345a156c12af?sa=X&ved=2ahUKEwjH5veTsPb3AhXfR7gEHf-GACYQ8gF6BAgVEAE', true],
            [sys_config('cont-call'), sys_config('cont-call-link')],
            [sys_config('cont-email'), sys_config('cont-email-link')],
        ],
    ],
];
