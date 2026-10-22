
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('work_orders_and_process')) {
            return;
        }

        Schema::create('work_orders_and_process', function (Blueprint $table) {
            $table->id();

            $table->integer('sale_order_id')->nullable();
            $table->integer('sale_order_item_id')->nullable();
            $table->integer('purchase_order_id')->nullable();
            $table->integer('po_item_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('grade_id')->nullable();
            $table->integer('size_id')->nullable();
            $table->decimal('material_send_for_work', 20, 3)->nullable();
            $table->decimal('pending_raw_material', 20, 3)->nullable()->comment('po_item - material_send_for_work = pendind_material_rm');
            $table->string('heat_no', 255)->nullable()->comment('Heat no from po_item table');
            $table->string('j_messages')->nullable();
            $table->string('total_operation_perfome')->nullable()->comment('Total step count for making the finished material.');
            $table->string('status')->default('pending');
            $table->unsignedTinyInteger('current_operation')->default(1);
            $table->uuid('submission_token')->nullable()->unique();

            $table->tinyInteger('shot_blasting')->nullable()->default(2)->comment('1 = yes, 2 = No (BB)');
            $table->tinyInteger('acid_pickling')->nullable()->default(2)->comment('1 = yes, 2 = No (BB)');
            $table->string('fs_size', 255)->nullable()->default(null);
            $table->string('fs_size_unit', 255)->nullable()->default(null);
            $table->string('fs_observed_size', 255)->nullable()->default(null);
            $table->string('fs_observed_unit', 255)->nullable()->default(null);
            $table->tinyInteger('poin_discard')->default(2)->comment('1 = yes, 2 = No (BB)');
            $table->tinyInteger('straightening')->default(2)->comment('1 = yes, 2 = No (BB)');


            $table->decimal('fs_bundle_weight', 20, 3)->nullable()->default(null)->comment('from sale order');
            $table->string('fs_bundle_unit', 100)->nullable()->default(null);
            $table->integer('total_full_bundle_count')->nullable()->default(null);
            $table->decimal('lose_bundle_weight', 20, 3)->nullable()->default(null);
            $table->integer('lose_bundle_count')->default(0);
            
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes('delete_at');
        });
    }

    public function down(): void
    {
        $legacyWorkOrderMigrationExists = Schema::hasTable('migrations')
            && \Illuminate\Support\Facades\DB::table('migrations')
                ->whereIn('migration', [
                    '2026_10_19_091426_create_work_orders_table',
                    '2026_10_19_092924_create_work_orders_table',
                ])
                ->exists();

        if ($legacyWorkOrderMigrationExists) {
            return;
        }

        Schema::dropIfExists('work_orders_and_process');
    }
};
