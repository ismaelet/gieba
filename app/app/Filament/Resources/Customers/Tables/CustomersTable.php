<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Models\Customer;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('info')
                    ->label('Info')
                    ->sortable()
                    ->wrap()
                    ->searchable()
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),

                DeleteAction::make(),

                Action::make('merge')
                    ->label('Unisci')
                    ->color('info')
                    ->icon(Heroicon::ArrowsPointingIn)
                    ->modalSubmitActionLabel('Unisci clienti')
                    ->schema(fn (Customer $record) => [
                        Select::make('target_customer_id')
                            ->label('Unisci con cliente')
                            ->aboveContent('Il cliente che verrà mantenuto è quello selezionato qui sotto.')
                            ->searchable()
                            ->getSearchResultsUsing(
                                fn (string $search) => Customer::query()
                                    ->whereKeyNot($record->id)
                                    ->where('name', 'like', '%'.$search.'%')
                                    ->orWhere('id', $search)
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->getOptionLabelUsing(
                                fn ($value) => Customer::find($value)?->name ?? (string) $value
                            )
                            ->required(),
                    ])
                    ->action(function (Customer $record, array $data): void {
                        Order::where('customer_id', $record->id)
                            ->update([
                                'customer_id' => $data['target_customer_id'],
                            ]);

                        $record->delete();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
