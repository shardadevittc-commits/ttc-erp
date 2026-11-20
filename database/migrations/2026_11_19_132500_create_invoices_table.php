<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            return;
        }

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gate_id')->unique();
            $table->string('billed_to')->nullable();
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->string('gst_type', 20)->nullable();
            $table->decimal('cash_discount_percent', 8, 3)->default(0);
            $table->decimal('cash_discount_amount', 20, 3)->default(0);
            $table->decimal('labor_charges', 20, 3)->default(0);
            $table->decimal('other_charges', 20, 3)->default(0);
            $table->decimal('taxable_amount', 20, 3)->default(0);
            $table->decimal('sgst_percent', 8, 3)->default(0);
            $table->decimal('sgst_amount', 20, 3)->default(0);
            $table->decimal('cgst_percent', 8, 3)->default(0);
            $table->decimal('cgst_amount', 20, 3)->default(0);
            $table->decimal('igst_percent', 8, 3)->default(0);
            $table->decimal('igst_amount', 20, 3)->default(0);
            $table->decimal('gst_amount', 20, 3)->default(0);
            $table->decimal('tds_amount', 20, 3)->default(0);
            $table->decimal('insurance_amount', 20, 3)->default(0);
            $table->decimal('tcs_amount', 20, 3)->default(0);
            $table->unsignedInteger('due_days')->default(0);
            $table->decimal('advance_payment', 20, 3)->default(0);
            $table->decimal('total_amount', 20, 3)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('gate_id')->references('id')->on('gate_and_gaadi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
