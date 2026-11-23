<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['invoices', 'scrap_dispatch'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'public_token')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->uuid('public_token')->nullable();
                    $table->unique('public_token', $table->getTable() . '_public_token_unique');
                });
            }

            DB::table($tableName)
                ->whereNull('public_token')
                ->select('id')
                ->orderBy('id')
                ->chunkById(100, function ($records) use ($tableName): void {
                    foreach ($records as $record) {
                        DB::table($tableName)
                            ->where('id', $record->id)
                            ->update(['public_token' => (string) Str::uuid()]);
                    }
                });
        }
    }

    public function down(): void
    {
        foreach (['invoices', 'scrap_dispatch'] as $tableName) {
            if (Schema::hasColumn($tableName, 'public_token')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropUnique($tableName . '_public_token_unique');
                    $table->dropColumn('public_token');
                });
            }
        }
    }
};
