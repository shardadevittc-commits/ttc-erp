<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gate_and_gaadi') || Schema::hasColumn('gate_and_gaadi', 'check_out_by')) {
            return;
        }

        Schema::table('gate_and_gaadi', function (Blueprint $table) {
            $table->unsignedBigInteger('check_out_by')->nullable()->after('check_out');
            $table->foreign('check_out_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('gate_and_gaadi') || ! Schema::hasColumn('gate_and_gaadi', 'check_out_by')) {
            return;
        }

        Schema::table('gate_and_gaadi', function (Blueprint $table) {
            $table->dropForeign(['check_out_by']);
            $table->dropColumn('check_out_by');
        });
    }
};
