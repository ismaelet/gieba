<?php

use App\Http\Controllers\Ecommerce\CustomerController;
use Legacy\System\Classes\Route;

/**
 * Customers
 */
Route::add('/api/customers', function () {
    echo json_encode(CustomerController::get());
});
