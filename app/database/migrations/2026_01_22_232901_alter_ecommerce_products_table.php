<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('ecommerce_products', 'products');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('products', 'ecommerce_products');
    }
};
