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
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();

            $table->string('size_name', 50);

            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();

            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();

            $table->decimal('qty', 12, 2)->default(0);
            $table->decimal('opening_stock', 12, 2)->default(0);

            $table->string('location', 100)->nullable();

            $table->decimal('avg_bundle_weight', 12, 3)->nullable();

            // Useful for steel inventory
            $table->decimal('min_stock_level', 12, 2)->default(0);
            $table->string('rack_no', 50)->nullable();
            $table->string('warehouse', 100)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
