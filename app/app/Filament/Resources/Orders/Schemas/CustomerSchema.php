<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Customer;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;

trait CustomerSchema
{
    protected static function getCustomerSchema(): Fieldset
    {
        return Fieldset::make('Dati del cliente')
            ->schema([
                Select::make('customer_id')
                    ->label('Cliente')
                    ->relationship('customer', 'name')
                    ->searchable(['name', 'phone'])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (
                        $state,
                        $old,
                        ?Order $record,
                        Get $get,
                        Set $set,
                        $livewire,
                    ): void {
                        if (blank($state)) {
                            $set('customer_data', [
                                'phone' => '',
                                'info' => '',
                            ]);

                            return;
                        }

                        $hasOpenOrder = Order::query()
                            ->where('customer_id', $state)
                            ->where('billed', false)
                            ->whereNotIn('status', [6, 7, 8, 99])
                            ->when(
                                $record,
                                fn ($query) => $query->whereKeyNot($record->getKey()),
                            )
                            ->exists();

                        if ($hasOpenOrder) {
                            // Restore the previous selection until the user confirms.
                            $set('customer_id', $old);

                            $previousCustomer = filled($old)
                                ? Customer::find($old)
                                : null;

                            $set('customer_data', [
                                'phone' => $previousCustomer?->phone ?? '',
                                'info' => $previousCustomer?->info ?? '',
                            ]);

                            $livewire->mountAction('confirmCustomerSelection', [
                                'customerId' => $state,
                            ]);

                            return;
                        }

                        $customer = Customer::find($state);

                        $set('customer_data', [
                            'phone' => $customer?->phone ?? '',
                            'info' => $customer?->info ?? '',
                        ]);
                    })
                    ->suffixAction(
                        Action::make('copy')
                            ->icon(Heroicon::ClipboardDocumentList)
                            ->tooltip('Copia nome cliente')
                            ->color('gray')
                            ->action(function (Get $get, $livewire) {
                                $livewire->dispatch('clipboard:copy', text: Customer::whereKey($get('customer_id'))->value('name'));

                                Notification::make()
                                    ->title('Copiato negli appunti')
                                    ->success()
                                    ->send();
                            })
                    )

                    // Customer creation form
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome completo')
                            ->placeholder('Nome completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Numero di telefono')
                            ->placeholder('Numero di telefono')
                            ->maxLength(50),

                        Textarea::make('info')
                            ->label('Dettagli cliente')
                            ->placeholder('Dettagli cliente')
                            ->autosize(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        if (Customer::where('name', $data['name'])->exists()) {
                            Notification::make()
                                ->title('Errore: esiste già un cliente con questo nome')
                                ->danger()
                                ->send();

                            return Customer::where('name', $data['name'])->value('id');
                        }

                        $customer = Customer::create($data);

                        Notification::make()
                            ->title('Cliente creato')
                            ->success()
                            ->send();

                        return $customer->getKey();
                    })
                    ->createOptionAction(function ($action) {
                        return $action
                            ->label('Nuovo cliente')
                            ->modalHeading('Crea cliente')
                            ->modalSubmitActionLabel('Crea');
                    }),

                Group::make()
                    ->statePath('customer_data')
                    ->visible(fn (Get $get) => filled($get('customer_id')))
                    ->schema([
                        TextInput::make('phone')
                            ->label('Numero di telefono')
                            ->placeholder('Numero di telefono')
                            ->afterStateHydrated(fn (?Order $record, Set $set) => $set('phone', $record?->customer?->phone ?? ''))
                            ->copyable(),
                        Textarea::make('info')
                            ->label('Dettagli cliente')
                            ->placeholder('Dettagli cliente')
                            ->autosize()
                            ->afterStateHydrated(fn (?Order $record, Set $set) => $set('info', $record?->customer?->info ?? '')),
                    ]),
            ])
            ->columns(1)
            ->columnSpanFull();
    }
}
