<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            // Purchase Order Reference
            $table->unsignedBigInteger('purchase_order_id')->nullable();

            // Purchase Order Common Details
            $table->unsignedBigInteger('cust_id')->nullable();
            $table->unsignedBigInteger('b_rate')->nullable();
            $table->unsignedBigInteger('ref_saleorder_id')->nullable();
            $table->enum('basis', ['FOR', 'EX'])->nullable();

            // Product Details
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('size_unit_id')->nullable();

            // Item Details
            $table->string('length')->nullable();
            $table->unsignedBigInteger('rate')->nullable();
            $table->unsignedBigInteger('qty')->nullable();
            $table->string('qty_unit')->nullable();
            $table->string('qty_varaiate')->nullable();

            // Remarks
            $table->text('remarks')->nullable();

            // Created By
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // Purchase Order Reference
            $table->foreign('purchase_order_id')
                ->references('id')
                ->on('purchase_orders')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
