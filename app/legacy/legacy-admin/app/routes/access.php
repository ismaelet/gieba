<?php

use Legacy\System\Classes\Route;
use Legacy\System\Classes\View;

// login
Route::add('/login', function () {
    View::setPage('access/login')
        ->setTitle('Login')
        ->setMiddlewares(['not-admin'])
        ->setTemplate('void')
        ->show();
});

// logout
Route::add('/logout', function () {
    View::setPage('access/logout')
        ->setTitle('Logout')
        ->setMiddlewares(['admin'])
        ->setTemplate('void')
        ->show();
});
