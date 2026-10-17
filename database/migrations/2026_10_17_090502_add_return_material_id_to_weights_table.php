<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weights', function (Blueprint $table) {
            $table->unsignedBigInteger('return_material_id')
                ->nullable()
                ->after('cust_id');
        });
    }

    public function down(): void
    {
        Schema::table('weights', function (Blueprint $table) {
            $table->dropColumn('return_material_id');
        });
    }
};
