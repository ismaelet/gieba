<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete orders that don't have any order_items
        DB::table('orders')
            ->whereNotIn('id', function ($query) {
                $query->select('order_id')->from('order_items');
            })
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
