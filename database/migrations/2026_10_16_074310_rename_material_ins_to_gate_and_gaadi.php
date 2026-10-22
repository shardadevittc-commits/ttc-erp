<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('material_ins', 'gate_and_gaadi');

        Schema::table('gate_and_gaadi', function (Blueprint $table) {
            $table->unsignedBigInteger('id')
                ->comment('id known as vehicle_id')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('gate_and_gaadi', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->comment('')->change();
        });

        Schema::rename('gate_and_gaadi', 'material_ins');
    }
};
