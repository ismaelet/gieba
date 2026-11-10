<?php

namespace App\Filament\Resources\SupplierOrders;

use App\Filament\Resources\SupplierOrders\Pages\CreateSupplierOrder;
use App\Filament\Resources\SupplierOrders\Pages\EditSupplierOrder;
use App\Filament\Resources\SupplierOrders\Pages\ListSupplierOrders;
use App\Filament\Resources\SupplierOrders\Schemas\SupplierOrderForm;
use App\Filament\Resources\SupplierOrders\Tables\SupplierOrdersTable;
use App\Models\Supplier\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupplierOrderResource extends Resource
{
    /**
     * Property: The model the resource corresponds to
     */
    protected static ?string $model = Order::class;

    /**
     * Property: The navigation icon
     */
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Square3Stack3d;

    /**
     * Property: The label of a singular resource
     */
    protected static ?string $label = 'Ordine fornitore';

    /**
     * Property: The label of a plural resource
     */
    protected static ?string $pluralLabel = 'Ordini fornitori';

    /**
     * Property: The navigation sort order
     */
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SupplierOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupplierOrdersTable::configure($table);
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
            'index' => ListSupplierOrders::route('/'),
            'create' => CreateSupplierOrder::route('/create'),
            'edit' => EditSupplierOrder::route('/{record}/edit'),
        ];
    }
}
