<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sizes', function (Blueprint $table) {
            // Status: 1 = Active, 2 = Deactive
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->after('size_name')
                ->comment('1 = Active, 2 = Deactive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sizes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
