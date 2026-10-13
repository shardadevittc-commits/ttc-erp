<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove city_id only if it exists
        if (Schema::hasColumn('customers', 'city_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('city_id');
            });
        }

        Schema::table('customers', function (Blueprint $table) {
            // Add city_name if it does not exist
            if (!Schema::hasColumn('customers', 'city_name')) {
                $table->text('city_name')->nullable()->after('state_id');
            }

            // Add customer_name if it does not exist
            if (!Schema::hasColumn('customers', 'customer_name')) {
                $table->text('customer_name')->nullable()->after('company_name');
            }

            // Add GST details if it does not exist
            if (!Schema::hasColumn('customers', 'gst_details')) {
                $table->longText('gst_details')->nullable()->after('gst_no');
            }
        });
    }

    public function down(): void
    {
        // Remove newly added columns if they exist
        $columnsToDrop = [];

        foreach (['city_name', 'customer_name', 'gst_details'] as $column) {
            if (Schema::hasColumn('customers', $column)) {
                $columnsToDrop[] = $column;
            }
        }

        if (!empty($columnsToDrop)) {
            Schema::table('customers', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }

        // Restore city_id only if it does not exist
        if (!Schema::hasColumn('customers', 'city_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unsignedBigInteger('city_id')
                    ->nullable()
                    ->after('state_id');
            });
        }
    }
};