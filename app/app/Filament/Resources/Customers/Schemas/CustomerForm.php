<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome cliente')
                    ->placeholder('Nome cliente')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->placeholder('Telefono')
                    ->maxLength(20),
                Textarea::make('info')
                    ->label('Dettagli cliente')
                    ->placeholder('Dettagli cliente')
                    ->autosize()
                    ->columnSpanFull(),
            ]);
    }
}
