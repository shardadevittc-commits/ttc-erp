php artisan make:migration create_lab_results_table

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();

            // Vehicle and weight details
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('weight_id')->nullable();
            $table->json('formlist')->nullable();

            // Order details
            $table->unsignedBigInteger('cust_id')->nullable();
            $table->unsignedBigInteger('purchase_order_item_id');
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();

            // Dynamic composition and property
            $table->unsignedBigInteger('grade_category_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->decimal('actual_value', 12, 4)->nullable();

            $table->timestamps();

            $table->index('purchase_order_item_id');
            $table->index('grade_id');
            $table->index('property_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
