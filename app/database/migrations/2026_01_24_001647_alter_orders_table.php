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
            $table->renameColumn('price', 'price_gross');
            $table->decimal('price_net', 10, 2)->after('customer_id')->default(0);
            $table->decimal('vat', 10, 2)->after('price_gross')->default(0);
            $table->decimal('total_discount', 10, 2)->after('vat')->default(0);
        });

        $orders = Order::with('order_items')->get();

        foreach ($orders as $order) {
            $order->price_net = $order->calcPriceNet();
            $order->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('price_gross', 'price');
            $table->dropColumn('total_discount');
            $table->dropColumn('vat');
            $table->dropColumn('price_net');
        });
    }
};
