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
            $table->decimal('total_vat', 15, 2)->default(0)->after('unit_price_gross');
            $table->decimal('total_price_net', 15, 2)->default(0)->after('total_vat');
        });

        $orderItems = OrderItem::all();
        foreach ($orderItems as $item) {
            $item->recalculatePrices();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('total_price_net');
            $table->dropColumn('total_vat');
        });
    }
};
