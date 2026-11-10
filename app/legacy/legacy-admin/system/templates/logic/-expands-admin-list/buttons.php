<?php

use Legacy\System\Classes\View;

// setup variables
$results = [];
$buttons = View::get('buttons') ?? [];

// generate the buttons
foreach ($buttons as $i => $raw) {

    $button = [];

    $type = $raw['type'] ?? 'custom';
    $title = $raw['title'] ?? false;
    $name = $raw['name'] ?? false;
    $warning = $raw['warning'] ?? false;
    $color = $raw['color'] ?? 'c1';
    $url = $raw['url'] ?? false;

    if ($type == 'edit') {
        $title = legacy__('Edit');
        $color = 'blu';
        $listFilters = explode('?', $_SERVER['REQUEST_URI'], 2)[1] ?? '';
        $listFilters = urlencode($listFilters);
        $url = 'add?id='.$row[$index].'&list-filters='.$listFilters;
    } elseif ($type == 'delete') {
        $title = legacy__('Delete');
        $name = 'delete';
        $color = 'red';
        $warning = legacy__('Are you sure you want to delete the item?');
    }

    if (strpos($url, '{{')) {
        $urlIndex = explode('}}', explode('{{', $url)[1])[0];
        $url = str_replace('{{'.$urlIndex.'}}', $row[$urlIndex], $url) ?? false;
    }

    $button['title'] = $title;
    $button['warning'] = $warning;
    $button['color'] = $color;
    $button['name'] = $name;
    $button['url'] = $url;
    $button['download'] = isset($raw['download']);
    $button['custom-class'] = $raw['custom-class'] ?? false;
    $button['type'] = $url ? 'link' : 'button';

    // additional code
    if (function_exists('_expand_buttons')) {
        _expand_buttons($button, $i, $row);
    }

    if ($button) {
        $entry['buttons'][] = $button;
    }
}
