<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('dispatch_material')) {
            return;
        }

        Schema::table('dispatch_material', function (Blueprint $table) {
            if (! Schema::hasColumn('dispatch_material', 'weight_id')) {
                $table->unsignedBigInteger('weight_id')->nullable();
            }
            if (! Schema::hasColumn('dispatch_material', 'actual_qty')) {
                $table->decimal('actual_qty', 20, 3)->nullable();
            }
            if (! Schema::hasColumn('dispatch_material', 'status')) {
                $table->string('status', 30)->default('pending');
            }
        });

        if (! Schema::hasIndex('dispatch_material', 'dispatch_material_gate_status_idx')) {
            Schema::table('dispatch_material', function (Blueprint $table) {
                $table->index(['gate_id', 'status'], 'dispatch_material_gate_status_idx');
            });
        }

        if (! Schema::hasIndex('dispatch_material', 'dispatch_material_weight_id_foreign')
            && Schema::hasColumn('dispatch_material', 'weight_id')) {
            Schema::table('dispatch_material', function (Blueprint $table) {
                $table->foreign('weight_id')->references('id')->on('weights');
            });
        }

        DB::table('dispatch_material')
            ->where('dispatch_qty', '>', 0)
            ->update([
                'actual_qty' => DB::raw('COALESCE(actual_qty, dispatch_qty)'),
                'status' => 'dispatched',
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('dispatch_material')) {
            return;
        }

        Schema::table('dispatch_material', function (Blueprint $table) {
            if (Schema::hasIndex('dispatch_material', 'dispatch_material_gate_status_idx')) {
                $table->dropIndex('dispatch_material_gate_status_idx');
            }
            if (Schema::hasColumn('dispatch_material', 'weight_id')) {
                $table->dropForeign(['weight_id']);
            }
            $columns = array_values(array_filter(
                ['weight_id', 'actual_qty', 'status'],
                fn ($column) => Schema::hasColumn('dispatch_material', $column)
            ));
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
