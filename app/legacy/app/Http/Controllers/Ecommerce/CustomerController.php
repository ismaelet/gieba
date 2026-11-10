<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\Customer;

class CustomerController extends Controller
{
    public static function get(): array|false
    {
        $filter = strtolower($_GET['filter']);
        $exclude = isset($_GET['exclude']) ? explode(',', $_GET['exclude']) : null;

        $customers = Customer::whereRaw("LOWER(name) LIKE '%".$filter."%'")
            ->whereNotIn('id', $exclude)
            ->orderBy('userId', 'desc')
            ->limit(10)
            ->select('id', 'name')
            ->get();

        if ($customers->count()) {
            $results = [];

            foreach ($customers as $customer) {
                $results[] = [
                    'id' => $customer->id,
                    'name' => $customer->id.' - '.$customer->name,
                ];
            }

            return $results;
        }

        return false;
    }
}
