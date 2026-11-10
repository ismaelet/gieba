<?php

namespace App\Filament\Resources\SupplierOrders\Schemas;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

trait OrderItemsSchema
{
    protected static function getOrderItemsSchema(): Repeater
    {
        return Repeater::make('order_items')
            ->label('Articoli dell\'ordine')
            ->addActionLabel('+')
            ->relationship()
            ->extraAttributes(['class' => 'order-repeater-item'])
            ->schema([
                Select::make('product_id')
                    ->label('Prodotto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->required()
                    ->columnSpan(2)

                    // Copy product name action
                    ->suffixAction(
                        Action::make('copy')
                            ->icon(Heroicon::ClipboardDocumentList)
                            ->tooltip('Copia nome prodotto')
                            ->color('gray')
                            ->action(function (Get $get, $livewire) {
                                $livewire->dispatch('clipboard:copy', text: Product::whereKey($get('product_id'))->value('name'));

                                Notification::make()
                                    ->title('Copiato negli appunti')
                                    ->success()
                                    ->send();
                            })
                    )

                    // Create product option
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome prodotto')
                            ->placeholder('Nome prodotto')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        if (Product::where('name', $data['name'])->exists()) {
                            Notification::make()
                                ->title('Errore: esiste già un prodotto con questo nome')
                                ->danger()
                                ->send();

                            return Product::where('name', $data['name'])->value('id');
                        }

                        $product = Product::create($data);

                        Notification::make()
                            ->title('Prodotto creato')
                            ->success()
                            ->send();

                        return $product->getKey();
                    })
                    ->createOptionAction(function ($action) {
                        return $action
                            ->label('Nuovo prodotto')
                            ->modalHeading('Crea prodotto')
                            ->modalSubmitActionLabel('Crea');
                    }),

                // Product info
                TextInput::make('info')
                    ->label('Dettagli')
                    ->placeholder('Informazioni aggiuntive sul prodotto')
                    ->extraInputAttributes([
                        'x-on:mouseenter' => '$el.title = $el.value',
                        'x-on:focus' => '$el.title = $el.value',
                        'x-on:input' => '$el.title = $el.value',
                    ])
                    ->columnSpan(3),

                TextInput::make('quantity')
                    ->label('Qty')
                    ->default(1)
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Stato')
                    ->default(function (Get $get) {
                        return $get('../../status') ?? '1';
                    })
                    ->live()
                    ->options(
                        collect(config('ecommerce.supplier_order_statuses'))
                            ->mapWithKeys(fn (array $status, string $key): array => [$key => $status['label']])
                            ->toArray()
                    )
                    ->extraInputAttributes(fn (callable $get) => ['class' => 'text-status-'.$get('status')])
                    ->afterStateUpdated(fn (callable $set, callable $get) => self::updateOrderStatus($get, $set))
                    ->required()
                    ->columnSpan(2),
            ])
            ->columns(8)
            ->columnSpanFull()
            ->defaultItems(1)
            ->required();
    }
}
