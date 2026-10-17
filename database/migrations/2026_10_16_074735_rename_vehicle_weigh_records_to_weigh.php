<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('vehicle_weigh_records', 'weigh');

        Schema::table('weigh', function (Blueprint $table) {
            $table->renameColumn('material_in_id', 'vehicle_id');
        });

        Schema::table('weigh', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')
                ->comment('from gate_and_gaadi primary id')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('weigh', function (Blueprint $table) {
            $table->unsignedBigInteger('material_in_id')
                ->comment('')
                ->change();
        });

        Schema::table('weigh', function (Blueprint $table) {
            $table->renameColumn('vehicle_id', 'material_in_id');
        });

        Schema::rename('weigh', 'vehicle_weigh_records');
    }
};