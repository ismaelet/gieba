<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Tables\Columns\OrderItemsColumn;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            /**
             * Query
             */
            ->query(
                Order::query()
                    ->with([
                        'customer',
                        'ordered_order_items',
                    ])
            )

            /**
             * Columns
             */
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->extraAttributes(fn (Order $record): array => [
                        'id' => 'order-id-'.$record->id,
                    ])
                    ->searchable(),

                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                TextColumn::make('customer.phone')
                    ->label('Telefono')
                    ->wrap()
                    ->copyable()
                    ->copyMessage('Numero copiato')
                    ->copyMessageDuration(1500)
                    ->toggleable()
                    ->toggledHiddenByDefault(false),

                OrderItemsColumn::make('order_items.product.name')
                    ->searchable()
                    ->label('Articoli'),

                TextColumn::make('price_gross')
                    ->label('Totale')
                    ->sortable()
                    ->money('eur')
                    ->default(0)
                    ->toggleable()
                    ->toggledHiddenByDefault(false),

                TextColumn::make('status')
                    ->label('Stato')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => config('ecommerce.order_statuses')[$state]['label'] ?? $state)
                    ->color(fn (int $state): string => 'status-'.$state)
                    ->toggleable()
                    ->toggledHiddenByDefault(false),

                ToggleColumn::make('billed')
                    ->label('Fatturato')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(false),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->sortable()
                    ->dateTime('d/m/Y')
                    ->toggleable()
                    ->toggledHiddenByDefault(false),
            ])
            ->searchable(['customer.info', 'customer.phone', 'order_items.info', 'price_gross'])

            /**
             * Sort
             */
            ->defaultSort('id', 'desc')

            /**
             * Pagination
             */
            ->defaultPaginationPageOption(50)

            /**
             * Filters
             */
            ->filters([
                // status filter
                Filter::make('status')
                    ->schema([
                        CheckboxList::make('statuses')
                            ->label('Stato')
                            ->options(
                                collect(config('ecommerce.order_statuses'))
                                    ->mapWithKeys(fn ($status, $key) => [$key => $status['label']])
                                    ->union([
                                        1001 => 'Fatturato',
                                        1002 => 'Non fatturato',
                                    ])
                            )
                            ->columns(4)
                            ->default([1, 2, 3, 4, 5, 6, 1001, 1002]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $statuses = array_values(array_filter($data['statuses'] ?? []));

                        // numeric statuses
                        $realStatuses = array_filter($statuses, fn ($s) => $s < 1000);

                        if ($realStatuses === []) {
                            $query->where(function (Builder $q) {
                                $q->whereNull('orders.status');
                            });
                        } else {
                            $query->where(function (Builder $q) use ($realStatuses) {
                                $q->whereIn('orders.status', $realStatuses)
                                    ->orWhereExists(function (QueryBuilder $sub) use ($realStatuses) {
                                        $sub->selectRaw('1')
                                            ->from('order_items')
                                            ->whereColumn('order_items.order_id', 'orders.id')
                                            ->whereIn('order_items.status', $realStatuses);
                                    });
                            });
                        }

                        // billed / not billed
                        if (in_array(1001, $statuses) && ! in_array(1002, $statuses)) {
                            $query->where('billed', true);
                        }

                        if (in_array(1002, $statuses) && ! in_array(1001, $statuses)) {
                            $query->where('billed', false);
                        }
                    })->columnSpan(2),

                // Customer filter
                SelectFilter::make('customer')
                    ->label('Cliente')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->columnSpan(2),

                // Creation date filter
                Filter::make('created_at')
                    ->label('Data creazione')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Da'),

                        DatePicker::make('until')
                            ->label('A'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                // Price filter
                Filter::make('price')
                    ->schema([
                        Group::make([
                            TextInput::make('price_from')
                                ->label('Prezzo minimo')
                                ->placeholder('0')
                                ->suffix('€')
                                ->numeric(),

                            TextInput::make('price_until')
                                ->label('Prezzo massimo')
                                ->placeholder('0')
                                ->suffix('€')
                                ->numeric(),
                        ])->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'] ?? null,
                                fn (Builder $query, $price) => $query->where('price_gross', '>=', $price)
                            )
                            ->when(
                                $data['price_until'] ?? null,
                                fn (Builder $query, $price) => $query->where('price_gross', '<=', $price)
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->deferFilters(false)

            /**
             * Actions
             */
            ->recordActions([
                ActionGroup::make([
                    Action::make('download-quote')
                        ->label('Scarica preventivo')
                        ->icon(Heroicon::ArrowDownTray)
                        ->color('info')
                        ->modalHeading('Scarica preventivo')
                        ->modalSubmitActionLabel('Scarica')
                        ->schema([
                            Toggle::make('use_order_date')
                                ->label('Usa la data di creazione dell’ordine')
                                ->helperText('Se disattivato, verrà utilizzata la data odierna.')
                                ->default(false),
                        ])
                        ->action(function (
                            Order $record,
                            array $data,
                            ListOrders $livewire,
                        ): void {
                            $url = route('admin.orders.quote.download', [
                                'order' => $record->id,
                                'date_source' => ($data['use_order_date'] ?? false)
                                    ? 'order'
                                    : 'today',
                            ]);

                            $livewire->js(
                                'window.open('.json_encode($url).', "_blank")',
                            );
                        }),

                    Action::make('download-delivery-note')
                        ->label('Scarica bolla')
                        ->icon(Heroicon::ArrowDownTray)
                        ->color('indigo')
                        ->modalHeading('Scarica bolla')
                        ->modalSubmitActionLabel('Scarica')
                        ->schema([
                            Toggle::make('use_order_date')
                                ->label('Usa la data di creazione dell’ordine')
                                ->helperText('Se disattivato, verrà utilizzata la data odierna.')
                                ->default(false),
                        ])
                        ->action(function (
                            Order $record,
                            array $data,
                            ListOrders $livewire,
                        ): void {
                            $url = route('admin.orders.delivery-note.download', [
                                'order' => $record->id,
                                'date_source' => ($data['use_order_date'] ?? false)
                                    ? 'order'
                                    : 'today',
                            ]);

                            $livewire->js(
                                'window.open('.json_encode($url).', "_blank")',
                            );
                        }),

                    Action::make('merge')
                        ->label('Unisci a quest\'ordine')
                        ->color('amber')
                        ->icon(Heroicon::ArrowsPointingIn)
                        ->modalSubmitActionLabel('Unisci ordini')
                        ->schema(fn (Order $record) => [
                            Select::make('target_order_id')
                                ->label('Unisci con ordine')
                                ->aboveContent('Gli aricoli dell\'ordine selezionato qui sotto verranno tutti trasferiti in quest\'ordine. I dati che verranno mantenuti saranno quelli di quest\'ordine, non quelli dell\'ordine selezionato sotto.')
                                ->searchable()
                                ->getSearchResultsUsing(
                                    fn (string $search) => Order::query()
                                        ->whereKeyNot($record->id)
                                        ->where('id', $search)
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn (Order $order) => [
                                            $order->id => 'Ordine '.$order->idCode().' ('.($order->customer?->name ?? '—').')',
                                        ])
                                        ->toArray()
                                )
                                ->getOptionLabelUsing(function ($value) {
                                    $order = Order::with('customer')->find($value);

                                    return $order
                                        ? 'Ordine '.$order->idCode().' ('.($order->customer?->name ?? '—').')'
                                        : (string) $value;
                                })
                                ->required(),
                        ])
                        ->action(function (Order $record, array $data): void {
                            DB::transaction(function () use ($record, $data): void {
                                $targetOrder = Order::findOrFail($data['target_order_id']);

                                OrderItem::where('order_id', $targetOrder->id)
                                    ->update([
                                        'order_id' => $record->id,
                                    ]);

                                $record->recalculateTotals();

                                $targetOrder->delete();
                            });
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->recordUrl(fn (Order $record, ListOrders $livewire) => OrderResource::getUrl('edit', [
                'record' => $record,
                'return_url' => self::currentListUrl($record, $livewire),
            ]))
            ->headerActions([
                Action::make('selectAllStatuses')
                    ->label('Seleziona tutti gli stati')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('primary')
                    ->outlined()
                    ->visible(function (ListOrders $livewire): bool {
                        $filters = $livewire->getTableFiltersForm()?->getState() ?? [];

                        $selected = data_get($filters, 'status.statuses', []);

                        $allStatuses = array_keys(config('ecommerce.order_statuses'));

                        sort($selected);
                        sort($allStatuses);

                        return $selected !== $allStatuses;
                    })
                    ->action(function (ListOrders $livewire): void {
                        $form = $livewire->getTableFiltersForm();

                        $filters = $form?->getState() ?? [];
                        $all = array_map('strval', array_keys(config('ecommerce.order_statuses')));

                        // Push the state back into the filters form
                        $filters['status']['statuses'] = $all;
                        $form?->fill($filters);

                        // Now apply (Filament will read from the form state)
                    }),
                Action::make('clearStatusFilter')
                    ->label('Deseleziona tutti gli stati')
                    ->icon(Heroicon::XMark)
                    ->color('danger')
                    ->outlined()
                    ->visible(function (ListOrders $livewire): bool {
                        $filters = $livewire->getTableFiltersForm()?->getState() ?? [];

                        $values = data_get($filters, 'status.statuses', []);

                        return ! empty($values);
                    })
                    ->action(function (ListOrders $livewire): void {
                        $livewire->removeTableFilter('status');
                    }),
            ])
            ->toolbarActions([

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function currentListUrl(Order $record, ?ListOrders $livewire = null): string
    {
        $fragment = 'order-id-'.$record->id;

        // 1) Best: build from Livewire state (this updates when you paginate)
        if ($livewire) {
            $filtersFormState = $livewire->getTableFiltersForm()?->getState() ?? [];

            $params = array_filter([
                'filters' => $filtersFormState ?: null,
                'search' => method_exists($livewire, 'getTableSearch') ? ($livewire->getTableSearch() ?: null) : null,
                'sortColumn' => method_exists($livewire, 'getTableSortColumn') ? ($livewire->getTableSortColumn() ?: null) : null,
                'sortDirection' => method_exists($livewire, 'getTableSortDirection') ? ($livewire->getTableSortDirection() ?: null) : null,

                // pagination: Filament uses tablePage in most setups
                'page' => method_exists($livewire, 'getTablePage') ? ($livewire->getTablePage() ?: null) : null,
            ], fn ($v) => $v !== null);

            return OrderResource::getUrl('index', $params).'#'.$fragment;
        }

        // 2) Fallback: referer (only works for full page loads)
        $referer = request()->header('referer');
        if (filled($referer)) {
            $sanitized = self::sanitizeReturnUrl($referer);

            if (filled($sanitized) && self::isOrdersIndexPath($sanitized)) {
                return $sanitized.'#'.$fragment;
            }
        }

        // 3) Final fallback
        return OrderResource::getUrl('index').'#'.$fragment;
    }

    private static function sanitizeReturnUrl(string $url): ?string
    {
        $parts = parse_url($url);

        $path = $parts['path'] ?? null;
        if (! filled($path)) {
            return null;
        }

        $query = [];
        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
            unset($query['return_url']);
        }

        $qs = $query ? ('?'.http_build_query($query)) : '';

        return $path.$qs;
    }

    private static function isOrdersIndexPath(string $relativeUrl): bool
    {
        $path = parse_url($relativeUrl, PHP_URL_PATH) ?: '';
        $indexPath = parse_url(OrderResource::getUrl('index'), PHP_URL_PATH) ?: '';

        return $path === $indexPath;
    }
}
