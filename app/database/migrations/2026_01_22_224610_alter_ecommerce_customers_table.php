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
        Schema::rename('ecommerce_customers', 'customers');

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('userId', 'user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('user_id', 'userId');
            $table->dropTimestamps();
        });

        Schema::rename('customers', 'ecommerce_customers');
    }
};
