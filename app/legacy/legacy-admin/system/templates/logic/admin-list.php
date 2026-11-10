<?php

use Legacy\System\Classes\DB;
use Legacy\System\Classes\View;

// setup variables
$values = [];
$listRows = [];
$currentPage = $_GET['page'] ?? 1;
$pageConfig = View::get('pageConfig') ?? [];
$queryConfig = View::get('queryConfig');
$fields = View::get('fields');
$buttons = View::get('buttons') ?? [];

/*
* Page configuration fields
*	? (bool) add: manages the visibility of the add button
*	? (bool) search bar: manages the visibility of the search bar
*/
$pageConfig['add'] = $pageConfig['add'] ?? true;
$pageConfig['search-bar'] = $pageConfig['search-bar'] ?? true;

/*
* Query fields:
*	table: the name of the table
*	index: the index field of the table
*	? (array) select: the list of fields to fetch in the query
*	? (string) from: the table(s) from which to fetch the data
*	? (string) where: the WHERE clause
*	? (string) where-search: an additional WHERE clause to be used when the user search something
*	? (string) group: the GROUP BY clause
*	? (string) order-index: the argumento of the GROUP BY clause
*	? (string: ASC,DESC) order-direction: the direction of the ordering
*	? (int) maximum-results: the maximum number of rows to be fetched
*/

$queryConfig['select'] = isset($queryConfig['select']) ? implode(', ', $queryConfig['select']) : '*';
$queryConfig['from'] = $queryConfig['from'] ?? $queryConfig['table'];
$queryConfig['where'] = $queryConfig['where'] ?? null;
$queryConfig['where-search'] = $queryConfig['where-search'] ?? null;
$queryConfig['group'] = $queryConfig['group'] ?? null;
$queryConfig['order-index'] = $_GET['index'] ?? ($queryConfig['order-index'] ?? $queryConfig['index']);
$queryConfig['order-direction'] = $_GET['order'] ?? ($queryConfig['order-direction'] ?? 'DESC');
$queryConfig['maximum-results'] = $queryConfig['maximum-results'] ?? 40;

/*
* Table interactions
*/
if (isset($_POST['delete'])) {
    DB::delete($queryConfig['table'].' WHERE '.$queryConfig['index'].' = ?', [$_POST['delete']]);
}

/*
* Execute the query
*/
// query setup
$queryConfig['search'] = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : null;
$queryConfig['where-search'] = $queryConfig['search'] ? $queryConfig['where-search'] : null;
$queryConfig['start'] = ($currentPage - 1) * $queryConfig['maximum-results'];

$queryConfig['select-string'] = $queryConfig['select'].' FROM '.$queryConfig['from'];
$queryConfig['where-string'] = implode(' AND ', array_filter([$queryConfig['where'], $queryConfig['where-search']]));
$queryConfig['where-string'] = $queryConfig['where-string'] ? 'WHERE '.$queryConfig['where-string'] : $queryConfig['where-string'];
$queryConfig['group-by-string'] = $queryConfig['group'] ? ' GROUP BY '.$queryConfig['group'] : '';
$queryConfig['order-by-string'] = 'ORDER BY '.$queryConfig['order-index'].' '.strtoupper($queryConfig['order-direction']);
$queryConfig['limit-string'] = 'LIMIT '.$queryConfig['start'].', '.$queryConfig['maximum-results'];

$sql = implode(' ', [$queryConfig['select-string'], $queryConfig['where-string'], $queryConfig['group-by-string'], $queryConfig['order-by-string'], $queryConfig['limit-string']]);
$sqlCount = implode(' ', [$queryConfig['from'], $queryConfig['where-string'], $queryConfig['group-by-string']]);

// setup query values
for ($i = 0; $i < substr_count($queryConfig['where-string'], '?'); $i++) {
    $values[] = $queryConfig['search'];
}

// execute the queries
$queryResults = DB::select($sql, $values);
$resultCount = DB::count($sqlCount, $values);

/*
* Setup the page
*/
// table layout
$pageConfig['grid-columns'] = [];

foreach ($fields as $field) {
    $fieldType = $field['type'] ?? null;

    if ($fieldType != 'hidden') {
        $columnSize = $field['size'] ?? 'max-content';
        $columnSize = is_int($columnSize) ? 'minmax('.(4 * $columnSize).'rem, '.$columnSize.'fr'.')' : $columnSize;
        $pageConfig['grid-columns'][] = $columnSize;
    }
}

$pageConfig['grid-columns'] = implode(' ', $pageConfig['grid-columns']);

