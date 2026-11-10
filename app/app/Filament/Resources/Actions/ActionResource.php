<?php

namespace App\Filament\Resources\Actions;

use App\Filament\Resources\Actions\Pages\ListActions;
use App\Models\Action;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActionResource extends Resource
{
    /**
     * Property: The model the resource corresponds to
     */
    protected static ?string $model = Action::class;

    /**
     * The label of a singular resource
     */
    protected static ?string $label = 'Azione';

    /**
     * The label of a plural resource
     */
    protected static ?string $pluralLabel = 'Storico';

    /**
     * Property: The navigation icon
     */
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedClock;

    /**
     * Property: The navigation sort order
     */
    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.username')
                    ->label('Utente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Data')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Azione')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(100);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActions::route('/'),
        ];
    }
}
