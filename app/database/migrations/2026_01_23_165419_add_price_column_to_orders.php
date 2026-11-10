<?php

use App\Models\Order;
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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('customer_id');
            $table->unsignedInteger('quantity')->default(0)->after('price');
        });

        $orders = Order::with('order_items')->get();

        foreach ($orders as $order) {
            $order->price = $order->calcPriceGross();
            $order->quantity = $order->calcQuantity();
            $order->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('quantity');
        });
    }
};
