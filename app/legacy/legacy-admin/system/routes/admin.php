<?php

use Legacy\System\Classes\DB;
use Legacy\System\Classes\Route;
use Legacy\System\Classes\View;

// sections
$sections = DB::select('slug, category FROM admin_sections');

while ($section = $sections->fetch_assoc()) {
    Route::add('/'.$section['category'].'/'.$section['slug'], function ($path) {
        global $id;

        $id = $_GET['id'] ?? null;

        // fetch section data
        $uriParts = explode('/', explode('legacy-admin/', $path)[1], 2);
        $section = DB::row('* FROM admin_sections WHERE slug=? AND category=?', [$uriParts[1], $uriParts[0]]);

        // setup view
        View::setPage('section-'.$section['category'].'/'.$section['slug'])
            ->setTitle($section['name'])
            ->setTemplate(in_array($section['template'], ['admin', 'void']) ? $section['template'] : 'admin-'.$section['template'])
            ->addMiddlewares(['admin'])
            ->show();
    });
}
