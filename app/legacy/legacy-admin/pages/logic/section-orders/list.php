<?php

use Ecommerce\Cart;
use Legacy\System\Classes\DB;

use function Ecommerce\download_quote;
use function Ecommerce\ecommerce_config;

$statuses = ecommerce_config('cart-statuses');
$statusIds = array_keys($statuses);
$defaultCartStatuses = [1, 2, 3, 4, 5, 6];
$defaultBilledStatuses = [0, 1];

$selectedCartStatuses = $_GET['filter-status'] ?? $defaultCartStatuses;
$selectedBilledStatuses = $_GET['filter-billed'] ?? $defaultBilledStatuses;
$selectedCartStatusesSql = implode(',', $selectedCartStatuses);
$selectedBilledStatusesSql = implode(',', $selectedBilledStatuses);

$minimumStartDate = (new DateTime(DB::find('time FROM ecommerce_carts ORDER BY time ASC')))->format('Y-m-d');
$currentDate = (new DateTime('tomorrow'))->format('Y-m-d');
$startDate = ! empty($_GET['start-date']) ? $_GET['start-date'] : $minimumStartDate;
$endDate = ! empty($_GET['end-date']) ? $_GET['end-date'] : $currentDate;

/*
* Page configuration
*/
// $pageConfig['add'] = false;
$pageConfig['row-action'] = 'add?id={{id}}';

/*
* Query config
*/
$queryConfig['table'] = Cart::TABLE;
$queryConfig['index'] = 'id';
$queryConfig['select'] = [Cart::TABLE.'.'.Cart::INDEX, 'name', Cart::TABLE.'.status', 'phone', 'date'];
$queryConfig['from'] = '
	'.Cart::TABLE.'
	LEFT JOIN ecommerce_customers
		ON '.Cart::TABLE.'.customerId = ecommerce_customers.id
';
$queryConfig['order-index'] = 'date DESC, id';

// $queryConfig['where'] = '((status IN (' . $selectedCartStatusesSql . ') OR ecommerce_carts.id IN (SELECT cartId FROM ecommerce_orders WHERE status IN (' . $selectedCartStatusesSql . '))) AND billed IN (' . $selectedBilledStatusesSql . ') AND time >= "' . $startDate . '" AND time <= "' . $endDate . '")';
$queryConfig['where'] = '((ecommerce_carts.id IN (SELECT cartId FROM ecommerce_orders WHERE status IN ('.$selectedCartStatusesSql.'))) AND billed IN ('.$selectedBilledStatusesSql.') AND time >= "'.$startDate.'" AND time <= "'.$endDate.'")';

// handle search
$queryConfig['where-search'] = "
	(
		ecommerce_carts.id=? OR LPAD(ecommerce_carts.id, 6, '0')=? OR CONCAT('#',LPAD(ecommerce_carts.id, 6, '0'))=? OR
		LOWER(ecommerce_customers.name) LIKE CONCAT('%',?,'%') OR
		LOWER(ecommerce_customers.info) LIKE CONCAT('%',?,'%') OR
		phone LIKE CONCAT('%',?,'%') OR
		ecommerce_carts.id IN
			(
				SELECT cartId FROM ecommerce_orders
				INNER JOIN ecommerce_products
				ON ecommerce_orders.productId = ecommerce_products.id
				WHERE
					(
						LOWER(name) LIKE CONCAT('%',?,'%') OR
						LOWER(info) LIKE CONCAT('%',?,'%')
					)
				AND status IN (".$selectedCartStatusesSql.')
			)
	)
';

// select fields
$rowLinks = 'add?id={{id}}';

$fields = [
    ['title' => 'Ordine', 'name' => 'id'],
    ['title' => 'Cliente', 'name' => 'name', 'size' => 3],
    ['title' => 'Prodotti', 'name' => 'orders', 'size' => 3, 'not-sortable' => true],
    ['title' => 'Totale', 'name' => 'price', 'not-sortable' => true],
    ['title' => 'Stato', 'name' => 'cartStatus', 'not-sortable' => true],
    ['title' => 'Fatturato', 'name' => 'billed', 'type' => 'custom'],
    ['title' => 'Data', 'name' => 'date', 'parse' => 'date'],

    ['type' => 'btn'],
];

$buttons = [
    ['type' => 'edit'],
    ['name' => 'download-quote', 'title' => 'Preventivo', 'color' => 'orange'],
    ['name' => 'download-delivery-quote', 'title' => 'Bolla di consegna', 'color' => 'green'],
];

function _expand_results(&$entry, $field, $key, $row)
{
    $cart = new Cart($row['id']);
    $customer = $cart->customer;

    switch ($field['name']) {
        // ID
        case 'id':
            $entry['value'] = $cart->idCode;
            break;

            // customer name
        case 'name':
            $entry['value'] = $cart->customer->name;
            if ($customer->phone) {
                $entry['value'] .= '<br><object><a href="tel:'.$customer->phone.'">'.$customer->phone.'</a></object>';
            }
            break;

            // price
        case 'price':
            $entry['value'] = $cart->priceString;
            break;

            // orders
        case 'orders':
            $orderRows = [];

            foreach ($cart->orders as $order) {
                $orderRows[] = '
			<span class="order-row">
			<span class="order-status order-status--'.$order->status.'"></span>
			<span><strong>'.$order->quantity.' x</strong> '.($order->product->name ?: '[Articolo Rimosso]').'</span>
			</span>';
            }

            $entry['value'] = implode('', $orderRows);
            break;

            // status
        case 'cartStatus':
            $entry['value'] = $cart->statusString;
            $entry['custom-classes'] = ['status'.$cart->status];
            break;

            // billed
        case 'billed':
            $entry['content'] = '<label class="checkbox billed">
			<input type="checkbox" class="js-billed"  value="'.$cart->id.'" '.($cart->billed ? 'checked' : '').'>
			<span></span>
			</label>';
            break;
    }
}

/*
* Download quote
*/
if (isset($_POST['download-quote'])) {
    $cartId = $_POST['download-quote'];

    download_quote($cartId, 'Preventivo', 1);
}

if (isset($_POST['download-delivery-quote'])) {
    $cartId = $_POST['download-delivery-quote'];

    download_quote($cartId, 'Bolla di Consegna', 2);
}
