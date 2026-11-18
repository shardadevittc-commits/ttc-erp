<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders_and_process', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->integer('sale_order_id')->nullable();
            $table->integer('sale_order_item_id')->nullable();
            $table->integer('purchase_order_id')->nullable();
            $table->integer('po_item_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('grade_id')->nullable();
            $table->integer('size_id')->nullable();

            $table->decimal('material_send_for_work', 20, 3)->nullable();
            $table->decimal('pending_raw_material', 20, 3)->nullable();

            $table->string('heat_no', 255)->nullable();
            $table->string('j_messages', 191)->nullable();
            $table->string('total_operation_perfome', 191)->nullable();

            $table->string('status', 191)->default('pending');

            $table->decimal('fs_bundle_weight', 20, 3)->nullable();
            $table->string('fs_bundle_unit', 100)->nullable();
            $table->integer('total_full_bundle_count')->nullable();
            $table->decimal('lose_bundle_weight', 20, 3)->nullable();
            $table->integer('lose_bundle_count')->nullable();

            $table->tinyInteger('shot_blasting')->nullable()->default(2);
            $table->tinyInteger('acid_pickling')->nullable()->default(2);
            $table->tinyInteger('phosphating')->nullable();

            $table->text('acid_remarks')->nullable();

            $table->tinyInteger('second_acid_pickling')->default(2);
            $table->tinyInteger('second_phosphating')->default(2);
            $table->text('second_acid_remarks')->nullable();

            $table->string('fs_size', 255)->nullable();
            $table->string('fs_size_unit', 255)->nullable();
            $table->string('fs_observed_size', 255)->nullable();
            $table->string('fs_observed_unit', 255)->nullable();

            $table->string('final_fs_observed_size', 100)->nullable();
            $table->string('final_fs_observed_unit', 100)->nullable();

            $table->string('rm_size', 100)->nullable();
            $table->string('rm_size_unit', 100)->nullable();
            $table->string('rm_observed_size', 100)->nullable();
            $table->string('rm_observed_unit', 100)->nullable();

            $table->string('hardness', 100)->nullable();
            $table->string('hardness_unit', 100)->nullable();
            $table->string('hardness_observed', 100)->nullable();
            $table->string('hardness_observed_unit', 100)->nullable();

            $table->string('batch_no', 255)->nullable();

            $table->tinyInteger('pointing')->nullable()->default(2);
            $table->tinyInteger('poin_discard')->default(2);
            $table->tinyInteger('straightening')->default(2);

            $table->string('cutting_type', 100)->nullable();

            $table->decimal('per_pcs_weight', 20, 3)->nullable();
            $table->integer('finished_pcs')->nullable();
            $table->decimal('finished_pcs_weight', 20, 0)->nullable();
            $table->integer('rejected_pcs')->nullable();
            $table->decimal('rejected_pcs_weight', 20, 0)->nullable();

            $table->integer('no_of_boxes')->nullable();
            $table->integer('pcs_per_box')->nullable();
            $table->integer('loss_box_pcs')->nullable();
            $table->integer('loss_box_count')->nullable();

            $table->integer('created_by')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('delete_at')->nullable();

            $table->unsignedTinyInteger('current_operation')->default(1);

            $table->string('submission_token')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders_and_process');
    }
};
