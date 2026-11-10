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
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('customerId', 'customer_id');
            $table->renameColumn('downPayment', 'down_payment');
            $table->renameColumn('downPaymentWithVat', 'down_payment_with_vat');
            $table->renameColumn('fixedDiscount', 'fixed_discount');
            $table->renameColumn('discountWithVat', 'discount_with_vat');
            $table->renameColumn('percentageDiscount', 'percentage_discount');
            $table->renameColumn('printDescriptions', 'print_descriptions');
            $table->renameColumn('printStatus', 'print_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'customerId');
            $table->renameColumn('down_payment', 'downPayment');
            $table->renameColumn('down_payment_with_vat', 'downPaymentWithVat');
            $table->renameColumn('fixed_discount', 'fixedDiscount');
            $table->renameColumn('discount_with_vat', 'discountWithVat');
            $table->renameColumn('percentage_discount', 'percentageDiscount');
            $table->renameColumn('print_descriptions', 'printDescriptions');
            $table->renameColumn('print_status', 'printStatus');
        });
    }
};
