<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE vehicle_weigh_records
            MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Weight ID'
        ");

        DB::statement("
            ALTER TABLE vehicle_weigh_records
            MODIFY COLUMN material_in_id BIGINT UNSIGNED NULL COMMENT 'Gate ID From Material_Ins'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE vehicle_weigh_records
            MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT ''
        ");

        DB::statement("
            ALTER TABLE vehicle_weigh_records
            MODIFY COLUMN material_in_id BIGINT UNSIGNED NULL COMMENT ''
        ");
    }
};
