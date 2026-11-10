<?php

use App\Models\OrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('net_product_price', 'unit_price_net');
            $table->renameColumn('price', 'total_price_gross');
            $table->decimal('total_price_gross', 10, 2)->default(0.00)->change();
            $table->decimal('unit_price_net', 10, 2)->default(0.00)->change();
            $table->decimal('unit_price_gross', 10, 2)->after('unit_price_net')->default(0.00);
        });

        $orderItems = OrderItem::all();

        foreach ($orderItems as $item) {
            $item->unit_price_gross = $item->has_vat ? round($item->unit_price_net * 1.22, 2) : $item->unit_price_net;
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('unit_price_net', 'net_product_price');
            $table->renameColumn('total_price_gross', 'price');
            $table->dropColumn('unit_price_gross');
        });
    }
};
