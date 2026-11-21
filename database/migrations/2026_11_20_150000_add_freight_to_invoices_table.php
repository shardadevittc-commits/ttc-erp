<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'freight_type')) {
                $table->unsignedTinyInteger('freight_type')->default(1)->after('other_charges');
            }

            if (! Schema::hasColumn('invoices', 'freight_amount')) {
                $table->decimal('freight_amount', 20, 3)->default(0)->after('freight_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'freight_amount')) {
                $table->dropColumn('freight_amount');
            }

            if (Schema::hasColumn('invoices', 'freight_type')) {
                $table->dropColumn('freight_type');
            }
        });
    }
};
