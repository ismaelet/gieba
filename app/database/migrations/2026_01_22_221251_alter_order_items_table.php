<?php

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
            $table->renameColumn('cartId', 'order_id');
            $table->renameColumn('productId', 'product_id');
            $table->renameColumn('netProductPrice', 'net_product_price');
            $table->renameColumn('hasVat', 'has_vat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('order_id', 'cartId');
            $table->renameColumn('product_id', 'productId');
            $table->renameColumn('net_product_price', 'netProductPrice');
            $table->renameColumn('has_vat', 'hasVat');
        });
    }
};
