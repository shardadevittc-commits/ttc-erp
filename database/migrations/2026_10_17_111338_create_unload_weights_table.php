<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unload_weights', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('weight_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('cust_id');

            $table->string('formlist')->default('Material In');

            $table->decimal('tareweight', 12, 2)->nullable();
            $table->decimal('loadedweight', 12, 2)->nullable();
            $table->decimal('unload_tare_weight', 12, 2)->nullable();
            $table->decimal('unload_loaded_weight', 12, 2)->nullable();
            $table->decimal('material_weight', 12, 2)->default(0);

            // Multiple material entries
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('purchase_order_item_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();

            $table->decimal('received_qty', 12, 3)->default(0);
            $table->decimal('actual_qty', 12, 3)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unload_weights');
    }
};
