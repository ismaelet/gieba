<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;

trait OrderItemsSchema
{
    abstract public static function updateOrderStatus(Get $get, Set $set): void;

    abstract private static function parseNumber(?string $number): string;

    abstract private static function parseToFloat(?string $number): float;

    abstract public static function extraAttributesForPrices(): array;

    abstract public static function extraAttributesForInt(): array;

    abstract private static function updatePrices(Get $get, Set $set, ?string $fieldName = null, ?float $unitPriceNet = null): void;

    abstract private static function updateOrderTotals(Get $get, Set $set): void;

    protected static function getOrderItemsSchema(): Repeater
    {
        return Repeater::make('ordered_order_items')
            ->label('Articoli dell\'ordine')
            ->addActionLabel('+')
            ->addAction(
                fn (Action $action) => $action
                    ->requiresConfirmation()
                    ->modalHidden(fn (Get $get): bool => ! $get('billed'))
                    ->modalHeading('Aggiungi articolo')
                    ->modalDescription('L\'ordine è già fatturato. Sei sicuro di voler aggiungere un nuovo articolo?')
                    ->modalSubmitActionLabel('Aggiungi')
                    ->modalCancelActionLabel('Annulla')
            )
            ->relationship()
            ->afterStateUpdated(function (Get $get, Set $set) {
                self::updateOrderStatus($get, $set);
                self::updateOrderTotals($get, $set);
            })
            ->extraAttributes(['class' => 'order-repeater-item'])
            ->schema([
                Hidden::make('real_unit_price_net')
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Set $set, ?OrderItem $record) {
                        $set('real_unit_price_net', $record?->unit_price_net);
                    }),

                Select::make('product_id')
                    ->label('Prodotto')
                    ->options(
                        Product::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        if ((float) $get('unit_price_net') > 0) {
                            return;
                        }

                        $product = filled($state) ? Product::find($state) : null;
                        $customer = Customer::find($get('../../customer_id'));
                        $unitPriceNet = 0;

                        if ($customer && $product) {
                            $unitPriceNet = (float) OrderItem::where('product_id', $state)
                                ->whereHas('order.customer', fn ($query) => $query->whereKey($customer->getKey()))
                                ->orderByDesc('created_at')
                                ->value('unit_price_net');
                        }

                        if (! $unitPriceNet && $product) {
                            $unitPriceNet = $product->price;
                        }

                        self::updatePrices(get: $get, set: $set, unitPriceNet: $unitPriceNet);
                    })
                    ->columnSpan(9)

                    // Copy product name action
                    ->suffixAction(
                        Action::make('copy')
                            ->icon(Heroicon::ClipboardDocumentList)
                            ->tooltip('Copia nome prodotto')
                            ->color('gray')
                            ->action(function (Get $get, $livewire) {
                                $livewire->dispatch('clipboard:copy', text: Product::whereKey($get('product_id'))->value('name'));

                                Notification::make()
                                    ->title('Copiato negli appunti')
                                    ->success()
                                    ->send();
                            })
                    )

                    // Create product option
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome prodotto')
                            ->placeholder('Nome prodotto')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        if (Product::where('name', $data['name'])->exists()) {
                            Notification::make()
                                ->title('Errore: esiste già un prodotto con questo nome')
                                ->danger()
                                ->send();

                            return Product::where('name', $data['name'])->value('id');
                        }

                        $product = Product::create($data);

                        Notification::make()
                            ->title('Prodotto creato')
                            ->success()
                            ->send();

