<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('scrap_dispatch')) {
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

                foreach (['pointing', 'point_discard', 'bedsawcut', 'misc'] as $category) {
                    $table->double($category . '_dispatch', 20, 3)->nullable()->comment('Weight converted to KG');
                    $table->string($category . '_type', 20)->nullable();
                    $table->double($category . '_rate', 20, 3)->nullable();
                    $table->double($category . '_total', 20, 3)->nullable();
                }

                foreach (['acid_pickling', 'short_blasting'] as $category) {
                    $table->double($category . '_dispatch', 20, 3)->nullable();
                    $table->string($category . '_type', 20)->nullable();
                    $table->double($category . '_rate', 20, 3)->nullable();
                    $table->double($category . '_total', 20, 3)->nullable();
                }

                $table->unsignedBigInteger('created_by')->nullable();
                $table->uuid('submission_token')->nullable()->unique();
                $table->timestamps();
            });

            return;
        }

        Schema::table('scrap_dispatch', function (Blueprint $table) {
            foreach (['acid_pickling', 'short_blasting'] as $category) {
                if (! Schema::hasColumn('scrap_dispatch', $category . '_dispatch')) {
                    $table->double($category . '_dispatch', 20, 3)->nullable();
                }
                if (! Schema::hasColumn('scrap_dispatch', $category . '_type')) {
                    $table->string($category . '_type', 20)->nullable();
                }
                if (! Schema::hasColumn('scrap_dispatch', $category . '_rate')) {
                    $table->double($category . '_rate', 20, 3)->nullable();
                }
                if (! Schema::hasColumn('scrap_dispatch', $category . '_total')) {
                    $table->double($category . '_total', 20, 3)->nullable();
                }
            }

            if (! Schema::hasColumn('scrap_dispatch', 'submission_token')) {
                $table->uuid('submission_token')->nullable()->unique();
            }
        });
    }

    public function down(): void
    {
        if (
            Schema::hasTable('scrap_dispatch')
            && ! Schema::hasColumn('scrap_dispatch', 'vehicle_id')
        ) {
            Schema::drop('scrap_dispatch');

            return;
        }

        if (! Schema::hasTable('scrap_dispatch')) {
            return;
        }

        Schema::table('scrap_dispatch', function (Blueprint $table) {
            if (Schema::hasColumn('scrap_dispatch', 'submission_token')) {
                $table->dropUnique(['submission_token']);
                $table->dropColumn('submission_token');
            }

            $columns = [];
            foreach (['acid_pickling', 'short_blasting'] as $category) {
                foreach (['_dispatch', '_type', '_rate', '_total'] as $suffix) {
                    $column = $category . $suffix;
                    if (Schema::hasColumn('scrap_dispatch', $column)) {
                        $columns[] = $column;
                    }
                }
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
