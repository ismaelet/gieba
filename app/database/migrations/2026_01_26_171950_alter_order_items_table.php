<?php

use App\Models\Order;
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
            $table->float('unit_price_net')->default(0)->change();
            $table->float('unit_price_gross')->default(0)->change();
        });

        $orderItems = OrderItem::all();

        foreach ($orderItems as $item) {
            $item->recalculatePrices();
        }

        $orders = Order::with('order_items')->get();

        foreach ($orders as $order) {
            $order->recalculateTotals();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
