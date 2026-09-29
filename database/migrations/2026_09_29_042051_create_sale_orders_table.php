<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id')->nullable();

            $table->text('payment_term')->nullable();
            $table->text('customer_po_no')->nullable();
            $table->decimal('order_qty', 20, 3)->nullable();
            $table->decimal('basic_rate', 20, 3)->nullable();
            $table->text('dispatch_date')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedTinyInteger('freight_basis')->nullable()->comment('1 = EX, 2 = FOR');

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_orders');
    }
};
