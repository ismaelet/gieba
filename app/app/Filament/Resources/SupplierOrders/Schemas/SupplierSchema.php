<?php

namespace App\Filament\Resources\SupplierOrders\Schemas;

use App\Models\Supplier\Supplier;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

trait SupplierSchema
{
    protected static function getSupplierSchema(): Fieldset
    {
        return Fieldset::make('Dati del fornitore')
            ->schema([
                Select::make('supplier_id')
                    ->label('Fornitore')
                    ->relationship('supplier', 'name')
                    ->searchable(['name'])
                    ->required()
                    ->suffixAction(
                        Action::make('copy')
                            ->icon(Heroicon::ClipboardDocumentList)
                            ->tooltip('Copia nome fornitore')
                            ->color('gray')
                            ->action(function (Get $get, $livewire) {
                                $livewire->dispatch('clipboard:copy', text: Supplier::whereKey($get('supplier_id'))->value('name'));

                                Notification::make()
                                    ->title('Copiato negli appunti')
                                    ->success()
                                    ->send();
                            })
                    )

                    // Supplier creation form
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome completo')
                            ->placeholder('Nome completo')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        if (Supplier::where('name', $data['name'])->exists()) {
                            Notification::make()
                                ->title('Errore: esiste già un fornitore con questo nome')
                                ->danger()
                                ->send();

                            return Supplier::where('name', $data['name'])->value('id');
                        }

                        $supplier = Supplier::create($data);

                        Notification::make()
                            ->title('Fornitore creato')
                            ->success()
                            ->send();

                        return $supplier->getKey();
                    })
                    ->createOptionAction(function ($action) {
                        return $action
                            ->label('Nuovo fornitore')
                            ->modalHeading('Crea fornitore')
                            ->modalSubmitActionLabel('Crea');
                    }),
            ])
            ->columns(1);
    }
}
