<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gate_id');
            $table->unsignedBigInteger('sale_order_id');
            $table->unsignedBigInteger('sale_order_item_id');
            $table->unsignedBigInteger('weight_id')->nullable();
            $table->decimal('actual_qty', 20, 3)->nullable();
            $table->decimal('dispatch_qty', 20, 3)->default(0);
            $table->string('status', 30)->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('sale_order_id');
            $table->index('sale_order_item_id');
            $table->index(['gate_id', 'sale_order_item_id'], 'dispatch_material_gate_item_idx');
            $table->index(['gate_id', 'status'], 'dispatch_material_gate_status_idx');

            $table->foreign('gate_id')->references('id')->on('gate_and_gaadi');
            $table->foreign('sale_order_id')->references('id')->on('sale_orders');
            $table->foreign('sale_order_item_id')->references('id')->on('sale_order_items');
            $table->foreign('weight_id')->references('id')->on('weights');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_material');
    }
};
