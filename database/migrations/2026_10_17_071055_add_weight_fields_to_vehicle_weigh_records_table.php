<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weigh', function (Blueprint $table) {
            $table->decimal('material_weight', 12, 2)->nullable();

            $table->decimal('unload_tare_weight', 12, 2)->nullable();

            $table->decimal('unload_loaded_weight', 12, 2)->nullable();

            $table->string('first_weight', 10)->nullable();

            $table->string('second_weight', 10)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('weigh', function (Blueprint $table) {
            $table->dropColumn([
                'material_weight',
                'unload_tare_weight',
                'unload_loaded_weight',
                'first_weight',
                'second_weight',
            ]);
        });
    }
};
