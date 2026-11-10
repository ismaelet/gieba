<?php

namespace App\Filament\Resources\SupplierOrders\Tables;

use App\Filament\Resources\SupplierOrders\SupplierOrderResource;
use App\Filament\Tables\Columns\OrderItemsColumn;
use App\Models\Supplier\Order;
use App\Models\Supplier\OrderItem;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Livewire\Component;

class SupplierOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->defaultPaginationPageOption(50)
            ->query(
                Order::query()
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->extraAttributes(fn (Order $record): array => [
                        'id' => 'order-id-'.$record->id,
                    ])
                    ->searchable(),

                TextColumn::make('supplier.name')
                    ->label('Fornitore')
                    ->sortable()
                    ->searchable(),

                OrderItemsColumn::make('order_items.product.name')
                    ->searchable()
                    ->label('Articoli'),

                TextColumn::make('status')
                    ->label('Stato')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => config('ecommerce.supplier_order_statuses')[$state]['label'] ?? $state)
                    ->color(fn (int $state): string => 'status-'.$state),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->sortable()
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Filter::make('status')
                    ->schema([
                        CheckboxList::make('statuses')
                            ->label('Stato')
                            ->options(
                                collect(config('ecommerce.supplier_order_statuses'))
                                    ->mapWithKeys(fn ($status, $key) => [$key => $status['label']])
                                    ->toArray()
                            )
                            ->columns(4)
                            ->default([1, 2, 3]),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $statuses = array_values(array_filter($data['statuses'] ?? []));

                        if ($statuses === []) {
                            $query->where(function (Builder $q) {
                                $q->whereNull('supplier_orders.status');
                            });

                            return;
                        }

                        $query->where(function (Builder $q) use ($statuses) {
                            $q->whereIn('supplier_orders.status', $statuses)
                                ->orWhereExists(function (QueryBuilder $sub) use ($statuses) {
                                    $sub->selectRaw('1')
                                        ->from('supplier_order_items')
                                        ->whereColumn('supplier_order_items.supplier_order_id', 'supplier_orders.id')
                                        ->whereIn('supplier_order_items.status', $statuses);
                                });
                        });
                    })->columnSpan(2),

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
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->deferFilters(false)
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->url(fn (Order $record, Component $livewire) => SupplierOrderResource::getUrl('edit', [
                            'record' => $record,
                            'return_url' => self::currentListUrl($record, $livewire),
                        ])),

                    DeleteAction::make(),

                    Action::make('merge')
                        ->label('Unisci')
                        ->color('info')
                        ->icon(Heroicon::ArrowsPointingIn)
                        ->modalSubmitActionLabel('Unisci ordini')
                        ->schema(fn (Order $record) => [
                            Select::make('target_order_id')
                                ->label('Unisci con ordine')
                                ->aboveContent('Gli aricoli verranno tutti trasferiti nell\'ordine qui. I dati che verranno mantenuti saranno quelli dell\'ordine selezionato qui sotto.')
                                ->searchable()
                                ->getSearchResultsUsing(
                                    fn (string $search) => Order::query()
                                        ->whereKeyNot($record->id)
                                        ->where('id', $search)
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn (Order $order) => [
                                            $order->id => 'Ordine '.$order->idCode().' ('.($order->supplier?->name ?? '—').')',
                                        ])
                                        ->toArray()
                                )
                                ->getOptionLabelUsing(function ($value) {
                                    $order = Order::with('supplier')->find($value);

                                    return $order
                                        ? 'Ordine '.$order->idCode().' ('.($order->supplier?->name ?? '—').')'
                                        : (string) $value;
                                })
                                ->required(),
                        ])
                        ->action(function (Order $record, array $data): void {
                            $targetOrder = Order::find($data['target_order_id']);

                            OrderItem::where('supplier_order_id', $record->id)
                                ->update([
                                    'supplier_order_id' => $targetOrder->id,
                                ]);

                            $targetOrder->recalculateTotals();

                            $record->delete();
                        }),
                ]),
            ])
            ->recordUrl(fn (Order $record, Component $livewire) => SupplierOrderResource::getUrl('edit', [
                'record' => $record,
                'return_url' => self::currentListUrl($record, $livewire),
            ]))
            ->headerActions([
                Action::make('selectAllStatuses')
                    ->label('Seleziona tutti gli stati')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('primary')
                    ->outlined()
                    ->visible(function (Component $livewire): bool {
                        $filters = $livewire->getTableFiltersForm()?->getState() ?? [];

                        $selected = data_get($filters, 'status.statuses', []);

                        $allStatuses = array_keys(config('ecommerce.supplier_order_statuses'));

                        sort($selected);
                        sort($allStatuses);

                        return $selected !== $allStatuses;
                    })
                    ->action(function (Component $livewire): void {
                        $form = $livewire->getTableFiltersForm();

                        $filters = $form?->getState() ?? [];
                        $all = array_map('strval', array_keys(config('ecommerce.supplier_order_statuses')));

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
                    ->visible(function (Component $livewire): bool {
                        $filters = $livewire->getTableFiltersForm()?->getState() ?? [];

                        $values = data_get($filters, 'status.statuses', []);

                        return ! empty($values);
                    })
                    ->action(function (Component $livewire): void {
                        $livewire->removeTableFilter('status');
                    }),
            ])
            ->toolbarActions([

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function currentListUrl(Order $record, ?Component $livewire = null): string
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

            return SupplierOrderResource::getUrl('index', $params).'#'.$fragment;
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
        return SupplierOrderResource::getUrl('index').'#'.$fragment;
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
        $indexPath = parse_url(SupplierOrderResource::getUrl('index'), PHP_URL_PATH) ?: '';

        return $path === $indexPath;
    }
}