                        return $product->getKey();
                    })
                    ->createOptionAction(function ($action) {
                        return $action
                            ->label('Nuovo prodotto')
                            ->modalHeading('Crea prodotto')
                            ->modalSubmitActionLabel('Crea');
                    }),

                // Product info
                TextInput::make('info')
                    ->label('Dettagli')
                    ->placeholder('Informazioni aggiuntive sul prodotto')
                    ->autocomplete(false)
                    ->extraInputAttributes([
                        'x-on:mouseenter' => '$el.title = $el.value',
                        'x-on:focus' => '$el.title = $el.value',
                        'x-on:input' => '$el.title = $el.value',
                    ])
                    ->columnSpan(8),

                TextInput::make('quantity')
                    ->label('Qty')
                    ->default(1)
                    ->autocomplete(false)
                    ->live(onBlur: true)
                    ->extraInputAttributes(self::extraAttributesForInt())
                    ->afterStateUpdated(fn (TextInput $component, Get $get, Set $set) => self::updatePrices(
                        get: $get,
                        set: $set,
                        fieldName: $component->getName()
                    ))
                    ->columnSpan(2),

                Select::make('status')
                    ->label('Stato')
                    ->default(function (Get $get) {
                        return $get('../../status') ?? '1';
                    })
                    ->live()
                    ->options(
                        collect(config('ecommerce.order_statuses'))
                            ->mapWithKeys(fn (array $status, string $key): array => [$key => $status['label']])
                            ->toArray()
                    )
                    ->extraInputAttributes(fn () => ['class' => 'status-selector'])
                    ->afterStateUpdated(fn (callable $set, callable $get) => self::updateOrderStatus($get, $set))
                    ->required()
                    ->columnSpan(4),

                TextInput::make('unit_price_net')
                    ->label('Pr. netto')
                    ->default(0)
                    ->autocomplete(false)
                    ->live(onBlur: true)
                    ->extraInputAttributes(self::extraAttributesForPrices())
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->dehydrateStateUsing(fn ($state) => self::parseToFloat($state))
                    ->afterStateUpdated(
                        fn (TextInput $component, Get $get, Set $set) => self::updatePrices(
                            get: $get,
                            set: $set,
                            fieldName: $component->getName()
                        )
                    )
                    ->columnSpan(2),

                TextInput::make('unit_price_gross')
                    ->label('Pr. ivato')
                    ->default(0)
                    ->autocomplete(false)
                    ->live(onBlur: true)
                    ->extraInputAttributes(self::extraAttributesForPrices())
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->dehydrateStateUsing(fn ($state) => self::parseToFloat($state))
                    ->afterStateUpdated(
                        fn (TextInput $component, Get $get, Set $set) => self::updatePrices(
                            get: $get,
                            set: $set,
                            fieldName: $component->getName()
                        )
                    )
                    ->columnSpan(2),

                Checkbox::make('has_vat')
                    ->label('IVA')
                    ->extraFieldWrapperAttributes(['class' => 'order-vat-field'])
                    ->aboveLabel(
                        Action::make('toggleAllVat')
                            ->label('IVA')
                            ->link()
                            ->color('gray')
                            ->tooltip('Attiva o disattiva IVA per tutti gli articoli')
                            ->action(fn (Get $get, Set $set) => self::toggleOrderItemsVat($get, $set))
                    )
                    ->default(true)
                    ->live()
                    ->afterStateUpdated(
                        fn (Checkbox $component, Get $get, Set $set) => self::updatePrices(
                            get: $get,
                            set: $set,
                            fieldName: $component->getName()
                        )
                    )
                    ->columnSpan(1),

                TextInput::make('total_price_gross')
                    ->label('Totale')
                    ->default(0)
                    ->autocomplete(false)
                    ->live(onBlur: true)
                    ->extraInputAttributes(self::extraAttributesForPrices())
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->dehydrateStateUsing(fn ($state) => self::parseToFloat($state))
                    ->afterStateUpdated(
                        fn (TextInput $component, Get $get, Set $set) => self::updatePrices(
                            get: $get,
                            set: $set,
                            fieldName: $component->getName()
                        )
                    )
                    ->columnSpan(2),
            ])
            ->columns(30)
            ->columnSpanFull()
            ->defaultItems(1)
            ->required();
    }

    protected static function toggleOrderItemsVat(Get $get, Set $set): void
    {
        $items = $get('../../ordered_order_items') ?? [];
        $hasVat = collect($items)->contains(fn (array $item): bool => empty($item['has_vat']));

        foreach ($items as $key => $item) {
            $set("../../ordered_order_items.{$key}.has_vat", $hasVat, shouldCallUpdatedHooks: true);
        }
    }
}
