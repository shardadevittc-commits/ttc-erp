<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Remove old city_id column
            $table->dropColumn('city_id');

            // Add city_name
            $table->text('city_name')->nullable()->after('state_id');
            $table->text('customer_name')->nullable()->after('company_name');

            // Add GST details
            $table->longText('gst_details')->nullable()->after('gst_no');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['city_name', 'gst_details']);

            // Restore old city_id
            $table->unsignedBigInteger('city_id')->nullable()->after('state_id');
        });
    }
};
