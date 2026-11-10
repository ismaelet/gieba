<?php

global $user, $header;

// theme
$header['color'] = 'light';
$header['floating'] = false;
$header['sticky'] = false;
$header['mobile-threshold'] = 768;

// structure
$header['structure'] = [
    'left' => ['logo'],
    'center' => [],
    'right' => ['desktop-navigation'],
    'mobile-left' => ['search-icon'],
    'mobile-center' => ['logo'],
    'mobile-right' => ['mobile-icon'],
];

// components
$header['privacy'] = false;

// name
$header['name'] = sys_config('name');
$header['name-mob'] = $header['name'];

// links
$header['links'] = [];
$header['megamenus'] = [];
$h_links = [];
$h_links[] = ['title' => 'Home', 'loc' => 'home', 'url' => client_url('/')];

foreach ($h_links as $h_link) {

    $valid = true;

    // check logged filter
    if (isset($h_link['logged'])) {
        if ($h_link['logged'] && ! logged()) {
            $valid = false;
        } elseif (! $h_link['logged'] && logged()) {
            $valid = false;
        }
    }

    // check admin filter
    if (isset($h_link['admin']) && ! $user->admin) {
        $valid = false;
    }
    // check manager filter
    if (isset($h_link['manager']) && ! $user->manager) {
        $valid = false;
    }
    // check cart filter
    if (isset($h_link['cart']) && ! $user->cart_count) {
        $valid = false;
    }
    // add it to the list if valid
    if ($valid) {
        $header['links'][] = $h_link;
    }
}

unset($h_link);
unset($h_links);

// dropdowns
/*$header['dropdowns']['*dropdown-name*'] = [
    ['Title', client_url('/link')],
];*/

// languages
$header['languages'] = sys_config('lang-all');

// login bar
$header['login-link'] = ['Login', client_url('/login')];
if (! sys_config('static')) {
    $header['logged-link'] = [$user->username, client_url('/profile')];
}

// load components
include_once '-expands-header/components.php';
