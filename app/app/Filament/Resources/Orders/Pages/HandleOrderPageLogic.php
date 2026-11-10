<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Models\Customer;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

trait HandleOrderPageLogic
{
    /**
     * Update the order and related data
     */
    protected function updateOrder(): void
    {
        $state = $this->form->getState();

        $customerId = $state['customer_id'] ?? null;

        if ($customerId) {
            Customer::find($customerId)->update([
                'phone' => data_get($state, 'customer_data.phone'),
                'info' => data_get($state, 'customer_data.info'),
            ]);
        }

        // Recalculate unit prices
        $this->record->order_items()->each(function ($item) {
            $item->load('product');
            $item->recalculatePrices();
            $item->product->update([
                'price' => $item->unit_price_net,
            ]);
        });

        // Recalculate totals
        $this->record->recalculateTotals();
    }

    /**
     * Store the return URL in the session
     */
    protected function storeReturnUrlInSession(): void
    {
        $returnUrl = request()->query('return_url');

        if ($returnUrl && Str::startsWith($returnUrl, ['/admin', static::getResource()::getUrl('index')])) {
            session()->put('orders.return_url', $returnUrl);
        }
    }

    /**
     * Get the return URL from the session or default to the index page
     */
    protected function getReturnUrlFromSession(): string
    {
        if (session()->has('orders.return_url')) {
            return session()->pull('orders.return_url');
        }

        return static::getResource()::getUrl('index');
    }

    public function confirmCustomerSelectionAction(): Action
    {
        return Action::make('confirmCustomerSelection')
            ->modalHeading('Ordine già esistente')
            ->modalDescription(
                'Esiste già un ordine aperto per questo cliente. Creare comunque un nuovo ordine?'
            )
            ->modalSubmitActionLabel('Crea comunque')
            ->modalCancelActionLabel('Annulla')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                $customer = Customer::find($arguments['customerId']);

                if (! $customer) {
                    return;
                }

                $this->data['customer_id'] = $customer->getKey();

                $this->data['customer_data'] = [
                    'phone' => $customer->phone ?? '',
                    'info' => $customer->info ?? '',
                ];
            });
    }
}
