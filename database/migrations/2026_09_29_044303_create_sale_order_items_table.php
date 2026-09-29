<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->id();

            $table->text('size_unit')->nullable();

            $table->unsignedBigInteger('sale_order_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();

            $table->decimal('sale_item_prices', 20, 3)->nullable();
            $table->decimal('size_extra', 20, 3)->nullable();
            $table->decimal('item_qty', 20, 3)->nullable();

            $table->unsignedTinyInteger('qty_type')->nullable()->comment('1 = Tons, 2 = KGs');

            $table->decimal('dispatch_qty', 20, 3)->nullable();
            $table->decimal('dispatched_qty', 20, 3)->nullable();
            $table->decimal('pending_qty', 20, 3)->nullable();

            $table->unsignedTinyInteger('s_marked_completed')->nullable()->comment('1 = Not Completed, 2 = Completed');

            $table->text('item_remarks')->nullable();

            $table->unsignedTinyInteger('straightening')->nullable()->comment('1 = Yes, 2 = No');
            $table->unsignedTinyInteger('point_discard')->nullable()->comment('1 = Yes, 2 = No');
            $table->unsignedTinyInteger('phosphating')->nullable()->comment('1 = Yes, 2 = No');

            $table->text('hardness')->nullable();

            $table->string('hardness_unit', 20)->nullable()->comment('BHN, HRC');

            $table->string('pcs_length_unit', 20)->nullable()->comment('MM, Inches');

            $table->string('coating', 50)->nullable()->comment('autoblack, galvanized');

            $table->unsignedBigInteger('product_type')->nullable();

            $table->decimal('fs_bundle_weight', 20, 3)->nullable();

            $table->unsignedTinyInteger('fs_bundle_unit')->nullable()->comment('1 = KG, 2 = Tons');

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_order_items');
    }
};
