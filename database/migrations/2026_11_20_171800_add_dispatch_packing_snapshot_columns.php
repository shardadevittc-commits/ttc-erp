<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dispatch_material')) {
            Schema::table('dispatch_material', function (Blueprint $table) {
                if (! Schema::hasColumn('dispatch_material', 'dispatch_qty_unit')) {
                    $table->string('dispatch_qty_unit', 20)->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'fs_bundle_weight')) {
                    $table->decimal('fs_bundle_weight', 20, 3)->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'fs_bundle_unit')) {
                    $table->string('fs_bundle_unit', 20)->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'total_full_bundle_count')) {
                    $table->integer('total_full_bundle_count')->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'lose_bundle_weight')) {
                    $table->decimal('lose_bundle_weight', 20, 3)->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'lose_bundle_count')) {
                    $table->integer('lose_bundle_count')->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'per_pcs_weight')) {
                    $table->decimal('per_pcs_weight', 20, 3)->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'total_pcs')) {
                    $table->integer('total_pcs')->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'no_of_boxes')) {
                    $table->integer('no_of_boxes')->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'loss_box_pcs')) {
                    $table->integer('loss_box_pcs')->nullable();
                }
                if (! Schema::hasColumn('dispatch_material', 'loss_box_count')) {
                    $table->integer('loss_box_count')->nullable();
                }
            });
        }

        if (Schema::hasTable('work_orders_and_process')) {
            Schema::table('work_orders_and_process', function (Blueprint $table) {
                if (! Schema::hasColumn('work_orders_and_process', 'per_pcs_weight')) {
                    $table->decimal('per_pcs_weight', 20, 3)->nullable();
                }
                if (! Schema::hasColumn('work_orders_and_process', 'finished_pcs')) {
                    $table->integer('finished_pcs')->nullable();
                }
                if (! Schema::hasColumn('work_orders_and_process', 'finished_pcs_weight')) {
                    $table->decimal('finished_pcs_weight', 20, 0)->nullable();
                }
                if (! Schema::hasColumn('work_orders_and_process', 'no_of_boxes')) {
                    $table->integer('no_of_boxes')->nullable();
                }
                if (! Schema::hasColumn('work_orders_and_process', 'pcs_per_box')) {
                    $table->integer('pcs_per_box')->nullable();
                }
                if (! Schema::hasColumn('work_orders_and_process', 'loss_box_pcs')) {
                    $table->integer('loss_box_pcs')->nullable();
                }
                if (! Schema::hasColumn('work_orders_and_process', 'loss_box_count')) {
                    $table->integer('loss_box_count')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // These columns may already exist from the original table migrations.
    }
};
