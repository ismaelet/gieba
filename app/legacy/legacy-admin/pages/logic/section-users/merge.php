<?php

use App\Models\Ecommerce\Customer;
use App\Models\Ecommerce\Order;

$id = $_GET['id'];

/*
* Page configuration
*/
$pageConfig['free'] = true;
$pageConfig['parcial-title'] = 'Unifica il cliente';

/*
* Query configuration
*/
$queryConfig['table'] = 'ecommerce_customers';
$queryConfig['index-field'] = 'id';
$queryConfig['title-field'] = 'name';

/*
* Form
*/
$searchInputConfig = [
    'search' => 'ecommerce_customers',
    'search-fields' => ['name'],
    'index-field' => 'id',
    'visible-field' => 'name',
    'placeholder' => 'Nome del cliente',
    'filter' => 'id <> '.$id,
    'required' => true,
];

/*
* Events
*/
if (isset($_POST['submit'])) {
    $customertId = $id;
    $mainCustomerId = $_POST['id'];

    Order::where('customerId', $customertId)->update(['customerId' => $mainCustomerId]);
    Customer::where('id', $customertId)->delete();

    legacy_redirect('list');
}
