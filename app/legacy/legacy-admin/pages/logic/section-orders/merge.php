<?php

use App\Models\Action;
use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\OrderItem;
use Illuminate\Support\Facades\Auth;

/**
 * * Page configuration
 */
$success = false;
$pageConfig['free'] = true;
$pageConfig['parcial-title'] = 'Unifica l\'articolo';

/**
 * * Form events
 */
if (isset($_POST['submit'])) {
    $result = mergeOrders();

    if ($result === true) {
        $success = true;
    } else {
        $error = $result;
    }
}

function mergeOrders(): string|true
{
    $firstId = $_POST['firstId'];
    $secondId = $_POST['secondId'];

    if (! Order::find($firstId)) {
        return 'first-order-not-found';
    }

    if (! Order::find($secondId)) {
        return 'second-order-not-found';
    }

    OrderItem::where('cartId', $secondId)->update(
        ['cartId' => $firstId]
    );

    Order::find($secondId)?->delete();

    Action::create([
        'user_id' => Auth::id(),
        'action' => 'Ha unito l\'ordine '.$secondId.' con l\'ordine '.$firstId,
    ]);

    return true;
}
