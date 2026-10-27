<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weights', function (Blueprint $table) {
            $table->id();

            // Existing material record reference
            $table->unsignedBigInteger('vehicle_id')->nullable()->index();

            // Vehicle and party details
            $table->string('truckno', 20);
            $table->unsignedBigInteger('cust_id')->index();
            $table->string('formlist', 30)->default('Material In');

            // Weighment details
            $table->string('rstno', 100)->nullable();
            $table->decimal('loadedweight', 12, 2)->nullable();
            $table->decimal('tareweight', 12, 2)->nullable();

            // Uploaded file paths
            $table->string('vehicleentryslip')->nullable();
            $table->string('weighmentslip')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes(); // deleted_at column
            $table->timestamps();

            // Optional indexes
            $table->index('formlist');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weights');
    }
};