// page bars
$pagesCount = ceil($resultCount / $queryConfig['maximum-results']);
$pageConfig['first-page'] = $currentPage - 2;
$pageConfig['first-page'] = $pageConfig['first-page'] < 1 ? 1 : $pageConfig['first-page'];
$pageConfig['last-page'] = $pageConfig['first-page'] + 4;
$pageConfig['last-page'] = $pageConfig['last-page'] > $pagesCount ? $pagesCount : $pageConfig['last-page'];

/*
* Process results
*/
while ($queryRow = $queryResults->fetch_assoc()) {
    $result = [];

    foreach ($fields as $key => $field) {

        // setup variables
        $entry = [];

        $field['type'] = $field['type'] ?? 'show';
        $field['name'] = $field['name'] ?? null;
        $field['max'] = $field['max'] ?? null;
        $field['parse'] = $field['parse'] ?? null;

        // fetch value
        switch (true) {
            case isset($field['value']):
                $entry['value'] = $field['value'];
                break;
            case $field['type'] == 'find':
                $entry['value'] = DB::find($field['find'], [$queryRow[$field['name']]]) ?: '-';
                break;
            case $field['name'] && isset($queryRow[$field['name']]):
                $entry['value'] = strlen($queryRow[$field['name']]) ? $queryRow[$field['name']] : '-';
                break;
            default:
                $entry['value'] = '-';
        }

        // parse value
        if ($field['parse']) {
            switch ($field['parse']) {
                case 'int':
                    $entry['value'] = (int) $entry['value'];
                    break;
                case 'float':
                    $entry['value'] = format($entry['value']);
                    break;
                case 'price':
                    $entry['value'] = function_exists('format_price') ? format_price($entry['value']) : format($entry['value']);
                    break;
                case 'date-day':
                    $entry['value'] = date('Y-m-d', strtotime($entry['value']));
                    break;
                case 'date-full':
                    $entry['value'] = date('Y-m-d H:i:s', strtotime($entry['value']));
                    break;
                case 'switch':
                    $entry['value'] = $entry['value'] ? $field['switch'][0] : $field['switch'][1];
                    break;
                case 'flip':
                    $entry['value'] = ! $entry['value'];
                    break;
            }
        }

        // additional code
        if (function_exists('_expand_results')) {
            _expand_results($entry, $field, $key, $queryRow);
        }

        // store entry
        $entry['type'] = $field['type'];
        $entry['name'] = $field['name'];
        $entry['maxlenght'] = $field['maxlenght'] ?? false;
        $entry['overflow'] = $field['overflow'] ?? false;
        $entry['max-size'] = $field['max-size'] ?? false;
        $entry['custom-classes'] = $entry['custom-classes'] ?? [];
        $entry['showed-value'] = $entry['maxlenght'] && strlen($entry['value']) > $entry['maxlenght'] ?
            substr($entry['value'], 0, ($entry['maxlenght']) - 3).'...' : $entry['value'];

        if ($field['type'] == 'btn') {
            _generate_buttons($entry, $queryRow, $queryConfig, $buttons);
        }

        $result[] = $entry;
    }

    // store data
    $listRows[$queryRow[$queryConfig['index']]] = $result;
}

/*
* Generate buttons
*/
function _generate_buttons(&$entry, $queryRow, $queryConfig, $buttons)
{
    foreach ($buttons as $buttonKey => $button) {
        $type = $button['type'] ?? 'custom';
        $title = $button['title'] ?? null;
        $name = $button['name'] ?? null;
        $warning = $button['warning'] ?? null;
        $color = $button['color'] ?? null;
        $url = $button['url'] ?? null;

        switch ($type) {
            case 'edit':
                $title = legacy__('Edit');
                $color = 'blue';
                $queryFilter = explode('?', $_SERVER['REQUEST_URI'], 2)[1] ?? '';
                $queryFilter = str_replace('id=', 'filter=', $queryFilter);
                $url = 'add?id='.$queryRow[$queryConfig['index']].'&'.$queryFilter;
                break;
            case 'delete':
                $title = legacy__('Delete');
                $name = 'delete';
                $color = 'red';
                $warning = legacy__('Are you sure you want to delete the item?');
                break;
        }

        if (strpos($url, '{{')) {
            $urlIndex = explode('}}', explode('{{', $url)[1])[0];
            $url = str_replace('{{'.$urlIndex.'}}', $queryRow[$urlIndex], $url) ?? null;
        }

        $button['title'] = $title;
        $button['warning'] = $warning;
        $button['color'] = $color;
        $button['name'] = $name;
        $button['url'] = $url;
        $button['download'] = isset($button['download']);
        $button['custom-class'] = $button['custom-class'] ?? null;
        $button['type'] = $url ? 'link' : 'button';

        // additional code
        if (function_exists('_expand_buttons')) {
            _expand_buttons($button, $buttonKey, $queryRow);
        }

        if ($button) {
            $entry['buttons'][] = $button;
        }
    }
}
