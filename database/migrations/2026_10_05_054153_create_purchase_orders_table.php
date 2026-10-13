<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cust_id')->nullable();
            $table->unsignedBigInteger('b_rate')->nullable();
            $table->unsignedBigInteger('ref_saleorder_id')->nullable();

            $table->enum('basis', ['FOR', 'EX'])->nullable();

            // Payment Term: 30 Days / 2 Week / 3 Month
            $table->string('term')->nullable();

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
