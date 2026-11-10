<?php

namespace App\Filament\Resources\SupplierOrders\Schemas;

use App\Models\Supplier\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

trait OrderSchema
{
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
                        collect(config('ecommerce.supplier_order_statuses'))
                            ->mapWithKeys(fn (array $status, string $key): array => [$key => $status['label']])
                            ->toArray()
                    )
                    ->extraInputAttributes(fn (callable $get) => [
                        'class' => 'text-status-'.$get('status'),
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        self::updateOrderItemsStatuses($state, $get, $set);
                    })
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
