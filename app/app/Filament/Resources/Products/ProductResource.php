<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    /**
     * Property: The model the resource corresponds to
     */
    protected static ?string $model = Product::class;

    /**
     * Property: The navigation icon
     */
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    /**
     * Property: The label of a singular resource
     */
    protected static ?string $label = 'Prodotto';

    /**
     * Property: The label of a plural resource
     */
    protected static ?string $pluralLabel = 'Prodotti';

    /**
     * Property: The navigation sort order
     */
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
