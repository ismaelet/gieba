<?php

namespace Tests\Feature;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Models\OrderItem;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use Tests\TestCase;

class OrderItemQuantityTest extends TestCase
{
    #[DataProvider('invalidQuantities')]
    public function test_unit_price_calculations_reject_invalid_quantities(mixed $quantity): void
    {
        $item = new OrderItem([
            'quantity' => $quantity,
            'has_vat' => true,
            'total_price_gross' => '28.67',
        ]);

        foreach (['calcUnitPriceNet', 'calcUnitPriceGross', 'recalculatePrices'] as $method) {
            try {
                $item->{$method}();
                $this->fail("{$method} accepted an invalid quantity.");
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('quantity', $exception->errors());
            }
        }

        $this->assertNull($item->unit_price_net);
        $this->assertNull($item->unit_price_gross);
    }

    #[DataProvider('invalidQuantities')]
    public function test_order_form_rejects_invalid_quantities(mixed $quantity): void
    {
        $rules = null;

        DB::pretend(function () use (&$rules): void {
            $repeater = (new ReflectionMethod(OrderForm::class, 'getOrderItemsSchema'))->invoke(null);
            $repeater->container(Schema::make());

            foreach ($repeater->getDefaultChildComponents() as $field) {
                if ($field->getName() === 'quantity') {
                    $field->container(Schema::make());
                    $rules = $field->getValidationRules();
                }
            }
        });

        $this->assertNotNull($rules);
        $this->assertTrue(Validator::make(['quantity' => $quantity], ['quantity' => $rules])->fails());
        $this->assertTrue(Validator::make(['quantity' => '7'], ['quantity' => $rules])->passes());
    }

    public function test_valid_quantities_preserve_price_calculations_with_and_without_vat(): void
    {
        $item = new OrderItem(['quantity' => '7', 'has_vat' => true, 'total_price_gross' => '200.69']);

        $this->assertEqualsWithDelta(23.50, $item->calcUnitPriceNet(), 0.00001);
        $this->assertEqualsWithDelta(28.67, $item->calcUnitPriceGross(), 0.00001);

        $item->has_vat = false;

        $this->assertEqualsWithDelta(28.67, $item->calcUnitPriceNet(), 0.00001);
        $this->assertEqualsWithDelta(28.67, $item->calcUnitPriceGross(), 0.00001);
    }

    public static function invalidQuantities(): array
    {
        return [
            'reported zero quantity' => [0],
            'zero as form string' => ['0'],
            'negative quantity' => [-1],
            'missing quantity' => [null],
            'empty quantity' => [''],
            'fractional quantity' => ['1.5'],
        ];
    }
}
