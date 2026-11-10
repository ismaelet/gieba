<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Product;
use App\Models\Supplier\Order;
use App\Models\Supplier\OrderItem;
use App\Models\Supplier\Supplier;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

trait AddToSupplierButton
{
    protected static function getAddToSupplierButton(): Group
    {
        return Group::make()
            ->schema([
                Action::make('add_to_supplier')
                    ->label('Aggiungi a fornitore')
                    ->icon(Heroicon::Plus)
                    ->modalHeading('Aggiungi articoli ai fornitori')
                    ->modalSubmitActionLabel('Aggiungi')
                    ->modalWidth('7xl')

                    /**
                     * Schema
                     */
                    ->schema([
                        Repeater::make('lines')
                            ->label('Articoli')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(20)
                            ->default(function ($livewire): array {
                                $state = $livewire->form->getRawState();
                                $items = $state['ordered_order_items'] ?? [];

                                if (empty($items)) {
                                    return [];
                                }

                                $productIds = collect($items)->pluck('product_id')->filter()->unique()->values();

                                $namesById = $productIds->isEmpty()
                                    ? collect()
                                    : Product::query()->whereIn('id', $productIds)->pluck('name', 'id');

                                return collect($items)->map(function (array $item) use ($namesById) {
                                    $pid = $item['product_id'] ?? null;

                                    return [
                                        'order_item_id' => $item['id'] ?? null,
                                        'product_id' => $pid,
                                        'product_name' => $pid ? ($namesById[$pid] ?? ('#'.$pid)) : '-',
                                        'info' => $item['info'] ?? '',
                                        'quantity' => (int) ($item['quantity'] ?? 1),
                                        'supplier_id' => null,
                                    ];
                                })->values()->all();
                            })
                            ->schema([
                                Hidden::make('order_item_id'),
                                Hidden::make('product_id'),

                                TextInput::make('product_name')
                                    ->label('Prodotto')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(5),

                                TextInput::make('info')
                                    ->label('Info')
                                    ->columnSpan(7),

                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->columnSpan(2),

                                Select::make('supplier_id')
                                    ->label('Fornitore')
                                    ->options(fn () => Supplier::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(6),
                            ]),
                    ])

                    /**
                     * Action handler
                     */
                    ->action(function (array $data): void {
                        $lines = $data['lines'] ?? [];

                        // keep only lines that have a supplier selected
                        $lines = collect($lines)
                            ->filter(fn ($l) => ! empty($l['supplier_id']))
                            ->values();

                        if ($lines->isEmpty()) {
                            Notification::make()
                                ->title('Seleziona almeno un fornitore')
                                ->warning()
                                ->send();

                            return;
                        }

                        DB::transaction(function () use ($lines) {
                            $bySupplier = $lines->groupBy('supplier_id');

                            foreach ($bySupplier as $supplierId => $supplierLines) {

                                $supplierOrder = Order::firstOrCreate([
                                    'supplier_id' => $supplierId,
                                ], []);

                                foreach ($supplierLines as $line) {
                                    OrderItem::create([
                                        'supplier_order_id' => $supplierOrder->getKey(),
                                        'product_id' => (int) $line['product_id'],
                                        'quantity' => (int) $line['quantity'],
                                        'status' => 1,
                                        'info' => $line['info'] ?? '',
                                    ]);
                                }
                            }
                        });

                        Notification::make()
                            ->title('Articoli aggiunti ai fornitori')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
