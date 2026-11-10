<?php

use App\Models\Action;
use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\OrderItem;
use Ecommerce\Cart as OldCart;
use Ecommerce\Customer;
use Ecommerce\Product;
use Legacy\System\Classes\DB;

use function Ecommerce\ecommerce_config;

global $user;

$id = $_GET['id'] ?? false;
$redirectFilterStatus = $_GET['filter-status'] ?? false;
$newCart = (bool) ! $id;

// return url
$returnUrl = 'list';

if ($id) {
    $listFilters = $_GET['list-filters'] ?? false;
    if ($listFilters) {
        $returnUrl .= '?'.urldecode($listFilters);
    }
    $returnUrl .= '#row'.$id;
}

// cart data
if (! $newCart) {
    $oldCart = new OldCart($id);
    $cart = Order::find($id);
    $customer = $oldCart->customer;
}

// search fields
$searchCustomer = [
    'search' => Customer::TABLE,
    'search-fields' => 'name',
    'index-field' => Customer::INDEX,
    'index-name' => 'customerId',
    'index-value' => $oldCart->customer->id ?? 0,
    'visible-field' => 'name',
    'visible-name' => 'customerName',
    'visible-value' => $oldCart->customer->name ?? '',
    'placeholder' => 'Nome completo',
    'has-title' => true,
    'required' => true,
];

$searchProduct = [
    'search' => Product::TABLE,
    'search-fields' => 'name',
    'index-field' => Product::INDEX,
    'index-name' => 'productId[]',
    'visible-field' => 'name',
    'visible-name' => 'productName[]',
    'placeholder' => 'Nome del prodotto',
    'has-title' => true,
];

// form handling
if (isset($_POST['submit'])) {
    $cartStatus = $_POST['cartStatus'];
    $oldCartDate = $_POST['cartDate'];
    $customerId = $_POST['customerId'];
    $customerName = trim($_POST['customerName']);
    $customerPhone = $_POST['customerPhone'];
    $customerInfo = trim($_POST['customerInfo']);
    $downPayment = $_POST['downPayment'] ?: 0;
    $downPaymentWithVat = isset($_POST['downPaymentWithVat']);
    $fixedDiscount = $_POST['fixedDiscount'] ?: 0;
    $discountWithVat = isset($_POST['discountWithVat']);
    $percentageDiscount = $_POST['percentageDiscount'] ?: 0;
    $billed = isset($_POST['billed']);
    $printDescriptions = isset($_POST['printDescriptions']);
    $printStatus = isset($_POST['printStatus']);
    $printTotal = isset($_POST['print_total']);
    $printNotes = isset($_POST['print_notes']);
    $alwaysPrintPrices = isset($_POST['always_print_prices']);

    /*
    * Customer
    */
    if (! $customerId) {
        $customerId = DB::find('id FROM ecommerce_customers WHERE name = ?', [$customerName]);

        if (! $customerId) {
            $customerId = DB::add('ecommerce_customers (name)', [$customerName]);
        }
    }

    DB::update('ecommerce_customers SET phone = ?, info = ? WHERE id = ?', [$customerPhone, $customerInfo, $customerId]);

    /*
    * Cart
    */
    if ($newCart) {
        $id = DB::add('ecommerce_carts ()');
        $cart = Order::find($id);
    }

    $cart->update([
        'customerId' => $customerId,
        'date' => $oldCartDate,
        'downPayment' => $downPayment,
        'downPaymentWithVat' => $downPaymentWithVat,
        'fixedDiscount' => $fixedDiscount,
        'discountWithVat' => $discountWithVat,
        'percentageDiscount' => $percentageDiscount,
        'billed' => $billed,
        'printDescriptions' => $printDescriptions,
        'printStatus' => $printStatus,
        'print_total' => $printTotal,
        'print_notes' => $printNotes,
        'always_print_prices' => $alwaysPrintPrices,
    ]);

    if ($newCart) {

        // Save the action
        Action::create([
            'user_id' => $user->id,
            'action' => 'Ha creato l\'ordine '.$id,
        ]);
    }

    /*
    * Orders & products
    */
    $oldOrderQuantity = $oldCart->quantity ?? 0;
    $newOrderQuantity = 0;

    foreach ($_POST['productId'] as $productKey => $productId) {
        $orderId = $_POST['orderId'][$productKey];
        $orderInfo = $_POST['orderInfo'][$productKey];
        $orderQuantity = $_POST['orderQuantity'][$productKey];
        $orderPrice = $_POST['orderNetPrice'][$productKey] ?: 0;
        $orderTotalPrice = $_POST['orderTotalPrice'][$productKey] ?: 0;
        $orderStatus = $_POST['orderStatus'][$productKey];
        $orderVat = isset($_POST['orderVat'.($productKey + 1)]) ? 1 : 0;
        $productName = trim($_POST['productName'][$productKey]);

        /*
        * Product
        */
        // Add a new product
        if (! $productId && $productName) {
            $productId = DB::find('id FROM ecommerce_products WHERE name = ?', [$productName]);

            if (! $productId) {
                $productId = DB::add('ecommerce_products (name, price)', [$productName, $orderPrice]);
            }
        }

        // Update product price
        if ($productId) {
            DB::update('ecommerce_products SET price = ? WHERE id = ?', [$orderPrice, $productId]);
        }

        /*
        * Order
        */
        if ($productId && $orderQuantity) {
            // If is a valid order
            $orderParameters = [
                'cartId' => $id,
                'productId' => $productId,
                'quantity' => $orderQuantity,
                'netProductPrice' => $orderPrice,
                'price' => $orderTotalPrice,
                'status' => $orderStatus,
                'hasVat' => $orderVat,
                'info' => $orderInfo,
            ];

            if (! $orderId) {
                // Add the order
                OrderItem::create($orderParameters);
            } else {
                // Update the order
                OrderItem::find($orderId)->update($orderParameters);
            }

            // Update the cart status if is not set or is higher than the order status
            if (! $cartStatus || $orderStatus < $cartStatus) {
                $cartStatus = $orderStatus;
            }

            $newOrderQuantity += $orderQuantity;
        } elseif ($orderId) {
            // Delete the order if it's not valid
            OrderItem::find($orderId)->delete();
        }
    }

    // Total order item quantity
    if (! $newCart && $newOrderQuantity != $oldOrderQuantity) {
        Action::create([
            'user_id' => $user->id,
            'action' => 'Ha modificato la quantità di articoli dell\'ordine '.$id.' da '.$oldOrderQuantity.' a '.$newOrderQuantity,
        ]);
    }

    // Update the cart status
    $olderStatus = $cart->status;
    $newStatus = $cartStatus;

    if ($olderStatus != $newStatus) {
        $cart->update(['status' => $cartStatus]);

        if (! $newCart) {
            $olderStatusString = ecommerce_config('cart-statuses')[$olderStatus][1];
            $newStatusString = ecommerce_config('cart-statuses')[$newStatus][1];

            Action::create([
                'user_id' => $user->id,
                'action' => 'Ha modificato lo stato dell\'ordine '.$id.' da "'.$olderStatusString.'" a "'.$newStatusString.'"',
            ]);
        }
    }

    legacy_redirect($returnUrl);
}

// delete a cart
if (isset($_POST['delete'])) {
    DB::delete('ecommerce_carts WHERE id = ?', [$id]);
    DB::delete('ecommerce_orders WHERE cartId = ?', [$id]);

    legacy_redirect('list');
}

// delete a cart
if (isset($_POST['duplicate'])) {
    $newCart = $oldCart->duplicate();

    legacy_redirect('?id='.$newCart->id);
}
