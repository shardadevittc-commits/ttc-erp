<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEXES = [
        'purchase_order_items' => [
            'purchase_items_sale_order_grade_idx' => ['ref_saleorder_id', 'grade_id'],
        ],
        'poitem_receiving' => [
            'receiving_purchase_item_deleted_idx' => ['purchase_order_item_id', 'deleted_at'],
        ],
        'work_orders_and_process' => [
            'work_order_item_deleted_idx' => ['sale_order_item_id', 'delete_at'],
        ],
    ];

    public function up(): void
    {
        foreach (self::INDEXES as $tableName => $indexes) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            foreach ($indexes as $indexName => $columns) {
                if (Schema::hasIndex($tableName, $indexName)) {
                    continue;
                }

                Schema::table($tableName, function (Blueprint $table) use ($indexName, $columns) {
                    $table->index($columns, $indexName);
                });
            }
        }
    }

    public function down(): void
    {
        foreach (self::INDEXES as $tableName => $indexes) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            foreach (array_keys($indexes) as $indexName) {
                if (!Schema::hasIndex($tableName, $indexName)) {
                    continue;
                }

                Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }
        }
    }
};
