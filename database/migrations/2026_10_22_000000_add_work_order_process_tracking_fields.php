<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('work_orders_and_process')) {
            return;
        }

        Schema::table('work_orders_and_process', function (Blueprint $table) {
            if (!Schema::hasColumn('work_orders_and_process', 'current_operation')) {
                $table->unsignedTinyInteger('current_operation')->default(1);
            }

            if (!Schema::hasColumn('work_orders_and_process', 'submission_token')) {
                $table->uuid('submission_token')->nullable()->unique();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('work_orders_and_process')) {
            return;
        }

        $columns = array_filter([
            Schema::hasColumn('work_orders_and_process', 'current_operation') ? 'current_operation' : null,
            Schema::hasColumn('work_orders_and_process', 'submission_token') ? 'submission_token' : null,
        ]);

        if ($columns) {
            Schema::table('work_orders_and_process', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
