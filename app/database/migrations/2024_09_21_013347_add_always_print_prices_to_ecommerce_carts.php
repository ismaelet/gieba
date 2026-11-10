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
        Schema::table('ecommerce_carts', function (Blueprint $table) {
            $table->boolean('always_print_prices')->default(false)->after('print_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecommerce_carts', function (Blueprint $table) {
            $table->dropColumn('always_print_prices');
        });
    }
};
