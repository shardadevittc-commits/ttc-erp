<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gate_and_gaadi', function (Blueprint $table) {
            $table->timestamp('check_out')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('gate_and_gaadi', function (Blueprint $table) {
            $table->dropColumn('check_out');
        });
    }
};
