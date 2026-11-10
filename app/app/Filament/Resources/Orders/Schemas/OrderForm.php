<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OrderForm
{
    use AddToSupplierButton, CustomerSchema, DiscountsSchema, OrderItemsSchema, OrderSchema, TotalSchema;

    /**
     * Property: The VAT rate used for calculations
     */
    private const VAT = 0.22;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Customer data fieldset
                self::getCustomerSchema(),

                // Order data fieldset
                self::getOrderSchema(),

                // Discounts and down payment fieldset
                self::getDiscountsSchema(),

                // Add to supplier button
                self::getAddToSupplierButton(),

                // Order items repeater
                self::getOrderItemsSchema(),

                // Order totals panel
                self::getTotalSchema(),
            ]);
    }

    /**
     * Update the order item prices based on the changed field
     */
    private static function updatePrices(Get $get, Set $set, ?string $fieldName = null, ?float $unitPriceNet = null): void
    {
        $quantity = max(1, (int) $get('quantity'));
        $hasVat = (bool) $get('has_vat');
        $vatMul = 1 + self::VAT;

        $toNet = fn (float $gross): float => $hasVat ? ($gross / $vatMul) : $gross;
        $toGross = fn (float $net): float => $hasVat ? ($net * $vatMul) : $net;

        $unitPriceNet = $unitPriceNet ?? match ($fieldName) {
            'unit_price_net' => self::parseNumber($get('unit_price_net')),
            'unit_price_gross' => $toNet(self::parseNumber($get('unit_price_gross'))),
            'total_price_gross' => $toNet(self::parseNumber($get('total_price_gross')) / $quantity),
            default => (float) $get('real_unit_price_net'),
        };

        $set('real_unit_price_net', $unitPriceNet);

        $unitPriceGross = $toGross($unitPriceNet);
        $totalGross = $unitPriceGross * $quantity;

        $set('unit_price_net', self::parseNumber($unitPriceNet));
        $set('unit_price_gross', self::parseNumber($unitPriceGross));
        $set('total_price_gross', self::parseNumber($totalGross));

        self::updateOrderTotals(get: $get, set: $set);
    }

    /**
     * Update the order totals based on the order items
     */
    private static function updateOrderTotals(Get $get, Set $set): void
    {
        $items = $get(self::getElement($get, 'ordered_order_items')) ?? [];

        $sumNet = 0.0;
        $sumGross = 0.0;

        foreach ($items as $item) {
            $qty = max(1, (int) ($item['quantity'] ?? 1));

            $unitNet = (float) ($item['real_unit_price_net'] ?? 0);
            $unitGross = ! empty($item['has_vat']) ? $unitNet * (1 + self::VAT) : $unitNet;

            $sumNet += $unitNet * $qty;
            $sumGross += $unitGross * $qty;
        }

        // Discounts
        $downPayment = self::parseToFloat($get(self::getElement($get, 'down_payment')));
        $fixedDiscount = self::parseToFloat($get(self::getElement($get, 'fixed_discount')));
        $percentageDiscount = self::parseToFloat($get(self::getElement($get, 'percentage_discount')));

        $totalDiscount = $fixedDiscount + ($sumGross * ($percentageDiscount / 100));
        $finalGross = $sumGross - $totalDiscount - $downPayment;

        // Set totals
        $set(self::getElement($get, 'price_net'), self::parseNumber((string) $sumNet));
        $set(self::getElement($get, 'vat'), self::parseNumber((string) ($sumGross - $sumNet)));
        $set(self::getElement($get, 'down_payment'), self::parseNumber((string) $downPayment));
        $set(self::getElement($get, 'total_discount'), self::parseNumber((string) $totalDiscount));
        $set(self::getElement($get, 'price_gross'), self::parseNumber((string) $finalGross));
    }

    /**
     * Update the order item statuses based on the order status
     */
    private static function updateOrderItemsStatuses($state, Get $get, Set $set): void
    {
        $items = $get(self::getElement($get, 'ordered_order_items')) ?? [];

        // Update every repeater item's status
        $items = array_map(function (array $item) use ($state) {
            $item['status'] = $state;

            return $item;
        }, $items);

        $set('ordered_order_items', $items);
    }

    /**
     * Update the order status based on the order item statuses
     */
    public static function updateOrderStatus(Get $get, Set $set): void
    {
        $items = $get(self::getElement($get, 'ordered_order_items')) ?? [];
        $statusElement = self::getParentElement($get, 'status');
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

    /**
     * Get the parent element path for a given element, trying different relative paths
     */
    private static function getParentElement(Get $get, string $elementName): string
    {
        foreach (['../', '../../', '../../../', '../../../../'] as $prefix) {
            $path = $prefix.$elementName;

            if ($get($path) !== null) {
                return $path;
            }
        }

        return $elementName;
    }

    /**
     * Sanitize and format a number to 2 decimal places string
     */
    private static function parseNumber(?string $number): string
    {
        return number_format((float) self::sanitizeNumber($number), 2, '.', '');
    }

    /**
     * Sanitize and convert a number to float
     */
    private static function parseToFloat(?string $number): float
    {
        return (float) self::sanitizeNumber($number);
    }

    /**
     * Sanitize a number string, keeping only valid characters
     */
    private static function sanitizeNumber(?string $number): string
    {
        $value = str_replace(',', '.', $number ?? '0');

        // keep digits, dot and minus
        $value = preg_replace('/[^0-9\.-]/', '', $value);

        // allow only ONE minus, and only at the beginning
        $value = preg_replace('/(?!^)-/', '', $value);

        // allow only ONE dot
        $value = preg_replace('/(\..*)\./', '$1', $value);

        return $value;
    }

    /**
     * Extra attributes for price input fields
     */
    public static function extraAttributesForPrices(): array
    {
        return [
            'type' => 'text',
            'inputmode' => 'decimal',
            'oninput' => "
            let v = this.value
                .replace(/,/g, '.')
                .replace(/[^0-9.-]/g, '')
                .replace(/(?!^)-/g, '')
                .replace(/(\..*)\./g, '$1');

            const parts = v.split('.');

            if (parts.length === 2) {
                parts[1] = parts[1].slice(0, 2);
            }

            this.value = parts.join('.');
        ",
        ];
    }

    /**
     * Extra attributes for integer input fields
     */
    public static function extraAttributesForInt(): array
    {
        return [
            'type' => 'text',
            'inputmode' => 'numeric',
            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '');",
        ];
    }
}
