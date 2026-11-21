<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrap_dispatch', function (Blueprint $table) {
            $table->id();

            $table->integer('vehicle_id')->unsigned();
            $table->integer('customer_id')->unsigned();

            $table->string('vehicle_type', 255)->nullable();

            $table->double('first_weight', 20, 3)->nullable();
            $table->double('last_weight', 20, 3)->nullable();
            $table->string('weight_slip', 200)->nullable();
            $table->double('actual_weight', 20, 3)->nullable();

            $table->string('invoice_no', 10)->nullable();
            $table->date('invoice_date')->nullable();

            $table->double('invoice_amt', 20, 3)->nullable();
            $table->double('invoice_gst', 20, 3)->nullable();
            $table->double('misc_amt', 20, 3)->nullable();
            $table->double('final_amt', 20, 3)->nullable();

            $table->string('mode_of_payment', 50)->nullable()->comment('Mode of Payment');
            $table->string('remarks', 255)->nullable();

            $table->double('pointing_dispatch', 20, 3)->nullable()->comment('Weight converted to KG');
            $table->string('pointing_type', 20)->nullable();
            $table->double('pointing_rate', 20, 3)->nullable();
            $table->double('pointing_total', 20, 3)->nullable();

            $table->double('point_discard_dispatch', 20, 3)->nullable()->comment('Weight converted to KG');
            $table->string('point_discard_type', 20)->nullable();
            $table->double('point_discard_rate', 20, 3)->nullable();
            $table->double('point_discard_total', 20, 3)->nullable();

            $table->double('bedsawcut_dispatch', 20, 3)->nullable()->comment('Weight converted to KG');
            $table->string('bedsawcut_type', 20)->nullable();
            $table->double('bedsawcut_rate', 20, 3)->nullable();
            $table->double('bedsawcut_total', 20, 3)->nullable();

            $table->double('misc_dispatch', 20, 3)->nullable()->comment('Weight converted to KG');
            $table->string('misc_type', 20)->nullable();
            $table->double('misc_rate', 20, 3)->nullable();
            $table->double('misc_total', 20, 3)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrap_dispatch');
    }
};
