<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->text('cust_code')->nullable();
            $table->text('company_name')->nullable();
            $table->text('email')->nullable();
            $table->text('country_code')->nullable();
            $table->text('mobile')->nullable();

            // 1 = Active, 2 = Deactive
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment('1 = Active, 2 = Deactive');

            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();

            $table->longText('address')->nullable();

            $table->text('gst_no')->nullable();
            $table->text('pincode')->nullable();

            // 1 = Yes, 2 = No
            $table->unsignedTinyInteger('buyer')
                ->default(2)
                ->comment('1 = Yes, 2 = No');

            // 1 = Yes, 2 = No
            $table->unsignedTinyInteger('supplier')
                ->default(2)
                ->comment('1 = Yes, 2 = No');

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
