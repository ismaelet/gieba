<?php

use Ecommerce\Order;
use Ecommerce\Product;
use Legacy\System\Classes\DB;

/**
 * * Page configuration
 */
$pageConfig['free'] = true;
$pageConfig['parcial-title'] = 'Unifica l\'articolo';

/*
* Query configuration
*/
$queryConfig['table'] = 'ecommerce_products';
$queryConfig['index-field'] = 'id';
$queryConfig['title-field'] = 'name';

/*
* Form
*/
$searchInputConfig = [
    'search' => Product::TABLE,
    'search-fields' => ['name'],
    'index-field' => Product::INDEX,
    'visible-field' => 'name',
    'filter' => Product::INDEX.' <> '.$_GET['id'],
    'placeholder' => 'Nome del prodotto',
    'required' => true,
];

/*
* Events
*/
if (isset($_POST['submit'])) {
    $productId = $_GET['id'];
    $mainProductId = $_POST['id'];

    DB::update(Order::TABLE.' SET '.Order::PRODUCT_INDEX.' = ? WHERE '.Order::PRODUCT_INDEX.' = ?', [$mainProductId, $productId]);
    DB::delete(Product::TABLE.' WHERE '.Product::INDEX.' = ?', [$productId]);

    legacy_redirect('list');
}
