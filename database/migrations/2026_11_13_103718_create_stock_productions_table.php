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
        Schema::create('stock_productions', function (Blueprint $table) {
            $table->id();

            // Links
            $table->unsignedBigInteger('work_order_id')->nullable();          // work_orders_and_process.id
            $table->unsignedBigInteger('poitem_receiving_id')->nullable();    // kis lot se material gaya
            $table->unsignedBigInteger('purchase_order_item_id')->nullable();
            $table->unsignedBigInteger('dispatch_id')->nullable();
            $table->unsignedBigInteger('sale_order_id')->nullable();          // baad mein link hoga
            $table->unsignedBigInteger('sale_order_item_id')->nullable();     // baad mein link hoga
            $table->timestamp('linked_to_sale_order_at')->nullable();

            // Kya banana hai (sale order item form wale fields)
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->string('size_extra', 100)->nullable();
            $table->string('unit', 20)->nullable();                           // MM / THOUG
            $table->unsignedBigInteger('grade_id')->nullable();

            // Qty
            $table->decimal('item_qty', 20, 3)->default(0);
            $table->tinyInteger('qty_type')->nullable();                      // 1 = Tons, 2 = KGs

            // Steps (Yes = 1, No = 2)
            $table->tinyInteger('straightening')->nullable();
            $table->tinyInteger('point_discard')->nullable();
            $table->tinyInteger('phosphating')->nullable();

            // Specs
            $table->string('hardness', 100)->nullable();
            $table->string('hardness_unit', 100)->nullable();                 // BHN / HRC
            $table->string('pcs_length_unit', 100)->nullable();
            $table->string('coating', 50)->nullable();                        // autoblack / galvanized / N/A

            // Bundle
            $table->decimal('fs_bundle_weight', 20, 3)->nullable();
            $table->tinyInteger('fs_bundle_unit')->nullable();                // 1 = KG, 2 = Tons

            $table->text('item_remarks')->nullable();
            $table->string('status', 50)->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('work_order_id');
            $table->index('poitem_receiving_id');
            $table->index('sale_order_id');
            $table->index('sale_order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_productions');
    }
};
