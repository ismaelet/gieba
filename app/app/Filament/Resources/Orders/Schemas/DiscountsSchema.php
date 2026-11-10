<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

trait DiscountsSchema
{
    protected static function getDiscountsSchema(): Fieldset
    {
        return Fieldset::make('Acconti e sconti')
            ->schema([
                TextInput::make('down_payment')
                    ->label('Acconto')
                    ->suffix('€')
                    ->default('0.00')
                    ->live(onBlur: true)
                    ->extraInputAttributes(self::extraAttributesForPrices())
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->dehydrateStateUsing(fn ($state) => self::parseToFloat($state))
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateOrderTotals(get: $get, set: $set);
                    }),

                Checkbox::make('down_payment_with_vat')
                    ->label('L\'acconto è comprensivo di IVA'),

                TextInput::make('fixed_discount')
                    ->label('Sconto fisso')
                    ->suffix('€')
                    ->default('0.00')
                    ->live(onBlur: true)
                    ->extraInputAttributes(self::extraAttributesForPrices())
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->dehydrateStateUsing(fn ($state) => self::parseToFloat($state))
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateOrderTotals(get: $get, set: $set);
                    }),

                Checkbox::make('discount_with_vat')
                    ->label('Lo sconto fisso è comprensivo di IVA'),

                TextInput::make('percentage_discount')
                    ->label('Sconto percentuale')
                    ->suffix('%')
                    ->numeric()
                    ->default('0')
                    ->live(onBlur: true)
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->dehydrateStateUsing(fn ($state) => self::parseToFloat($state))
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateOrderTotals(get: $get, set: $set);
                    }),
            ])->columns(1);
    }
}
