<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poitem_receiving', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehical_id')->nullable()->comment('vehical_id = gate.id');
            $table->unsignedBigInteger('weight_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('purchase_order_item_id')->nullable();

            $table->decimal('po_item_qty', 12, 2)->default(0);
            $table->decimal('received_qty', 12, 2)->default(0);
            $table->decimal('production_used_qty', 12, 2)->default(0)->commet('after production');
            $table->decimal('order_price', 12, 2)->default(0);
            $table->decimal('received_price', 12, 2)->default(0);

            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();

            $table->string('furnance_heat_no')->nullable()->comment('received furnance heat no');
            $table->string('genrated_heat_no')->nullable()->comment('against received furnance');
            $table->string('lab_test')->nullable()->comment('1 = Yes 2 = No');
            $table->text('item_remarks')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poitem_receiving');
    }
};