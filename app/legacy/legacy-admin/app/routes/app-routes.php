<?php

use Legacy\System\Classes\Route;
use Legacy\System\Classes\View;

// home
Route::add('/', function () {
    View::setPage('home')
        ->setTemplate('admin')
        ->addMiddlewares(['admin'])
        ->show();
});
