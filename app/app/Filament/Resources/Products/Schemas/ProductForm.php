<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome prodotto')
                    ->placeholder('Nome prodotto')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->label('Prezzo netto di default')
                    ->placeholder('Prezzo netto di default')
                    ->required()
                    ->default('0.00')
                    ->extraInputAttributes(OrderForm::extraAttributesForPrices()),
            ]);
    }
}
