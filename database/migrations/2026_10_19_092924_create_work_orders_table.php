
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();

            $table->integer('sale_order_id')->nullable();
            $table->integer('sale_order_item_id')->nullable();
            $table->integer('purchase_order_id')->nullable();
            $table->integer('po_item_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('grade_id')->nullable();
            $table->integer('size_id')->nullable();

            $table->decimal('material_send_for_work', 20, 3)->nullable();

            $table->decimal('pending_raw_material', 20, 3)->nullable()
                ->comment('po_item - material_send_for_work = pendind_material_rm');

            $table->string('heat_no', 255)->nullable()
                ->comment('Heat no from po_item table');

            $table->string('j_messages')->nullable();

            $table->string('total_operation_perfome')->nullable()
                ->comment('Total step count for making the finished material.');

            $table->string('status')->default('pending');

            $table->integer('created_by');

            $table->timestamps();
            $table->softDeletes('delete_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
