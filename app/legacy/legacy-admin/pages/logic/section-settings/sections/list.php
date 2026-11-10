<?php

/*
* Page configuration
*/
// $pageConfig['add'] = false;
$pageConfig['row-action'] = 'add?id={{id}}';

/*
* Query configuration
*/
$queryConfig['table'] = 'admin_sections';
$queryConfig['index'] = 'id';
$queryConfig['select'] = ['admin_sections.id', 'admin_sections.slug', 'admin_sections.name', 'admin_sections.url', 'admin_sections.template', 'admin_sections.hidden', 'admin_categories.name as categoryName'];
$queryConfig['from'] = 'admin_sections INNER JOIN admin_categories ON admin_sections.category = admin_categories.slug';
$queryConfig['where-search'] = "LOWER(admin_sections.name) LIKE CONCAT('%',?,'%') OR LOWER(admin_sections.slug) LIKE CONCAT('%',?,'%') OR LOWER(category) LIKE CONCAT('%',?,'%') OR LOWER(admin_categories.name) LIKE CONCAT('%',?,'%')";
$queryConfig['order-index'] = 'category ASC, admin_sections.name';
$queryConfig['order-direction'] = 'ASC';

/*
* List fields
*/
$fields = [
    ['title' => 'Nome', 'name' => 'name', 'size' => 2],
    ['title' => 'Slug', 'name' => 'slug', 'size' => 1],
    ['title' => 'URL', 'name' => 'url', 'size' => 2],
    ['title' => 'Template', 'name' => 'template'],
    ['title' => 'Gruppo', 'name' => 'categoryName'],
    ['title' => 'Visibile', 'name' => 'hidden', 'type' => 'boolean-icon', 'parse' => 'flip', 'size' => 1],

    ['type' => 'btn'],
];

$buttons = [
    ['type' => 'edit'],
    ['type' => 'delete'],
];
