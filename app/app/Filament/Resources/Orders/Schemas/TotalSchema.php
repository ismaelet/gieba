<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;

trait TotalSchema
{
    protected static function getTotalSchema(): Group
    {
        return Group::make()
            ->extraAttributes(['class' => 'total-panel'])
            ->schema([
                TextInput::make('price_net')
                    ->label('Imponibile')
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->dehydrated(false)
                    ->readOnly(),

                TextInput::make('vat')
                    ->label('IVA')
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->dehydrated(false)
                    ->readOnly(),

                TextInput::make('down_payment')
                    ->label('Acconto')
                    ->visible(fn ($state) => self::parseToFloat($state) > 0)
                    ->dehydrated(false)
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->readOnly(),

                TextInput::make('total_discount')
                    ->label('Sconto')
                    ->visible(fn ($state) => self::parseToFloat($state) > 0)
                    ->dehydrated(false)
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->readOnly(),

                TextInput::make('price_gross')
                    ->label('Totale a pagare')
                    ->dehydrated(false)
                    ->afterStateHydrated(fn ($state, TextInput $component) => $component->state(self::parseNumber($state)))
                    ->readOnly(),
            ])->columns(5)
            ->columnSpanFull();
    }
}
