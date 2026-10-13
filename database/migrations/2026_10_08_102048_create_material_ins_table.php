<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_ins', function (Blueprint $table) {
            $table->id();

            $table->string('tokenno')->nullable();
            $table->unsignedBigInteger('cust_id');
            $table->string('formlist')->nullable();
            $table->string('truckno');
            $table->string('vehicleentryslip')->nullable();
            $table->string('transportname')->nullable();
            $table->unsignedInteger('noofpersons')->nullable();
            $table->string('drivername')->nullable();
            $table->string('contactno', 15)->nullable();

            // Created By
            $table->unsignedBigInteger('created_by')->nullable();

            // Created At & Updated At
            $table->timestamps();

            // Soft Delete
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_ins');
    }
};
