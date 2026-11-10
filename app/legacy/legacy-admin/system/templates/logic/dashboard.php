<?php

global $view, $user;

/*
* Load configurations
* 	- requires a dashboard name, set in the router
* 	- requires a file in app/config named dashboard-*name* with all the configurations stored in a $dashboardConfig variable
*/
$dashboardName = $this->get('dashboard-name');
require_once SERVER_ROOT.'/app/config/dashboard-'.$dashboardName.'.php';

// load template parts
$view->styles->add(SERVER_ROOT.'/system/templates/styles/-expands-dashboard/list.css');
$this->logic->add('main', 'template');

// fetch current section
$currentPath = false;

if (strpos($this->name, '-')) {
    $path = explode('/', explode('section-', str_replace('add', 'list', $this->name))[1], 2);
    $currentPath = [
        'category' => $path[0],
        'link' => $path[1],
    ];
}

// fetch categories
$dashboardNavigation = [];

foreach ($dashboardConfig['navigation'] as $category) {
    $dashboardCategory = [];
    $dashboardCategory['title'] = $category['title'];
    $dashboardCategory['slug'] = $category['slug'];
    $dashboardCategory['checked'] = $currentPath && $dashboardCategory['slug'] == $currentPath['category'];
    $dashboardCategory['links'] = [];

    foreach ($category['links'] as $link) {
        $dashboardLink = [];
        $dashboardLink['title'] = $link['title'];
        $dashboardLink['slug'] = $link['slug'];
        $dashboardLink['checked'] = $dashboardCategory['checked'] && $dashboardLink['slug'] == $currentPath['link'];
        $dashboardLink['url'] = $link['url'] ?? client_url('/'.$dashboardConfig['path'].'/'.$category['slug'].'/'.$link['slug']);

        $dashboardCategory['links'][] = $dashboardLink;
    }

    $dashboardNavigation[] = $dashboardCategory;
}
