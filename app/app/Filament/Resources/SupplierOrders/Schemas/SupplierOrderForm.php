<?php

namespace App\Filament\Resources\SupplierOrders\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SupplierOrderForm
{
    use OrderItemsSchema, OrderSchema, SupplierSchema;

    /**
     * Property: The VAT rate used for calculations
     */
    private const VAT = 0.22;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Supplier data fieldset
                self::getSupplierSchema(),

                // Order data fieldset
                self::getOrderSchema(),

                // Order items repeater
                self::getOrderItemsSchema(),
            ]);
    }

    /**
     * Update the order item statuses based on the order status
     */
    private static function updateOrderItemsStatuses($state, Get $get, Set $set): void
    {
        $items = $get(self::getElement($get, 'order_items')) ?? [];

        // Update every repeater item's status
        $items = array_map(function (array $item) use ($state) {
            $item['status'] = $state;

            return $item;
        }, $items);

        $set('order_items', $items);
    }

    /**
     * Update the order status based on the order item statuses
     */
    public static function updateOrderStatus(Get $get, Set $set): void
    {
        $items = $get(self::getElement($get, 'order_items')) ?? [];
        $statusElement = '../../status';
        $statuses = [];

        foreach ($items as $item) {
            if (! array_key_exists('status', $item)) {
                continue;
            }

            $value = $item['status'];

            if ($value === null || $value === '') {
                continue;
            }

            $statuses[] = (int) $value;
        }

        if ($statuses === []) {
            return;
        }

        $min = (string) min($statuses);

        if ((string) $get($statusElement) === $min) {
            return;
        }

        $set($statusElement, $min);
    }

    /**
     * Get an element value trying different relative paths
     */
    private static function getElement(Get $get, string $elementName): ?string
    {
        foreach (['', '../', '../../', '../../../'] as $prefix) {
            $element = $get($prefix.$elementName);

            if ($element !== null) {
                return $prefix.$elementName;
            }
        }

        return $elementName;
    }
}
