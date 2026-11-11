<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

trait OrderSchema
{
    abstract private static function updateOrderItemsStatuses($state, Get $get, Set $set): void;

    protected static function getOrderSchema(): Fieldset
    {
        return Fieldset::make('Dati dell\'ordine')
            ->schema([
                TextInput::make('id')
                    ->label('Numero dell\'ordine')
                    ->placeholder('Numero dell\'ordine')
                    ->readOnly()
                    ->dehydrated(false)
                    ->visible(fn (?Order $record) => filled($record))
                    ->copyable(),

                DatePicker::make('created_at')
                    ->label('Data')
                    ->default(now())
                    ->readOnly()
                    ->columnSpan(fn (?Order $record) => filled($record) ? 1 : 2),

                Select::make('status')
                    ->label('Stato')
                    ->default('1')
                    ->options(
                        collect(config('ecommerce.order_statuses'))
                            ->mapWithKeys(fn (array $status, string $key): array => [$key => $status['label']])
                            ->toArray()
                    )
                    ->extraInputAttributes(fn () => ['class' => 'status-selector'])
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        self::updateOrderItemsStatuses($state, $get, $set);
                    })
                    ->live()
                    ->required()
                    ->columnSpanFull(),

                Checkbox::make('billed')
                    ->label('Fatturato'),
                Checkbox::make('print_descriptions')
                    ->label('Stampa dettagli articoli')
                    ->default(true),
                Checkbox::make('print_notes')
                    ->label('Stampa note'),
                Checkbox::make('print_status')
                    ->label('Stampa stato ordine'),
                Checkbox::make('always_print_prices')
                    ->label('Stampa i prezzi nella bolla'),
                Checkbox::make('print_total')
                    ->label('Stampa il totale')
                    ->default(true),
            ]);
    }
}
