<?php

namespace Tests\Feature;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use Tests\TestCase;

class OrderItemsVatTest extends TestCase
{
    #[DataProvider('vatStates')]
    public function test_toggling_vat_updates_every_item_and_runs_its_price_hooks(array $items, bool $expected): void
    {
        $get = $this->createMock(Get::class);
        $get->expects($this->once())
            ->method('__invoke')
            ->with('../../ordered_order_items')
            ->willReturn($items);

        $updates = [];
        $set = $this->createMock(Set::class);
        $set->expects($this->exactly(count($items)))
            ->method('__invoke')
            ->willReturnCallback(function (string $path, mixed $state, bool $isAbsolute = false, bool $shouldCallUpdatedHooks = false) use (&$updates): mixed {
                $this->assertTrue($shouldCallUpdatedHooks);
                $updates[$path] = $state;

                return $state;
            });

        (new ReflectionMethod(OrderForm::class, 'toggleOrderItemsVat'))->invoke(null, $get, $set);

        $this->assertSame(
            array_combine(
                array_map(fn (string $key): string => "../../ordered_order_items.{$key}.has_vat", array_keys($items)),
                array_fill(0, count($items), $expected),
            ),
            $updates,
        );
    }

    public static function vatStates(): array
    {
        return [
            'all selected' => [['record-1' => ['has_vat' => true], 'uuid-2' => ['has_vat' => true]], false],
            'none selected' => [['record-1' => ['has_vat' => false], 'uuid-2' => ['has_vat' => false]], true],
            'mixed selection' => [['record-1' => ['has_vat' => true], 'uuid-2' => ['has_vat' => false]], true],
            'empty repeater' => [[], false],
        ];
    }
}
