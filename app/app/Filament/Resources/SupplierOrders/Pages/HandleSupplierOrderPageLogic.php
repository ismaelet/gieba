<?php

namespace App\Filament\Resources\SupplierOrders\Pages;

use Illuminate\Support\Str;

trait HandleSupplierOrderPageLogic
{
    /**
     * Store the return URL in the session
     */
    protected function storeReturnUrlInSession(): void
    {
        $returnUrl = request()->query('return_url');

        if ($returnUrl && Str::startsWith($returnUrl, ['/admin', static::getResource()::getUrl('index')])) {
            session()->put('supplier_orders.return_url', $returnUrl);
        }
    }

    /**
     * Get the return URL from the session or default to the index page
     */
    protected function getReturnUrlFromSession(): string
    {
        if (session()->has('supplier_orders.return_url')) {
            return session()->pull('supplier_orders.return_url');
        }

        return static::getResource()::getUrl('index');
    }
}
